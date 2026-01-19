<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة معدل التحويل</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عدد الزوار</label>
          <input v-model.number="visitors" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="10000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عدد التحويلات</label>
          <input v-model.number="conversions" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="200">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">قيمة التحويل الواحد (ريال)</label>
          <input v-model.number="conversionValue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة الاستحواذ (ريال)</label>
          <input v-model.number="acquisitionCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="5000">
        </div>
        
        <div class="bg-blue-50 p-4 rounded-lg">
          <h4 class="font-medium text-blue-800 mb-2">محاكاة التحسين</h4>
          <label class="block text-sm text-gray-600 mb-2">معدل التحويل المستهدف (%)</label>
          <input v-model.number="targetRate" type="range" min="0" max="20" step="0.5" class="w-full">
          <span class="text-lg font-bold text-blue-600">{{ targetRate }}%</span>
        </div>
      </div>
      
      <div class="space-y-4">
        <div class="bg-gradient-to-br from-green-50 to-teal-100 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">النتائج الحالية</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">معدل التحويل</span>
              <p class="text-3xl font-bold" :class="rateColor">{{ conversionRate.toFixed(2) }}%</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">إجمالي الإيرادات</span>
              <p class="text-2xl font-bold text-green-600">{{ formatNumber(totalRevenue) }} ريال</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">تكلفة لكل تحويل</span>
              <p class="text-2xl font-bold text-orange-600">{{ formatNumber(costPerConversion) }} ريال</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">صافي الربح</span>
              <p class="text-2xl font-bold" :class="netProfit >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatNumber(netProfit) }} ريال</p>
            </div>
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">بعد التحسين ({{ targetRate }}%)</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">التحويلات الجديدة</span>
              <p class="text-2xl font-bold text-purple-600">{{ formatNumber(projectedConversions) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">الإيرادات الجديدة</span>
              <p class="text-2xl font-bold text-green-600">{{ formatNumber(projectedRevenue) }} ريال</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center col-span-2">
              <span class="text-sm text-gray-600">الزيادة في الإيرادات</span>
              <p class="text-3xl font-bold text-green-600">+{{ formatNumber(revenueIncrease) }} ريال</p>
              <p class="text-sm text-green-500">(+{{ increasePercentage.toFixed(0) }}%)</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveData" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConversionRateCalculator',
  data() {
    return {
      visitors: 10000,
      conversions: 200,
      conversionValue: 500,
      acquisitionCost: 5000,
      targetRate: 5
    }
  },
  computed: {
    conversionRate() { return this.visitors > 0 ? (this.conversions / this.visitors) * 100 : 0; },
    totalRevenue() { return this.conversions * this.conversionValue; },
    costPerConversion() { return this.conversions > 0 ? this.acquisitionCost / this.conversions : 0; },
    netProfit() { return this.totalRevenue - this.acquisitionCost; },
    projectedConversions() { return Math.round(this.visitors * (this.targetRate / 100)); },
    projectedRevenue() { return this.projectedConversions * this.conversionValue; },
    revenueIncrease() { return this.projectedRevenue - this.totalRevenue; },
    increasePercentage() { return this.totalRevenue > 0 ? (this.revenueIncrease / this.totalRevenue) * 100 : 0; },
    rateColor() {
      if (this.conversionRate >= 5) return 'text-green-600';
      if (this.conversionRate >= 2) return 'text-yellow-600';
      return 'text-red-600';
    }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(Math.round(n)); },
    saveData() {
      localStorage.setItem('conversionRate', JSON.stringify({ visitors: this.visitors, conversions: this.conversions, rate: this.conversionRate }));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
