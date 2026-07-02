import { Toaster } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import NotFound from "@/pages/NotFound";
import { Route, Switch } from "wouter";
import ErrorBoundary from "./components/ErrorBoundary";
import { ThemeProvider } from "./contexts/ThemeContext";
import Navbar from "./components/Navbar";
import Footer from "./components/Footer";
import Home from "./pages/Home";
import { lazy, Suspense } from "react";

// Lazy load pages
const Competitions = lazy(() => import("./pages/Competitions"));
const CompetitionDetail = lazy(() => import("./pages/CompetitionDetail"));
const About = lazy(() => import("./pages/About"));
const Training = lazy(() => import("./pages/Training"));
const Prizes = lazy(() => import("./pages/Prizes"));
const Champions = lazy(() => import("./pages/Champions"));
const Partners = lazy(() => import("./pages/Partners"));
const FAQ = lazy(() => import("./pages/FAQ"));
const Contact = lazy(() => import("./pages/Contact"));
const Login = lazy(() => import("./pages/Login"));
const Register = lazy(() => import("./pages/Register"));

// Dashboards
const StudentDashboard = lazy(() => import("./pages/dashboards/StudentDashboard"));
const TeacherDashboard = lazy(() => import("./pages/dashboards/TeacherDashboard"));
const SchoolDashboard = lazy(() => import("./pages/dashboards/SchoolDashboard"));
const CoordinatorDashboard = lazy(() => import("./pages/dashboards/CoordinatorDashboard"));
const AdminDashboard = lazy(() => import("./pages/dashboards/AdminDashboard"));

function PageLoader() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-white">
      <div className="text-center">
        <div className="w-12 h-12 rounded-xl mx-auto mb-4 flex items-center justify-center animate-pulse"
          style={{ background: "linear-gradient(135deg, oklch(0.42 0.15 145), oklch(0.35 0.14 145))" }}>
          <span className="text-white text-xl font-black">ع</span>
        </div>
        <p className="text-gray-400 text-sm">جاري التحميل...</p>
      </div>
    </div>
  );
}

// Layout wrapper for public pages
function PublicLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      <main className="flex-1 pt-16">
        {children}
      </main>
      <Footer />
    </div>
  );
}

// Layout for dashboard pages (no footer)
function DashboardPageLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-gray-50">
      {children}
    </div>
  );
}

function Router() {
  return (
    <Switch>
      {/* Public Pages */}
      <Route path="/">
        <PublicLayout><Home /></PublicLayout>
      </Route>
      <Route path="/competitions">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><Competitions /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/competitions/:slug">
        {(params) => (
          <PublicLayout>
            <Suspense fallback={<PageLoader />}><CompetitionDetail slug={params.slug} /></Suspense>
          </PublicLayout>
        )}
      </Route>
      <Route path="/about">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><About /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/training">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><Training /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/prizes">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><Prizes /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/champions">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><Champions /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/partners">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><Partners /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/faq">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><FAQ /></Suspense>
        </PublicLayout>
      </Route>
      <Route path="/contact">
        <PublicLayout>
          <Suspense fallback={<PageLoader />}><Contact /></Suspense>
        </PublicLayout>
      </Route>

      {/* Auth Pages */}
      <Route path="/login">
        <Suspense fallback={<PageLoader />}><Login /></Suspense>
      </Route>
      <Route path="/register">
        <Suspense fallback={<PageLoader />}><Register /></Suspense>
      </Route>

      {/* Dashboard Pages */}
      <Route path="/dashboard/student">
        <DashboardPageLayout>
          <Suspense fallback={<PageLoader />}><StudentDashboard /></Suspense>
        </DashboardPageLayout>
      </Route>
      <Route path="/dashboard/teacher">
        <DashboardPageLayout>
          <Suspense fallback={<PageLoader />}><TeacherDashboard /></Suspense>
        </DashboardPageLayout>
      </Route>
      <Route path="/dashboard/school">
        <DashboardPageLayout>
          <Suspense fallback={<PageLoader />}><SchoolDashboard /></Suspense>
        </DashboardPageLayout>
      </Route>
      <Route path="/dashboard/coordinator">
        <DashboardPageLayout>
          <Suspense fallback={<PageLoader />}><CoordinatorDashboard /></Suspense>
        </DashboardPageLayout>
      </Route>
      <Route path="/dashboard/admin">
        <DashboardPageLayout>
          <Suspense fallback={<PageLoader />}><AdminDashboard /></Suspense>
        </DashboardPageLayout>
      </Route>

      {/* Fallback */}
      <Route path="/404" component={NotFound} />
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <ErrorBoundary>
      <ThemeProvider defaultTheme="light">
        <TooltipProvider>
          <Toaster position="top-center" richColors />
          <Router />
        </TooltipProvider>
      </ThemeProvider>
    </ErrorBoundary>
  );
}

export default App;
