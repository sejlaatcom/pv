import { useState } from "react";
import { HelpCircle, ChevronDown, ChevronUp, Search } from "lucide-react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

const faqs = [
  {
    category: "التسجيل والحساب",
    questions: [
      { q: "كيف أسجل في المنصة؟", a: "يمكنك التسجيل بالنقر على زر 'إنشاء حساب' واختيار نوع حسابك (طالب، معلم، مدرسة، منسق). ثم اتبع خطوات التسجيل البسيطة." },
      { q: "ما أنواع الحسابات المتاحة؟", a: "تتوفر خمسة أنواع من الحسابات: طالب، معلم، مدرسة، منسق، ومشرف/مدير. كل نوع يمتلك صلاحيات ولوحة تحكم مخصصة." },
      { q: "هل التسجيل مجاني؟", a: "نعم، التسجيل في المنصة مجاني تماماً. بعض المسابقات قد تتطلب رسوم تسجيل بسيطة يتم الإعلان عنها مسبقاً." },
      { q: "كيف أسترجع كلمة المرور؟", a: "يمكنك استخدام خاصية 'نسيت كلمة المرور' في صفحة تسجيل الدخول، وسيصلك رابط إعادة التعيين على بريدك الإلكتروني." },
    ],
  },
  {
    category: "المسابقات",
    questions: [
      { q: "كيف أشترك في مسابقة؟", a: "بعد تسجيل الدخول، اذهب إلى صفحة المسابقات، اختر المسابقة المناسبة، واضغط على 'سجّل في المسابقة'. سيتم مراجعة طلبك والموافقة عليه." },
      { q: "ما المراحل الدراسية المتاحة؟", a: "تشمل مسابقاتنا جميع المراحل: الابتدائية، المتوسطة، الثانوية، والجامعية. كل مسابقة تحدد المرحلة المستهدفة." },
      { q: "ما التخصصات المتاحة في المسابقات؟", a: "تشمل المسابقات: اللغة العربية، اللغة الإنجليزية، الرياضيات، العلوم، الثقافة العامة، والقراءة. وتُضاف تخصصات جديدة باستمرار." },
      { q: "هل يمكنني الاشتراك في أكثر من مسابقة؟", a: "نعم، يمكنك الاشتراك في أكثر من مسابقة في نفس الوقت، شريطة استيفاء شروط كل مسابقة." },
      { q: "كيف أعرف نتائج المسابقة؟", a: "تظهر النتائج في لوحة التحكم الخاصة بك فور إعلانها. كما يتم إرسال إشعار بريدي بالنتائج." },
    ],
  },
  {
    category: "الجوائز والشهادات",
    questions: [
      { q: "ما أنواع الجوائز المتاحة؟", a: "تشمل الجوائز: جوائز مالية للمراكز الأولى، شهادات تقدير معتمدة، دروع تكريمية، وفرص للمشاركة في مسابقات دولية." },
      { q: "كيف أحصل على شهادة المشاركة؟", a: "تُصدر شهادة المشاركة تلقائياً لجميع المشاركين بعد انتهاء المسابقة. يمكنك تحميلها من لوحة التحكم." },
      { q: "هل الشهادات معتمدة؟", a: "نعم، شهاداتنا معتمدة ومعترف بها من قِبل المؤسسات التعليمية الشريكة." },
    ],
  },
  {
    category: "المدارس والمؤسسات",
    questions: [
      { q: "كيف أسجل مدرستي في المنصة؟", a: "اختر نوع الحساب 'مدرسة' عند التسجيل، وأكمل بيانات المدرسة. سيتم التحقق من البيانات وتفعيل الحساب خلال 24-48 ساعة." },
      { q: "هل يمكن للمدرسة تسجيل طلابها جماعياً؟", a: "نعم، يمكن للمدرسة تسجيل طلابها في المسابقات بشكل جماعي من خلال لوحة التحكم الخاصة بالمدرسة." },
      { q: "كيف يمكن للمنسق إدارة المدارس التابعة؟", a: "يمتلك المنسق لوحة تحكم خاصة تتيح له عرض وإدارة جميع المدارس التابعة ومتابعة مشاركاتها." },
    ],
  },
];

export default function FAQ() {
  const [openItems, setOpenItems] = useState<string[]>([]);
  const [search, setSearch] = useState("");
  const [activeCategory, setActiveCategory] = useState("");

  const toggle = (id: string) => {
    setOpenItems(prev => prev.includes(id) ? prev.filter(i => i !== id) : [...prev, id]);
  };

  const filtered = faqs.map(section => ({
    ...section,
    questions: section.questions.filter(q =>
      !search || q.q.includes(search) || q.a.includes(search)
    ),
  })).filter(section =>
    (!activeCategory || section.category === activeCategory) && section.questions.length > 0
  );

  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 50% 50%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <HelpCircle className="w-4 h-4" />
            الأسئلة الشائعة
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">كيف يمكننا مساعدتك؟</h1>
          <p className="text-white/75 text-lg mb-8">إجابات لأكثر الأسئلة شيوعاً</p>
          {/* Search */}
          <div className="max-w-lg mx-auto relative">
            <Search className="absolute top-1/2 -translate-y-1/2 right-4 w-5 h-5 text-gray-400" />
            <input
              type="text"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="ابحث في الأسئلة..."
              className="w-full pr-12 pl-4 py-4 rounded-2xl text-gray-800 focus:outline-none text-sm"
            />
          </div>
        </div>
      </div>

      <div className="container py-16">
        {/* Category Filter */}
        <div className="flex flex-wrap gap-3 mb-10 justify-center">
          <button onClick={() => setActiveCategory("")}
            className={`px-4 py-2 rounded-full text-sm font-semibold transition-all ${!activeCategory ? "text-white shadow-sm" : "bg-gray-100 text-gray-600 hover:bg-gray-200"}`}
            style={!activeCategory ? { background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" } : {}}>
            جميع الأقسام
          </button>
          {faqs.map((section) => (
            <button key={section.category} onClick={() => setActiveCategory(section.category)}
              className={`px-4 py-2 rounded-full text-sm font-semibold transition-all ${activeCategory === section.category ? "text-white shadow-sm" : "bg-gray-100 text-gray-600 hover:bg-gray-200"}`}
              style={activeCategory === section.category ? { background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" } : {}}>
              {section.category}
            </button>
          ))}
        </div>

        {/* FAQ Sections */}
        <div className="max-w-3xl mx-auto space-y-8">
          {filtered.map((section) => (
            <div key={section.category}>
              <h2 className="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                <div className="w-2 h-6 rounded-full" style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}></div>
                {section.category}
              </h2>
              <div className="space-y-3">
                {section.questions.map((item, i) => {
                  const id = `${section.category}-${i}`;
                  const isOpen = openItems.includes(id);
                  return (
                    <div key={i} className={`border rounded-2xl overflow-hidden transition-all ${isOpen ? "border-green-200 shadow-sm" : "border-gray-100"}`}>
                      <button onClick={() => toggle(id)}
                        className="w-full flex items-center justify-between p-5 text-right hover:bg-gray-50 transition-colors">
                        <span className="font-semibold text-gray-800 text-sm">{item.q}</span>
                        {isOpen
                          ? <ChevronUp className="w-4 h-4 flex-shrink-0 text-green-600" />
                          : <ChevronDown className="w-4 h-4 flex-shrink-0 text-gray-400" />
                        }
                      </button>
                      {isOpen && (
                        <div className="px-5 pb-5 pt-0">
                          <div className="h-px mb-4" style={{ background: "oklch(0.92 0.03 145)" }}></div>
                          <p className="text-gray-600 text-sm leading-relaxed">{item.a}</p>
                        </div>
                      )}
                    </div>
                  );
                })}
              </div>
            </div>
          ))}

          {filtered.length === 0 && (
            <div className="text-center py-12">
              <HelpCircle className="w-12 h-12 mx-auto mb-4 text-gray-200" />
              <p className="text-gray-500">لم يتم العثور على نتائج للبحث</p>
            </div>
          )}
        </div>

        {/* Contact CTA */}
        <div className="text-center mt-16 p-8 rounded-2xl" style={{ background: "oklch(0.97 0.02 145)" }}>
          <h3 className="text-xl font-black text-gray-800 mb-2">لم تجد إجابتك؟</h3>
          <p className="text-gray-500 mb-5">فريق الدعم لدينا جاهز للمساعدة</p>
          <Button asChild className="text-white font-bold"
            style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
            <Link href="/contact">تواصل معنا</Link>
          </Button>
        </div>
      </div>
    </div>
  );
}
