import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import DashboardShell from "@/components/DashboardShell";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import { Trophy, Users, School, LayoutDashboard, BarChart3, MapPin, CheckCircle } from "lucide-react";

const navItems = [
  { href: "/dashboard/coordinator", label: "الرئيسية", icon: LayoutDashboard },
  { href: "/competitions", label: "المسابقات", icon: Trophy },
  { href: "/dashboard/coordinator#schools", label: "المدارس التابعة", icon: School },
  { href: "/dashboard/coordinator#stats", label: "الإحصاءات", icon: BarChart3 },
];

export default function CoordinatorDashboard() {
  const { user, isAuthenticated, loading } = useAuth();
  const { data: schools, isLoading: schoolsLoading } = trpc.schools.list.useQuery(undefined, { enabled: isAuthenticated });

  if (loading) return <div className="min-h-screen flex items-center justify-center"><div className="w-10 h-10 rounded-xl animate-pulse" style={{ background: "oklch(0.42 0.15 145)" }}></div></div>;

  if (!isAuthenticated) {
    return (
      <div className="min-h-screen flex items-center justify-center p-6" style={{ background: "oklch(0.97 0.02 145)" }}>
        <div className="text-center max-w-sm">
          <Users className="w-16 h-16 mx-auto mb-4" style={{ color: "oklch(0.42 0.15 145)" }} />
          <h2 className="text-2xl font-black text-gray-800 mb-3">تسجيل الدخول مطلوب</h2>
          <Button asChild className="text-white" style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
            <a href={getLoginUrl()}>تسجيل الدخول</a>
          </Button>
        </div>
      </div>
    );
  }

  return (
    <DashboardShell navItems={navItems} title="لوحة تحكم المنسق" roleLabel="منسق" roleColor="#b45309">
      {/* Welcome */}
      <div className="mb-6 p-6 rounded-2xl text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, #78350f, #b45309)" }}>
        <h2 className="text-2xl font-black mb-1">مرحباً، {user?.name ?? "المنسق"} 👋</h2>
        <p className="text-white/70">أدر المدارس التابعة وتابع مشاركاتها في المسابقات</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          { label: "المدارس التابعة", value: schools?.length ?? 0, icon: School, color: "#b45309", bg: "#fffbeb" },
          { label: "المدارس الموثّقة", value: schools?.filter(s => s.isVerified).length ?? 0, icon: CheckCircle, color: "#166534", bg: "#f0fdf4" },
          { label: "إجمالي المشاركين", value: "—", icon: Users, color: "#1e40af", bg: "#eff6ff" },
          { label: "المسابقات النشطة", value: "—", icon: Trophy, color: "#7c3aed", bg: "#f5f3ff" },
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

      {/* Schools List */}
      <div id="schools" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div className="p-5 border-b border-gray-100">
          <h3 className="font-bold text-gray-800 flex items-center gap-2">
            <School className="w-5 h-5" style={{ color: "#b45309" }} />
            المدارس التابعة
          </h3>
        </div>
        <div className="p-4">
          {schoolsLoading ? (
            <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-16 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
          ) : schools && schools.length > 0 ? (
            <div className="space-y-3">
              {schools.map((school) => (
                <div key={school.id} className="flex items-center justify-between p-4 rounded-xl" style={{ background: "oklch(0.98 0.01 145)" }}>
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl flex items-center justify-center" style={{ background: "#fffbeb" }}>
                      <School className="w-5 h-5" style={{ color: "#b45309" }} />
                    </div>
                    <div>
                      <p className="font-semibold text-gray-800">{school.schoolName}</p>
                      <div className="flex gap-2 text-xs text-gray-400">
                        {school.city && <span className="flex items-center gap-1"><MapPin className="w-3 h-3" />{school.city}</span>}
                      </div>
                    </div>
                  </div>
                  <span className={`px-2 py-1 rounded-full text-xs font-semibold ${school.isVerified ? "bg-green-100 text-green-700" : "bg-yellow-100 text-yellow-700"}`}>
                    {school.isVerified ? "موثّقة" : "قيد التوثيق"}
                  </span>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-8">
              <School className="w-10 h-10 mx-auto mb-3 text-gray-200" />
              <p className="text-gray-400 text-sm">لا توجد مدارس تابعة بعد</p>
            </div>
          )}
        </div>
      </div>

      {/* Stats Section */}
      <div id="stats" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div className="p-5 border-b border-gray-100">
          <h3 className="font-bold text-gray-800 flex items-center gap-2">
            <BarChart3 className="w-5 h-5" style={{ color: "#b45309" }} />
            الإحصاءات الإجمالية
          </h3>
        </div>
        <div className="p-6 text-center">
          <BarChart3 className="w-12 h-12 mx-auto mb-3 text-gray-200" />
          <p className="text-gray-500 text-sm">ستظهر هنا إحصاءات المنطقة</p>
        </div>
      </div>
    </DashboardShell>
  );
}
