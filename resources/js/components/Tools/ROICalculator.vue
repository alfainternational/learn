<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة ROI التسويقي</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">إجمالي الإيرادات من الحملة (ريال)</label>
          <input v-model.number="revenue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="100000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة الإعلانات (ريال)</label>
          <input v-model.number="adCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="20000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة الإنتاج والتصميم (ريال)</label>
          <input v-model.number="productionCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="5000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة الأدوات والاشتراكات (ريال)</label>
          <input v-model.number="toolsCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="2000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة العمالة (ريال)</label>
          <input v-model.number="laborCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="10000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكاليف أخرى (ريال)</label>
          <input v-model.number="otherCost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-indigo-50 to-blue-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">نتائج ROI</h3>
        <div class="space-y-4">
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">إجمالي التكاليف</span>
            <p class="text-2xl font-bold text-gray-800">{{ formatNumber(totalCost) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">صافي الربح</span>
            <p class="text-2xl font-bold" :class="netProfit >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatNumber(netProfit) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">نسبة ROI</span>
            <p class="text-4xl font-bold" :class="roiColor">{{ roi.toFixed(1) }}%</p>
            <div class="mt-2 h-3 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full transition-all duration-500" :class="roiBgColor" :style="{ width: Math.min(Math.max(roi + 100, 0), 200) / 2 + '%' }"></div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">ROAS (العائد على الإنفاق الإعلاني)</span>
            <p class="text-2xl font-bold text-purple-600">{{ roas.toFixed(2) }}x</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">تقييم الحملة</span>
            <p class="text-xl font-bold" :class="roiColor">{{ roiLabel }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveData" class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ROICalculator',
  data() {
    return {
      revenue: 100000,
      adCost: 20000,
      productionCost: 5000,
      toolsCost: 2000,
      laborCost: 10000,
      otherCost: 0
    }
  },
  computed: {
    totalCost() { return this.adCost + this.productionCost + this.toolsCost + this.laborCost + this.otherCost; },
    netProfit() { return this.revenue - this.totalCost; },
    roi() { return this.totalCost > 0 ? (this.netProfit / this.totalCost) * 100 : 0; },
    roas() { return this.adCost > 0 ? this.revenue / this.adCost : 0; },
    roiColor() {
      if (this.roi >= 100) return 'text-green-600';
      if (this.roi >= 50) return 'text-blue-600';
      if (this.roi >= 0) return 'text-yellow-600';
      return 'text-red-600';
    },
    roiBgColor() {
      if (this.roi >= 100) return 'bg-green-500';
      if (this.roi >= 50) return 'bg-blue-500';
      if (this.roi >= 0) return 'bg-yellow-500';
      return 'bg-red-500';
    },
    roiLabel() {
      if (this.roi >= 200) return 'ممتاز! 🎉';
      if (this.roi >= 100) return 'جيد جداً';
      if (this.roi >= 50) return 'جيد';
      if (this.roi >= 0) return 'مقبول';
      return 'خسارة ❌';
    }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(Math.round(n)); },
    saveData() {
      localStorage.setItem('roiCalculator', JSON.stringify({ revenue: this.revenue, totalCost: this.totalCost, roi: this.roi }));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
