import {
  int,
  mysqlEnum,
  mysqlTable,
  text,
  timestamp,
  varchar,
  boolean,
  decimal,
  date,
} from "drizzle-orm/mysql-core";

// ===== جدول المستخدمين =====
export const users = mysqlTable("users", {
  id: int("id").autoincrement().primaryKey(),
  openId: varchar("openId", { length: 64 }).notNull().unique(),
  name: text("name"),
  email: varchar("email", { length: 320 }),
  phone: varchar("phone", { length: 20 }),
  loginMethod: varchar("loginMethod", { length: 64 }),
  role: mysqlEnum("role", ["student", "teacher", "school", "coordinator", "admin", "user"]).default("user").notNull(),
  avatarUrl: text("avatarUrl"),
  isActive: boolean("isActive").default(true).notNull(),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
  lastSignedIn: timestamp("lastSignedIn").defaultNow().notNull(),
});

// ===== جدول ملفات الطلاب =====
export const studentProfiles = mysqlTable("student_profiles", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  studentId: varchar("studentId", { length: 50 }),
  schoolId: int("schoolId"),
  grade: varchar("grade", { length: 50 }),
  educationLevel: mysqlEnum("educationLevel", ["primary", "middle", "secondary", "university"]),
  dateOfBirth: date("dateOfBirth"),
  city: varchar("city", { length: 100 }),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

// ===== جدول ملفات المعلمين =====
export const teacherProfiles = mysqlTable("teacher_profiles", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  schoolId: int("schoolId"),
  subject: varchar("subject", { length: 100 }),
  city: varchar("city", { length: 100 }),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

// ===== جدول المدارس =====
export const schools = mysqlTable("schools", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  schoolName: varchar("schoolName", { length: 200 }).notNull(),
  schoolCode: varchar("schoolCode", { length: 50 }),
  city: varchar("city", { length: 100 }),
  region: varchar("region", { length: 100 }),
  educationType: mysqlEnum("educationType", ["government", "private", "international"]).default("government"),
  principalName: varchar("principalName", { length: 100 }),
  phone: varchar("phone", { length: 20 }),
  address: text("address"),
  isVerified: boolean("isVerified").default(false),
  coordinatorId: int("coordinatorId"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

// ===== جدول المسابقات =====
export const competitions = mysqlTable("competitions", {
  id: int("id").autoincrement().primaryKey(),
  title: varchar("title", { length: 200 }).notNull(),
  slug: varchar("slug", { length: 200 }).notNull().unique(),
  description: text("description"),
  shortDescription: varchar("shortDescription", { length: 500 }),
  category: mysqlEnum("category", ["arabic", "english", "math", "science", "general", "reading", "other"]).notNull(),
  educationLevel: mysqlEnum("educationLevel", ["primary", "middle", "secondary", "university", "all"]).default("all"),
  ageFrom: int("ageFrom"),
  ageTo: int("ageTo"),
  startDate: timestamp("startDate"),
  endDate: timestamp("endDate"),
  registrationDeadline: timestamp("registrationDeadline"),
  maxParticipants: int("maxParticipants"),
  status: mysqlEnum("status", ["upcoming", "active", "completed", "cancelled"]).default("upcoming"),
  imageUrl: text("imageUrl"),
  rules: text("rules"),
  prizes: text("prizes"),
  stages: text("stages"),
  isFeatured: boolean("isFeatured").default(false),
  createdBy: int("createdBy"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
});

// ===== جدول التسجيل في المسابقات =====
export const competitionRegistrations = mysqlTable("competition_registrations", {
  id: int("id").autoincrement().primaryKey(),
  competitionId: int("competitionId").notNull(),
  userId: int("userId").notNull(),
  schoolId: int("schoolId"),
  status: mysqlEnum("status", ["pending", "approved", "rejected", "withdrawn"]).default("pending"),
  registeredAt: timestamp("registeredAt").defaultNow().notNull(),
  approvedAt: timestamp("approvedAt"),
  notes: text("notes"),
});

// ===== جدول النتائج =====
export const results = mysqlTable("results", {
  id: int("id").autoincrement().primaryKey(),
  competitionId: int("competitionId").notNull(),
  userId: int("userId").notNull(),
  score: decimal("score", { precision: 10, scale: 2 }),
  rank: int("rank"),
  stage: varchar("stage", { length: 50 }),
  status: mysqlEnum("status", ["qualified", "eliminated", "winner", "runner_up"]),
  notes: text("notes"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

// ===== جدول الشهادات =====
export const certificates = mysqlTable("certificates", {
  id: int("id").autoincrement().primaryKey(),
  userId: int("userId").notNull(),
  competitionId: int("competitionId").notNull(),
  certificateNumber: varchar("certificateNumber", { length: 100 }).notNull().unique(),
  type: mysqlEnum("type", ["participation", "winner", "runner_up", "excellence"]).default("participation"),
  issuedAt: timestamp("issuedAt").defaultNow().notNull(),
  certificateUrl: text("certificateUrl"),
});

// ===== جدول الجوائز =====
export const prizes = mysqlTable("prizes", {
  id: int("id").autoincrement().primaryKey(),
  competitionId: int("competitionId").notNull(),
  rank: int("rank").notNull(),
  title: varchar("title", { length: 200 }).notNull(),
  description: text("description"),
  value: decimal("value", { precision: 10, scale: 2 }),
  currency: varchar("currency", { length: 10 }).default("SAR"),
  imageUrl: text("imageUrl"),
});

// ===== جدول الشركاء =====
export const partners = mysqlTable("partners", {
  id: int("id").autoincrement().primaryKey(),
  name: varchar("name", { length: 200 }).notNull(),
  logoUrl: text("logoUrl"),
  websiteUrl: text("websiteUrl"),
  type: mysqlEnum("type", ["sponsor", "educational", "media", "government"]).default("sponsor"),
  isActive: boolean("isActive").default(true),
  sortOrder: int("sortOrder").default(0),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

// ===== جدول رسائل التواصل =====
export const contactMessages = mysqlTable("contact_messages", {
  id: int("id").autoincrement().primaryKey(),
  name: varchar("name", { length: 100 }).notNull(),
  email: varchar("email", { length: 320 }).notNull(),
  phone: varchar("phone", { length: 20 }),
  subject: varchar("subject", { length: 200 }),
  message: text("message").notNull(),
  status: mysqlEnum("status", ["new", "read", "replied"]).default("new"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
});

// ===== جدول المشتركين في النشرة البريدية =====
export const newsletterSubscribers = mysqlTable("newsletter_subscribers", {
  id: int("id").autoincrement().primaryKey(),
  email: varchar("email", { length: 320 }).notNull().unique(),
  name: varchar("name", { length: 100 }),
  isActive: boolean("isActive").default(true),
  subscribedAt: timestamp("subscribedAt").defaultNow().notNull(),
});

// ===== أنواع TypeScript =====
export type User = typeof users.$inferSelect;
export type InsertUser = typeof users.$inferInsert;
export type Competition = typeof competitions.$inferSelect;
export type InsertCompetition = typeof competitions.$inferInsert;
export type CompetitionRegistration = typeof competitionRegistrations.$inferSelect;
export type Result = typeof results.$inferSelect;
export type Certificate = typeof certificates.$inferSelect;
export type School = typeof schools.$inferSelect;
export type Prize = typeof prizes.$inferSelect;
export type Partner = typeof partners.$inferSelect;
export type ContactMessage = typeof contactMessages.$inferSelect;
export type NewsletterSubscriber = typeof newsletterSubscribers.$inferSelect;
