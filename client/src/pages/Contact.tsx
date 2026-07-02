import { useState } from "react";
import { trpc } from "@/lib/trpc";
import { toast } from "sonner";
import { Mail, Phone, MapPin, Clock, Send, Bell, CheckCircle } from "lucide-react";
import { Button } from "@/components/ui/button";

export default function Contact() {
  const [form, setForm] = useState({ name: "", email: "", phone: "", subject: "", message: "" });
  const [newsletter, setNewsletter] = useState({ email: "", name: "" });
  const [newsletterSuccess, setNewsletterSuccess] = useState(false);

  const contactMutation = trpc.contact.send.useMutation({
    onSuccess: () => {
      toast.success("تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.");
      setForm({ name: "", email: "", phone: "", subject: "", message: "" });
    },
    onError: (err) => toast.error(err.message ?? "حدث خطأ أثناء الإرسال"),
  });

  const newsletterMutation = trpc.newsletter.subscribe.useMutation({
    onSuccess: () => {
      setNewsletterSuccess(true);
      toast.success("تم الاشتراك في النشرة البريدية بنجاح!");
    },
    onError: (err) => toast.error(err.message ?? "حدث خطأ"),
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.name || !form.email || !form.message) {
      toast.error("يرجى تعبئة جميع الحقول المطلوبة");
      return;
    }
    contactMutation.mutate(form);
  };

  const handleNewsletter = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newsletter.email) { toast.error("يرجى إدخال البريد الإلكتروني"); return; }
    newsletterMutation.mutate(newsletter);
  };

  const contactInfo = [
    { icon: Mail, label: "البريد الإلكتروني", value: "info@ilm-platform.sa", color: "#166534", bg: "#f0fdf4" },
    { icon: Phone, label: "الهاتف", value: "+966 11 000 0000", color: "#1e40af", bg: "#eff6ff" },
    { icon: MapPin, label: "العنوان", value: "الرياض، المملكة العربية السعودية", color: "#7c3aed", bg: "#f5f3ff" },
    { icon: Clock, label: "أوقات العمل", value: "الأحد - الخميس: 8ص - 5م", color: "#b45309", bg: "#fffbeb" },
  ];

  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 40% 50%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <Mail className="w-4 h-4" />
            تواصل معنا
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">نحن هنا لمساعدتك</h1>
          <p className="text-white/75 text-lg max-w-2xl mx-auto">
            تواصل معنا لأي استفسار أو اقتراح، وسيرد عليك فريقنا في أقرب وقت
          </p>
        </div>
      </div>

      <div className="container py-16">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
          {/* Contact Info */}
          <div className="space-y-5">
            <h2 className="text-2xl font-black text-gray-800 mb-6">معلومات التواصل</h2>
            {contactInfo.map((info) => (
              <div key={info.label} className="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:shadow-sm transition-shadow">
                <div className="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style={{ background: info.bg }}>
                  <info.icon className="w-5 h-5" style={{ color: info.color }} />
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-0.5">{info.label}</p>
                  <p className="font-semibold text-gray-700 text-sm">{info.value}</p>
                </div>
              </div>
            ))}

            {/* Social */}
            <div className="p-5 rounded-2xl" style={{ background: "oklch(0.97 0.02 145)" }}>
              <h3 className="font-bold text-gray-700 mb-3 text-sm">تابعنا على</h3>
              <div className="flex gap-3">
                {["تويتر", "إنستغرام", "يوتيوب", "لينكدإن"].map((platform) => (
                  <button key={platform}
                    onClick={() => toast.info("قريباً")}
                    className="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-600 hover:border-green-300 hover:text-green-700 transition-colors">
                    {platform}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Contact Form */}
          <div className="lg:col-span-2">
            <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
              <h2 className="text-2xl font-black text-gray-800 mb-6">أرسل لنا رسالة</h2>
              <form onSubmit={handleSubmit} className="space-y-5">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">الاسم الكامل *</label>
                    <input
                      type="text"
                      value={form.name}
                      onChange={(e) => setForm({ ...form, name: e.target.value })}
                      placeholder="أدخل اسمك الكامل"
                      className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-400 text-sm transition-colors"
                      required
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني *</label>
                    <input
                      type="email"
                      value={form.email}
                      onChange={(e) => setForm({ ...form, email: e.target.value })}
                      placeholder="example@email.com"
                      className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-400 text-sm transition-colors"
                      style={{ direction: "ltr" }}
                      required
                    />
                  </div>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">رقم الهاتف</label>
                    <input
                      type="tel"
                      value={form.phone}
                      onChange={(e) => setForm({ ...form, phone: e.target.value })}
                      placeholder="+966 5X XXX XXXX"
                      className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-400 text-sm transition-colors"
                      style={{ direction: "ltr" }}
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">الموضوع</label>
                    <select
                      value={form.subject}
                      onChange={(e) => setForm({ ...form, subject: e.target.value })}
                      className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-400 text-sm transition-colors bg-white">
                      <option value="">اختر الموضوع</option>
                      <option value="general">استفسار عام</option>
                      <option value="competition">استفسار عن مسابقة</option>
                      <option value="technical">مشكلة تقنية</option>
                      <option value="partnership">طلب شراكة</option>
                      <option value="other">أخرى</option>
                    </select>
                  </div>
                </div>
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">الرسالة *</label>
                  <textarea
                    value={form.message}
                    onChange={(e) => setForm({ ...form, message: e.target.value })}
                    placeholder="اكتب رسالتك هنا..."
                    rows={5}
                    className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-400 text-sm transition-colors resize-none"
                    required
                  />
                </div>
                <Button type="submit" disabled={contactMutation.isPending} size="lg"
                  className="w-full text-white font-bold py-6 rounded-xl shadow-lg"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  <Send className="w-4 h-4 ml-2" />
                  {contactMutation.isPending ? "جاري الإرسال..." : "إرسال الرسالة"}
                </Button>
              </form>
            </div>
          </div>
        </div>

        {/* Newsletter Section */}
        <div className="mt-16 p-10 rounded-3xl text-white"
          style={{ background: "linear-gradient(135deg, oklch(0.35 0.14 145), oklch(0.25 0.12 145))" }}>
          <div className="max-w-2xl mx-auto text-center">
            <div className="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5"
              style={{ background: "oklch(1 0 0 / 0.1)" }}>
              <Bell className="w-7 h-7 text-white" />
            </div>
            <h2 className="text-3xl font-black mb-3">اشترك في النشرة البريدية</h2>
            <p className="text-white/75 mb-8">
              احصل على آخر أخبار المسابقات والفعاليات التعليمية مباشرة في بريدك الإلكتروني
            </p>
            {newsletterSuccess ? (
              <div className="flex items-center justify-center gap-3 p-4 rounded-2xl" style={{ background: "oklch(1 0 0 / 0.1)" }}>
                <CheckCircle className="w-6 h-6 text-green-300" />
                <p className="font-semibold">شكراً! تم اشتراكك بنجاح في النشرة البريدية</p>
              </div>
            ) : (
              <form onSubmit={handleNewsletter} className="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                <input
                  type="text"
                  value={newsletter.name}
                  onChange={(e) => setNewsletter({ ...newsletter, name: e.target.value })}
                  placeholder="اسمك"
                  className="flex-1 px-4 py-3 rounded-xl text-gray-800 text-sm focus:outline-none"
                />
                <input
                  type="email"
                  value={newsletter.email}
                  onChange={(e) => setNewsletter({ ...newsletter, email: e.target.value })}
                  placeholder="بريدك الإلكتروني"
                  className="flex-1 px-4 py-3 rounded-xl text-gray-800 text-sm focus:outline-none"
                  style={{ direction: "ltr" }}
                  required
                />
                <Button type="submit" disabled={newsletterMutation.isPending}
                  className="text-gray-900 font-bold px-6 py-3 rounded-xl whitespace-nowrap"
                  style={{ background: "linear-gradient(135deg, oklch(0.72 0.15 75), oklch(0.65 0.17 70))" }}>
                  {newsletterMutation.isPending ? "..." : "اشترك"}
                </Button>
              </form>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
