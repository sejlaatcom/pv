import { describe, expect, it, vi, beforeEach } from "vitest";
import { appRouter } from "./routers";
import type { TrpcContext } from "./_core/context";

// Mock db module
vi.mock("./db", () => ({
  getCompetitions: vi.fn().mockResolvedValue([]),
  getFeaturedCompetitions: vi.fn().mockResolvedValue([]),
  getCompetitionBySlug: vi.fn().mockResolvedValue(undefined),
  getCompetitionById: vi.fn().mockResolvedValue(undefined),
  createCompetition: vi.fn().mockResolvedValue({ insertId: 1 }),
  updateCompetition: vi.fn().mockResolvedValue({}),
  registerForCompetition: vi.fn().mockResolvedValue({ insertId: 1 }),
  getUserRegistrations: vi.fn().mockResolvedValue([]),
  getCompetitionRegistrations: vi.fn().mockResolvedValue([]),
  checkUserRegistration: vi.fn().mockResolvedValue(undefined),
  getUserResults: vi.fn().mockResolvedValue([]),
  getCompetitionResults: vi.fn().mockResolvedValue([]),
  getUserCertificates: vi.fn().mockResolvedValue([]),
  getSchools: vi.fn().mockResolvedValue([]),
  getSchoolByUserId: vi.fn().mockResolvedValue(undefined),
  createSchool: vi.fn().mockResolvedValue({ insertId: 1 }),
  getPartners: vi.fn().mockResolvedValue([]),
  createContactMessage: vi.fn().mockResolvedValue({ insertId: 1 }),
  subscribeToNewsletter: vi.fn().mockResolvedValue({}),
  getPrizesByCompetition: vi.fn().mockResolvedValue([]),
  getStudentProfile: vi.fn().mockResolvedValue(undefined),
  createStudentProfile: vi.fn().mockResolvedValue({ insertId: 1 }),
  getTeacherProfile: vi.fn().mockResolvedValue(undefined),
  createTeacherProfile: vi.fn().mockResolvedValue({ insertId: 1 }),
  getAdminStats: vi.fn().mockResolvedValue({ users: 0, competitions: 0, registrations: 0, schools: 0 }),
  getAllUsers: vi.fn().mockResolvedValue([]),
  getContactMessages: vi.fn().mockResolvedValue([]),
  getUserById: vi.fn().mockResolvedValue(undefined),
  upsertUser: vi.fn().mockResolvedValue(undefined),
  getUserByOpenId: vi.fn().mockResolvedValue(undefined),
}));

function makeCtx(overrides: Partial<TrpcContext> = {}): TrpcContext {
  return {
    user: null,
    req: { protocol: "https", headers: {} } as TrpcContext["req"],
    res: { clearCookie: vi.fn() } as unknown as TrpcContext["res"],
    ...overrides,
  };
}

function makeUser(role: string = "user") {
  return {
    id: 1,
    openId: "test-user",
    email: "test@example.com",
    name: "Test User",
    loginMethod: "manus",
    role: role as "user" | "admin",
    phone: null,
    avatarUrl: null,
    isActive: true,
    createdAt: new Date(),
    updatedAt: new Date(),
    lastSignedIn: new Date(),
  };
}

describe("competitions router", () => {
  it("lists competitions publicly", async () => {
    const caller = appRouter.createCaller(makeCtx());
    const result = await caller.competitions.list({});
    expect(Array.isArray(result)).toBe(true);
  });

  it("returns featured competitions publicly", async () => {
    const caller = appRouter.createCaller(makeCtx());
    const result = await caller.competitions.featured();
    expect(Array.isArray(result)).toBe(true);
  });

  it("throws NOT_FOUND for unknown slug", async () => {
    const caller = appRouter.createCaller(makeCtx());
    await expect(caller.competitions.bySlug({ slug: "nonexistent" })).rejects.toThrow();
  });

  it("blocks non-admin from creating competition", async () => {
    const caller = appRouter.createCaller(makeCtx({ user: makeUser("user") }));
    await expect(caller.competitions.create({
      title: "Test", slug: "test", category: "arabic",
    })).rejects.toThrow();
  });
});

describe("contact router", () => {
  it("sends contact message publicly", async () => {
    const caller = appRouter.createCaller(makeCtx());
    const result = await caller.contact.send({
      name: "أحمد",
      email: "ahmed@example.com",
      message: "رسالة اختبار طويلة بما فيه الكفاية",
    });
    expect(result.success).toBe(true);
  });
});

describe("newsletter router", () => {
  it("subscribes to newsletter publicly", async () => {
    const caller = appRouter.createCaller(makeCtx());
    const result = await caller.newsletter.subscribe({ email: "test@example.com", name: "Test" });
    expect(result.success).toBe(true);
  });
});

describe("registrations router", () => {
  it("blocks unauthenticated registration", async () => {
    const caller = appRouter.createCaller(makeCtx({ user: null }));
    await expect(caller.registrations.register({ competitionId: 1 })).rejects.toThrow();
  });
});
