<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">لوحة تحكم المشرف</h1>
            <div class="flex gap-2">
                <span class="text-sm text-gray-500">آخر تحديث: {{ lastUpdated }}</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">إجمالي المستخدمين</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_users }}</p>
                <div class="flex items-center mt-2 text-sm">
                    <span class="text-green-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        {{ stats.new_users_month }}
                    </span>
                    <span class="text-gray-400 mr-2">هذا الشهر</span>
                </div>
            </div>

            <!-- Active Subscriptions -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">الاشتراكات النشطة</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.active_subscriptions }}</p>
                <div class="flex items-center mt-2 text-sm">
                    <span class="text-gray-400">الإيرادات: {{ stats.revenue_this_month }} ر.س</span>
                </div>
            </div>

            <!-- Marketing Plans -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">الخطط التسويقية</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_plans }}</p>
                <div class="flex items-center mt-2 text-sm">
                    <span class="text-gray-400">المكتملة: {{ stats.completed_plans || 0 }}</span>
                </div>
            </div>
            
            <!-- Pending Ads -->
             <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">حملات بانتظار المراجعة</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_campaigns || 0 }}</p>
                <router-link to="/admin/campaigns" class="text-sm text-primary-600 hover:text-primary-700 mt-2 block">
                    مراجعة الطلبات &larr;
                </router-link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">أحدث المشتركين</h3>
                    <router-link to="/admin/users" class="text-sm text-primary-600 hover:text-primary-700">عرض الكل</router-link>
                </div>
                <div class="divide-y divide-gray-100">
                    <div v-for="user in recentUsers" :key="user.id" class="p-4 hover:bg-gray-50 flex items-center gap-4 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ user.name }}</h4>
                            <p class="text-sm text-gray-500">{{ user.email }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                            {{ user.created_at_human }}
                        </span>
                    </div>
                    <div v-if="recentUsers.length === 0" class="p-8 text-center text-gray-500">
                        لا يوجد مستخدمين جدد
                    </div>
                </div>
            </div>

            <!-- Recent Marketing Plans -->
             <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">أحدث الخطط</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div v-for="plan in recentPlans" :key="plan.id" class="p-4 hover:bg-gray-50 flex items-center gap-4 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ plan.title }}</h4>
                            <p class="text-sm text-gray-500">{{ plan.business_name || 'بدون اسم' }}</p>
                        </div>
                         <div class="flex flex-col items-end">
                            <span :class="{'bg-green-100 text-green-700': plan.status === 'completed', 'bg-gray-100 text-gray-700': plan.status !== 'completed'}" class="px-2 py-1 text-xs font-medium rounded-full">
                                {{ plan.status === 'completed' ? 'مكتملة' : 'مسودة' }}
                            </span>
                             <span class="text-xs text-gray-400 mt-1">{{ plan.user?.name }}</span>
                         </div>
                    </div>
                     <div v-if="recentPlans.length === 0" class="p-8 text-center text-gray-500">
                        لا توجد خطط حديثة
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({
    total_users: 0,
    active_subscriptions: 0,
    total_plans: 0,
    revenue_this_month: 0,
    pending_campaigns: 0,
    new_users_month: 0
});

const recentUsers = ref([]);
const recentPlans = ref([]);
const lastUpdated = ref(new Date().toLocaleTimeString('ar-EG'));

const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/v1/admin/dashboard');
        if (response.data.success) {
            stats.value = response.data.data.stats;
            recentUsers.value = response.data.data.recent_users.map(u => ({
                ...u,
                created_at_human: new Date(u.created_at).toLocaleDateString('ar-EG')
            }));
            recentPlans.value = response.data.data.recent_plans;
            lastUpdated.value = new Date().toLocaleTimeString('ar-EG');
        }
    } catch (error) {
        console.error('Error fetching admin dashboard data:', error);
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>
