import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import DashboardShell from "@/components/DashboardShell";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import {
  Trophy, Users, School, LayoutDashboard, BarChart3, ListChecks, Award,
  MapPin, Phone, CheckCircle, AlertCircle, BookOpen, Calendar
} from "lucide-react";

const navItems = [
  { href: "/dashboard/school", label: "الرئيسية", icon: LayoutDashboard },
  { href: "/competitions", label: "المسابقات", icon: Trophy },
  { href: "/dashboard/school#participants", label: "المشاركون", icon: Users },
  { href: "/dashboard/school#stats", label: "الإحصاءات", icon: BarChart3 },
];

const educationTypeLabels: Record<string, string> = {
  government: "حكومية", private: "أهلية", international: "دولية",
};

export default function SchoolDashboard() {
  const { user, isAuthenticated, loading } = useAuth();
  const { data: mySchool, isLoading: schoolLoading } = trpc.schools.mySchool.useQuery(undefined, { enabled: isAuthenticated });
  const { data: competitions, isLoading: compsLoading } = trpc.competitions.list.useQuery({}, { enabled: isAuthenticated });

  if (loading) return <div className="min-h-screen flex items-center justify-center"><div className="w-10 h-10 rounded-xl animate-pulse" style={{ background: "oklch(0.42 0.15 145)" }}></div></div>;

  if (!isAuthenticated) {
    return (
      <div className="min-h-screen flex items-center justify-center p-6" style={{ background: "oklch(0.97 0.02 145)" }}>
        <div className="text-center max-w-sm">
          <School className="w-16 h-16 mx-auto mb-4" style={{ color: "oklch(0.42 0.15 145)" }} />
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
    <DashboardShell navItems={navItems} title="لوحة تحكم المدرسة" roleLabel="مدرسة" roleColor="#7c3aed">
      {/* Welcome */}
      <div className="mb-6 p-6 rounded-2xl text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, #4c1d95, #7c3aed)" }}>
        <div className="absolute top-0 left-0 right-0 bottom-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 80% 50%, #a78bfa 0%, transparent 50%)" }}></div>
        <div className="relative z-10">
          <h2 className="text-2xl font-black mb-1">
            {mySchool ? mySchool.schoolName : (user?.name ?? "المدرسة")} 🏫
          </h2>
          <p className="text-white/70">
            {mySchool
              ? `${educationTypeLabels[mySchool.educationType ?? 'government'] ?? ''} — ${mySchool.city ?? ''}`
              : "تابع إحصاءات مدرستك ومشاركات طلابها في المسابقات"}
          </p>
        </div>
      </div>

      {/* School Info Card */}
      {schoolLoading ? (
        <div className="mb-6 h-20 bg-gray-50 rounded-2xl animate-pulse"></div>
      ) : mySchool ? (
        <div className="mb-6 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
          <div className="flex items-center gap-4">
            <div className="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style={{ background: "#f5f3ff" }}>
              <School className="w-7 h-7" style={{ color: "#7c3aed" }} />
            </div>
            <div className="flex-1 min-w-0">
              <h3 className="text-lg font-black text-gray-800 truncate">{mySchool.schoolName}</h3>
              <div className="flex flex-wrap gap-3 text-sm text-gray-500 mt-1">
                {mySchool.city && (
                  <span className="flex items-center gap-1">
                    <MapPin className="w-3.5 h-3.5" />
                    {mySchool.city}
                    {mySchool.region ? ` — ${mySchool.region}` : ''}
                  </span>
                )}
                {mySchool.phone && (
                  <span className="flex items-center gap-1" style={{ direction: "ltr" }}>
                    <Phone className="w-3.5 h-3.5" />
                    {mySchool.phone}
                  </span>
                )}
              </div>
            </div>
            <span className={`flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 ${mySchool.isVerified ? "bg-green-100 text-green-700" : "bg-yellow-100 text-yellow-700"}`}>
              {mySchool.isVerified ? <CheckCircle className="w-3.5 h-3.5" /> : <AlertCircle className="w-3.5 h-3.5" />}
              {mySchool.isVerified ? "موثّقة" : "قيد التوثيق"}
            </span>
          </div>
        </div>
      ) : (
        <div className="mb-6 p-5 bg-white rounded-2xl border-2 border-dashed border-purple-200">
          <div className="text-center py-4">
            <School className="w-10 h-10 mx-auto mb-3" style={{ color: "#7c3aed" }} />
            <p className="text-gray-600 font-semibold mb-2">لم يتم تسجيل بيانات المدرسة بعد</p>
            <p className="text-gray-400 text-sm mb-4">أكمل تسجيل بيانات مدرستك للاستفادة من جميع الميزات</p>
            <Button size="sm" className="text-white text-xs gap-2" style={{ background: "linear-gradient(135deg, #7c3aed, #6d28d9)" }}>
              إضافة بيانات المدرسة
            </Button>
          </div>
        </div>
      )}

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          { label: "إجمالي الطلاب", value: "—", icon: Users, color: "#7c3aed", bg: "#f5f3ff" },
          { label: "المشاركات", value: "—", icon: ListChecks, color: "#166534", bg: "#f0fdf4" },
          { label: "المسابقات الجارية", value: activeComps.length, icon: Trophy, color: "#b45309", bg: "#fffbeb" },
          { label: "الجوائز", value: "—", icon: Award, color: "#be185d", bg: "#fdf2f8" },
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
        {/* Participants */}
        <div id="participants" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <Users className="w-5 h-5" style={{ color: "#7c3aed" }} />
              المشاركون من المدرسة
            </h3>
          </div>
          <div className="p-6 text-center">
            <Users className="w-12 h-12 mx-auto mb-3 text-gray-200" />
            <p className="text-gray-500 text-sm mb-2">لا يوجد مشاركون بعد</p>
            <p className="text-gray-400 text-xs">سيظهر هنا الطلاب المنتسبون للمدرسة والمسجلون في المسابقات</p>
          </div>
        </div>

        {/* Available Competitions */}
        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <Trophy className="w-5 h-5" style={{ color: "oklch(0.72 0.15 75)" }} />
              المسابقات المتاحة
            </h3>
            <Button variant="ghost" size="sm" asChild className="text-xs">
              <Link href="/competitions">عرض الكل</Link>
            </Button>
          </div>
          <div className="p-4">
            {compsLoading ? (
              <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-14 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
            ) : activeComps.length > 0 ? (
              <div className="space-y-3">
                {activeComps.slice(0, 4).map((comp) => (
                  <Link key={comp.id} href={`/competitions/${comp.slug}`}>
                    <div className="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50 transition-colors cursor-pointer">
                      <div>
                        <p className="text-sm font-semibold text-gray-700">{comp.title}</p>
                        <p className="text-xs text-gray-400">{comp.category}</p>
                      </div>
                      <span className="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">جارية</span>
                    </div>
                  </Link>
                ))}
              </div>
            ) : (
              <div className="text-center py-6">
                <Trophy className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                <p className="text-gray-400 text-sm">لا توجد مسابقات جارية</p>
              </div>
            )}
          </div>
        </div>

        {/* Statistics */}
        <div id="stats" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-2">
          <div className="p-5 border-b border-gray-100">
            <h3 className="font-bold text-gray-800 flex items-center gap-2">
              <BarChart3 className="w-5 h-5" style={{ color: "#7c3aed" }} />
              الإحصاءات التفصيلية
            </h3>
          </div>
          <div className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              {[
                { label: "إجمالي التسجيلات", value: "—", desc: "عدد الطلاب المسجلين في مسابقات" },
                { label: "نسبة القبول", value: "—", desc: "نسبة الطلاب المقبولين" },
                { label: "الجوائز المحققة", value: "—", desc: "جوائز حققها طلاب المدرسة" },
              ].map((item) => (
                <div key={item.label} className="p-4 rounded-xl text-center" style={{ background: "oklch(0.98 0.01 145)" }}>
                  <div className="text-3xl font-black text-gray-300 mb-1">{item.value}</div>
                  <div className="text-sm font-semibold text-gray-700 mb-1">{item.label}</div>
                  <div className="text-xs text-gray-400">{item.desc}</div>
                </div>
              ))}
            </div>
            <div className="p-3 rounded-xl text-xs text-purple-700 bg-purple-50">
              ستظهر الإحصاءات التفصيلية بعد مشاركة طلاب المدرسة في المسابقات وتسجيل نتائجهم.
            </div>
          </div>
        </div>
      </div>
    </DashboardShell>
  );
}
