import { Trophy, Medal, Star, Crown, Award } from "lucide-react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

const champions = [
  { name: "أحمد محمد العمري", school: "مدرسة الرياض النموذجية", competition: "مسابقة اللغة العربية 2024", rank: 1, subject: "اللغة العربية", level: "ثانوي", region: "الرياض" },
  { name: "سارة خالد الزهراني", school: "مدرسة الأمل للبنات", competition: "مسابقة الرياضيات 2024", rank: 1, subject: "الرياضيات", level: "متوسط", region: "جدة" },
  { name: "عمر عبدالله الشمري", school: "ثانوية الفيصل", competition: "مسابقة العلوم 2024", rank: 1, subject: "العلوم", level: "ثانوي", region: "الدمام" },
  { name: "نورة فهد الحربي", school: "مدرسة التميز", competition: "مسابقة الثقافة العامة 2024", rank: 2, subject: "الثقافة العامة", level: "جامعي", region: "مكة المكرمة" },
  { name: "يوسف إبراهيم القحطاني", school: "مدرسة الإبداع", competition: "مسابقة اللغة الإنجليزية 2024", rank: 2, subject: "الإنجليزية", level: "ثانوي", region: "الرياض" },
  { name: "هند سعد المطيري", school: "مدرسة النور", competition: "مسابقة القراءة 2024", rank: 3, subject: "القراءة", level: "ابتدائي", region: "الطائف" },
];

const rankColors = {
  1: { bg: "from-yellow-400 to-amber-500", badge: "bg-amber-100 text-amber-700", icon: Crown },
  2: { bg: "from-gray-300 to-gray-400", badge: "bg-gray-100 text-gray-600", icon: Medal },
  3: { bg: "from-amber-600 to-amber-700", badge: "bg-orange-100 text-orange-700", icon: Award },
};

const subjectColors: Record<string, string> = {
  "اللغة العربية": "#166534", "الرياضيات": "#7c3aed", "العلوم": "#0891b2",
  "الثقافة العامة": "#b45309", "الإنجليزية": "#1e40af", "القراءة": "#be185d",
};

export default function Champions() {
  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="py-20 text-white relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, oklch(0.25 0.12 145), oklch(0.35 0.14 145))" }}>
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: "radial-gradient(circle at 30% 60%, oklch(0.72 0.15 75) 0%, transparent 50%)" }}></div>
        <div className="container relative z-10 text-center">
          <div className="badge-gold inline-flex mb-6">
            <Trophy className="w-4 h-4" />
            أبطال المسابقات
          </div>
          <h1 className="text-4xl md:text-5xl font-black mb-6">قاعة الأبطال</h1>
          <p className="text-white/75 text-lg max-w-2xl mx-auto">
            نفخر بتكريم المتميزين والفائزين في مسابقاتنا التعليمية
          </p>
        </div>
      </div>

      <div className="container py-16">
        {/* Top 3 Podium */}
        <div className="text-center mb-12">
          <h2 className="text-3xl font-black text-gray-800 mb-3">أبطال 2024</h2>
          <p className="text-gray-500">المتميزون الحاصلون على المراكز الأولى</p>
        </div>

        {/* Champions Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
          {champions.map((champion, i) => {
            const rankInfo = rankColors[champion.rank as 1 | 2 | 3] ?? rankColors[3];
            const RankIcon = rankInfo.icon;
            return (
              <div key={i} className="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all overflow-hidden">
                {/* Card Header */}
                <div className={`h-24 bg-gradient-to-br ${rankInfo.bg} flex items-center justify-center relative`}>
                  <div className="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                    <RankIcon className="w-8 h-8 text-white" />
                  </div>
                  <div className="absolute top-3 right-3">
                    <span className={`px-2 py-1 rounded-full text-xs font-black ${rankInfo.badge}`}>
                      المركز {champion.rank}
                    </span>
                  </div>
                </div>

                {/* Card Body */}
                <div className="p-5">
                  {/* Avatar */}
                  <div className="flex items-center gap-3 mb-4">
                    <div className="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-lg flex-shrink-0"
                      style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                      {champion.name.charAt(0)}
                    </div>
                    <div>
                      <h3 className="font-black text-gray-800">{champion.name}</h3>
                      <p className="text-xs text-gray-500">{champion.school}</p>
                    </div>
                  </div>

                  <div className="space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-gray-400">المسابقة</span>
                      <span className="text-xs font-semibold text-gray-700 text-left">{champion.competition}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-gray-400">التخصص</span>
                      <span className="text-xs font-semibold px-2 py-0.5 rounded-full"
                        style={{ background: `${subjectColors[champion.subject]}15`, color: subjectColors[champion.subject] }}>
                        {champion.subject}
                      </span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-gray-400">المرحلة</span>
                      <span className="text-xs font-semibold text-gray-700">{champion.level}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-gray-400">المنطقة</span>
                      <span className="text-xs font-semibold text-gray-700">{champion.region}</span>
                    </div>
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        {/* Motivational Section */}
        <div className="p-10 rounded-3xl text-center" style={{ background: "oklch(0.97 0.02 145)" }}>
          <Star className="w-14 h-14 mx-auto mb-4" style={{ color: "oklch(0.72 0.15 75)" }} />
          <h2 className="text-2xl font-black text-gray-800 mb-3">كن البطل القادم!</h2>
          <p className="text-gray-500 mb-6 max-w-lg mx-auto">
            انضم إلى مجتمع المتميزين وحقق حلمك في الوصول إلى قاعة الأبطال
          </p>
          <div className="flex flex-wrap justify-center gap-4">
            <Button asChild size="lg" className="text-white font-bold"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              <Link href="/competitions">شارك الآن</Link>
            </Button>
            <Button asChild size="lg" variant="outline" className="border-green-200 text-green-700">
              <Link href="/training">التدريب والاستعداد</Link>
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}
