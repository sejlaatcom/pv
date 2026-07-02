import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import { toast } from "sonner";
import {
  Trophy, Calendar, Users, Award, CheckCircle, ArrowRight,
  Clock, Target, BookOpen, Star, AlertCircle, Layers
} from "lucide-react";

interface CompetitionDetailProps { slug: string; }

const categoryLabels: Record<string, string> = {
  arabic: "اللغة العربية", english: "الإنجليزية", math: "الرياضيات",
  science: "العلوم", general: "الثقافة العامة", reading: "القراءة", other: "أخرى",
};
const levelLabels: Record<string, string> = {
  primary: "ابتدائي", middle: "متوسط", secondary: "ثانوي", university: "جامعي", all: "جميع المراحل",
};
const statusColors: Record<string, string> = {
  upcoming: "bg-blue-100 text-blue-700", active: "bg-green-100 text-green-700",
  completed: "bg-gray-100 text-gray-600", cancelled: "bg-red-100 text-red-700",
};
const statusLabels: Record<string, string> = {
  upcoming: "قادمة", active: "جارية", completed: "منتهية", cancelled: "ملغاة",
};

export default function CompetitionDetail({ slug }: CompetitionDetailProps) {
  const { user, isAuthenticated } = useAuth();
  const { data: competition, isLoading, error } = trpc.competitions.bySlug.useQuery({ slug });
  const { data: registration } = trpc.registrations.checkRegistration.useQuery(
    { competitionId: competition?.id ?? 0 },
    { enabled: isAuthenticated && !!competition?.id }
  );

  const registerMutation = trpc.registrations.register.useMutation({
    onSuccess: () => toast.success("تم التسجيل في المسابقة بنجاح! سيتم مراجعة طلبك."),
    onError: (err) => toast.error(err.message ?? "حدث خطأ أثناء التسجيل"),
  });

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 pt-8">
        <div className="container">
          <div className="animate-pulse space-y-4">
            <div className="h-64 bg-gray-200 rounded-2xl"></div>
            <div className="h-8 bg-gray-200 rounded w-1/2"></div>
            <div className="h-4 bg-gray-200 rounded w-3/4"></div>
          </div>
        </div>
      </div>
    );
  }

  if (error || !competition) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <AlertCircle className="w-16 h-16 mx-auto mb-4 text-red-300" />
          <h2 className="text-2xl font-black text-gray-700 mb-2">المسابقة غير موجودة</h2>
          <Button asChild variant="outline"><Link href="/competitions">العودة للمسابقات</Link></Button>
        </div>
      </div>
    );
  }

  const isRegistered = !!registration;
  const canRegister = competition.status === 'upcoming' || competition.status === 'active';

  // Parse stages and prizes from JSON strings
  let stages: string[] = [];
  let prizesData: { rank: number; title: string; value?: string }[] = [];
  try { stages = competition.stages ? JSON.parse(competition.stages) : []; } catch { stages = []; }
  try { prizesData = competition.prizes ? JSON.parse(competition.prizes) : []; } catch { prizesData = []; }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero */}
      <div className="py-16 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 20% 50%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10">
          <Link href="/competitions" className="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm mb-6 transition-colors">
            <ArrowRight className="w-4 h-4" />
            العودة للمسابقات
          </Link>
          <div className="flex flex-wrap items-center gap-3 mb-4">
            <span className="badge-gold">{categoryLabels[competition.category]}</span>
            {competition.educationLevel && (
              <span className="badge-primary" style={{ background: "oklch(1 0 0 / 0.1)", color: "white", borderColor: "oklch(1 0 0 / 0.2)" }}>
                {levelLabels[competition.educationLevel]}
              </span>
            )}
            <span className={`px-3 py-1 rounded-full text-xs font-semibold ${statusColors[competition.status ?? 'upcoming']}`}>
              {statusLabels[competition.status ?? 'upcoming']}
            </span>
            {competition.isFeatured && (
              <span className="flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"
                style={{ background: "oklch(0.72 0.15 75 / 0.2)", color: "oklch(0.85 0.12 75)", border: "1px solid oklch(0.72 0.15 75 / 0.3)" }}>
                <Star className="w-3 h-3 fill-current" />
                مسابقة مميزة
              </span>
            )}
          </div>
          <h1 className="text-3xl md:text-4xl lg:text-5xl font-black mb-4">{competition.title}</h1>
          {competition.shortDescription && (
            <p className="text-white/75 text-lg max-w-2xl">{competition.shortDescription}</p>
          )}
        </div>
      </div>

      <div className="container py-10">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-6">
            {/* Description */}
            {competition.description && (
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 className="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                  <BookOpen className="w-5 h-5" style={{ color: "oklch(0.42 0.15 145)" }} />
                  وصف المسابقة
                </h2>
                <p className="text-gray-600 leading-relaxed">{competition.description}</p>
              </div>
            )}

            {/* Rules */}
            {competition.rules && (
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 className="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                  <CheckCircle className="w-5 h-5" style={{ color: "oklch(0.42 0.15 145)" }} />
                  شروط المشاركة
                </h2>
                <div className="space-y-2">
                  {competition.rules.split('\n').filter(Boolean).map((rule, i) => (
                    <div key={i} className="flex items-start gap-3">
                      <div className="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5"
                        style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                        {i + 1}
                      </div>
                      <p className="text-gray-600 text-sm">{rule}</p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Stages */}
            {stages.length > 0 && (
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 className="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                  <Layers className="w-5 h-5" style={{ color: "oklch(0.42 0.15 145)" }} />
                  مراحل المسابقة
                </h2>
                <div className="space-y-3">
                  {stages.map((stage, i) => (
                    <div key={i} className="flex items-center gap-4 p-4 rounded-xl" style={{ background: "oklch(0.97 0.01 145)" }}>
                      <div className="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black flex-shrink-0"
                        style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                        {i + 1}
                      </div>
                      <p className="text-gray-700 font-medium">{stage}</p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Prizes */}
            {prizesData.length > 0 && (
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 className="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                  <Award className="w-5 h-5" style={{ color: "oklch(0.72 0.15 75)" }} />
                  الجوائز
                </h2>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  {prizesData.map((prize, i) => {
                    const colors = [
                      { bg: "from-yellow-400 to-amber-500", text: "text-amber-700", light: "#fffbeb" },
                      { bg: "from-gray-300 to-gray-400", text: "text-gray-600", light: "#f9fafb" },
                      { bg: "from-amber-600 to-amber-700", text: "text-amber-800", light: "#fef3c7" },
                    ];
                    const c = colors[i] ?? colors[0];
                    return (
                      <div key={i} className="text-center p-5 rounded-2xl border-2" style={{ background: c.light, borderColor: "transparent" }}>
                        <div className={`w-14 h-14 rounded-2xl bg-gradient-to-br ${c.bg} flex items-center justify-center mx-auto mb-3 shadow-md`}>
                          <Trophy className="w-7 h-7 text-white" />
                        </div>
                        <p className="font-black text-gray-800 text-lg">المركز {prize.rank}</p>
                        <p className="font-semibold text-gray-700 mt-1">{prize.title}</p>
                        {prize.value && <p className={`text-sm font-bold mt-1 ${c.text}`}>{prize.value}</p>}
                      </div>
                    );
                  })}
                </div>
              </div>
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-5">
            {/* Registration Card */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
              <h3 className="text-lg font-black text-gray-800 mb-5">التسجيل في المسابقة</h3>

              {/* Info */}
              <div className="space-y-3 mb-6">
                {competition.startDate && (
                  <div className="flex items-center gap-3 text-sm">
                    <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ background: "#f0fdf4" }}>
                      <Calendar className="w-4 h-4" style={{ color: "#166534" }} />
                    </div>
                    <div>
                      <p className="text-gray-400 text-xs">تاريخ البداية</p>
                      <p className="text-gray-700 font-semibold">{new Date(competition.startDate).toLocaleDateString('ar-SA')}</p>
                    </div>
                  </div>
                )}
                {competition.endDate && (
                  <div className="flex items-center gap-3 text-sm">
                    <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ background: "#fef2f2" }}>
                      <Clock className="w-4 h-4 text-red-500" />
                    </div>
                    <div>
                      <p className="text-gray-400 text-xs">تاريخ الانتهاء</p>
                      <p className="text-gray-700 font-semibold">{new Date(competition.endDate).toLocaleDateString('ar-SA')}</p>
                    </div>
                  </div>
                )}
                {competition.registrationDeadline && (
                  <div className="flex items-center gap-3 text-sm">
                    <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ background: "#fffbeb" }}>
                      <Target className="w-4 h-4" style={{ color: "#b45309" }} />
                    </div>
                    <div>
                      <p className="text-gray-400 text-xs">آخر موعد للتسجيل</p>
                      <p className="text-gray-700 font-semibold">{new Date(competition.registrationDeadline).toLocaleDateString('ar-SA')}</p>
                    </div>
                  </div>
                )}
                {(competition.ageFrom || competition.ageTo) && (
                  <div className="flex items-center gap-3 text-sm">
                    <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ background: "#f5f3ff" }}>
                      <Users className="w-4 h-4" style={{ color: "#7c3aed" }} />
                    </div>
                    <div>
                      <p className="text-gray-400 text-xs">الفئة العمرية</p>
                      <p className="text-gray-700 font-semibold">
                        {competition.ageFrom && competition.ageTo
                          ? `${competition.ageFrom} - ${competition.ageTo} سنة`
                          : competition.ageFrom ? `من ${competition.ageFrom} سنة`
                          : `حتى ${competition.ageTo} سنة`}
                      </p>
                    </div>
                  </div>
                )}
                {competition.maxParticipants && (
                  <div className="flex items-center gap-3 text-sm">
                    <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ background: "#eff6ff" }}>
                      <Users className="w-4 h-4 text-blue-600" />
                    </div>
                    <div>
                      <p className="text-gray-400 text-xs">الحد الأقصى للمشاركين</p>
                      <p className="text-gray-700 font-semibold">{competition.maxParticipants} مشارك</p>
                    </div>
                  </div>
                )}
              </div>

              {/* Register Button */}
              {isRegistered ? (
                <div className="p-4 rounded-xl text-center" style={{ background: "#f0fdf4", border: "1px solid #bbf7d0" }}>
                  <CheckCircle className="w-8 h-8 mx-auto mb-2" style={{ color: "#166534" }} />
                  <p className="font-bold text-green-800">أنت مسجل في هذه المسابقة</p>
                  <p className="text-xs text-green-600 mt-1">الحالة: {registration?.status === 'pending' ? 'قيد المراجعة' : registration?.status === 'approved' ? 'مقبول' : 'مرفوض'}</p>
                </div>
              ) : isAuthenticated ? (
                canRegister ? (
                  <Button
                    onClick={() => registerMutation.mutate({ competitionId: competition.id })}
                    disabled={registerMutation.isPending}
                    className="w-full text-white font-bold py-6 rounded-xl shadow-lg"
                    style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                    {registerMutation.isPending ? "جاري التسجيل..." : "سجّل في المسابقة"}
                  </Button>
                ) : (
                  <div className="p-4 rounded-xl text-center bg-gray-50 border border-gray-200">
                    <p className="text-gray-500 text-sm">التسجيل غير متاح حالياً</p>
                  </div>
                )
              ) : (
                <div className="space-y-3">
                  <Button asChild className="w-full text-white font-bold py-6 rounded-xl shadow-lg"
                    style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                    <a href={getLoginUrl()}>سجّل دخولك للمشاركة</a>
                  </Button>
                  <p className="text-center text-xs text-gray-400">
                    ليس لديك حساب؟{" "}
                    <Link href="/register" className="font-semibold" style={{ color: "oklch(0.42 0.15 145)" }}>
                      إنشاء حساب مجاني
                    </Link>
                  </p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
