import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/views/Home.vue'),
        meta: { title: 'خطّط - منصة التخطيط التسويقي الذكية' }
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/Auth/Login.vue'),
        meta: { title: 'تسجيل الدخول', guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/Auth/Register.vue'),
        meta: { title: 'إنشاء حساب', guest: true }
    },
    {
        path: '/dashboard',
        component: () => import('@/layouts/AuthenticatedLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('@/views/Dashboard/Index.vue'),
                meta: { title: 'لوحة التحكم' }
            },
            {
                path: '/plans',
                name: 'plans',
                component: () => import('@/views/Plans/Index.vue'),
                meta: { title: 'خططي التسويقية' }
            },
            {
                path: '/plans/create',
                name: 'plans.create',
                component: () => import('@/views/Plans/Create.vue'),
                meta: { title: 'إنشاء خطة جديدة' }
            },
            {
                path: '/plans/:id',
                name: 'plans.show',
                component: () => import('@/views/Plans/Show.vue'),
                meta: { title: 'عرض الخطة' }
            },
            {
                path: '/plans/:id/edit',
                name: 'plans.edit',
                component: () => import('@/views/Plans/Edit.vue'),
                meta: { title: 'تعديل الخطة' }
            },
            {
                path: '/templates',
                name: 'templates',
                component: () => import('@/views/Templates/Index.vue'),
                meta: { title: 'مكتبة القوالب' }
            },
            {
                path: '/learn',
                name: 'courses.index',
                component: () => import('@/views/Courses/Index.vue'),
                meta: { title: 'الدورات التعليمية' }
            },
            {
                path: '/learn/lessons/:id',
                name: 'lessons.show',
                component: () => import('@/views/Lessons/Show.vue'),
                meta: { title: 'الدرس' }
            },
            {
                path: '/learn/quizzes/:id',
                name: 'quizzes.take',
                component: () => import('@/views/Quizzes/Take.vue'),
                meta: { title: 'الاختبار' }
            },
            {
                path: '/learn/quizzes/:id/results/:attemptId',
                name: 'quizzes.results',
                component: () => import('@/views/Quizzes/Results.vue'),
                meta: { title: 'نتائج الاختبار' }
            },
            {
                path: '/progress',
                name: 'progress.dashboard',
                component: () => import('@/views/Progress/Dashboard.vue'),
                meta: { title: 'تقدمي' }
            },
            {
                path: '/certificates/:number',
                name: 'certificates.show',
                component: () => import('@/views/Certificates/Show.vue'),
                meta: { title: 'الشهادة' }
            },
            {
                path: '/campaigns',
                name: 'campaigns.index',
                component: () => import('@/views/Campaigns/Index.vue'),
                meta: { title: 'تحليل الحملات' }
            },
            {
                path: '/campaigns/create',
                name: 'campaigns.create',
                component: () => import('@/views/Campaigns/Create.vue'),
                meta: { title: 'إنشاء حملة' }
            },
            {
                path: '/campaigns/:id',
                name: 'campaigns.show',
                component: () => import('@/views/Campaigns/Show.vue'),
                meta: { title: 'تفاصيل الحملة' }
            },
            {
                path: '/campaigns/:id/analysis',
                name: 'campaigns.analysis',
                component: () => import('@/views/Campaigns/Analysis.vue'),
                meta: { title: 'تحليل الحملة' }
            },
            // Tasks
            {
                path: '/tasks',
                name: 'tasks.board',
                component: () => import('@/views/Tasks/Board.vue'),
                meta: { title: 'لوحة المهام' }
            },
            {
                path: '/tasks/list',
                name: 'tasks.list',
                component: () => import('@/views/Tasks/List.vue'),
                meta: { title: 'قائمة المهام' }
            },
            {
                path: '/tasks/:id',
                name: 'tasks.show',
                component: () => import('@/views/Tasks/Show.vue'),
                meta: { title: 'تفاصيل المهمة' }
            },
            // Calendar
            {
                path: '/calendar',
                name: 'calendar.index',
                component: () => import('@/views/Calendar/Index.vue'),
                meta: { title: 'التقويم' }
            },
            // Analytics
            {
                path: '/analytics',
                name: 'analytics.dashboard',
                component: () => import('@/views/Analytics/Dashboard.vue'),
                meta: { title: 'التحليلات' }
            },
            {
                path: '/analytics/compare',
                name: 'analytics.compare',
                component: () => import('@/views/Analytics/Compare.vue'),
                meta: { title: 'مقارنة الحملات' }
            },
            {
                path: '/analytics/reports',
                name: 'analytics.reports',
                component: () => import('@/views/Analytics/Reports.vue'),
                meta: { title: 'التقارير' }
            },
            // Teams
            {
                path: '/teams',
                name: 'teams.index',
                component: () => import('@/views/Teams/Index.vue'),
                meta: { title: 'الفرق' }
            },
            {
                path: '/teams/:id',
                name: 'teams.show',
                component: () => import('@/views/Teams/Show.vue'),
                meta: { title: 'تفاصيل الفريق' }
            },
            {
                path: '/teams/invitations',
                name: 'teams.invitations',
                component: () => import('@/views/Teams/Invitations.vue'),
                meta: { title: 'الدعوات' }
            }
        ]
    },
    {
        path: '/pricing',
        name: 'pricing',
        component: () => import('@/views/Pricing.vue'),
        meta: { title: 'الأسعار' }
    },
    // New Routes
    {
        path: '/profile',
        component: () => import('@/layouts/AuthenticatedLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '', name: 'profile', component: () => import('@/views/Profile/Edit.vue'), meta: { title: 'الملف الشخصي' } }
        ]
    },
    {
        path: '/notifications',
        component: () => import('@/layouts/AuthenticatedLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '', name: 'notifications', component: () => import('@/views/Notifications/Index.vue'), meta: { title: 'الإشعارات' } }
        ]
    },
    {
        path: '/subscription',
        component: () => import('@/layouts/AuthenticatedLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '', name: 'subscription', component: () => import('@/views/Subscription/Index.vue'), meta: { title: 'الاشتراك' } },
            { path: 'history', name: 'subscription.history', component: () => import('@/views/Subscription/History.vue'), meta: { title: 'سجل الاشتراكات' } }
        ]
    },
    {
        path: '/templates/:id',
        component: () => import('@/layouts/AuthenticatedLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '', name: 'templates.show', component: () => import('@/views/Templates/Show.vue'), meta: { title: 'تفاصيل القالب' } }
        ]
    },
    // Admin Routes
    {
        path: '/admin',
        component: () => import('@/layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
            {
                path: 'dashboard',
                alias: '',
                name: 'admin.dashboard',
                component: () => import('@/views/Admin/Dashboard.vue'),
                meta: { title: 'لوحة القيادة' }
            },
            {
                path: 'users',
                name: 'admin.users',
                component: () => import('@/views/Admin/Users/Index.vue'),
                meta: { title: 'إدارة المستخدمين' }
            },
            {
                path: 'campaigns',
                name: 'admin.campaigns',
                component: () => import('@/views/Admin/AdCampaigns/Index.vue'),
                meta: { title: 'إدارة الحملات' }
            },
            {
                path: 'settings',
                name: 'admin.settings',
                component: () => import('@/views/Admin/Settings/Index.vue'),
                meta: { title: 'إعدادات النظام' }
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/'
    }
];

const router = createRouter({
    history: createWebHistory(document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '/'),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },
});

// Navigation guards
// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Set page title
    document.title = to.meta.title || 'خطّط';

    const checkAdmin = () => {
        if (to.meta.requiresAdmin && authStore.user?.role !== 'admin') {
            return false;
        }
        return true;
    };

    // Check authentication
    if (to.meta.requiresAuth) {
        if (authStore.isAuthenticated) {
            if (checkAdmin()) next();
            else next({ name: 'dashboard' });
        } else if (localStorage.getItem('token')) {
            // Token exists but not authenticated in store (e.g. reload)
            try {
                await authStore.fetchUser();
                if (authStore.isAuthenticated) {
                    if (checkAdmin()) next();
                    else next({ name: 'dashboard' });
                } else {
                    next({ name: 'login', query: { redirect: to.fullPath } });
                }
            } catch (error) {
                // Token invalid
                next({ name: 'login', query: { redirect: to.fullPath } });
            }
        } else {
            // No token
            next({ name: 'login', query: { redirect: to.fullPath } });
        }
    } else if (to.meta.guest && (authStore.isAuthenticated || localStorage.getItem('token'))) {
        // Prevent guests from accessing login/register if already logged in
        if (!authStore.isAuthenticated && localStorage.getItem('token')) {
            try {
                await authStore.fetchUser();
                if (authStore.isAuthenticated) {
                    next({ name: 'dashboard' });
                    return;
                }
            } catch (e) { }
        }

        if (authStore.isAuthenticated) {
            next({ name: 'dashboard' });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
