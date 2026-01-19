<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إدارة المستخدمين</h1>
                <p class="text-sm text-gray-500 mt-1">عرض وإدارة جميع المستخدمين في المنصة</p>
            </div>
            <!-- Search -->
            <div class="relative max-w-md w-full sm:w-auto">
                <input 
                    v-model="searchQuery" 
                    @input="debouncedSearch"
                    type="text" 
                    placeholder="بحث في المستخدمين..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                <span class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاشتراك</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانضمام</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="loading" v-for="i in 5" :key="i">
                           <td colspan="6" class="px-6 py-4 text-center">
                               <div class="h-4 bg-gray-100 rounded animate-pulse w-3/4 mx-auto"></div>
                           </td>
                        </tr>
                        <tr v-else v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                            <!-- User Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img v-if="user.avatar_url" :src="user.avatar_url" class="h-10 w-10 rounded-full object-cover">
                                        <div v-else class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- Role -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    :class="{
                                        'bg-purple-100 text-purple-800': user.role === 'admin',
                                        'bg-blue-100 text-blue-800': user.role === 'advertiser',
                                        'bg-gray-100 text-gray-800': user.role === 'user'
                                    }"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ translateRole(user.role) }}
                                </span>
                            </td>
                            <!-- Subscription -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ translateTier(user.subscription_tier) }}</span>
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="user.is_active !== false" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    نشط
                                </span>
                                <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    موقوف
                                </span>
                            </td>
                            <!-- Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(user.created_at).toLocaleDateString('ar-EG') }}
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        @click="openEditModal(user)"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-lg hover:bg-indigo-100 transition-colors"
                                        title="تعديل">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button 
                                        v-if="user.is_active !== false"
                                        @click="suspendUser(user)"
                                        class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg hover:bg-red-100 transition-colors"
                                        title="إيقاف الحساب">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    </button>
                                     <button 
                                        v-else
                                        @click="activateUser(user)"
                                        class="text-green-600 hover:text-green-900 bg-green-50 p-2 rounded-lg hover:bg-green-100 transition-colors"
                                        title="تفعيل الحساب">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!loading && users.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                لا يوجد مستخدمين مطابقين للبحث
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between" v-if="totalPages > 1">
                <button 
                    @click="changePage(currentPage - 1)" 
                    :disabled="currentPage === 1"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    السابق
                </button>
                <span class="text-sm text-gray-700">
                    صفحة {{ currentPage }} من {{ totalPages }}
                </span>
                <button 
                    @click="changePage(currentPage + 1)" 
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    التالي
                </button>
            </div>
        </div>
        <!-- Edit User Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEditModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" dir="rtl">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                تعديل بيانات المستخدم
                            </h3>
                            <div class="mt-2 text-sm text-gray-500">
                                {{ editingUser?.name }} ({{ editingUser?.email }})
                            </div>
                            
                            <div class="mt-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الدور (Role)</label>
                                    <select v-model="editForm.role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="user">مستخدم (User)</option>
                                        <option value="admin">مسؤول (Admin)</option>
                                        <option value="advertiser">معلن (Advertiser)</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">باقة الاشتراك</label>
                                    <select v-model="editForm.subscription_tier" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="free">مجاني (Free)</option>
                                        <option value="basic">أعمال (Basic)</option>
                                        <option value="pro">احترافي (Pro)</option>
                                        <option value="enterprise">مؤسسات (Enterprise)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button 
                            @click="saveUser"
                            type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="saving">
                            {{ saving ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
                        </button>
                        <button 
                            @click="closeEditModal"
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Debug Info (Temporary) -->
        <div class="mt-8 p-4 bg-gray-900 text-green-400 rounded-lg text-xs font-mono" dir="ltr">
            <p><strong>Debug Info:</strong></p>
            <p>Users Count: {{ users.length }}</p>
            <p>Loading: {{ loading }}</p>
            <p>Raw Response Data: {{ debugData }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const users = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const debugData = ref(null);

// Modal State
const showEditModal = ref(false);
const editingUser = ref(null);
const saving = ref(false);
const editForm = reactive({
    role: 'user',
    subscription_tier: 'free'
});

const fetchUsers = async () => {
    loading.value = true;
    try {
        const params = { page: currentPage.value };
        if (searchQuery.value) {
            params.search = searchQuery.value;
        }

        const response = await axios.get('/api/v1/admin/users', { params });
        
        console.log('Full Response:', response);
        console.log('Response Data:', response.data);
        console.log('Response Status:', response.status);
        
        debugData.value = {
            success: response.data?.success,
            dataType: typeof response.data?.data,
            isArray: Array.isArray(response.data?.data),
            hasNestedData: response.data?.data?.data ? true : false,
            status: response.status,
            fullData: response.data
        };

        if (response.data && response.data.success) {
            const result = response.data.data;
            if (result && result.data && Array.isArray(result.data)) {
                 users.value = result.data;
                 totalPages.value = result.last_page || 1;
            } else if (Array.isArray(result)) {
                 users.value = result;
                 totalPages.value = 1;
            } else {
                 users.value = [];
                 debugData.value.error = 'Unrecognized structure';
                 console.error('Unexpected data structure:', result);
            }
        } else {
            users.value = [];
            debugData.value.error = response.data?.message || 'No success flag';
        }
    } catch (error) {
        console.error('Error fetching users:', error);
        console.error('Error response:', error.response);
        
        debugData.value = { 
            error: error.message,
            status: error.response?.status,
            statusText: error.response?.statusText,
            responseData: error.response?.data,
            isAuthError: error.response?.status === 401 || error.response?.status === 403
        };
        
        users.value = [];
        
        // Show user-friendly error
        if (error.response?.status === 401) {
            alert('جلسة العمل منتهية. يرجى تسجيل الدخول مرة أخرى.');
        } else if (error.response?.status === 403) {
            alert('ليس لديك صلاحية الوصول لهذه الصفحة.');
        }
    } finally {
        loading.value = false;
    }
};

let timeout = null;
const debouncedSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        currentPage.value = 1;
        fetchUsers();
    }, 300);
};

const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        fetchUsers();
    }
};

const translateRole = (role) => {
    const map = { 'admin': 'مسؤول', 'user': 'مستخدم', 'advertiser': 'معلن' };
    return map[role] || role;
};

const translateTier = (tier) => {
    const map = { 'free': 'مجاني', 'basic': 'أعمال', 'pro': 'احترافي', 'enterprise': 'مؤسسات' };
    return map[tier] || tier;
};

const suspendUser = async (user) => {
    if (!confirm(`هل أنت متأكد من إيقاف حساب ${user.name}؟`)) return;
    try {
        await axios.post(`/api/v1/admin/users/${user.id}/suspend`);
        fetchUsers();
    } catch (error) {
        console.error('Error suspending user:', error);
    }
};

const activateUser = async (user) => {
    try {
        await axios.post(`/api/v1/admin/users/${user.id}/activate`);
        fetchUsers();
    } catch (error) {
        console.error('Error activating user:', error);
    }
};

const openEditModal = (user) => {
    editingUser.value = user;
    editForm.role = user.role;
    editForm.subscription_tier = user.subscription_tier;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingUser.value = null;
};

const saveUser = async () => {
    if (!editingUser.value) return;
    saving.value = true;
    try {
        // Assuming PUT /api/v1/admin/users/:id exists and accepts these fields
        // If the controller is just standard resource, it should map to update()
        await axios.put(`/api/v1/admin/users/${editingUser.value.id}`, editForm);
        
        // Update local list
        const index = users.value.findIndex(u => u.id === editingUser.value.id);
        if (index !== -1) {
            users.value[index] = { ...users.value[index], ...editForm };
        }
        
        closeEditModal();
        alert('تم تحديث بيانات المستخدم بنجاح');
    } catch (error) {
        console.error('Error updating user:', error);
        alert('حدث خطأ أثناء التحديث');
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    fetchUsers();
});
</script>
