import { Trophy, Target, Eye, Heart, Users, School, Award, BookOpen, Star, CheckCircle } from "lucide-react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

const values = [
  { icon: Target, title: "التميز الأكاديمي", desc: "نسعى لرفع مستوى الأداء الأكاديمي من خلال المنافسة الصحية والتحفيز المستمر", color: "#166534", bg: "#f0fdf4" },
  { icon: Heart, title: "القيم الإسلامية", desc: "نلتزم بالقيم الإسلامية والأخلاق الحميدة في جميع أنشطتنا ومسابقاتنا", color: "#7c3aed", bg: "#f5f3ff" },
  { icon: Users, title: "الشمولية", desc: "نرحب بجميع الطلاب من مختلف المراحل الدراسية والمناطق الجغرافية", color: "#1e40af", bg: "#eff6ff" },
  { icon: Star, title: "الابتكار", desc: "نطور باستمرار أساليب تعليمية مبتكرة تجعل التعلم ممتعاً وفعالاً", color: "#b45309", bg: "#fffbeb" },
];

const stats = [
  { value: "500+", label: "مسابقة نُظِّمت" },
  { value: "50,000+", label: "مشارك" },
  { value: "200+", label: "مدرسة وجامعة" },
  { value: "15+", label: "منطقة جغرافية" },
];

const team = [
  { name: "أ. محمد العمري", role: "المدير التنفيذي", initial: "م" },
  { name: "أ. سارة الزهراني", role: "مديرة التطوير التعليمي", initial: "س" },
  { name: "أ. خالد الشمري", role: "مدير التقنية", initial: "خ" },
  { name: "أ. نورة الحربي", role: "مديرة الشراكات", initial: "ن" },
];

export default function About() {
  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 20% 50%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <BookOpen className="w-4 h-4" />
            عن المنصة
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">منصة علم للمسابقات التعليمية</h1>
          <p className="text-white/75 text-lg max-w-2xl mx-auto leading-relaxed">
            منصة رائدة في تنظيم المسابقات الثقافية والتعليمية، تهدف إلى تنمية القدرات الأكاديمية وتحفيز الطلاب على التميز والإبداع
          </p>
        </div>
      </div>

      {/* Stats */}
      <div className="py-12" style={{ background: "oklch(0.97 0.02 145)" }}>
        <div className="container">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {stats.map((stat) => (
              <div key={stat.label} className="text-center">
                <div className="text-4xl font-black mb-2" style={{ color: "oklch(0.42 0.15 145)" }}>{stat.value}</div>
                <div className="text-gray-600 font-medium">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Mission & Vision */}
      <div className="py-16">
        <div className="container">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <div className="p-8 rounded-3xl" style={{ background: "oklch(0.97 0.02 145)" }}>
              <div className="w-14 h-14 rounded-2xl flex items-center justify-center mb-5"
                style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                <Target className="w-7 h-7 text-white" />
              </div>
              <h2 className="text-2xl font-black text-gray-800 mb-4">رسالتنا</h2>
              <p className="text-gray-600 leading-relaxed">
                تقديم بيئة تنافسية صحية ومحفّزة للطلاب في جميع المراحل الدراسية، من خلال مسابقات ثقافية وتعليمية متنوعة تنمّي مهاراتهم الأكاديمية وتعزز ثقتهم بأنفسهم.
              </p>
            </div>
            <div className="p-8 rounded-3xl" style={{ background: "oklch(0.97 0.02 145)" }}>
              <div className="w-14 h-14 rounded-2xl flex items-center justify-center mb-5"
                style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
                <Eye className="w-7 h-7 text-white" />
              </div>
              <h2 className="text-2xl font-black text-gray-800 mb-4">رؤيتنا</h2>
              <p className="text-gray-600 leading-relaxed">
                أن نكون المنصة التعليمية التنافسية الأولى في المملكة العربية السعودية والوطن العربي، ونسهم في بناء جيل متميز قادر على المنافسة محلياً وعالمياً.
              </p>
            </div>
          </div>

          {/* Values */}
          <div className="text-center mb-10">
            <h2 className="text-3xl font-black text-gray-800 mb-3">قيمنا الأساسية</h2>
            <p className="text-gray-500">المبادئ التي تحكم عملنا وتوجّه مسيرتنا</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            {values.map((val) => (
              <div key={val.title} className="p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow">
                <div className="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style={{ background: val.bg }}>
                  <val.icon className="w-6 h-6" style={{ color: val.color }} />
                </div>
                <h3 className="font-black text-gray-800 mb-2">{val.title}</h3>
                <p className="text-gray-500 text-sm leading-relaxed">{val.desc}</p>
              </div>
            ))}
          </div>

          {/* Team */}
          <div className="text-center mb-10">
            <h2 className="text-3xl font-black text-gray-800 mb-3">فريق العمل</h2>
            <p className="text-gray-500">نخبة من المتخصصين في التعليم والتقنية</p>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            {team.map((member) => (
              <div key={member.name} className="text-center">
                <div className="w-20 h-20 rounded-2xl flex items-center justify-center text-white text-2xl font-black mx-auto mb-3 shadow-md"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  {member.initial}
                </div>
                <h3 className="font-bold text-gray-800 text-sm">{member.name}</h3>
                <p className="text-gray-500 text-xs mt-1">{member.role}</p>
              </div>
            ))}
          </div>

          {/* CTA */}
          <div className="text-center p-12 rounded-3xl text-white"
            style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
            <Trophy className="w-14 h-14 mx-auto mb-4 opacity-80" />
            <h2 className="text-3xl font-black mb-4">انضم إلى مجتمع المتميزين</h2>
            <p className="text-white/75 mb-8 max-w-lg mx-auto">سجّل الآن وابدأ رحلتك في عالم المسابقات التعليمية</p>
            <div className="flex flex-wrap justify-center gap-4">
              <Button asChild size="lg" className="text-gray-900 font-bold"
                style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
                <Link href="/register">إنشاء حساب مجاني</Link>
              </Button>
              <Button asChild size="lg" variant="outline" className="border-white/30 text-white hover:bg-white/10">
                <Link href="/competitions">استعرض المسابقات</Link>
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
