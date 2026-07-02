import { useState } from "react";
import { Link, useLocation } from "wouter";
import { useAuth } from "@/_core/hooks/useAuth";
import { trpc } from "@/lib/trpc";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";
import {
  Trophy, Menu, X, LogOut, Home, Bell, ChevronDown, User
} from "lucide-react";
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem,
  DropdownMenuSeparator, DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";

interface NavItem {
  href: string;
  label: string;
  icon: React.ComponentType<{ className?: string }>;
}

interface DashboardShellProps {
  children: React.ReactNode;
  navItems: NavItem[];
  title: string;
  roleLabel: string;
  roleColor: string;
}

export default function DashboardShell({ children, navItems, title, roleLabel, roleColor }: DashboardShellProps) {
  const [location] = useLocation();
  const { user, logout } = useAuth();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const logoutMutation = trpc.auth.logout.useMutation({
    onSuccess: () => { logout(); toast.success("تم تسجيل الخروج بنجاح"); },
  });

  return (
    <div className="min-h-screen flex" style={{ background: "#f8fafc" }}>
      {/* Sidebar */}
      <aside className={`fixed inset-y-0 right-0 z-50 w-64 bg-white shadow-xl flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:shadow-none lg:border-l border-gray-100 ${sidebarOpen ? "translate-x-0" : "translate-x-full lg:translate-x-0"}`}>
        {/* Logo */}
        <div className="p-5 border-b border-gray-100">
          <Link href="/" className="flex items-center gap-2">
            <div className="w-9 h-9 rounded-xl flex items-center justify-center"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              <Trophy className="w-4 h-4 text-white" />
            </div>
            <div>
              <div className="text-base font-black" style={{ color: "oklch(0.35 0.15 145)", fontFamily: "'Cairo', sans-serif" }}>منصة علم</div>
              <div className="text-xs" style={{ color: roleColor }}>{roleLabel}</div>
            </div>
          </Link>
        </div>

        {/* Nav Items */}
        <nav className="flex-1 p-4 space-y-1 overflow-y-auto">
          {navItems.map((item) => {
            const isActive = location === item.href;
            return (
              <Link key={item.href} href={item.href}
                onClick={() => setSidebarOpen(false)}
                className={`sidebar-item ${isActive ? "active" : ""}`}>
                <item.icon className="w-4 h-4 flex-shrink-0" />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>

        {/* User Info */}
        <div className="p-4 border-t border-gray-100">
          <div className="flex items-center gap-3 p-3 rounded-xl" style={{ background: "oklch(0.97 0.01 145)" }}>
            <div className="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
              style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
              {user?.name?.charAt(0) ?? "م"}
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-gray-800 truncate">{user?.name ?? "المستخدم"}</p>
              <p className="text-xs truncate" style={{ color: roleColor }}>{roleLabel}</p>
            </div>
            <button onClick={() => logoutMutation.mutate()} className="text-gray-400 hover:text-red-500 transition-colors" title="تسجيل الخروج">
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>
      </aside>

      {/* Overlay */}
      {sidebarOpen && (
        <div className="fixed inset-0 z-40 bg-black/40 lg:hidden" onClick={() => setSidebarOpen(false)} />
      )}

      {/* Main Content */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Top Bar */}
        <header className="bg-white border-b border-gray-100 px-4 lg:px-6 h-16 flex items-center justify-between sticky top-0 z-30 shadow-sm">
          <div className="flex items-center gap-3">
            <button onClick={() => setSidebarOpen(true)} className="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
              <Menu className="w-5 h-5" />
            </button>
            <h1 className="text-lg font-bold text-gray-800">{title}</h1>
          </div>
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="sm" asChild className="text-gray-500 hover:text-gray-700">
              <Link href="/" className="flex items-center gap-1.5">
                <Home className="w-4 h-4" />
                <span className="hidden sm:inline text-sm">الرئيسية</span>
              </Link>
            </Button>
          </div>
        </header>

        {/* Page Content */}
        <main className="flex-1 p-4 lg:p-6 overflow-auto">
          {children}
        </main>
      </div>
    </div>
  );
}
