import { BookOpen, Target, Clock, Star, CheckCircle, ArrowLeft, PlayCircle, FileText, Brain, Lightbulb } from "lucide-react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

const subjects = [
  { id: "arabic", label: "اللغة العربية", icon: "📖", color: "#166534", bg: "#f0fdf4", tips: ["مراجعة قواعد النحو والصرف", "قراءة النصوص الأدبية الكلاسيكية", "التدرب على الإملاء والكتابة", "حفظ المفردات والمصطلحات"] },
  { id: "english", label: "اللغة الإنجليزية", icon: "🔤", color: "#1e40af", bg: "#eff6ff", tips: ["تعزيز المفردات اليومية", "ممارسة قواعد اللغة", "الاستماع والمحادثة", "القراءة المكثفة"] },
  { id: "math", label: "الرياضيات", icon: "🔢", color: "#7c3aed", bg: "#f5f3ff", tips: ["حل المسائل التدريبية", "مراجعة المفاهيم الأساسية", "التدرب على الحسابات الذهنية", "فهم المنطق الرياضي"] },
  { id: "science", label: "العلوم", icon: "🔬", color: "#0891b2", bg: "#ecfeff", tips: ["مراجعة المفاهيم العلمية", "التجارب العملية", "حفظ المصطلحات العلمية", "ربط النظرية بالتطبيق"] },
  { id: "general", label: "الثقافة العامة", icon: "🌍", color: "#b45309", bg: "#fffbeb", tips: ["متابعة الأخبار والأحداث", "قراءة الكتب المتنوعة", "الاطلاع على التاريخ والجغرافيا", "تنمية الوعي الثقافي"] },
  { id: "reading", label: "القراءة", icon: "📚", color: "#be185d", bg: "#fdf2f8", tips: ["القراءة اليومية المنتظمة", "تحليل النصوص وفهمها", "توسيع المخزون اللغوي", "تنمية مهارات الاستيعاب"] },
];

const stages = [
  { num: 1, title: "التقييم الذاتي", desc: "حدد نقاط قوتك وضعفك في كل مادة", icon: Target, color: "#166534" },
  { num: 2, title: "وضع خطة الدراسة", desc: "ضع جدولاً زمنياً منظماً للمذاكرة", icon: Clock, color: "#1e40af" },
  { num: 3, title: "التدريب المكثف", desc: "حل الأسئلة التدريبية والاختبارات السابقة", icon: Brain, color: "#7c3aed" },
  { num: 4, title: "المراجعة والتثبيت", desc: "راجع المادة وثبّت المعلومات في ذهنك", icon: CheckCircle, color: "#b45309" },
];

export default function Training() {
  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 80% 30%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <BookOpen className="w-4 h-4" />
            التدريب والاستعداد
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">استعد للمنافسة بثقة</h1>
          <p className="text-white/75 text-lg max-w-2xl mx-auto">
            دليلك الشامل للتحضير للمسابقات التعليمية وتحقيق أفضل النتائج
          </p>
        </div>
      </div>

      <div className="container py-16">
        {/* Preparation Stages */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-black text-gray-800 mb-3">خطوات الاستعداد</h2>
          <p className="text-gray-500">اتبع هذه الخطوات لتحقيق أفضل النتائج</p>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          {stages.map((stage) => (
            <div key={stage.num} className="relative">
              {stage.num < 4 && (
                <div className="hidden lg:block absolute top-8 left-0 w-full h-0.5 z-0" style={{ background: "oklch(0.88 0.05 145)" }}></div>
              )}
              <div className="relative z-10 text-center p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md"
                  style={{ background: `${stage.color}15`, border: `2px solid ${stage.color}30` }}>
                  <stage.icon className="w-8 h-8" style={{ color: stage.color }} />
                </div>
                <div className="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-black mx-auto mb-3"
                  style={{ background: stage.color }}>
                  {stage.num}
                </div>
                <h3 className="font-black text-gray-800 mb-2">{stage.title}</h3>
                <p className="text-gray-500 text-sm">{stage.desc}</p>
              </div>
            </div>
          ))}
        </div>

        {/* Subjects */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-black text-gray-800 mb-3">نصائح لكل مادة</h2>
          <p className="text-gray-500">إرشادات تخصصية للتحضير لكل مسابقة</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
          {subjects.map((subject) => (
            <div key={subject.id} className="p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style={{ background: subject.bg }}>
                  {subject.icon}
                </div>
                <h3 className="font-black text-gray-800 text-lg">{subject.label}</h3>
              </div>
              <ul className="space-y-2">
                {subject.tips.map((tip) => (
                  <li key={tip} className="flex items-start gap-2 text-sm text-gray-600">
                    <CheckCircle className="w-4 h-4 flex-shrink-0 mt-0.5" style={{ color: subject.color }} />
                    {tip}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Tips Section */}
        <div className="p-8 rounded-3xl mb-16" style={{ background: "oklch(0.97 0.02 145)" }}>
          <div className="flex items-center gap-3 mb-6">
            <div className="w-12 h-12 rounded-xl flex items-center justify-center"
              style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
              <Lightbulb className="w-6 h-6 text-white" />
            </div>
            <h2 className="text-2xl font-black text-gray-800">نصائح ذهبية للمتسابقين</h2>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {[
              "ابدأ التحضير مبكراً ولا تؤجل المذاكرة",
              "خصص وقتاً يومياً ثابتاً للمراجعة",
              "حل أسئلة المسابقات السابقة للتدرب",
              "احرص على النوم الكافي قبل يوم المسابقة",
              "تناول وجبة صحية قبل الاختبار",
              "اقرأ الأسئلة بعناية قبل الإجابة",
              "ابدأ بالأسئلة السهلة ثم الصعبة",
              "راجع إجاباتك قبل التسليم",
            ].map((tip, i) => (
              <div key={i} className="flex items-start gap-3 p-3 bg-white rounded-xl">
                <div className="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  {i + 1}
                </div>
                <p className="text-gray-700 text-sm">{tip}</p>
              </div>
            ))}
          </div>
        </div>

        {/* CTA */}
        <div className="text-center">
          <h2 className="text-2xl font-black text-gray-800 mb-4">جاهز للمنافسة؟</h2>
          <p className="text-gray-500 mb-6">سجّل الآن وابدأ رحلتك نحو التميز</p>
          <div className="flex flex-wrap justify-center gap-4">
            <Button asChild size="lg" className="text-white font-bold"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              <Link href="/competitions">استعرض المسابقات</Link>
            </Button>
            <Button asChild size="lg" variant="outline" className="border-green-200 text-green-700">
              <Link href="/register">إنشاء حساب</Link>
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}
