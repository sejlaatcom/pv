import { useState } from "react";
import { Link } from "wouter";
import { trpc } from "@/lib/trpc";
import { Button } from "@/components/ui/button";
import { Trophy, Search, Filter, ChevronLeft, Calendar, Users, Star } from "lucide-react";

const categories = [
  { id: "", label: "جميع التخصصات" },
  { id: "arabic", label: "اللغة العربية" },
  { id: "english", label: "الإنجليزية" },
  { id: "math", label: "الرياضيات" },
  { id: "science", label: "العلوم" },
  { id: "general", label: "الثقافة العامة" },
  { id: "reading", label: "القراءة" },
];

const levels = [
  { id: "", label: "جميع المراحل" },
  { id: "primary", label: "ابتدائي" },
  { id: "middle", label: "متوسط" },
  { id: "secondary", label: "ثانوي" },
  { id: "university", label: "جامعي" },
];

const statuses = [
  { id: "", label: "جميع الحالات" },
  { id: "upcoming", label: "قادمة" },
  { id: "active", label: "جارية" },
  { id: "completed", label: "منتهية" },
];

const categoryLabels: Record<string, string> = {
  arabic: "اللغة العربية", english: "الإنجليزية", math: "الرياضيات",
  science: "العلوم", general: "الثقافة العامة", reading: "القراءة", other: "أخرى",
};
const statusColors: Record<string, string> = {
  upcoming: "bg-blue-100 text-blue-700", active: "bg-green-100 text-green-700",
  completed: "bg-gray-100 text-gray-600", cancelled: "bg-red-100 text-red-700",
};
const statusLabels: Record<string, string> = {
  upcoming: "قادمة", active: "جارية", completed: "منتهية", cancelled: "ملغاة",
};

export default function Competitions() {
  const [category, setCategory] = useState("");
  const [level, setLevel] = useState("");
  const [status, setStatus] = useState("");
  const [search, setSearch] = useState("");

  const { data: competitions, isLoading } = trpc.competitions.list.useQuery({
    category: category || undefined,
    educationLevel: level || undefined,
    status: status || undefined,
  });

  const filtered = competitions?.filter(c =>
    !search || c.title.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="py-16 text-white" style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="container text-center">
          <div className="badge-gold inline-flex mb-4">
            <Trophy className="w-4 h-4" />
            المسابقات
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-4">استعرض جميع المسابقات</h1>
          <p className="text-white/70 text-lg max-w-xl mx-auto">اختر مسابقتك المفضلة وابدأ رحلتك التنافسية</p>
        </div>
      </div>

      <div className="container py-10">
        {/* Search & Filters */}
        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8">
          <div className="flex flex-col gap-4">
            {/* Search */}
            <div className="relative">
              <Search className="absolute top-1/2 -translate-y-1/2 right-3 w-4 h-4 text-gray-400" />
              <input
                type="text"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="ابحث عن مسابقة..."
                className="w-full pr-10 pl-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-green-400 text-sm"
              />
            </div>

            {/* Filter Tabs */}
            <div className="flex flex-wrap gap-3">
              <div className="flex flex-wrap gap-2">
                <span className="text-xs font-semibold text-gray-500 flex items-center gap-1 ml-2">
                  <Filter className="w-3 h-3" />
                  التخصص:
                </span>
                {categories.map((cat) => (
                  <button key={cat.id} onClick={() => setCategory(cat.id)}
                    className={`px-3 py-1.5 rounded-full text-xs font-semibold transition-all ${
                      category === cat.id
                        ? "text-white shadow-sm"
                        : "bg-gray-100 text-gray-600 hover:bg-gray-200"
                    }`}
                    style={category === cat.id ? { background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" } : {}}>
                    {cat.label}
                  </button>
                ))}
              </div>
            </div>

            <div className="flex flex-wrap gap-2">
              <span className="text-xs font-semibold text-gray-500 flex items-center gap-1 ml-2">المرحلة:</span>
              {levels.map((lv) => (
                <button key={lv.id} onClick={() => setLevel(lv.id)}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold transition-all ${
                    level === lv.id ? "text-white shadow-sm" : "bg-gray-100 text-gray-600 hover:bg-gray-200"
                  }`}
                  style={level === lv.id ? { background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" } : {}}>
                  {lv.label}
                </button>
              ))}
            </div>

            <div className="flex flex-wrap gap-2">
              <span className="text-xs font-semibold text-gray-500 flex items-center gap-1 ml-2">الحالة:</span>
              {statuses.map((st) => (
                <button key={st.id} onClick={() => setStatus(st.id)}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold transition-all ${
                    status === st.id ? "text-white shadow-sm" : "bg-gray-100 text-gray-600 hover:bg-gray-200"
                  }`}
                  style={status === st.id ? { background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" } : {}}>
                  {st.label}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Results Count */}
        <div className="flex items-center justify-between mb-5">
          <p className="text-gray-500 text-sm">
            {isLoading ? "جاري التحميل..." : `${filtered?.length ?? 0} مسابقة`}
          </p>
        </div>

        {/* Competitions Grid */}
        {isLoading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1,2,3,4,5,6].map((i) => (
              <div key={i} className="competition-card animate-pulse">
                <div className="h-48 bg-gray-100"></div>
                <div className="p-5 space-y-3">
                  <div className="h-4 bg-gray-100 rounded w-3/4"></div>
                  <div className="h-3 bg-gray-100 rounded w-full"></div>
                  <div className="h-3 bg-gray-100 rounded w-2/3"></div>
                </div>
              </div>
            ))}
          </div>
        ) : filtered && filtered.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filtered.map((comp, i) => (
              <Link key={comp.id} href={`/competitions/${comp.slug}`}
                className="competition-card block animate-fade-in-up"
                style={{ animationDelay: `${i * 60}ms` }}>
                <div className="h-48 relative overflow-hidden" style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
                  <div className="absolute inset-0 flex items-center justify-center">
                    <Trophy className="w-16 h-16 text-white/15" />
                  </div>
                  {comp.isFeatured && (
                    <div className="absolute top-3 right-3 flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold"
                      style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))", color: "oklch(0.15 0 0)" }}>
                      <Star className="w-3 h-3 fill-current" />
                      مميزة
                    </div>
                  )}
                  <div className="absolute top-3 left-3">
                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${statusColors[comp.status ?? 'upcoming']}`}>
                      {statusLabels[comp.status ?? 'upcoming']}
                    </span>
                  </div>
                </div>
                <div className="p-5">
                  <div className="flex items-center gap-2 mb-2">
                    <span className="badge-primary text-xs">{categoryLabels[comp.category]}</span>
                    {comp.educationLevel && comp.educationLevel !== 'all' && (
                      <span className="badge-gold text-xs">{levels.find(l => l.id === comp.educationLevel)?.label}</span>
                    )}
                  </div>
                  <h3 className="font-black text-gray-800 text-lg mb-2 line-clamp-2">{comp.title}</h3>
                  <p className="text-gray-500 text-sm line-clamp-2 mb-4">{comp.shortDescription ?? comp.description ?? "مسابقة تعليمية متميزة"}</p>
                  <div className="flex items-center justify-between text-xs text-gray-400">
                    <div className="flex items-center gap-1">
                      <Calendar className="w-3 h-3" />
                      {comp.startDate ? new Date(comp.startDate).toLocaleDateString('ar-SA') : "قريباً"}
                    </div>
                    {comp.maxParticipants && (
                      <div className="flex items-center gap-1">
                        <Users className="w-3 h-3" />
                        {comp.maxParticipants} مشارك
                      </div>
                    )}
                    <span className="font-semibold flex items-center gap-1" style={{ color: "oklch(0.42 0.15 145)" }}>
                      التفاصيل
                      <ChevronLeft className="w-3 h-3" />
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        ) : (
          <div className="text-center py-20 rounded-2xl bg-white border border-gray-100">
            <Trophy className="w-16 h-16 mx-auto mb-4 text-gray-200" />
            <h3 className="text-xl font-bold text-gray-600 mb-2">لا توجد مسابقات</h3>
            <p className="text-gray-400 mb-6">لم يتم العثور على مسابقات بالمعايير المحددة</p>
            <Button onClick={() => { setCategory(""); setLevel(""); setStatus(""); setSearch(""); }}
              variant="outline" className="border-green-200 text-green-700">
              إعادة تعيين الفلاتر
            </Button>
          </div>
        )}
      </div>
    </div>
  );
}
