<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة القيمة المقترحة (ROI)</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الإيرادات المتوقعة (ريال)</label>
          <input v-model.number="expectedRevenue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">التكلفة الإجمالية (ريال)</label>
          <input v-model.number="totalCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الفترة الزمنية (أشهر)</label>
          <input v-model.number="timePeriod" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="12">
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">النتائج</h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center p-3 bg-white rounded-lg">
            <span class="text-gray-600">صافي الربح</span>
            <span class="text-xl font-bold text-green-600">{{ formatNumber(netProfit) }} ريال</span>
          </div>
          <div class="flex justify-between items-center p-3 bg-white rounded-lg">
            <span class="text-gray-600">نسبة ROI</span>
            <span class="text-xl font-bold" :class="roi >= 0 ? 'text-green-600' : 'text-red-600'">{{ roi.toFixed(2) }}%</span>
          </div>
          <div class="flex justify-between items-center p-3 bg-white rounded-lg">
            <span class="text-gray-600">العائد الشهري</span>
            <span class="text-xl font-bold text-blue-600">{{ formatNumber(monthlyReturn) }} ريال</span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveResults" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">
        حفظ النتائج
      </button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">
        تصدير PDF
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ValueCalculator',
  data() {
    return {
      expectedRevenue: 0,
      totalCost: 0,
      timePeriod: 12
    }
  },
  computed: {
    netProfit() {
      return this.expectedRevenue - this.totalCost;
    },
    roi() {
      if (this.totalCost === 0) return 0;
      return ((this.expectedRevenue - this.totalCost) / this.totalCost) * 100;
    },
    monthlyReturn() {
      if (this.timePeriod === 0) return 0;
      return this.netProfit / this.timePeriod;
    }
  },
  methods: {
    formatNumber(num) {
      return new Intl.NumberFormat('ar-SA').format(num);
    },
    saveResults() {
      const results = {
        expectedRevenue: this.expectedRevenue,
        totalCost: this.totalCost,
        timePeriod: this.timePeriod,
        netProfit: this.netProfit,
        roi: this.roi,
        monthlyReturn: this.monthlyReturn
      };
      localStorage.setItem('valueCalculatorResults', JSON.stringify(results));
      alert('تم حفظ النتائج بنجاح!');
    },
    exportPDF() {
      window.print();
    }
  }
}
</script>
