<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">سجل الاشتراكات والمدفوعات</h1>
      <router-link to="/subscription" class="text-sm text-primary-600 hover:underline">
        &larr; إدارة الباقات
      </router-link>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
         <div class="card p-4 bg-white">
            <h3 class="text-sm font-medium text-gray-500">إجمالي المدفوعات</h3>
             <p class="text-2xl font-bold text-gray-900 mt-2">{{ totalSpent }} ريال</p>
         </div>
         <div class="card p-4 bg-white">
            <h3 class="text-sm font-medium text-gray-500">آخر عملية دقع</h3>
             <p class="text-xl font-bold text-gray-900 mt-2">{{ lastPaymentDate }}</p>
         </div>
          <div class="card p-4 bg-white">
            <h3 class="text-sm font-medium text-gray-500">الحالة الحالية</h3>
             <p class="text-xl font-bold text-green-600 mt-2">نشط</p>
         </div>
    </div>

    <!-- History Table -->
    <div class="card bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفاتورة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                     <tr v-if="loading">
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">جاري التحميل...</td>
                     </tr>
                     <tr v-else-if="transactions.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">لا توجد معاملات سابقة</td>
                     </tr>
                    <tr v-for="transaction in transactions" :key="transaction.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ new Date(transaction.created_at).toLocaleDateString('ar-SA') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ transaction.type === 'subscription' ? 'اشتراك شهري' : 'عملية شراء' }} 
                            <span v-if="transaction.payment_method" class="text-xs text-gray-400">({{ transaction.payment_method }})</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ transaction.amount }} {{ transaction.currency }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': transaction.status === 'completed',
                                    'bg-yellow-100 text-yellow-800': transaction.status === 'pending',
                                    'bg-red-100 text-red-800': transaction.status === 'failed'
                                }">
                                {{ transaction.status === 'completed' ? 'ناجحة' : (transaction.status === 'pending' ? 'قيد المعالجة' : 'فشلت') }}
                            </span>
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button @click="downloadInvoice(transaction)" class="text-primary-600 hover:text-primary-900">
                                تحميل PDF
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="links.length > 3" class="px-6 py-4 border-t border-gray-200 flex justify-center">
             <nav class="flex gap-1">
                <button 
                    v-for="(link, i) in links" 
                    :key="i"
                    :disabled="!link.url || link.active"
                    @click="fetchTransactions(link.url)"
                    class="px-3 py-1 text-sm rounded border"
                    :class="link.active ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    v-html="link.label"
                >
                </button>
            </nav>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const transactions = ref([]);
const links = ref([]);
const loading = ref(true);

const totalSpent = computed(() => {
    return transactions.value
        .filter(t => t.status === 'completed')
        .reduce((sum, t) => sum + parseFloat(t.amount), 0)
        .toFixed(2);
});

const lastPaymentDate = computed(() => {
    const completed = transactions.value.filter(t => t.status === 'completed');
    if (completed.length === 0) return '-';
    return new Date(completed[0].created_at).toLocaleDateString('ar-SA');
});

const fetchTransactions = async (url = '/transactions') => {
    loading.value = true;
    try {
        const res = await axios.get(url);
        transactions.value = res.data.data.data; // Laravel paginate returns { success: true, data: { data: [], links: [] } }
        links.value = res.data.data.links;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const downloadInvoice = (transaction) => {
    // Implement mock download or real endpoint
    alert('سيتم تفعيل تحميل الفاتورة قريباً');
};

onMounted(() => {
    fetchTransactions();
});
</script>
