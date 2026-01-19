<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة TAM/SAM/SOM</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="space-y-4">
        <h3 class="font-semibold text-lg text-blue-800">TAM - السوق الكلي</h3>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عدد العملاء المحتملين</label>
          <input v-model.number="tamCustomers" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">متوسط الإنفاق السنوي (ريال)</label>
          <input v-model.number="avgSpending" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
        </div>
        <div class="bg-blue-100 p-4 rounded-lg">
          <span class="text-sm text-gray-600">حجم TAM</span>
          <p class="text-2xl font-bold text-blue-800">{{ formatNumber(tam) }} ريال</p>
        </div>
      </div>
      
      <div class="space-y-4">
        <h3 class="font-semibold text-lg text-green-800">SAM - السوق المتاح</h3>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">نسبة السوق المستهدف (%)</label>
          <input v-model.number="samPercentage" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="0" max="100">
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
          <span class="text-sm text-gray-600">حجم SAM</span>
          <p class="text-2xl font-bold text-green-800">{{ formatNumber(sam) }} ريال</p>
        </div>
      </div>
      
      <div class="space-y-4">
        <h3 class="font-semibold text-lg text-purple-800">SOM - السوق القابل للتحقيق</h3>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الحصة السوقية المتوقعة (%)</label>
          <input v-model.number="somPercentage" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="0" max="100">
        </div>
        <div class="bg-purple-100 p-4 rounded-lg">
          <span class="text-sm text-gray-600">حجم SOM</span>
          <p class="text-2xl font-bold text-purple-800">{{ formatNumber(som) }} ريال</p>
        </div>
      </div>
    </div>
    
    <div class="mt-8">
      <div class="h-8 bg-gray-200 rounded-full overflow-hidden flex">
        <div class="bg-blue-500 h-full" :style="{ width: '100%' }"></div>
      </div>
      <div class="h-8 bg-gray-200 rounded-full overflow-hidden flex mt-2">
        <div class="bg-green-500 h-full" :style="{ width: samPercentage + '%' }"></div>
      </div>
      <div class="h-8 bg-gray-200 rounded-full overflow-hidden flex mt-2">
        <div class="bg-purple-500 h-full" :style="{ width: (samPercentage * somPercentage / 100) + '%' }"></div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveResults" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MarketSizeCalculator',
  data() {
    return {
      tamCustomers: 0,
      avgSpending: 0,
      samPercentage: 30,
      somPercentage: 10
    }
  },
  computed: {
    tam() { return this.tamCustomers * this.avgSpending; },
    sam() { return this.tam * (this.samPercentage / 100); },
    som() { return this.sam * (this.somPercentage / 100); }
  },
  methods: {
    formatNumber(num) { return new Intl.NumberFormat('ar-SA').format(num); },
    saveResults() {
      localStorage.setItem('marketSizeResults', JSON.stringify({ tam: this.tam, sam: this.sam, som: this.som }));
      alert('تم حفظ النتائج!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
