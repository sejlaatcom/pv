import { Link } from "wouter";
import { Trophy, Mail, Phone, MapPin, Facebook, Twitter, Instagram, Youtube, Send } from "lucide-react";
import { useState } from "react";
import { trpc } from "@/lib/trpc";
import { toast } from "sonner";

export default function Footer() {
  const [email, setEmail] = useState("");
  const subscribeMutation = trpc.newsletter.subscribe.useMutation({
    onSuccess: () => { toast.success("تم الاشتراك في النشرة البريدية بنجاح!"); setEmail(""); },
    onError: () => toast.error("حدث خطأ، يرجى المحاولة مجدداً"),
  });

  return (
    <footer className="text-white" style={{ background: "linear-gradient(180deg, oklch(0.20 0.10 145) 0%, oklch(0.15 0.08 145) 100%)" }}>
      {/* Newsletter Section */}
      <div className="border-b border-white/10" style={{ background: "oklch(0.42 0.15 145 / 0.3)" }}>
        <div className="container py-10">
          <div className="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
              <h3 className="text-xl font-bold text-white mb-1">اشترك في نشرتنا البريدية</h3>
              <p className="text-white/70 text-sm">احصل على آخر أخبار المسابقات والفعاليات التعليمية</p>
            </div>
            <div className="flex gap-2 w-full md:w-auto">
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="أدخل بريدك الإلكتروني"
                className="flex-1 md:w-72 px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white placeholder:text-white/50 focus:outline-none focus:border-white/50 text-sm"
                style={{ direction: "ltr", textAlign: "left" }}
              />
              <button
                onClick={() => email && subscribeMutation.mutate({ email })}
                disabled={subscribeMutation.isPending || !email}
                className="px-5 py-2.5 rounded-lg font-semibold text-sm transition-all duration-150 disabled:opacity-50 flex items-center gap-2"
                style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))", color: "oklch(0.15 0 0)" }}>
                <Send className="w-4 h-4" />
                اشتراك
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Main Footer */}
      <div className="container py-14">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
          {/* Brand */}
          <div className="lg:col-span-1">
            <Link href="/" className="flex items-center gap-2 mb-4">
              <div className="w-10 h-10 rounded-xl flex items-center justify-center"
                style={{ background: "linear-gradient(135deg, oklch(0.55 0.15 145), oklch(0.42 0.15 145))" }}>
                <Trophy className="w-5 h-5 text-white" />
              </div>
              <div>
                <div className="text-xl font-black text-white" style={{ fontFamily: "'Cairo', sans-serif" }}>منصة علم</div>
                <div className="text-xs" style={{ color: "oklch(0.72 0.15 75)" }}>للمسابقات التعليمية</div>
              </div>
            </Link>
            <p className="text-white/60 text-sm leading-relaxed mb-5">
              منصة تعليمية تنافسية متكاملة تتيح للطلاب المشاركة في مسابقات ثقافية وعلمية عبر مراحل دراسية متعددة.
            </p>
            <div className="flex gap-3">
              {[
                { icon: Twitter, href: "#", label: "تويتر" },
                { icon: Facebook, href: "#", label: "فيسبوك" },
                { icon: Instagram, href: "#", label: "انستغرام" },
                { icon: Youtube, href: "#", label: "يوتيوب" },
              ].map(({ icon: Icon, href, label }) => (
                <a key={label} href={href} aria-label={label}
                  className="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-150 hover:scale-110"
                  style={{ background: "oklch(1 0 0 / 0.08)" }}>
                  <Icon className="w-4 h-4 text-white/70 hover:text-white" />
                </a>
              ))}
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-white font-bold mb-5 text-base">روابط سريعة</h4>
            <ul className="space-y-3">
              {[
                { href: "/competitions", label: "المسابقات" },
                { href: "/training", label: "التدريب والاستعداد" },
                { href: "/prizes", label: "الجوائز" },
                { href: "/champions", label: "أبطال المسابقات" },
                { href: "/partners", label: "الشركاء" },
                { href: "/about", label: "عن المنصة" },
              ].map((link) => (
                <li key={link.href}>
                  <Link href={link.href}
                    className="text-white/60 hover:text-white text-sm transition-colors duration-150 flex items-center gap-2">
                    <span className="w-1.5 h-1.5 rounded-full flex-shrink-0" style={{ background: "oklch(0.72 0.15 75)" }}></span>
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Support */}
          <div>
            <h4 className="text-white font-bold mb-5 text-base">الدعم والمساعدة</h4>
            <ul className="space-y-3">
              {[
                { href: "/faq", label: "الأسئلة الشائعة" },
                { href: "/contact", label: "تواصل معنا" },
                { href: "/register", label: "إنشاء حساب" },
                { href: "/login", label: "تسجيل الدخول" },
              ].map((link) => (
                <li key={link.href}>
                  <Link href={link.href}
                    className="text-white/60 hover:text-white text-sm transition-colors duration-150 flex items-center gap-2">
                    <span className="w-1.5 h-1.5 rounded-full flex-shrink-0" style={{ background: "oklch(0.72 0.15 75)" }}></span>
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="text-white font-bold mb-5 text-base">تواصل معنا</h4>
            <ul className="space-y-4">
              <li className="flex items-start gap-3">
                <Mail className="w-4 h-4 mt-0.5 flex-shrink-0" style={{ color: "oklch(0.72 0.15 75)" }} />
                <span className="text-white/60 text-sm" style={{ direction: "ltr" }}>info@ilm-platform.sa</span>
              </li>
              <li className="flex items-start gap-3">
                <Phone className="w-4 h-4 mt-0.5 flex-shrink-0" style={{ color: "oklch(0.72 0.15 75)" }} />
                <span className="text-white/60 text-sm" style={{ direction: "ltr" }}>+966 11 000 0000</span>
              </li>
              <li className="flex items-start gap-3">
                <MapPin className="w-4 h-4 mt-0.5 flex-shrink-0" style={{ color: "oklch(0.72 0.15 75)" }} />
                <span className="text-white/60 text-sm">المملكة العربية السعودية، الرياض</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom Bar */}
      <div className="border-t border-white/10">
        <div className="container py-5">
          <div className="flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-white/40">
            <p>© {new Date().getFullYear()} منصة علم للمسابقات التعليمية. جميع الحقوق محفوظة.</p>
            <div className="flex gap-5">
              <Link href="/privacy" className="hover:text-white/70 transition-colors">سياسة الخصوصية</Link>
              <Link href="/terms" className="hover:text-white/70 transition-colors">الشروط والأحكام</Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
