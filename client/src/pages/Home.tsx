import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { trpc } from "@/lib/trpc";
import {
  Trophy, Star, Users, BookOpen, Award, ArrowLeft, Play,
  GraduationCap, Zap, Target, CheckCircle, ChevronLeft,
  Globe, Calculator, FlaskConical, Newspaper, AlignLeft
} from "lucide-react";

const educationLevels = [
  { id: "primary", label: "ابتدائي", icon: "🏫", desc: "الصفوف 1-6", color: "from-emerald-400 to-emerald-600", bg: "bg-emerald-50", border: "border-emerald-200" },
  { id: "middle", label: "متوسط", icon: "📚", desc: "الصفوف 7-9", color: "from-blue-400 to-blue-600", bg: "bg-blue-50", border: "border-blue-200" },
  { id: "secondary", label: "ثانوي", icon: "🎓", desc: "الصفوف 10-12", color: "from-purple-400 to-purple-600", bg: "bg-purple-50", border: "border-purple-200" },
  { id: "university", label: "جامعي", icon: "🏛️", desc: "المرحلة الجامعية", color: "from-amber-400 to-amber-600", bg: "bg-amber-50", border: "border-amber-200" },
];

const competitionCategories = [
  { id: "arabic", label: "اللغة العربية", icon: AlignLeft, desc: "نحو، إملاء، تعبير", color: "#166534", bg: "#f0fdf4", border: "#bbf7d0" },
  { id: "english", label: "اللغة الإنجليزية", icon: Globe, desc: "Grammar, Vocabulary, Writing", color: "#1e40af", bg: "#eff6ff", border: "#bfdbfe" },
  { id: "math", label: "الرياضيات", icon: Calculator, desc: "جبر، هندسة، إحصاء", color: "#7c3aed", bg: "#f5f3ff", border: "#ddd6fe" },
  { id: "science", label: "العلوم", icon: FlaskConical, desc: "فيزياء، كيمياء، أحياء", color: "#0891b2", bg: "#ecfeff", border: "#a5f3fc" },
  { id: "general", label: "الثقافة العامة", icon: Star, desc: "معلومات عامة ومتنوعة", color: "#b45309", bg: "#fffbeb", border: "#fde68a" },
  { id: "reading", label: "القراءة", icon: BookOpen, desc: "فهم وتحليل النصوص", color: "#be185d", bg: "#fdf2f8", border: "#fbcfe8" },
];

const stats = [
  { value: "٥٠٠٠+", label: "طالب مشارك", icon: Users },
  { value: "١٢٠+", label: "مسابقة منظمة", icon: Trophy },
  { value: "٣٠٠+", label: "مدرسة مشاركة", icon: GraduationCap },
  { value: "٢٠٠+", label: "جائزة موزعة", icon: Award },
];

const features = [
  { icon: Zap, title: "تنافس بسهولة", desc: "سجّل في المسابقات بخطوات بسيطة وابدأ رحلتك التنافسية" },
  { icon: Target, title: "تتبع تقدمك", desc: "راقب نتائجك وشهاداتك من لوحة تحكم شخصية متكاملة" },
  { icon: CheckCircle, title: "احصل على شهادات", desc: "اكسب شهادات معتمدة تُضاف إلى سجلك الأكاديمي" },
  { icon: Award, title: "فز بجوائز قيّمة", desc: "مسابقات بجوائز نقدية وعينية وفرص أكاديمية مميزة" },
];

export default function Home() {
  const { data: featuredCompetitions, isLoading } = trpc.competitions.featured.useQuery();

  const categoryLabels: Record<string, string> = {
    arabic: "اللغة العربية", english: "اللغة الإنجليزية", math: "الرياضيات",
    science: "العلوم", general: "الثقافة العامة", reading: "القراءة", other: "أخرى",
  };
  const statusLabels: Record<string, string> = {
    upcoming: "قادمة", active: "جارية", completed: "منتهية", cancelled: "ملغاة",
  };
  const statusColors: Record<string, string> = {
    upcoming: "bg-blue-100 text-blue-700", active: "bg-green-100 text-green-700",
    completed: "bg-gray-100 text-gray-600", cancelled: "bg-red-100 text-red-700",
  };

  return (
    <div className="min-h-screen bg-white">
      {/* ===== Hero Section ===== */}
      <section className="hero-gradient hero-pattern min-h-screen flex items-center relative overflow-hidden pt-16">
        {/* Decorative elements */}
        <div className="absolute top-20 left-10 w-64 h-64 rounded-full opacity-10" style={{ background: "oklch(0.72 0.15 75)" }}></div>
        <div className="absolute bottom-20 right-10 w-48 h-48 rounded-full opacity-10" style={{ background: "oklch(0.55 0.15 145)" }}></div>
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full opacity-5" style={{ background: "oklch(0.72 0.15 75)" }}></div>

        <div className="container py-20 relative z-10">
          <div className="max-w-4xl mx-auto text-center">
            {/* Badge */}
            <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold mb-8 animate-fade-in"
              style={{ background: "oklch(0.72 0.15 75 / 0.15)", border: "1px solid oklch(0.72 0.15 75 / 0.3)", color: "oklch(0.85 0.12 75)" }}>
              <Star className="w-4 h-4 fill-current" />
              المنصة الرائدة في المسابقات التعليمية
            </div>

            {/* Main Title */}
            <h1 className="text-5xl md:text-6xl lg:text-7xl font-black text-white mb-6 leading-tight animate-fade-in-up">
              العب،{" "}
              <span style={{ color: "oklch(0.82 0.13 75)" }}>تعلّم،</span>
              <br />
              تنافس،{" "}
              <span className="relative">
                <span style={{ color: "oklch(0.82 0.13 75)" }}>وافز</span>
                <span className="absolute -bottom-2 right-0 left-0 h-1 rounded-full opacity-60"
                  style={{ background: "linear-gradient(90deg, oklch(0.72 0.15 75), transparent)" }}></span>
              </span>
            </h1>

            <p className="text-lg md:text-xl text-white/75 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-100">
              منصة تعليمية تنافسية متكاملة تتيح للطلاب المشاركة في مسابقات ثقافية وعلمية عبر مراحل دراسية متعددة، مع شهادات معتمدة وجوائز قيّمة.
            </p>

            {/* CTA Buttons */}
            <div className="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up delay-200">
              <Button size="lg" asChild className="text-base font-bold px-8 py-6 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200"
                style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))", color: "oklch(0.15 0 0)" }}>
                <Link href="/register" className="flex items-center gap-2">
                  <Trophy className="w-5 h-5" />
                  سجّل الآن مجاناً
                </Link>
              </Button>
              <Button size="lg" variant="outline" asChild
                className="text-base font-semibold px-8 py-6 rounded-xl border-2 border-white/30 text-white hover:bg-white/10 hover:border-white/50 transition-all duration-200">
                <Link href="/competitions" className="flex items-center gap-2">
                  <Play className="w-5 h-5" />
                  استعرض المسابقات
                </Link>
              </Button>
            </div>

            {/* Stats Row */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 animate-fade-in-up delay-300">
              {stats.map((stat) => (
                <div key={stat.label} className="text-center p-4 rounded-2xl" style={{ background: "oklch(1 0 0 / 0.07)", border: "1px solid oklch(1 0 0 / 0.12)" }}>
                  <stat.icon className="w-6 h-6 mx-auto mb-2" style={{ color: "oklch(0.82 0.13 75)" }} />
                  <div className="text-2xl font-black text-white">{stat.value}</div>
                  <div className="text-xs text-white/60 mt-1">{stat.label}</div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Wave Divider */}
        <div className="absolute bottom-0 left-0 right-0">
          <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" className="w-full">
            <path d="M0 80L1440 80L1440 20C1200 70 960 0 720 40C480 80 240 10 0 50L0 80Z" fill="white" />
          </svg>
        </div>
      </section>

      {/* ===== Education Levels ===== */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="text-center mb-12">
            <div className="badge-primary inline-flex mb-4">
              <GraduationCap className="w-4 h-4" />
              المراحل التعليمية
            </div>
            <h2 className="text-3xl md:text-4xl font-black text-gray-900 mb-4">مسابقات لجميع المراحل الدراسية</h2>
            <p className="text-gray-500 max-w-xl mx-auto">نوفر مسابقات مصممة خصيصاً لكل مرحلة دراسية بمحتوى ملائم وتحديات مناسبة</p>
          </div>
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-5">
            {educationLevels.map((level, i) => (
              <Link key={level.id} href={`/competitions?level=${level.id}`}
                className={`group p-6 rounded-2xl border-2 ${level.bg} ${level.border} text-center card-hover cursor-pointer animate-fade-in-up`}
                style={{ animationDelay: `${i * 100}ms` }}>
                <div className="text-5xl mb-4">{level.icon}</div>
                <div className={`inline-flex items-center justify-center w-10 h-10 rounded-xl mb-3 bg-gradient-to-br ${level.color} opacity-0 group-hover:opacity-100 transition-opacity mx-auto`}>
                </div>
                <h3 className="text-xl font-black text-gray-800 mb-1">{level.label}</h3>
                <p className="text-sm text-gray-500">{level.desc}</p>
                <div className="mt-4 flex items-center justify-center gap-1 text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity"
                  style={{ color: "oklch(0.42 0.15 145)" }}>
                  استعرض المسابقات
                  <ChevronLeft className="w-4 h-4" />
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* ===== Competition Categories ===== */}
      <section className="py-20" style={{ background: "oklch(0.97 0.01 145)" }}>
        <div className="container">
          <div className="text-center mb-12">
            <div className="badge-gold inline-flex mb-4">
              <Trophy className="w-4 h-4" />
              التخصصات
            </div>
            <h2 className="text-3xl md:text-4xl font-black text-gray-900 mb-4">مسابقات في مختلف التخصصات</h2>
            <p className="text-gray-500 max-w-xl mx-auto">اختر مجالك المفضل وتنافس مع الأفضل من جميع أنحاء المملكة</p>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-5">
            {competitionCategories.map((cat, i) => (
              <Link key={cat.id} href={`/competitions?category=${cat.id}`}
                className="group p-6 rounded-2xl border-2 bg-white card-hover cursor-pointer animate-fade-in-up"
                style={{ borderColor: cat.border, animationDelay: `${i * 80}ms` }}>
                <div className="flex items-center gap-4 mb-3">
                  <div className="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style={{ background: cat.bg, border: `1px solid ${cat.border}` }}>
                    <cat.icon className="w-6 h-6" style={{ color: cat.color }} />
                  </div>
                  <div>
                    <h3 className="font-black text-gray-800 text-base">{cat.label}</h3>
                    <p className="text-xs text-gray-500">{cat.desc}</p>
                  </div>
                </div>
                <div className="flex items-center gap-1 text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity"
                  style={{ color: cat.color }}>
                  استعرض المسابقات
                  <ChevronLeft className="w-4 h-4" />
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* ===== Featured Competitions ===== */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="flex items-center justify-between mb-12">
            <div>
              <div className="badge-primary inline-flex mb-3">
                <Star className="w-4 h-4" />
                مميزة
              </div>
              <h2 className="text-3xl md:text-4xl font-black text-gray-900">أبرز المسابقات</h2>
            </div>
            <Button variant="outline" asChild className="border-green-200 text-green-700 hover:bg-green-50">
              <Link href="/competitions" className="flex items-center gap-2">
                عرض الكل
                <ArrowLeft className="w-4 h-4" />
              </Link>
            </Button>
          </div>

          {isLoading ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[1, 2, 3].map((i) => (
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
          ) : featuredCompetitions && featuredCompetitions.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {featuredCompetitions.map((comp, i) => (
                <Link key={comp.id} href={`/competitions/${comp.slug}`}
                  className="competition-card block animate-fade-in-up"
                  style={{ animationDelay: `${i * 100}ms` }}>
                  <div className="h-48 relative overflow-hidden" style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
                    <div className="absolute inset-0 flex items-center justify-center">
                      <Trophy className="w-16 h-16 text-white/20" />
                    </div>
                    <div className="absolute top-3 right-3 flex gap-2">
                      <span className={`px-2 py-1 rounded-full text-xs font-semibold ${statusColors[comp.status ?? 'upcoming']}`}>
                        {statusLabels[comp.status ?? 'upcoming']}
                      </span>
                    </div>
                  </div>
                  <div className="p-5">
                    <div className="flex items-center gap-2 mb-2">
                      <span className="badge-primary text-xs">{categoryLabels[comp.category]}</span>
                    </div>
                    <h3 className="font-black text-gray-800 text-lg mb-2 line-clamp-2">{comp.title}</h3>
                    <p className="text-gray-500 text-sm line-clamp-2 mb-4">{comp.shortDescription ?? comp.description}</p>
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-gray-400">
                        {comp.startDate ? new Date(comp.startDate).toLocaleDateString('ar-SA') : "قريباً"}
                      </span>
                      <span className="text-sm font-semibold flex items-center gap-1" style={{ color: "oklch(0.42 0.15 145)" }}>
                        التفاصيل
                        <ChevronLeft className="w-4 h-4" />
                      </span>
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          ) : (
            <div className="text-center py-16 rounded-2xl" style={{ background: "oklch(0.97 0.01 145)" }}>
              <Trophy className="w-16 h-16 mx-auto mb-4 text-gray-300" />
              <h3 className="text-xl font-bold text-gray-600 mb-2">المسابقات قادمة قريباً</h3>
              <p className="text-gray-400 mb-6">سيتم إضافة المسابقات قريباً، اشترك في النشرة البريدية لتكون أول من يعلم</p>
              <Button asChild style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }} className="text-white">
                <Link href="/register">سجّل الآن</Link>
              </Button>
            </div>
          )}
        </div>
      </section>

      {/* ===== Features Section ===== */}
      <section className="py-20" style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145) 0%, oklch(0.20 0.10 145) 100%)" }}>
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-black text-white mb-4">لماذا منصة علم؟</h2>
            <p className="text-white/60 max-w-xl mx-auto">نوفر بيئة تعليمية تنافسية متكاملة تحفز الطلاب على التميز والإبداع</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {features.map((feature, i) => (
              <div key={feature.title} className="text-center p-6 rounded-2xl animate-fade-in-up"
                style={{ background: "oklch(1 0 0 / 0.06)", border: "1px solid oklch(1 0 0 / 0.10)", animationDelay: `${i * 100}ms` }}>
                <div className="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
                  style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75 / 0.2), oklch(0.72 0.15 75 / 0.1))", border: "1px solid oklch(0.72 0.15 75 / 0.3)" }}>
                  <feature.icon className="w-7 h-7" style={{ color: "oklch(0.82 0.13 75)" }} />
                </div>
                <h3 className="text-white font-bold text-lg mb-2">{feature.title}</h3>
                <p className="text-white/60 text-sm leading-relaxed">{feature.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ===== CTA Section ===== */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="max-w-3xl mx-auto text-center p-12 rounded-3xl relative overflow-hidden"
            style={{ background: "linear-gradient(135deg, oklch(0.97 0.02 145), oklch(0.95 0.03 145))", border: "2px solid oklch(0.88 0.05 145)" }}>
            <div className="absolute top-0 left-0 right-0 h-1 rounded-t-3xl"
              style={{ background: "linear-gradient(90deg, oklch(0.42 0.15 145), oklch(0.72 0.15 75), oklch(0.42 0.15 145))" }}></div>
            <Trophy className="w-16 h-16 mx-auto mb-6" style={{ color: "oklch(0.72 0.15 75)" }} />
            <h2 className="text-3xl md:text-4xl font-black text-gray-900 mb-4">ابدأ رحلتك التنافسية اليوم</h2>
            <p className="text-gray-500 mb-8 text-lg">انضم إلى آلاف الطلاب المتميزين وشارك في مسابقات تعليمية تنافسية تُثري مسيرتك الأكاديمية</p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button size="lg" asChild className="text-white font-bold px-8"
                style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                <Link href="/register">إنشاء حساب مجاني</Link>
              </Button>
              <Button size="lg" variant="outline" asChild className="border-green-200 text-green-700 hover:bg-green-50 font-semibold px-8">
                <Link href="/about">تعرف على المنصة</Link>
              </Button>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
