import { z } from "zod";
import { COOKIE_NAME } from "@shared/const";
import { getSessionCookieOptions } from "./_core/cookies";
import { systemRouter } from "./_core/systemRouter";
import { publicProcedure, protectedProcedure, router } from "./_core/trpc";
import { TRPCError } from "@trpc/server";
import {
  getCompetitions, getFeaturedCompetitions, getCompetitionBySlug, getCompetitionById,
  createCompetition, updateCompetition, registerForCompetition, getUserRegistrations,
  getCompetitionRegistrations, checkUserRegistration, getUserResults, getCompetitionResults,
  getUserCertificates, getSchools, getSchoolByUserId, createSchool, getPartners,
  createContactMessage, subscribeToNewsletter, getPrizesByCompetition, getStudentProfile,
  createStudentProfile, getTeacherProfile, createTeacherProfile, getAdminStats,
  getAllUsers, getContactMessages, getUserById
} from "./db";
import { nanoid } from "nanoid";

// Admin procedure
const adminProcedure = protectedProcedure.use(({ ctx, next }) => {
  if (ctx.user.role !== 'admin') throw new TRPCError({ code: 'FORBIDDEN', message: 'غير مصرح لك بالوصول' });
  return next({ ctx });
});

export const appRouter = router({
  system: systemRouter,
  auth: router({
    me: publicProcedure.query(opts => opts.ctx.user),
    logout: publicProcedure.mutation(({ ctx }) => {
      const cookieOptions = getSessionCookieOptions(ctx.req);
      ctx.res.clearCookie(COOKIE_NAME, { ...cookieOptions, maxAge: -1 });
      return { success: true } as const;
    }),
  }),

  // ===== Competitions =====
  competitions: router({
    list: publicProcedure.input(z.object({
      status: z.string().optional(),
      category: z.string().optional(),
      educationLevel: z.string().optional(),
    }).optional()).query(async ({ input }) => {
      return getCompetitions(input ?? {});
    }),
    featured: publicProcedure.query(async () => {
      return getFeaturedCompetitions();
    }),
    bySlug: publicProcedure.input(z.object({ slug: z.string() })).query(async ({ input }) => {
      const comp = await getCompetitionBySlug(input.slug);
      if (!comp) throw new TRPCError({ code: 'NOT_FOUND', message: 'المسابقة غير موجودة' });
      return comp;
    }),
    byId: publicProcedure.input(z.object({ id: z.number() })).query(async ({ input }) => {
      const comp = await getCompetitionById(input.id);
      if (!comp) throw new TRPCError({ code: 'NOT_FOUND', message: 'المسابقة غير موجودة' });
      return comp;
    }),
    prizes: publicProcedure.input(z.object({ competitionId: z.number() })).query(async ({ input }) => {
      return getPrizesByCompetition(input.competitionId);
    }),
    create: adminProcedure.input(z.object({
      title: z.string().min(3),
      slug: z.string().min(3),
      description: z.string().optional(),
      shortDescription: z.string().optional(),
      category: z.enum(['arabic', 'english', 'math', 'science', 'general', 'reading', 'other']),
      educationLevel: z.enum(['primary', 'middle', 'secondary', 'university', 'all']).optional(),
      ageFrom: z.number().optional(),
      ageTo: z.number().optional(),
      startDate: z.string().optional(),
      endDate: z.string().optional(),
      registrationDeadline: z.string().optional(),
      maxParticipants: z.number().optional(),
      rules: z.string().optional(),
      prizes: z.string().optional(),
      stages: z.string().optional(),
      isFeatured: z.boolean().optional(),
    })).mutation(async ({ input, ctx }) => {
      return createCompetition({
        ...input,
        startDate: input.startDate ? new Date(input.startDate) : undefined,
        endDate: input.endDate ? new Date(input.endDate) : undefined,
        registrationDeadline: input.registrationDeadline ? new Date(input.registrationDeadline) : undefined,
        createdBy: ctx.user.id,
      });
    }),
    update: adminProcedure.input(z.object({
      id: z.number(),
      data: z.object({
        title: z.string().optional(),
        status: z.enum(['upcoming', 'active', 'completed', 'cancelled']).optional(),
        isFeatured: z.boolean().optional(),
      }),
    })).mutation(async ({ input }) => {
      return updateCompetition(input.id, input.data);
    }),
  }),

  // ===== Registrations =====
  registrations: router({
    register: protectedProcedure.input(z.object({
      competitionId: z.number(),
      schoolId: z.number().optional(),
    })).mutation(async ({ input, ctx }) => {
      const existing = await checkUserRegistration(ctx.user.id, input.competitionId);
      if (existing) throw new TRPCError({ code: 'CONFLICT', message: 'أنت مسجل بالفعل في هذه المسابقة' });
      return registerForCompetition({ ...input, userId: ctx.user.id });
    }),
    myRegistrations: protectedProcedure.query(async ({ ctx }) => {
      return getUserRegistrations(ctx.user.id);
    }),
    checkRegistration: protectedProcedure.input(z.object({ competitionId: z.number() })).query(async ({ input, ctx }) => {
      return checkUserRegistration(ctx.user.id, input.competitionId);
    }),
    competitionRegistrations: adminProcedure.input(z.object({ competitionId: z.number() })).query(async ({ input }) => {
      return getCompetitionRegistrations(input.competitionId);
    }),
  }),

  // ===== Results =====
  results: router({
    myResults: protectedProcedure.query(async ({ ctx }) => {
      return getUserResults(ctx.user.id);
    }),
    competitionResults: publicProcedure.input(z.object({ competitionId: z.number() })).query(async ({ input }) => {
      return getCompetitionResults(input.competitionId);
    }),
  }),

  // ===== Certificates =====
  certificates: router({
    myCertificates: protectedProcedure.query(async ({ ctx }) => {
      return getUserCertificates(ctx.user.id);
    }),
  }),

  // ===== Schools =====
  schools: router({
    list: protectedProcedure.query(async ({ ctx }) => {
      if (ctx.user.role === 'coordinator') return getSchools(ctx.user.id);
      if (ctx.user.role === 'admin') return getSchools();
      throw new TRPCError({ code: 'FORBIDDEN' });
    }),
    mySchool: protectedProcedure.query(async ({ ctx }) => {
      return getSchoolByUserId(ctx.user.id);
    }),
    create: protectedProcedure.input(z.object({
      schoolName: z.string().min(2),
      schoolCode: z.string().optional(),
      city: z.string().optional(),
      region: z.string().optional(),
      educationType: z.enum(['government', 'private', 'international']).optional(),
      principalName: z.string().optional(),
      phone: z.string().optional(),
      address: z.string().optional(),
    })).mutation(async ({ input, ctx }) => {
      return createSchool({ ...input, userId: ctx.user.id });
    }),
  }),

  // ===== Partners =====
  partners: router({
    list: publicProcedure.query(async () => {
      return getPartners();
    }),
  }),

  // ===== Contact =====
  contact: router({
    send: publicProcedure.input(z.object({
      name: z.string().min(2),
      email: z.string().email(),
      phone: z.string().optional(),
      subject: z.string().optional(),
      message: z.string().min(10),
    })).mutation(async ({ input }) => {
      await createContactMessage(input);
      return { success: true };
    }),
    list: adminProcedure.query(async () => {
      return getContactMessages();
    }),
  }),

  // ===== Newsletter =====
  newsletter: router({
    subscribe: publicProcedure.input(z.object({
      email: z.string().email(),
      name: z.string().optional(),
    })).mutation(async ({ input }) => {
      await subscribeToNewsletter(input.email, input.name);
      return { success: true };
    }),
  }),

  // ===== User Profile =====
  profile: router({
    studentProfile: protectedProcedure.query(async ({ ctx }) => {
      return getStudentProfile(ctx.user.id);
    }),
    teacherProfile: protectedProcedure.query(async ({ ctx }) => {
      return getTeacherProfile(ctx.user.id);
    }),
    createStudentProfile: protectedProcedure.input(z.object({
      studentId: z.string().optional(),
      schoolId: z.number().optional(),
      grade: z.string().optional(),
      educationLevel: z.enum(['primary', 'middle', 'secondary', 'university']).optional(),
      city: z.string().optional(),
    })).mutation(async ({ input, ctx }) => {
      return createStudentProfile({ ...input, userId: ctx.user.id });
    }),
    createTeacherProfile: protectedProcedure.input(z.object({
      schoolId: z.number().optional(),
      subject: z.string().optional(),
      city: z.string().optional(),
    })).mutation(async ({ input, ctx }) => {
      return createTeacherProfile({ ...input, userId: ctx.user.id });
    }),
  }),

  // ===== Admin Dashboard =====
  admin: router({
    stats: adminProcedure.query(async () => {
      return getAdminStats();
    }),
    users: adminProcedure.query(async () => {
      return getAllUsers(100);
    }),
    messages: adminProcedure.query(async () => {
      return getContactMessages(50);
    }),
  }),
});

export type AppRouter = typeof appRouter;
