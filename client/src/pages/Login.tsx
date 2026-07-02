import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import { Trophy, LogIn, ArrowRight } from "lucide-react";

export default function Login() {
  return (
    <div className="min-h-screen flex" style={{ background: "linear-gradient(135deg, oklch(0.97 0.02 145) 0%, oklch(0.95 0.03 145) 100%)" }}>
      {/* Left Panel - Decorative */}
      <div className="hidden lg:flex lg:w-1/2 items-center justify-center relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145) 0%, oklch(0.35 0.14 145) 100%)" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 30% 40%, oklch(0.72 0.15 75) 0%, transparent 50%), radial-gradient(circle at 70% 70%, oklch(0.55 0.15 145) 0%, transparent 40%)" }}>
        </div>
        <div className="relative z-10 text-center text-white p-12">
          <div className="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl"
            style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
            <Trophy className="w-12 h-12 text-white" />
          </div>
          <h2 className="text-4xl font-black mb-4" style={{ fontFamily: "'Cairo', sans-serif" }}>منصة علم</h2>
          <p className="text-white/70 text-lg mb-8">للمسابقات الثقافية والتعليمية</p>
          <div className="space-y-4 text-right">
            {["مسابقات في مختلف التخصصات", "شهادات معتمدة ومعترف بها", "جوائز قيّمة ومميزة", "لجميع المراحل الدراسية"].map((item) => (
              <div key={item} className="flex items-center gap-3 text-white/80">
                <div className="w-2 h-2 rounded-full flex-shrink-0" style={{ background: "oklch(0.72 0.15 75)" }}></div>
                <span>{item}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Right Panel - Login Form */}
      <div className="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div className="w-full max-w-md">
          {/* Mobile Logo */}
          <div className="lg:hidden text-center mb-8">
            <Link href="/" className="inline-flex items-center gap-2">
              <div className="w-10 h-10 rounded-xl flex items-center justify-center"
                style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                <Trophy className="w-5 h-5 text-white" />
              </div>
              <span className="text-2xl font-black" style={{ color: "oklch(0.35 0.15 145)", fontFamily: "'Cairo', sans-serif" }}>منصة علم</span>
            </Link>
          </div>

          <div className="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <div className="mb-8">
              <h1 className="text-2xl font-black text-gray-900 mb-2">مرحباً بعودتك!</h1>
              <p className="text-gray-500">سجّل دخولك للوصول إلى لوحة التحكم والمسابقات</p>
            </div>

            {/* OAuth Login */}
            <div className="space-y-4">
              <Button asChild size="lg" className="w-full text-white font-bold py-6 rounded-xl shadow-lg hover:shadow-xl transition-all"
                style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                <a href={getLoginUrl()} className="flex items-center justify-center gap-3">
                  <LogIn className="w-5 h-5" />
                  تسجيل الدخول بحساب Manus
                </a>
              </Button>

              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <div className="w-full border-t border-gray-200"></div>
                </div>
                <div className="relative flex justify-center text-xs">
                  <span className="bg-white px-3 text-gray-400">أو</span>
                </div>
              </div>

              {/* Demo Info */}
              <div className="p-4 rounded-xl text-sm" style={{ background: "oklch(0.97 0.02 145)", border: "1px solid oklch(0.88 0.05 145)" }}>
                <p className="font-semibold text-gray-700 mb-2">نموذج تجريبي</p>
                <p className="text-gray-500 text-xs leading-relaxed">
                  هذا نموذج أولي تجريبي. يمكنك تسجيل الدخول بحساب Manus للوصول إلى لوحة التحكم وتجربة جميع الميزات.
                </p>
              </div>
            </div>

            <div className="mt-6 text-center">
              <p className="text-gray-500 text-sm">
                ليس لديك حساب؟{" "}
                <Link href="/register" className="font-semibold hover:underline" style={{ color: "oklch(0.42 0.15 145)" }}>
                  إنشاء حساب جديد
                </Link>
              </p>
            </div>
          </div>

          <div className="mt-6 text-center">
            <Link href="/" className="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 transition-colors">
              <ArrowRight className="w-4 h-4" />
              العودة للرئيسية
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
