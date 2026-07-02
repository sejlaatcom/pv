import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import DashboardShell from "@/components/DashboardShell";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import {
  Trophy, BookOpen, Award, Star, Clock, CheckCircle,
  LayoutDashboard, ListChecks, Medal, FileText, ArrowLeft, Calendar, Users
} from "lucide-react";

const navItems = [
  { href: "/dashboard/student", label: "الرئيسية", icon: LayoutDashboard },
  { href: "/dashboard/student#available", label: "المسابقات المتاحة", icon: Trophy },
  { href: "/dashboard/student#registrations", label: "تسجيلاتي", icon: ListChecks },
  { href: "/dashboard/student#results", label: "نتائجي", icon: Medal },
  { href: "/dashboard/student#certificates", label: "شهاداتي", icon: FileText },
];

const statusLabels: Record<string, string> = {
  pending: "قيد المراجعة", approved: "مقبول", rejected: "مرفوض", withdrawn: "منسحب",
};
const statusColors: Record<string, string> = {
  pending: "bg-yellow-100 text-yellow-700", approved: "bg-green-100 text-green-700",
  rejected: "bg-red-100 text-red-700", withdrawn: "bg-gray-100 text-gray-600",
};
const compStatusColors: Record<string, string> = {
  upcoming: "bg-blue-100 text-blue-700", active: "bg-green-100 text-green-700",
  completed: "bg-gray-100 text-gray-600", cancelled: "bg-red-100 text-red-700",
};
const compStatusLabels: Record<string, string> = {
  upcoming: "قادمة", active: "جارية", completed: "منتهية", cancelled: "ملغاة",
};
const categoryLabels: Record<string, string> = {
  arabic: "اللغة العربية", english: "الإنجليزية", math: "الرياضيات",
  science: "العلوم", general: "الثقافة العامة", reading: "القراءة", other: "أخرى",
};

export default function StudentDashboard() {
  const { user, isAuthenticated, loading } = useAuth();
  const { data: registrations, isLoading: regLoading } = trpc.registrations.myRegistrations.useQuery(undefined, { enabled: isAuthenticated });
  const { data: results, isLoading: resLoading } = trpc.results.myResults.useQuery(undefined, { enabled: isAuthenticated });
  const { data: certificates, isLoading: certLoading } = trpc.certificates.myCertificates.useQuery(undefined, { enabled: isAuthenticated });
  const { data: availableComps, isLoading: compsLoading } = trpc.competitions.list.useQuery({ status: 'active' }, { enabled: isAuthenticated });

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="w-10 h-10 rounded-xl animate-pulse" style={{ background: "oklch(0.42 0.15 145)" }}></div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return (
      <div className="min-h-screen flex items-center justify-center p-6" style={{ background: "oklch(0.97 0.02 145)" }}>
        <div className="text-center max-w-sm">
          <Trophy className="w-16 h-16 mx-auto mb-4" style={{ color: "oklch(0.42 0.15 145)" }} />
          <h2 className="text-2xl font-black text-gray-800 mb-3">تسجيل الدخول مطلوب</h2>
          <p className="text-gray-500 mb-6">يرجى تسجيل الدخول للوصول إلى لوحة التحكم</p>
          <Button asChild className="text-white" style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
            <a href={getLoginUrl()}>تسجيل الدخول</a>
          </Button>
        </div>
      </div>
    );
  }

  return (
    <DashboardShell navItems={navItems} title="لوحة تحكم الطالب" roleLabel="طالب" roleColor="#166534">
      {/* Welcome */}
      <div className="mb-6 p-6 rounded-2xl text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
        <div className="absolute top-0 left-0 right-0 bottom-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 20% 50%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="relative z-10">
          <h2 className="text-2xl font-black mb-1">مرحباً، {user?.name ?? "الطالب"} 👋</h2>
          <p className="text-white/70">استعرض المسابقات المتاحة وتابع نتائجك وشهاداتك</p>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          { label: "التسجيلات", value: registrations?.length ?? 0, icon: ListChecks, color: "#166534", bg: "#f0fdf4" },
          { label: "المسابقات المتاحة", value: availableComps?.length ?? 0, icon: Trophy, color: "#b45309", bg: "#fffbeb" },
          { label: "النتائج", value: results?.length ?? 0, icon: Medal, color: "#1e40af", bg: "#eff6ff" },
          { label: "الشهادات", value: certificates?.length ?? 0, icon: FileText, color: "#7c3aed", bg: "#f5f3ff" },
        ].map((stat) => (
          <div key={stat.label} className="p-5 rounded-2xl bg-white border border-gray-100 shadow-sm">
            <div className="flex items-center justify-between mb-3">
              <div className="w-10 h-10 rounded-xl flex items-center justify-center" style={{ background: stat.bg }}>
                <stat.icon className="w-5 h-5" style={{ color: stat.color }} />
              </div>
            </div>
            <div className="text-2xl font-black text-gray-800">{stat.value}</div>
            <div className="text-sm text-gray-500 mt-1">{stat.label}</div>
          </div>
        ))}
      </div>

      {/* Available Competitions */}
      <div id="available" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div className="p-5 border-b border-gray-100 flex items-center justify-between">
          <h3 className="font-bold text-gray-800 flex items-center gap-2">
            <Trophy className="w-5 h-5" style={{ color: "oklch(0.72 0.15 75)" }} />
            المسابقات المتاحة للتسجيل
          </h3>
          <Button variant="ghost" size="sm" asChild className="text-xs">
            <Link href="/competitions">عرض الكل</Link>
          </Button>
        </div>
        <div className="p-4">
          {compsLoading ? (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              {[1,2,3,4].map(i => <div key={i} className="h-24 bg-gray-50 rounded-xl animate-pulse"></div>)}
            </div>
          ) : availableComps && availableComps.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              {availableComps.slice(0, 4).map((comp) => (
                <Link key={comp.id} href={`/competitions/${comp.slug}`}>
                  <div className="p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:shadow-sm transition-all cursor-pointer"
                    style={{ background: "oklch(0.99 0.005 145)" }}>
                    <div className="flex items-start justify-between gap-2 mb-2">
                      <h4 className="font-semibold text-gray-800 text-sm leading-snug">{comp.title}</h4>
                      <span className={`px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0 ${compStatusColors[comp.status ?? 'upcoming']}`}>
                        {compStatusLabels[comp.status ?? 'upcoming']}
                      </span>
                    </div>
                    <p className="text-xs text-gray-500 mb-3 line-clamp-2">{comp.shortDescription ?? comp.description}</p>
                    <div className="flex items-center gap-3 text-xs text-gray-400">
                      <span className="flex items-center gap-1">
                        <BookOpen className="w-3 h-3" />
                        {categoryLabels[comp.category ?? 'other']}
                      </span>
                      {comp.endDate && (
                        <span className="flex items-center gap-1">
                          <Calendar className="w-3 h-3" />
                          {new Date(comp.endDate).toLocaleDateString('ar-SA')}
                        </span>
                      )}
                      {comp.maxParticipants && (
                        <span className="flex items-center gap-1">
                          <Users className="w-3 h-3" />
                          {comp.maxParticipants}
                        </span>
                      )}
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          ) : (
            <div className="text-center py-8">
              <Trophy className="w-10 h-10 mx-auto mb-3 text-gray-200" />
              <p className="text-gray-400 text-sm mb-3">لا توجد مسابقات نشطة حالياً</p>
              <Button size="sm" asChild className="text-white text-xs"
                style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                <Link href="/competitions">استعرض جميع المسابقات</Link>
              </Button>
            </div>
          )}
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Registrations */}
        <div id="registrations" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <ListChecks className="w-5 h-5" style={{ color: "oklch(0.42 0.15 145)" }} />
              تسجيلاتي في المسابقات
            </h3>
            <Button variant="ghost" size="sm" asChild className="text-xs">
              <Link href="/competitions">مسابقات جديدة</Link>
            </Button>
          </div>
          <div className="p-4">
            {regLoading ? (
              <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-16 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
            ) : registrations && registrations.length > 0 ? (
              <div className="space-y-3">
                {registrations.slice(0, 5).map((reg) => (
                  <div key={reg.id} className="flex items-center justify-between p-3 rounded-xl" style={{ background: "oklch(0.98 0.01 145)" }}>
                    <div>
                      <p className="text-sm font-semibold text-gray-700">مسابقة #{reg.competitionId}</p>
                      <p className="text-xs text-gray-400">{new Date(reg.registeredAt).toLocaleDateString('ar-SA')}</p>
                    </div>
                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${statusColors[reg.status ?? 'pending']}`}>
                      {statusLabels[reg.status ?? 'pending']}
                    </span>
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-8">
                <Trophy className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                <p className="text-gray-400 text-sm mb-3">لم تشترك في أي مسابقة بعد</p>
                <Button size="sm" asChild className="text-white text-xs"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  <Link href="/competitions">استعرض المسابقات</Link>
                </Button>
              </div>
            )}
          </div>
        </div>

        {/* Results */}
        <div id="results" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <Medal className="w-5 h-5" style={{ color: "oklch(0.72 0.15 75)" }} />
              نتائجي
            </h3>
          </div>
          <div className="p-4">
            {resLoading ? (
              <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-16 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
            ) : results && results.length > 0 ? (
              <div className="space-y-3">
                {results.slice(0, 5).map((result) => (
                  <div key={result.id} className="flex items-center justify-between p-3 rounded-xl" style={{ background: "oklch(0.98 0.01 145)" }}>
                    <div>
                      <p className="text-sm font-semibold text-gray-700">مسابقة #{result.competitionId}</p>
                      <p className="text-xs text-gray-400">المرحلة: {result.stage ?? "—"}</p>
                    </div>
                    <div className="text-left">
                      <p className="text-sm font-black" style={{ color: "oklch(0.42 0.15 145)" }}>{result.score ? `${result.score}%` : "—"}</p>
                      {result.rank && <p className="text-xs text-gray-400">المركز: {result.rank}</p>}
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-8">
                <Medal className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                <p className="text-gray-400 text-sm">لا توجد نتائج بعد</p>
                <p className="text-gray-300 text-xs mt-1">شارك في المسابقات لتظهر نتائجك هنا</p>
              </div>
            )}
          </div>
        </div>

        {/* Certificates */}
        <div id="certificates" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-2">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <FileText className="w-5 h-5" style={{ color: "#7c3aed" }} />
              شهاداتي
            </h3>
          </div>
          <div className="p-4">
            {certLoading ? (
              <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                {[1,2,3,4].map(i => <div key={i} className="h-32 bg-gray-50 rounded-xl animate-pulse"></div>)}
              </div>
            ) : certificates && certificates.length > 0 ? (
              <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                {certificates.map((cert) => (
                  <div key={cert.id} className="p-4 rounded-xl text-center border-2" style={{ background: "#f5f3ff", borderColor: "#ddd6fe" }}>
                    <Award className="w-8 h-8 mx-auto mb-2" style={{ color: "#7c3aed" }} />
                    <p className="text-xs font-semibold text-gray-700 mb-1">
                      شهادة {cert.type === 'winner' ? 'فوز' : cert.type === 'participation' ? 'مشاركة' : 'تميز'}
                    </p>
                    <p className="text-xs text-gray-400">{new Date(cert.issuedAt).toLocaleDateString('ar-SA')}</p>
                    {cert.certificateNumber && (
                      <p className="text-xs text-gray-300 mt-1">#{cert.certificateNumber}</p>
                    )}
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-8">
                <Award className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                <p className="text-gray-400 text-sm">لا توجد شهادات بعد</p>
                <p className="text-gray-300 text-xs mt-1">شارك في المسابقات للحصول على شهادات معتمدة</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </DashboardShell>
  );
}
