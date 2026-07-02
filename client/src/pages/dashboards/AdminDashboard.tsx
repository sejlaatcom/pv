import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import DashboardShell from "@/components/DashboardShell";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import {
  Trophy, Users, School, LayoutDashboard, BarChart3, Plus, Settings,
  MessageSquare, Award, ListChecks, TrendingUp, Shield, Eye
} from "lucide-react";
import { useState } from "react";

const navItems = [
  { href: "/dashboard/admin", label: "لوحة التحكم", icon: LayoutDashboard },
  { href: "/dashboard/admin#competitions", label: "المسابقات", icon: Trophy },
  { href: "/dashboard/admin#users", label: "المستخدمون", icon: Users },
  { href: "/dashboard/admin#schools", label: "المدارس", icon: School },
  { href: "/dashboard/admin#messages", label: "الرسائل", icon: MessageSquare },
  { href: "/dashboard/admin#reports", label: "التقارير", icon: BarChart3 },
];

const roleLabels: Record<string, string> = {
  student: "طالب", teacher: "معلم", school: "مدرسة",
  coordinator: "منسق", admin: "مدير", user: "مستخدم",
};
const roleColors: Record<string, string> = {
  student: "bg-green-100 text-green-700", teacher: "bg-blue-100 text-blue-700",
  school: "bg-purple-100 text-purple-700", coordinator: "bg-amber-100 text-amber-700",
  admin: "bg-red-100 text-red-700", user: "bg-gray-100 text-gray-600",
};

export default function AdminDashboard() {
  const { user, isAuthenticated, loading } = useAuth();
  const { data: stats, isLoading: statsLoading } = trpc.admin.stats.useQuery(undefined, { enabled: isAuthenticated && user?.role === 'admin' });
  const { data: allUsers, isLoading: usersLoading } = trpc.admin.users.useQuery(undefined, { enabled: isAuthenticated && user?.role === 'admin' });
  const { data: messages, isLoading: messagesLoading } = trpc.admin.messages.useQuery(undefined, { enabled: isAuthenticated && user?.role === 'admin' });
  const { data: competitions, isLoading: compLoading } = trpc.competitions.list.useQuery({}, { enabled: isAuthenticated });
  const [activeTab, setActiveTab] = useState<'competitions' | 'users' | 'messages'>('competitions');

  if (loading) return <div className="min-h-screen flex items-center justify-center"><div className="w-10 h-10 rounded-xl animate-pulse" style={{ background: "oklch(0.42 0.15 145)" }}></div></div>;

  if (!isAuthenticated) {
    return (
      <div className="min-h-screen flex items-center justify-center p-6" style={{ background: "oklch(0.97 0.02 145)" }}>
        <div className="text-center max-w-sm">
          <Shield className="w-16 h-16 mx-auto mb-4" style={{ color: "oklch(0.42 0.15 145)" }} />
          <h2 className="text-2xl font-black text-gray-800 mb-3">تسجيل الدخول مطلوب</h2>
          <Button asChild className="text-white" style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
            <a href={getLoginUrl()}>تسجيل الدخول</a>
          </Button>
        </div>
      </div>
    );
  }

  if (user?.role !== 'admin') {
    return (
      <div className="min-h-screen flex items-center justify-center p-6">
        <div className="text-center max-w-sm">
          <Shield className="w-16 h-16 mx-auto mb-4 text-red-400" />
          <h2 className="text-2xl font-black text-gray-800 mb-3">غير مصرح بالوصول</h2>
          <p className="text-gray-500 mb-4">هذه الصفحة مخصصة للمديرين فقط</p>
          <Button asChild variant="outline"><Link href="/">العودة للرئيسية</Link></Button>
        </div>
      </div>
    );
  }

  const statusLabels: Record<string, string> = { upcoming: "قادمة", active: "جارية", completed: "منتهية", cancelled: "ملغاة" };
  const statusColors: Record<string, string> = {
    upcoming: "bg-blue-100 text-blue-700", active: "bg-green-100 text-green-700",
    completed: "bg-gray-100 text-gray-600", cancelled: "bg-red-100 text-red-700",
  };
  const msgStatusLabels: Record<string, string> = { new: "جديدة", read: "مقروءة", replied: "مُجاب عليها" };
  const msgStatusColors: Record<string, string> = {
    new: "bg-red-100 text-red-700", read: "bg-gray-100 text-gray-600", replied: "bg-green-100 text-green-700",
  };

  return (
    <DashboardShell navItems={navItems} title="لوحة تحكم المدير" roleLabel="مدير" roleColor="#be185d">
      {/* Welcome */}
      <div className="mb-6 p-6 rounded-2xl text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.30 0.15 145), oklch(0.42 0.15 145))" }}>
        <div className="absolute top-0 left-0 right-0 bottom-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 20% 50%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="relative z-10">
          <h2 className="text-2xl font-black mb-1">لوحة تحكم المدير 🛡️</h2>
          <p className="text-white/70">مرحباً {user?.name} — إدارة كاملة للمنصة والمسابقات والمستخدمين</p>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          { label: "إجمالي المستخدمين", value: statsLoading ? "..." : stats?.users ?? 0, icon: Users, color: "#1e40af", bg: "#eff6ff", trend: "+12%" },
          { label: "المسابقات", value: statsLoading ? "..." : stats?.competitions ?? 0, icon: Trophy, color: "#b45309", bg: "#fffbeb", trend: "+3" },
          { label: "التسجيلات", value: statsLoading ? "..." : stats?.registrations ?? 0, icon: ListChecks, color: "#166534", bg: "#f0fdf4", trend: "+45%" },
          { label: "المدارس", value: statsLoading ? "..." : stats?.schools ?? 0, icon: School, color: "#7c3aed", bg: "#f5f3ff", trend: "+8" },
        ].map((stat) => (
          <div key={stat.label} className="p-5 rounded-2xl bg-white border border-gray-100 shadow-sm">
            <div className="flex items-center justify-between mb-3">
              <div className="w-10 h-10 rounded-xl flex items-center justify-center" style={{ background: stat.bg }}>
                <stat.icon className="w-5 h-5" style={{ color: stat.color }} />
              </div>
              <span className="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{stat.trend}</span>
            </div>
            <div className="text-2xl font-black text-gray-800">{stat.value}</div>
            <div className="text-sm text-gray-500 mt-1">{stat.label}</div>
          </div>
        ))}
      </div>

      {/* Tabs */}
      <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div className="border-b border-gray-100">
          <div className="flex">
            {[
              { key: 'competitions', label: 'المسابقات', icon: Trophy },
              { key: 'users', label: 'المستخدمون', icon: Users },
              { key: 'messages', label: 'الرسائل', icon: MessageSquare },
            ].map((tab) => (
              <button key={tab.key} onClick={() => setActiveTab(tab.key as any)}
                className={`flex items-center gap-2 px-5 py-4 text-sm font-semibold border-b-2 transition-colors ${
                  activeTab === tab.key
                    ? "border-green-600 text-green-700"
                    : "border-transparent text-gray-500 hover:text-gray-700"
                }`}>
                <tab.icon className="w-4 h-4" />
                {tab.label}
              </button>
            ))}
          </div>
        </div>

        <div className="p-4">
          {/* Competitions Tab */}
          {activeTab === 'competitions' && (
            <div>
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-bold text-gray-700">قائمة المسابقات</h3>
                <Button size="sm" asChild className="text-white text-xs"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  <Link href="/competitions" className="flex items-center gap-1">
                    <Plus className="w-3 h-3" />
                    إضافة مسابقة
                  </Link>
                </Button>
              </div>
              {compLoading ? (
                <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-14 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
              ) : competitions && competitions.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b border-gray-100">
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">المسابقة</th>
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">التصنيف</th>
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">الحالة</th>
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">الإجراءات</th>
                      </tr>
                    </thead>
                    <tbody>
                      {competitions.slice(0, 10).map((comp) => (
                        <tr key={comp.id} className="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                          <td className="py-3 px-3 font-medium text-gray-800">{comp.title}</td>
                          <td className="py-3 px-3 text-gray-500">{comp.category}</td>
                          <td className="py-3 px-3">
                            <span className={`px-2 py-1 rounded-full text-xs font-semibold ${statusColors[comp.status ?? 'upcoming']}`}>
                              {statusLabels[comp.status ?? 'upcoming']}
                            </span>
                          </td>
                          <td className="py-3 px-3">
                            <Button variant="ghost" size="sm" asChild className="text-xs">
                              <Link href={`/competitions/${comp.slug}`} className="flex items-center gap-1">
                                <Eye className="w-3 h-3" />
                                عرض
                              </Link>
                            </Button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="text-center py-8">
                  <Trophy className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                  <p className="text-gray-400 text-sm">لا توجد مسابقات بعد</p>
                </div>
              )}
            </div>
          )}

          {/* Users Tab */}
          {activeTab === 'users' && (
            <div>
              <h3 className="font-bold text-gray-700 mb-4">قائمة المستخدمين</h3>
              {usersLoading ? (
                <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-14 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
              ) : allUsers && allUsers.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b border-gray-100">
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">الاسم</th>
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">البريد</th>
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">الدور</th>
                        <th className="text-right py-3 px-3 font-semibold text-gray-600">تاريخ التسجيل</th>
                      </tr>
                    </thead>
                    <tbody>
                      {allUsers.slice(0, 15).map((u) => (
                        <tr key={u.id} className="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                          <td className="py-3 px-3">
                            <div className="flex items-center gap-2">
                              <div className="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                                {u.name?.charAt(0) ?? "م"}
                              </div>
                              <span className="font-medium text-gray-800">{u.name ?? "—"}</span>
                            </div>
                          </td>
                          <td className="py-3 px-3 text-gray-500 text-xs" style={{ direction: "ltr" }}>{u.email ?? "—"}</td>
                          <td className="py-3 px-3">
                            <span className={`px-2 py-1 rounded-full text-xs font-semibold ${roleColors[u.role ?? 'user']}`}>
                              {roleLabels[u.role ?? 'user']}
                            </span>
                          </td>
                          <td className="py-3 px-3 text-gray-400 text-xs">{new Date(u.createdAt).toLocaleDateString('ar-SA')}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="text-center py-8">
                  <Users className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                  <p className="text-gray-400 text-sm">لا يوجد مستخدمون بعد</p>
                </div>
              )}
            </div>
          )}

          {/* Messages Tab */}
          {activeTab === 'messages' && (
            <div>
              <h3 className="font-bold text-gray-700 mb-4">رسائل التواصل</h3>
              {messagesLoading ? (
                <div className="space-y-3">{[1,2,3].map(i => <div key={i} className="h-16 bg-gray-50 rounded-xl animate-pulse"></div>)}</div>
              ) : messages && messages.length > 0 ? (
                <div className="space-y-3">
                  {messages.map((msg) => (
                    <div key={msg.id} className="p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                      <div className="flex items-start justify-between gap-3">
                        <div className="flex-1 min-w-0">
                          <div className="flex items-center gap-2 mb-1">
                            <span className="font-semibold text-gray-800 text-sm">{msg.name}</span>
                            <span className="text-xs text-gray-400" style={{ direction: "ltr" }}>{msg.email}</span>
                          </div>
                          {msg.subject && <p className="text-sm text-gray-600 font-medium mb-1">{msg.subject}</p>}
                          <p className="text-xs text-gray-500 line-clamp-2">{msg.message}</p>
                        </div>
                        <div className="flex flex-col items-end gap-2 flex-shrink-0">
                          <span className={`px-2 py-1 rounded-full text-xs font-semibold ${msgStatusColors[msg.status ?? 'new']}`}>
                            {msgStatusLabels[msg.status ?? 'new']}
                          </span>
                          <span className="text-xs text-gray-400">{new Date(msg.createdAt).toLocaleDateString('ar-SA')}</span>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8">
                  <MessageSquare className="w-10 h-10 mx-auto mb-3 text-gray-200" />
                  <p className="text-gray-400 text-sm">لا توجد رسائل بعد</p>
                </div>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Reports */}
      <div id="reports" className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div className="p-5 border-b border-gray-100">
          <h3 className="font-bold text-gray-800 flex items-center gap-2">
            <TrendingUp className="w-5 h-5" style={{ color: "oklch(0.42 0.15 145)" }} />
            التقارير الإحصائية
          </h3>
        </div>
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {[
              { label: "تقرير المسابقات", desc: "إحصاءات تفصيلية لجميع المسابقات", icon: Trophy, color: "#b45309" },
              { label: "تقرير المشاركين", desc: "بيانات المشاركين والتسجيلات", icon: Users, color: "#1e40af" },
              { label: "تقرير الجوائز", desc: "توزيع الجوائز والفائزين", icon: Award, color: "#7c3aed" },
            ].map((report) => (
              <div key={report.label} className="p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors cursor-pointer"
                onClick={() => {}}>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-9 h-9 rounded-lg flex items-center justify-center" style={{ background: `${report.color}15` }}>
                    <report.icon className="w-4 h-4" style={{ color: report.color }} />
                  </div>
                  <h4 className="font-semibold text-gray-800 text-sm">{report.label}</h4>
                </div>
                <p className="text-xs text-gray-500">{report.desc}</p>
                <div className="mt-3 text-xs font-semibold" style={{ color: "oklch(0.42 0.15 145)" }}>
                  قريباً →
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </DashboardShell>
  );
}
