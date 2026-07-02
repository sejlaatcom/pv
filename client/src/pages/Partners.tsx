import { Handshake, Building2, GraduationCap, Globe, Heart, ArrowLeft } from "lucide-react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

const partnerTypes = [
  {
    type: "educational",
    title: "الشركاء التعليميون",
    icon: GraduationCap,
    color: "#166534",
    bg: "#f0fdf4",
    partners: [
      { name: "وزارة التعليم", type: "جهة حكومية", initial: "و" },
      { name: "جامعة الملك سعود", type: "جامعة", initial: "ج" },
      { name: "جامعة الملك عبدالعزيز", type: "جامعة", initial: "ج" },
      { name: "مدارس المستقبل", type: "مجموعة مدارس", initial: "م" },
    ],
  },
  {
    type: "corporate",
    title: "الشركاء التجاريون",
    icon: Building2,
    color: "#1e40af",
    bg: "#eff6ff",
    partners: [
      { name: "شركة التقنية التعليمية", type: "شركة تقنية", initial: "ش" },
      { name: "مؤسسة الإبداع", type: "مؤسسة", initial: "م" },
      { name: "مجموعة المعرفة", type: "مجموعة", initial: "م" },
      { name: "شركة الحلول الذكية", type: "شركة", initial: "ش" },
    ],
  },
  {
    type: "media",
    title: "الشركاء الإعلاميون",
    icon: Globe,
    color: "#7c3aed",
    bg: "#f5f3ff",
    partners: [
      { name: "قناة التعليم", type: "قناة تلفزيونية", initial: "ق" },
      { name: "منصة المعرفة", type: "منصة رقمية", initial: "م" },
      { name: "إذاعة الثقافة", type: "إذاعة", initial: "إ" },
      { name: "صحيفة التعليم", type: "صحيفة", initial: "ص" },
    ],
  },
];

const benefits = [
  { title: "الوصول لآلاف الطلاب", desc: "تواصل مباشر مع شريحة واسعة من الطلاب والمعلمين" },
  { title: "تعزيز الهوية المؤسسية", desc: "ظهور بارز في جميع فعاليات ومسابقات المنصة" },
  { title: "المسؤولية الاجتماعية", desc: "المساهمة في دعم التعليم وتنمية المجتمع" },
  { title: "شراكة استراتيجية", desc: "علاقة شراكة طويلة الأمد مع منصة رائدة" },
];

export default function Partners() {
  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 60% 40%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <Handshake className="w-4 h-4" />
            الشركاء
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">شركاؤنا في النجاح</h1>
          <p className="text-white/75 text-lg max-w-2xl mx-auto">
            نفخر بشراكاتنا الاستراتيجية مع المؤسسات التعليمية والتجارية الرائدة
          </p>
        </div>
      </div>

      <div className="container py-16">
        {/* Partners Sections */}
        {partnerTypes.map((section) => (
          <div key={section.type} className="mb-14">
            <div className="flex items-center gap-3 mb-8">
              <div className="w-12 h-12 rounded-xl flex items-center justify-center" style={{ background: section.bg }}>
                <section.icon className="w-6 h-6" style={{ color: section.color }} />
              </div>
              <h2 className="text-2xl font-black text-gray-800">{section.title}</h2>
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-5">
              {section.partners.map((partner) => (
                <div key={partner.name} className="p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow text-center">
                  <div className="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-black mx-auto mb-3"
                    style={{ background: `linear-gradient(135deg, ${section.color}, ${section.color}cc)` }}>
                    {partner.initial}
                  </div>
                  <h3 className="font-bold text-gray-800 text-sm mb-1">{partner.name}</h3>
                  <p className="text-xs text-gray-400">{partner.type}</p>
                </div>
              ))}
            </div>
          </div>
        ))}

        {/* Partnership Benefits */}
        <div className="p-8 rounded-3xl mb-12" style={{ background: "oklch(0.97 0.02 145)" }}>
          <div className="text-center mb-8">
            <h2 className="text-3xl font-black text-gray-800 mb-3">فوائد الشراكة</h2>
            <p className="text-gray-500">لماذا تختار الشراكة مع منصة علم؟</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            {benefits.map((benefit, i) => (
              <div key={i} className="p-5 bg-white rounded-2xl text-center">
                <div className="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  <Heart className="w-5 h-5 text-white" />
                </div>
                <h3 className="font-bold text-gray-800 text-sm mb-2">{benefit.title}</h3>
                <p className="text-gray-500 text-xs">{benefit.desc}</p>
              </div>
            ))}
          </div>
        </div>

        {/* CTA */}
        <div className="text-center p-12 rounded-3xl text-white"
          style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
          <Handshake className="w-14 h-14 mx-auto mb-4 opacity-80" />
          <h2 className="text-3xl font-black mb-4">كن شريكاً في التميز</h2>
          <p className="text-white/75 mb-8 max-w-lg mx-auto">
            انضم إلى شبكة شركائنا وساهم في دعم التعليم وتنمية الأجيال القادمة
          </p>
          <Button asChild size="lg" className="text-gray-900 font-bold"
            style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
            <Link href="/contact">تواصل معنا للشراكة</Link>
          </Button>
        </div>
      </div>
    </div>
  );
}
