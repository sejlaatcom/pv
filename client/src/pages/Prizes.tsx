import { Trophy, Award, Star, Gift, Medal, Crown, CheckCircle } from "lucide-react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

const mainPrizes = [
  { rank: 1, title: "المركز الأول", value: "5,000 ريال", icon: Crown, gradient: "from-yellow-400 to-amber-500", shadow: "shadow-amber-200", desc: "جائزة مالية + شهادة تقدير ذهبية + درع تكريمي" },
  { rank: 2, title: "المركز الثاني", value: "3,000 ريال", icon: Medal, gradient: "from-gray-300 to-gray-400", shadow: "shadow-gray-200", desc: "جائزة مالية + شهادة تقدير فضية + درع تكريمي" },
  { rank: 3, title: "المركز الثالث", value: "2,000 ريال", icon: Award, gradient: "from-amber-600 to-amber-700", shadow: "shadow-amber-100", desc: "جائزة مالية + شهادة تقدير برونزية + درع تكريمي" },
];

const certificates = [
  { type: "winner", title: "شهادة الفوز", desc: "تُمنح للفائزين بالمراكز الثلاثة الأولى", color: "#b45309", bg: "#fffbeb" },
  { type: "excellence", title: "شهادة التميز", desc: "تُمنح للمتميزين في أداء المسابقة", color: "#7c3aed", bg: "#f5f3ff" },
  { type: "participation", title: "شهادة المشاركة", desc: "تُمنح لجميع المشاركين في المسابقة", color: "#166534", bg: "#f0fdf4" },
];

const benefits = [
  "تعزيز السيرة الذاتية والملف الأكاديمي",
  "شهادات معتمدة ومعترف بها",
  "جوائز مالية قيّمة",
  "تكريم في حفلات رسمية",
  "فرص للمشاركة في مسابقات دولية",
  "بناء شبكة علاقات أكاديمية",
];

export default function Prizes() {
  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 70% 40%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <Trophy className="w-4 h-4" />
            الجوائز
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">جوائز تستحق المنافسة</h1>
          <p className="text-white/75 text-lg max-w-2xl mx-auto">
            نقدم جوائز قيّمة ومتنوعة تكريماً للمتميزين والفائزين في مسابقاتنا التعليمية
          </p>
        </div>
      </div>

      <div className="container py-16">
        {/* Main Prizes */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-black text-gray-800 mb-3">الجوائز الرئيسية</h2>
          <p className="text-gray-500">جوائز مالية وتكريمية للفائزين بالمراكز الأولى</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
          {mainPrizes.map((prize, i) => (
            <div key={prize.rank} className={`text-center p-8 rounded-3xl border-2 transition-all hover:shadow-xl ${i === 0 ? "border-amber-300 scale-105" : "border-gray-100"}`}
              style={{ background: i === 0 ? "linear-gradient(135deg, #fffbeb, #fef3c7)" : "white" }}>
              <div className={`w-20 h-20 rounded-3xl bg-gradient-to-br ${prize.gradient} flex items-center justify-center mx-auto mb-5 shadow-xl ${prize.shadow}`}>
                <prize.icon className="w-10 h-10 text-white" />
              </div>
              <div className="text-4xl font-black mb-2" style={{ color: "oklch(0.42 0.15 145)" }}>{prize.rank}</div>
              <h3 className="text-xl font-black text-gray-800 mb-2">{prize.title}</h3>
              <div className="text-3xl font-black mb-3" style={{ color: "oklch(0.72 0.15 75)" }}>{prize.value}</div>
              <p className="text-gray-500 text-sm">{prize.desc}</p>
            </div>
          ))}
        </div>

        {/* Certificates */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-black text-gray-800 mb-3">الشهادات</h2>
          <p className="text-gray-500">شهادات معتمدة تُعزز مسيرتك الأكاديمية</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
          {certificates.map((cert) => (
            <div key={cert.type} className="p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow">
              <div className="w-14 h-14 rounded-2xl flex items-center justify-center mb-4" style={{ background: cert.bg }}>
                <Award className="w-7 h-7" style={{ color: cert.color }} />
              </div>
              <h3 className="font-black text-gray-800 text-lg mb-2">{cert.title}</h3>
              <p className="text-gray-500 text-sm">{cert.desc}</p>
            </div>
          ))}
        </div>

        {/* Benefits */}
        <div className="p-8 rounded-3xl mb-16" style={{ background: "oklch(0.97 0.02 145)" }}>
          <div className="text-center mb-8">
            <h2 className="text-3xl font-black text-gray-800 mb-3">فوائد المشاركة</h2>
            <p className="text-gray-500">المشاركة في مسابقاتنا تفتح أمامك آفاقاً واسعة</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {benefits.map((benefit, i) => (
              <div key={i} className="flex items-center gap-3 p-4 bg-white rounded-xl">
                <CheckCircle className="w-5 h-5 flex-shrink-0" style={{ color: "oklch(0.42 0.15 145)" }} />
                <p className="text-gray-700 font-medium text-sm">{benefit}</p>
              </div>
            ))}
          </div>
        </div>

        {/* CTA */}
        <div className="text-center p-12 rounded-3xl text-white"
          style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
          <Gift className="w-14 h-14 mx-auto mb-4 opacity-80" />
          <h2 className="text-3xl font-black mb-4">شارك وافز بجوائز رائعة</h2>
          <p className="text-white/75 mb-8">سجّل الآن وابدأ رحلتك نحو الفوز</p>
          <Button asChild size="lg" className="text-gray-900 font-bold"
            style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
            <Link href="/competitions">استعرض المسابقات</Link>
          </Button>
        </div>
      </div>
    </div>
  );
}
