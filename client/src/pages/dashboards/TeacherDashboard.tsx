import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import DashboardShell from "@/components/DashboardShell";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import { Trophy, Users, BookOpen, LayoutDashboard, ListChecks, BarChart3, Calendar, ArrowLeft } from "lucide-react";

const navItems = [
  { href: "/dashboard/teacher", label: "الرئيسية", icon: LayoutDashboard },
  { href: "/competitions", label: "المسابقات", icon: Trophy },
  { href: "/dashboard/teacher#students", label: "طلابي", icon: Users },
  { href: "/dashboard/teacher#stats", label: "الإحصاءات", icon: BarChart3 },
];

const categoryLabels: Record<string, string> = {
  arabic: "اللغة العربية", english: "الإنجليزية", math: "الرياضيات",
  science: "العلوم", general: "الثقافة العامة", reading: "القراءة", other: "أخرى",
};

export default function TeacherDashboard() {
  const { user, isAuthenticated, loading } = useAuth();
  const { data: competitions, isLoading: compsLoading } = trpc.competitions.list.useQuery({}, { enabled: isAuthenticated });
  const { data: teacherProfile } = trpc.profile.teacherProfile.useQuery(undefined, { enabled: isAuthenticated });

  if (loading) return <div className="min-h-screen flex items-center justify-center"><div className="w-10 h-10 rounded-xl animate-pulse" style={{ background: "oklch(0.42 0.15 145)" }}></div></div>;

  if (!isAuthenticated) {
    return (
      <div className="min-h-screen flex items-center justify-center p-6" style={{ background: "oklch(0.97 0.02 145)" }}>
        <div className="text-center max-w-sm">
          <Trophy className="w-16 h-16 mx-auto mb-4" style={{ color: "oklch(0.42 0.15 145)" }} />
          <h2 className="text-2xl font-black text-gray-800 mb-3">تسجيل الدخول مطلوب</h2>
          <Button asChild className="text-white" style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
            <a href={getLoginUrl()}>تسجيل الدخول</a>
          </Button>
        </div>
      </div>
    );
  }

  const activeComps = competitions?.filter(c => c.status === 'active') ?? [];
  const upcomingComps = competitions?.filter(c => c.status === 'upcoming') ?? [];

  return (
    <DashboardShell navItems={navItems} title="لوحة تحكم المعلم" roleLabel="معلم" roleColor="#1e40af">
      {/* Welcome */}
      <div className="mb-6 p-6 rounded-2xl text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, #1e3a8a, #1e40af)" }}>
        <div className="absolute top-0 left-0 right-0 bottom-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 80% 50%, #60a5fa 0%, transparent 50%)" }}></div>
        <div className="relative z-10">
          <h2 className="text-2xl font-black mb-1">مرحباً، {user?.name ?? "المعلم"} 👋</h2>
          <p className="text-white/70">تابع طلابك ومشاركاتهم في المسابقات التعليمية</p>
          {teacherProfile?.subject && (
            <p className="text-white/60 text-sm mt-1">التخصص: {teacherProfile.subject}</p>
          )}
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          { label: "المسابقات الجارية", value: activeComps.length, icon: Trophy, color: "#166534", bg: "#f0fdf4" },
          { label: "المسابقات القادمة", value: upcomingComps.length, icon: Calendar, color: "#1e40af", bg: "#eff6ff" },
          { label: "إجمالي المسابقات", value: competitions?.length ?? 0, icon: ListChecks, color: "#b45309", bg: "#fffbeb" },
          { label: "طلابي المسجلون", value: "—", icon: Users, color: "#7c3aed", bg: "#f5f3ff" },
        ].map((stat) => (
          <div key={stat.label} className="p-5 rounded-2xl bg-white border border-gray-100 shadow-sm">
            <div className="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style={{ background: stat.bg }}>
              <stat.icon className="w-5 h-5" style={{ color: stat.color }} />
            </div>
            <div className="text-2xl font-black text-gray-800">{stat.value}</div>
            <div className="text-sm text-gray-500 mt-1">{stat.label}</div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Active Competitions for Students */}
        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <Trophy className="w-5 h-5" style={{ color: "oklch(0.72 0.15 75)" }} />
              المسابقات الجارية
            </h3>
            <Button variant="ghost" size="sm" asChild className="text-xs">
              <Link href="/competitions">عرض الكل</Link>
            </Button>
          </div>
          <div className="p-4">
            {compsLoading ? (
              <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-16 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
            ) : activeComps.length > 0 ? (
              <div className="space-y-3">
                {activeComps.slice(0, 4).map((comp) => (
                  <Link key={comp.id} href={`/competitions/${comp.slug}`}>
                    <div className="flex items-center justify-between p-3 rounded-xl hover:bg-blue-50 transition-colors cursor-pointer border border-transparent hover:border-blue-100">
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-semibold text-gray-700 truncate">{comp.title}</p>
                        <p className="text-xs text-gray-400">{categoryLabels[comp.category ?? 'other']}</p>
                      </div>
                      <ArrowLeft className="w-4 h-4 text-gray-300 flex-shrink-0" />
                    </div>
                  </Link>
                ))}
              </div>
            ) : (
              <div className="text-center py-6">
                <Trophy className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                <p className="text-gray-400 text-sm">لا توجد مسابقات جارية حالياً</p>
              </div>
            )}
          </div>
        </div>

        {/* Students Section */}
        <div id="students" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <Users className="w-5 h-5 text-blue-600" />
              قائمة الطلاب
            </h3>
          </div>
          <div className="p-6 text-center">
            <Users className="w-12 h-12 mx-auto mb-3 text-gray-200" />
            <p className="text-gray-500 text-sm mb-2">لا يوجد طلاب مسجلون بعد</p>
            <p className="text-gray-400 text-xs">سيظهر هنا الطلاب المرتبطون بك عند تسجيلهم</p>
            <div className="mt-4 p-3 rounded-xl text-xs text-blue-700 bg-blue-50 text-right">
              <p className="font-semibold mb-1">كيفية ربط الطلاب:</p>
              <p>يمكن للطلاب الانضمام إلى مجموعتك عند التسجيل في المسابقات باستخدام رمز المعلم الخاص بك.</p>
            </div>
          </div>
        </div>

        {/* Upcoming Competitions */}
        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <Calendar className="w-5 h-5 text-blue-500" />
              المسابقات القادمة
            </h3>
          </div>
          <div className="p-4">
            {compsLoading ? (
              <div className="space-y-3">{[1,2].map(i => <div key={i} className="h-16 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
            ) : upcomingComps.length > 0 ? (
              <div className="space-y-3">
                {upcomingComps.slice(0, 3).map((comp) => (
                  <Link key={comp.id} href={`/competitions/${comp.slug}`}>
                    <div className="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                      <div>
                        <p className="text-sm font-semibold text-gray-700">{comp.title}</p>
                        {comp.startDate && (
                          <p className="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                            <Calendar className="w-3 h-3" />
                            {new Date(comp.startDate).toLocaleDateString('ar-SA')}
                          </p>
                        )}
                      </div>
                      <span className="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">قادمة</span>
                    </div>
                  </Link>
                ))}
              </div>
            ) : (
              <div className="text-center py-6">
                <Calendar className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                <p className="text-gray-400 text-sm">لا توجد مسابقات قادمة</p>
              </div>
            )}
          </div>
        </div>

        {/* Quick Actions */}
        <div id="stats" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <BarChart3 className="w-5 h-5 text-purple-600" />
              إجراءات سريعة
            </h3>
          </div>
          <div className="p-4 space-y-3">
            <Button asChild className="w-full text-white justify-start gap-3"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              <Link href="/competitions">
                <Trophy className="w-4 h-4" />
                استعرض جميع المسابقات
              </Link>
            </Button>
            <Button asChild variant="outline" className="w-full justify-start gap-3">
              <Link href="/training">
                <BookOpen className="w-4 h-4" />
                موارد التدريب والاستعداد
              </Link>
            </Button>
            <Button asChild variant="outline" className="w-full justify-start gap-3">
              <Link href="/prizes">
                <Trophy className="w-4 h-4" />
                استعرض الجوائز المتاحة
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </DashboardShell>
  );
}
