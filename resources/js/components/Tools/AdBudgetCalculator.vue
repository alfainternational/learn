<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة ميزانية الإعلانات</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الهدف الإعلاني</label>
          <select v-model="goal" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="awareness">زيادة الوعي</option>
            <option value="traffic">زيادة الزيارات</option>
            <option value="leads">جمع العملاء المحتملين</option>
            <option value="sales">زيادة المبيعات</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الإيرادات المستهدفة (ريال)</label>
          <input v-model.number="targetRevenue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="100000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">متوسط قيمة الطلب (ريال)</label>
          <input v-model.number="avgOrderValue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">معدل التحويل المتوقع (%)</label>
          <input v-model.number="conversionRate" type="number" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة النقرة المتوقعة (ريال)</label>
          <input v-model.number="cpc" type="number" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">مدة الحملة (أيام)</label>
          <input v-model.number="campaignDays" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="30">
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-orange-50 to-red-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">حساب الميزانية</h3>
        <div class="space-y-4">
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">عدد الطلبات المطلوبة</span>
            <p class="text-2xl font-bold text-gray-800">{{ formatNumber(requiredOrders) }} طلب</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">عدد النقرات المطلوبة</span>
            <p class="text-2xl font-bold text-blue-600">{{ formatNumber(requiredClicks) }} نقرة</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">الميزانية الإجمالية المقترحة</span>
            <p class="text-3xl font-bold text-green-600">{{ formatNumber(totalBudget) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">الميزانية اليومية</span>
            <p class="text-2xl font-bold text-purple-600">{{ formatNumber(dailyBudget) }} ريال/يوم</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">تكلفة اكتساب العميل (CAC)</span>
            <p class="text-2xl font-bold text-orange-600">{{ formatNumber(cac) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">العائد على الإنفاق الإعلاني (ROAS)</span>
            <p class="text-2xl font-bold" :class="roas >= 3 ? 'text-green-600' : 'text-yellow-600'">{{ roas.toFixed(2) }}x</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveBudget" class="flex-1 bg-orange-600 text-white py-3 px-6 rounded-lg hover:bg-orange-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdBudgetCalculator',
  data() {
    return {
      goal: 'sales',
      targetRevenue: 100000,
      avgOrderValue: 500,
      conversionRate: 2,
      cpc: 2,
      campaignDays: 30
    }
  },
  computed: {
    requiredOrders() { return this.avgOrderValue > 0 ? Math.ceil(this.targetRevenue / this.avgOrderValue) : 0; },
    requiredClicks() { return this.conversionRate > 0 ? Math.ceil(this.requiredOrders / (this.conversionRate / 100)) : 0; },
    totalBudget() { return this.requiredClicks * this.cpc; },
    dailyBudget() { return this.campaignDays > 0 ? this.totalBudget / this.campaignDays : 0; },
    cac() { return this.requiredOrders > 0 ? this.totalBudget / this.requiredOrders : 0; },
    roas() { return this.totalBudget > 0 ? this.targetRevenue / this.totalBudget : 0; }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(Math.round(n)); },
    saveBudget() {
      localStorage.setItem('adBudget', JSON.stringify({ goal: this.goal, targetRevenue: this.targetRevenue, totalBudget: this.totalBudget }));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
