import { useState } from "react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import {
  Trophy, UserPlus, ArrowRight, GraduationCap, BookOpen, School,
  Users, Shield, CheckCircle, ChevronRight, ChevronLeft, Info
} from "lucide-react";

const roles = [
  {
    id: "student",
    label: "طالب",
    icon: GraduationCap,
    desc: "شارك في المسابقات وتتبع نتائجك",
    color: "#166534",
    bg: "#f0fdf4",
    border: "#bbf7d0",
    features: ["المشاركة في المسابقات", "عرض النتائج والشهادات", "لوحة تحكم شخصية"],
    formFields: [
      { id: "fullName", label: "الاسم الكامل", type: "text", placeholder: "أدخل اسمك الكامل", required: true },
      { id: "email", label: "البريد الإلكتروني", type: "email", placeholder: "example@email.com", required: true },
      { id: "grade", label: "الصف الدراسي", type: "select", options: ["الصف الأول", "الصف الثاني", "الصف الثالث", "الصف الرابع", "الصف الخامس", "الصف السادس", "الصف السابع", "الصف الثامن", "الصف التاسع", "الصف العاشر", "الصف الحادي عشر", "الصف الثاني عشر", "السنة الأولى جامعي", "السنة الثانية جامعي", "السنة الثالثة جامعي", "السنة الرابعة جامعي"], required: true },
      { id: "educationLevel", label: "المرحلة الدراسية", type: "select", options: ["ابتدائي", "متوسط", "ثانوي", "جامعي"], required: true },
      { id: "city", label: "المدينة", type: "text", placeholder: "مثال: الرياض", required: false },
      { id: "schoolName", label: "اسم المدرسة / الجامعة", type: "text", placeholder: "اسم مؤسستك التعليمية", required: false },
    ],
  },
  {
    id: "teacher",
    label: "معلم",
    icon: BookOpen,
    desc: "أدر طلابك وتابع مشاركاتهم",
    color: "#1e40af",
    bg: "#eff6ff",
    border: "#bfdbfe",
    features: ["إدارة قائمة الطلاب", "متابعة المشاركات", "تقارير الأداء"],
    formFields: [
      { id: "fullName", label: "الاسم الكامل", type: "text", placeholder: "أدخل اسمك الكامل", required: true },
      { id: "email", label: "البريد الإلكتروني", type: "email", placeholder: "example@email.com", required: true },
      { id: "subject", label: "المادة التي تدرّسها", type: "select", options: ["اللغة العربية", "اللغة الإنجليزية", "الرياضيات", "العلوم", "الثقافة العامة", "التاريخ", "الجغرافيا", "التربية الإسلامية", "أخرى"], required: true },
      { id: "schoolName", label: "اسم المدرسة", type: "text", placeholder: "اسم المدرسة التي تعمل بها", required: true },
      { id: "city", label: "المدينة", type: "text", placeholder: "مثال: جدة", required: false },
      { id: "phone", label: "رقم الجوال", type: "tel", placeholder: "+966 5X XXX XXXX", required: false },
    ],
  },
  {
    id: "school",
    label: "مدرسة",
    icon: School,
    desc: "سجّل مدرستك وأدر المشاركات",
    color: "#7c3aed",
    bg: "#f5f3ff",
    border: "#ddd6fe",
    features: ["إحصاءات المدرسة", "إدارة المشاركين", "تقارير شاملة"],
    formFields: [
      { id: "schoolName", label: "اسم المدرسة", type: "text", placeholder: "الاسم الرسمي للمدرسة", required: true },
      { id: "principalName", label: "اسم مدير المدرسة", type: "text", placeholder: "اسم المدير المسؤول", required: true },
      { id: "email", label: "البريد الإلكتروني الرسمي", type: "email", placeholder: "school@example.edu.sa", required: true },
      { id: "phone", label: "رقم الهاتف", type: "tel", placeholder: "+966 1X XXX XXXX", required: true },
      { id: "educationType", label: "نوع المدرسة", type: "select", options: ["حكومية", "أهلية", "دولية"], required: true },
      { id: "city", label: "المدينة", type: "text", placeholder: "مثال: مكة المكرمة", required: true },
      { id: "region", label: "المنطقة / المحافظة", type: "text", placeholder: "مثال: منطقة مكة المكرمة", required: false },
      { id: "schoolCode", label: "رمز المدرسة (اختياري)", type: "text", placeholder: "الرمز الوزاري للمدرسة", required: false },
    ],
  },
  {
    id: "coordinator",
    label: "منسق",
    icon: Users,
    desc: "نسّق بين المدارس وأدر الفعاليات",
    color: "#b45309",
    bg: "#fffbeb",
    border: "#fde68a",
    features: ["إدارة المدارس التابعة", "تنسيق المسابقات", "تقارير المنطقة"],
    formFields: [
      { id: "fullName", label: "الاسم الكامل", type: "text", placeholder: "أدخل اسمك الكامل", required: true },
      { id: "email", label: "البريد الإلكتروني", type: "email", placeholder: "coordinator@example.sa", required: true },
      { id: "organization", label: "الجهة التابع لها", type: "text", placeholder: "مثال: إدارة التعليم بالرياض", required: true },
      { id: "region", label: "المنطقة / المحافظة", type: "text", placeholder: "مثال: منطقة الرياض", required: true },
      { id: "phone", label: "رقم الجوال", type: "tel", placeholder: "+966 5X XXX XXXX", required: true },
      { id: "jobTitle", label: "المسمى الوظيفي", type: "text", placeholder: "مثال: منسق تعليمي", required: false },
    ],
  },
  {
    id: "admin",
    label: "مشرف / مدير",
    icon: Shield,
    desc: "أدر المنصة بالكامل",
    color: "#be185d",
    bg: "#fdf2f8",
    border: "#fbcfe8",
    features: ["إدارة كاملة للمنصة", "إنشاء المسابقات", "التقارير الإحصائية"],
    formFields: [
      { id: "fullName", label: "الاسم الكامل", type: "text", placeholder: "أدخل اسمك الكامل", required: true },
      { id: "email", label: "البريد الإلكتروني المؤسسي", type: "email", placeholder: "admin@ilm-platform.sa", required: true },
      { id: "organization", label: "الجهة المشغّلة", type: "text", placeholder: "اسم المؤسسة أو الجهة", required: true },
      { id: "phone", label: "رقم الجوال", type: "tel", placeholder: "+966 5X XXX XXXX", required: true },
      { id: "adminCode", label: "رمز التفعيل الإداري", type: "password", placeholder: "أدخل رمز التفعيل الممنوح لك", required: true },
    ],
  },
];

export default function Register() {
  const [selectedRole, setSelectedRole] = useState<string | null>(null);
  const [step, setStep] = useState<'select' | 'form'>('select');
  const [formData, setFormData] = useState<Record<string, string>>({});

  const selectedRoleData = roles.find(r => r.id === selectedRole);

  const handleRoleSelect = (roleId: string) => {
    setSelectedRole(roleId);
    setFormData({});
  };

  const handleNext = () => {
    if (selectedRole) setStep('form');
  };

  const handleBack = () => {
    setStep('select');
  };

  const handleInputChange = (fieldId: string, value: string) => {
    setFormData(prev => ({ ...prev, [fieldId]: value }));
  };

  return (
    <div className="min-h-screen" style={{ background: "linear-gradient(135deg, oklch(0.97 0.02 145) 0%, oklch(0.95 0.03 145) 100%)" }}>
      <div className="container py-12">
        {/* Header */}
        <div className="text-center mb-10">
          <Link href="/" className="inline-flex items-center gap-2 mb-8">
            <div className="w-10 h-10 rounded-xl flex items-center justify-center"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              <Trophy className="w-5 h-5 text-white" />
            </div>
            <span className="text-2xl font-black" style={{ color: "oklch(0.35 0.15 145)", fontFamily: "'Cairo', sans-serif" }}>منصة علم</span>
          </Link>
          <h1 className="text-3xl md:text-4xl font-black text-gray-900 mb-3">إنشاء حساب جديد</h1>
          <p className="text-gray-500 text-lg">اختر نوع حسابك للبدء في رحلتك التعليمية التنافسية</p>

          {/* Step indicator */}
          <div className="flex items-center justify-center gap-3 mt-6">
            <div className={`flex items-center gap-2 text-sm font-semibold transition-colors ${step === 'select' ? 'text-green-700' : 'text-gray-400'}`}>
              <div className={`w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-colors ${step === 'select' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500'}`}>1</div>
              اختيار الدور
            </div>
            <div className="w-8 h-px bg-gray-200"></div>
            <div className={`flex items-center gap-2 text-sm font-semibold transition-colors ${step === 'form' ? 'text-green-700' : 'text-gray-400'}`}>
              <div className={`w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-colors ${step === 'form' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500'}`}>2</div>
              إكمال البيانات
            </div>
          </div>
        </div>

        {/* Step 1: Role Selection */}
        {step === 'select' && (
          <div className="max-w-5xl mx-auto">
            <h2 className="text-center text-lg font-bold text-gray-700 mb-6">ما هو دورك؟</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
              {roles.map((role) => (
                <button
                  key={role.id}
                  onClick={() => handleRoleSelect(role.id)}
                  className="text-right p-6 rounded-2xl border-2 transition-all duration-200 hover:shadow-lg relative"
                  style={{
                    background: selectedRole === role.id ? role.bg : "white",
                    borderColor: selectedRole === role.id ? role.color : "#e5e7eb",
                    transform: selectedRole === role.id ? "scale(1.02)" : "scale(1)",
                  }}>
                  {selectedRole === role.id && (
                    <div className="absolute top-3 left-3">
                      <CheckCircle className="w-5 h-5" style={{ color: role.color }} />
                    </div>
                  )}
                  <div className="flex items-center gap-3 mb-3">
                    <div className="w-12 h-12 rounded-xl flex items-center justify-center"
                      style={{ background: role.bg, border: `1px solid ${role.border}` }}>
                      <role.icon className="w-6 h-6" style={{ color: role.color }} />
                    </div>
                    <div>
                      <h3 className="font-black text-gray-800 text-lg">{role.label}</h3>
                      <p className="text-xs text-gray-500">{role.desc}</p>
                    </div>
                  </div>
                  <ul className="space-y-1.5 mt-4">
                    {role.features.map((feature) => (
                      <li key={feature} className="flex items-center gap-2 text-sm text-gray-600">
                        <span className="w-1.5 h-1.5 rounded-full flex-shrink-0" style={{ background: role.color }}></span>
                        {feature}
                      </li>
                    ))}
                  </ul>
                </button>
              ))}
            </div>

            <div className="max-w-md mx-auto">
              <Button
                onClick={handleNext}
                disabled={!selectedRole}
                size="lg"
                className="w-full text-white font-bold py-6 rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                style={{ background: selectedRole ? "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" : undefined }}>
                التالي: إكمال البيانات
                <ChevronLeft className="w-5 h-5" />
              </Button>

              <div className="mt-6 text-center border-t border-gray-100 pt-6">
                <p className="text-gray-500 text-sm">
                  لديك حساب بالفعل؟{" "}
                  <Link href="/login" className="font-semibold hover:underline" style={{ color: "oklch(0.42 0.15 145)" }}>
                    تسجيل الدخول
                  </Link>
                </p>
              </div>
            </div>
          </div>
        )}

        {/* Step 2: Role-specific Form */}
        {step === 'form' && selectedRoleData && (
          <div className="max-w-2xl mx-auto">
            <button onClick={handleBack} className="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6 transition-colors">
              <ChevronRight className="w-4 h-4" />
              العودة لاختيار الدور
            </button>

            <div className="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
              {/* Role Header */}
              <div className="flex items-center gap-4 mb-8 p-4 rounded-2xl"
                style={{ background: selectedRoleData.bg, border: `1px solid ${selectedRoleData.border}` }}>
                <div className="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                  style={{ background: "white", border: `2px solid ${selectedRoleData.border}` }}>
                  <selectedRoleData.icon className="w-7 h-7" style={{ color: selectedRoleData.color }} />
                </div>
                <div>
                  <h3 className="font-black text-gray-800 text-xl">تسجيل كـ {selectedRoleData.label}</h3>
                  <p className="text-sm text-gray-500">{selectedRoleData.desc}</p>
                </div>
              </div>

              {/* Form Fields */}
              <div className="space-y-5 mb-8">
                {selectedRoleData.formFields.map((field) => (
                  <div key={field.id}>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">
                      {field.label}
                      {field.required && <span className="text-red-500 mr-1">*</span>}
                    </label>
                    {field.type === 'select' ? (
                      <select
                        value={formData[field.id] ?? ''}
                        onChange={(e) => handleInputChange(field.id, e.target.value)}
                        className="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                        style={{ focusRingColor: selectedRoleData.color } as any}
                        required={field.required}>
                        <option value="">اختر...</option>
                        {field.options?.map(opt => (
                          <option key={opt} value={opt}>{opt}</option>
                        ))}
                      </select>
                    ) : (
                      <input
                        type={field.type}
                        value={formData[field.id] ?? ''}
                        onChange={(e) => handleInputChange(field.id, e.target.value)}
                        placeholder={field.placeholder}
                        required={field.required}
                        className="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                        style={{ direction: field.type === 'email' || field.type === 'tel' ? 'ltr' : 'rtl' }}
                      />
                    )}
                  </div>
                ))}
              </div>

              {/* Info Note */}
              <div className="flex items-start gap-3 p-4 rounded-xl mb-6" style={{ background: "oklch(0.97 0.02 145)", border: "1px solid oklch(0.88 0.05 145)" }}>
                <Info className="w-4 h-4 flex-shrink-0 mt-0.5" style={{ color: "oklch(0.42 0.15 145)" }} />
                <p className="text-xs text-gray-600">
                  سيتم إنشاء حسابك عبر نظام Manus الآمن. البيانات التي أدخلتها ستُستخدم لإعداد ملفك الشخصي على المنصة.
                </p>
              </div>

              {/* Submit Button */}
              <Button asChild size="lg" className="w-full text-white font-bold py-6 rounded-xl shadow-lg"
                style={{ background: `linear-gradient(135deg, ${selectedRoleData.color}, ${selectedRoleData.color}dd)` }}>
                <a href={getLoginUrl()} className="flex items-center justify-center gap-3">
                  <UserPlus className="w-5 h-5" />
                  إنشاء الحساب والمتابعة
                </a>
              </Button>
              <p className="text-xs text-gray-400 text-center mt-3">
                بالتسجيل، أنت توافق على{" "}
                <a href="#" className="underline">شروط الاستخدام</a>{" "}
                و{" "}
                <a href="#" className="underline">سياسة الخصوصية</a>
              </p>
            </div>

            <div className="mt-6 text-center">
              <p className="text-gray-500 text-sm">
                لديك حساب بالفعل؟{" "}
                <Link href="/login" className="font-semibold hover:underline" style={{ color: "oklch(0.42 0.15 145)" }}>
                  تسجيل الدخول
                </Link>
              </p>
            </div>
          </div>
        )}

        <div className="mt-8 text-center">
          <Link href="/" className="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 transition-colors">
            <ArrowRight className="w-4 h-4" />
            العودة للرئيسية
          </Link>
        </div>
      </div>
    </div>
  );
}
