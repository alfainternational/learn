<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة القيمة الدائمة للعميل (CLV)</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">متوسط قيمة الطلب (ريال)</label>
          <input v-model.number="avgOrderValue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عدد الطلبات في السنة</label>
          <input v-model.number="ordersPerYear" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="4">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">متوسط عمر العميل (سنوات)</label>
          <input v-model.number="customerLifespan" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="3">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">هامش الربح (%)</label>
          <input v-model.number="profitMargin" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="30">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة اكتساب العميل CAC (ريال)</label>
          <input v-model.number="cac" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="200">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">معدل الاحتفاظ بالعملاء (%)</label>
          <input v-model.number="retentionRate" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="70">
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-emerald-50 to-teal-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">نتائج CLV</h3>
        <div class="space-y-4">
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">الإيرادات السنوية لكل عميل</span>
            <p class="text-2xl font-bold text-gray-800">{{ formatNumber(annualRevenue) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">القيمة الدائمة للعميل (CLV)</span>
            <p class="text-4xl font-bold text-emerald-600">{{ formatNumber(clv) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">صافي CLV (بعد خصم CAC)</span>
            <p class="text-2xl font-bold" :class="netClv >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatNumber(netClv) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">نسبة CLV:CAC</span>
            <p class="text-2xl font-bold" :class="clvCacRatioColor">{{ clvCacRatio.toFixed(1) }}:1</p>
            <p class="text-sm text-gray-500 mt-1">{{ clvCacRatioLabel }}</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">الحد الأقصى المقترح لـ CAC</span>
            <p class="text-2xl font-bold text-purple-600">{{ formatNumber(maxCac) }} ريال</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveData" class="flex-1 bg-emerald-600 text-white py-3 px-6 rounded-lg hover:bg-emerald-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CLVCalculator',
  data() {
    return {
      avgOrderValue: 500,
      ordersPerYear: 4,
      customerLifespan: 3,
      profitMargin: 30,
      cac: 200,
      retentionRate: 70
    }
  },
  computed: {
    annualRevenue() { return this.avgOrderValue * this.ordersPerYear; },
    clv() { return this.annualRevenue * this.customerLifespan * (this.profitMargin / 100); },
    netClv() { return this.clv - this.cac; },
    clvCacRatio() { return this.cac > 0 ? this.clv / this.cac : 0; },
    maxCac() { return this.clv * 0.33; },
    clvCacRatioColor() {
      if (this.clvCacRatio >= 3) return 'text-green-600';
      if (this.clvCacRatio >= 2) return 'text-yellow-600';
      return 'text-red-600';
    },
    clvCacRatioLabel() {
      if (this.clvCacRatio >= 3) return 'نسبة ممتازة! استثمار مربح';
      if (this.clvCacRatio >= 2) return 'نسبة جيدة، يمكن تحسينها';
      return 'نسبة منخفضة، راجع استراتيجية الاستحواذ';
    }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(Math.round(n)); },
    saveData() {
      localStorage.setItem('clvCalculator', JSON.stringify({ clv: this.clv, cac: this.cac, ratio: this.clvCacRatio }));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
