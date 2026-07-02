import { eq, desc, and, like, or, count, sql } from "drizzle-orm";
import { drizzle } from "drizzle-orm/mysql2";
import {
  users, InsertUser, competitions, competitionRegistrations,
  results, certificates, schools, partners, contactMessages,
  newsletterSubscribers, prizes, studentProfiles, teacherProfiles
} from "../drizzle/schema";
import { ENV } from './_core/env';

let _db: ReturnType<typeof drizzle> | null = null;

export async function getDb() {
  if (!_db && process.env.DATABASE_URL) {
    try {
      _db = drizzle(process.env.DATABASE_URL);
    } catch (error) {
      console.warn("[Database] Failed to connect:", error);
      _db = null;
    }
  }
  return _db;
}

// ===== Users =====
export async function upsertUser(user: InsertUser): Promise<void> {
  if (!user.openId) throw new Error("User openId is required for upsert");
  const db = await getDb();
  if (!db) { console.warn("[Database] Cannot upsert user: database not available"); return; }
  try {
    const values: InsertUser = { openId: user.openId };
    const updateSet: Record<string, unknown> = {};
    const textFields = ["name", "email", "loginMethod"] as const;
    type TextField = (typeof textFields)[number];
    const assignNullable = (field: TextField) => {
      const value = user[field];
      if (value === undefined) return;
      const normalized = value ?? null;
      values[field] = normalized;
      updateSet[field] = normalized;
    };
    textFields.forEach(assignNullable);
    if (user.lastSignedIn !== undefined) { values.lastSignedIn = user.lastSignedIn; updateSet.lastSignedIn = user.lastSignedIn; }
    if (user.role !== undefined) { values.role = user.role; updateSet.role = user.role; }
    else if (user.openId === ENV.ownerOpenId) { values.role = 'admin'; updateSet.role = 'admin'; }
    if (!values.lastSignedIn) values.lastSignedIn = new Date();
    if (Object.keys(updateSet).length === 0) updateSet.lastSignedIn = new Date();
    await db.insert(users).values(values).onDuplicateKeyUpdate({ set: updateSet });
  } catch (error) { console.error("[Database] Failed to upsert user:", error); throw error; }
}

export async function getUserByOpenId(openId: string) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(users).where(eq(users.openId, openId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function getUserById(id: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(users).where(eq(users.id, id)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function getAllUsers(limit = 50, offset = 0) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(users).limit(limit).offset(offset).orderBy(desc(users.createdAt));
}

export async function getUsersCount() {
  const db = await getDb();
  if (!db) return 0;
  const result = await db.select({ count: count() }).from(users);
  return result[0]?.count ?? 0;
}

// ===== Competitions =====
export async function getCompetitions(filters?: { status?: string; category?: string; educationLevel?: string }) {
  const db = await getDb();
  if (!db) return [];
  const conditions = [];
  if (filters?.status) conditions.push(eq(competitions.status, filters.status as any));
  if (filters?.category) conditions.push(eq(competitions.category, filters.category as any));
  if (filters?.educationLevel) conditions.push(or(eq(competitions.educationLevel, filters.educationLevel as any), eq(competitions.educationLevel, 'all')));
  const query = db.select().from(competitions).orderBy(desc(competitions.createdAt));
  if (conditions.length > 0) return query.where(and(...conditions));
  return query;
}

export async function getFeaturedCompetitions() {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(competitions).where(eq(competitions.isFeatured, true)).limit(6).orderBy(desc(competitions.createdAt));
}

export async function getCompetitionBySlug(slug: string) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(competitions).where(eq(competitions.slug, slug)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function getCompetitionById(id: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(competitions).where(eq(competitions.id, id)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function createCompetition(data: typeof competitions.$inferInsert) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  const result = await db.insert(competitions).values(data);
  return result;
}

export async function updateCompetition(id: number, data: Partial<typeof competitions.$inferInsert>) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.update(competitions).set(data).where(eq(competitions.id, id));
}

export async function getCompetitionsCount() {
  const db = await getDb();
  if (!db) return 0;
  const result = await db.select({ count: count() }).from(competitions);
  return result[0]?.count ?? 0;
}

// ===== Registrations =====
export async function registerForCompetition(data: typeof competitionRegistrations.$inferInsert) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.insert(competitionRegistrations).values(data);
}

export async function getUserRegistrations(userId: number) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(competitionRegistrations).where(eq(competitionRegistrations.userId, userId)).orderBy(desc(competitionRegistrations.registeredAt));
}

export async function getCompetitionRegistrations(competitionId: number) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(competitionRegistrations).where(eq(competitionRegistrations.competitionId, competitionId));
}

export async function getRegistrationsCount() {
  const db = await getDb();
  if (!db) return 0;
  const result = await db.select({ count: count() }).from(competitionRegistrations);
  return result[0]?.count ?? 0;
}

export async function checkUserRegistration(userId: number, competitionId: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(competitionRegistrations)
    .where(and(eq(competitionRegistrations.userId, userId), eq(competitionRegistrations.competitionId, competitionId)))
    .limit(1);
  return result.length > 0 ? result[0] : undefined;
}

// ===== Results =====
export async function getUserResults(userId: number) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(results).where(eq(results.userId, userId)).orderBy(desc(results.createdAt));
}

export async function getCompetitionResults(competitionId: number) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(results).where(eq(results.competitionId, competitionId)).orderBy(results.rank);
}

// ===== Certificates =====
export async function getUserCertificates(userId: number) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(certificates).where(eq(certificates.userId, userId)).orderBy(desc(certificates.issuedAt));
}

// ===== Schools =====
export async function getSchools(coordinatorId?: number) {
  const db = await getDb();
  if (!db) return [];
  if (coordinatorId) return db.select().from(schools).where(eq(schools.coordinatorId, coordinatorId));
  return db.select().from(schools).orderBy(desc(schools.createdAt));
}

export async function getSchoolByUserId(userId: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(schools).where(eq(schools.userId, userId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function createSchool(data: typeof schools.$inferInsert) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.insert(schools).values(data);
}

export async function getSchoolsCount() {
  const db = await getDb();
  if (!db) return 0;
  const result = await db.select({ count: count() }).from(schools);
  return result[0]?.count ?? 0;
}

// ===== Partners =====
export async function getPartners() {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(partners).where(eq(partners.isActive, true)).orderBy(partners.sortOrder);
}

// ===== Contact Messages =====
export async function createContactMessage(data: typeof contactMessages.$inferInsert) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.insert(contactMessages).values(data);
}

export async function getContactMessages(limit = 50) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(contactMessages).orderBy(desc(contactMessages.createdAt)).limit(limit);
}

// ===== Newsletter =====
export async function subscribeToNewsletter(email: string, name?: string) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.insert(newsletterSubscribers).values({ email, name }).onDuplicateKeyUpdate({ set: { isActive: true } });
}

// ===== Prizes =====
export async function getPrizesByCompetition(competitionId: number) {
  const db = await getDb();
  if (!db) return [];
  return db.select().from(prizes).where(eq(prizes.competitionId, competitionId)).orderBy(prizes.rank);
}

// ===== Student Profiles =====
export async function getStudentProfile(userId: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(studentProfiles).where(eq(studentProfiles.userId, userId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function createStudentProfile(data: typeof studentProfiles.$inferInsert) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.insert(studentProfiles).values(data);
}

// ===== Teacher Profiles =====
export async function getTeacherProfile(userId: number) {
  const db = await getDb();
  if (!db) return undefined;
  const result = await db.select().from(teacherProfiles).where(eq(teacherProfiles.userId, userId)).limit(1);
  return result.length > 0 ? result[0] : undefined;
}

export async function createTeacherProfile(data: typeof teacherProfiles.$inferInsert) {
  const db = await getDb();
  if (!db) throw new Error("Database not available");
  return db.insert(teacherProfiles).values(data);
}

// ===== Dashboard Stats =====
export async function getAdminStats() {
  const db = await getDb();
  if (!db) return { users: 0, competitions: 0, registrations: 0, schools: 0 };
  const [usersCount, competitionsCount, registrationsCount, schoolsCount] = await Promise.all([
    getUsersCount(), getCompetitionsCount(), getRegistrationsCount(), getSchoolsCount()
  ]);
  return { users: usersCount, competitions: competitionsCount, registrations: registrationsCount, schools: schoolsCount };
}
