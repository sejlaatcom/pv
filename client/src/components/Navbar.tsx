import { useState, useEffect } from "react";
import { Link, useLocation } from "wouter";
import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { getLoginUrl } from "@/const";
import {
  Menu, X, Trophy, ChevronDown, User, LogOut, LayoutDashboard,
  BookOpen, Award, Users, HelpCircle, Phone, Star, GraduationCap
} from "lucide-react";
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem,
  DropdownMenuSeparator, DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import { trpc } from "@/lib/trpc";
import { toast } from "sonner";

const navLinks = [
  { href: "/", label: "الرئيسية" },
  { href: "/competitions", label: "المسابقات" },
  { href: "/training", label: "التدريب" },
  { href: "/prizes", label: "الجوائز" },
  { href: "/champions", label: "أبطال المسابقات" },
  { href: "/about", label: "عن المنصة" },
  { href: "/contact", label: "تواصل معنا" },
];

const roleLabels: Record<string, string> = {
  student: "طالب",
  teacher: "معلم",
  school: "مدرسة",
  coordinator: "منسق",
  admin: "مدير",
  user: "مستخدم",
};

const roleDashboards: Record<string, string> = {
  student: "/dashboard/student",
  teacher: "/dashboard/teacher",
  school: "/dashboard/school",
  coordinator: "/dashboard/coordinator",
  admin: "/dashboard/admin",
  user: "/dashboard/student",
};

export default function Navbar() {
  const [location] = useLocation();
  const { user, isAuthenticated, logout } = useAuth();
  const [mobileOpen, setMobileOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  const logoutMutation = trpc.auth.logout.useMutation({
    onSuccess: () => { logout(); toast.success("تم تسجيل الخروج بنجاح"); },
  });

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  useEffect(() => { setMobileOpen(false); }, [location]);

  const dashboardPath = user?.role ? (roleDashboards[user.role] ?? "/dashboard/student") : "/dashboard/student";

  return (
    <nav className={`fixed top-0 right-0 left-0 z-50 transition-all duration-300 ${scrolled ? "bg-white/95 backdrop-blur-md shadow-md" : "bg-white/90 backdrop-blur-sm shadow-sm"}`}>
      <div className="container">
        <div className="flex items-center justify-between h-16 lg:h-18">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2 group flex-shrink-0">
            <div className="w-10 h-10 rounded-xl flex items-center justify-center shadow-md transition-transform group-hover:scale-105"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              <Trophy className="w-5 h-5 text-white" />
            </div>
            <div className="flex flex-col leading-tight">
              <span className="text-xl font-black" style={{ color: "oklch(0.35 0.15 145)", fontFamily: "'Cairo', sans-serif" }}>منصة المسابقات</span>
              <span className="text-xs font-medium" style={{ color: "oklch(0.72 0.15 75)", lineHeight: 1 }}>للمسابقات التعليمية</span>
            </div>
          </Link>

          {/* Desktop Nav */}
          <div className="hidden lg:flex items-center gap-1">
            {navLinks.map((link) => (
              <Link key={link.href} href={link.href}
                className={`px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 relative ${
                  location === link.href
                    ? "text-green-700 bg-green-50 font-semibold"
                    : "text-gray-600 hover:text-green-700 hover:bg-green-50"
                }`}>
                {link.label}
              </Link>
            ))}
          </div>

          {/* Auth Buttons */}
          <div className="hidden lg:flex items-center gap-2">
            {isAuthenticated && user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="outline" size="sm" className="gap-2 border-green-200 hover:border-green-400 hover:bg-green-50">
                    <div className="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                      style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                      {user.name?.charAt(0) ?? "م"}
                    </div>
                    <span className="max-w-24 truncate text-sm">{user.name ?? "المستخدم"}</span>
                    <span className="badge-primary text-xs">{roleLabels[user.role ?? "user"]}</span>
                    <ChevronDown className="w-3 h-3 opacity-60" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-52">
                  <DropdownMenuItem asChild>
                    <Link href={dashboardPath} className="flex items-center gap-2 cursor-pointer">
                      <LayoutDashboard className="w-4 h-4" />
                      لوحة التحكم
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem onClick={() => logoutMutation.mutate()} className="text-red-600 focus:text-red-600 cursor-pointer">
                    <LogOut className="w-4 h-4 ml-2" />
                    تسجيل الخروج
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <>
                <Button variant="outline" size="sm" asChild className="border-green-200 hover:border-green-400 hover:bg-green-50 text-green-700">
                  <Link href="/login">تسجيل الدخول</Link>
                </Button>
                <Button size="sm" asChild className="text-white shadow-md"
                  style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                  <Link href="/register">إنشاء حساب</Link>
                </Button>
              </>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setMobileOpen(!mobileOpen)}
            className="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
            aria-label="القائمة">
            {mobileOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      {/* Mobile Menu */}
      {mobileOpen && (
        <div className="lg:hidden bg-white border-t border-gray-100 shadow-lg animate-fade-in">
          <div className="container py-4 space-y-1">
            {navLinks.map((link) => (
              <Link key={link.href} href={link.href}
                className={`block px-4 py-3 rounded-lg text-sm font-medium transition-colors ${
                  location === link.href
                    ? "bg-green-50 text-green-700 font-semibold"
                    : "text-gray-600 hover:bg-gray-50 hover:text-green-700"
                }`}>
                {link.label}
              </Link>
            ))}
            <div className="pt-3 border-t border-gray-100 flex flex-col gap-2">
              {isAuthenticated && user ? (
                <>
                  <Link href={dashboardPath}
                    className="flex items-center gap-2 px-4 py-3 rounded-lg text-sm font-medium text-green-700 bg-green-50">
                    <LayoutDashboard className="w-4 h-4" />
                    لوحة التحكم
                  </Link>
                  <button onClick={() => logoutMutation.mutate()}
                    className="flex items-center gap-2 px-4 py-3 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 w-full text-right">
                    <LogOut className="w-4 h-4" />
                    تسجيل الخروج
                  </button>
                </>
              ) : (
                <>
                  <Button variant="outline" asChild className="w-full border-green-200 text-green-700">
                    <Link href="/login">تسجيل الدخول</Link>
                  </Button>
                  <Button asChild className="w-full text-white"
                    style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
                    <Link href="/register">إنشاء حساب مجاني</Link>
                  </Button>
                </>
              )}
            </div>
          </div>
        </div>
      )}
    </nav>
  );
}
