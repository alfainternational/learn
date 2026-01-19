<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">مقارنة القنوات التسويقية</h2>
    
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-3 text-right font-semibold">القناة</th>
            <th class="p-3 text-center font-semibold">التكلفة الشهرية</th>
            <th class="p-3 text-center font-semibold">الوصول المتوقع</th>
            <th class="p-3 text-center font-semibold">معدل التحويل %</th>
            <th class="p-3 text-center font-semibold">الإيرادات المتوقعة</th>
            <th class="p-3 text-center font-semibold">ROI</th>
            <th class="p-3 text-center font-semibold">الأولوية</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(channel, i) in channels" :key="i" class="border-b hover:bg-gray-50">
            <td class="p-3">
              <input v-model="channel.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="اسم القناة">
            </td>
            <td class="p-3">
              <input v-model.number="channel.cost" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center" placeholder="0">
            </td>
            <td class="p-3">
              <input v-model.number="channel.reach" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center" placeholder="0">
            </td>
            <td class="p-3">
              <input v-model.number="channel.conversionRate" type="number" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center" placeholder="0">
            </td>
            <td class="p-3 text-center font-medium text-green-600">{{ formatNumber(getRevenue(channel)) }}</td>
            <td class="p-3 text-center font-bold" :class="getROI(channel) >= 0 ? 'text-green-600' : 'text-red-600'">{{ getROI(channel).toFixed(1) }}%</td>
            <td class="p-3 text-center">
              <span :class="getPriorityClass(channel)" class="px-3 py-1 rounded-full text-xs font-medium">{{ getPriority(channel) }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <button @click="addChannel" class="mt-4 text-blue-600 hover:text-blue-800 font-medium">+ إضافة قناة</button>
    
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-blue-50 p-4 rounded-xl">
        <span class="text-sm text-gray-600">إجمالي التكلفة</span>
        <p class="text-2xl font-bold text-blue-800">{{ formatNumber(totalCost) }} ريال</p>
      </div>
      <div class="bg-green-50 p-4 rounded-xl">
        <span class="text-sm text-gray-600">إجمالي الإيرادات</span>
        <p class="text-2xl font-bold text-green-800">{{ formatNumber(totalRevenue) }} ريال</p>
      </div>
      <div class="bg-purple-50 p-4 rounded-xl">
        <span class="text-sm text-gray-600">متوسط ROI</span>
        <p class="text-2xl font-bold text-purple-800">{{ averageROI.toFixed(1) }}%</p>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveData" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChannelComparison',
  data() {
    return {
      avgOrderValue: 500,
      channels: [
        { name: 'فيسبوك', cost: 5000, reach: 50000, conversionRate: 2 },
        { name: 'جوجل', cost: 8000, reach: 30000, conversionRate: 4 },
        { name: 'انستغرام', cost: 4000, reach: 40000, conversionRate: 1.5 }
      ]
    }
  },
  computed: {
    totalCost() { return this.channels.reduce((sum, c) => sum + c.cost, 0); },
    totalRevenue() { return this.channels.reduce((sum, c) => sum + this.getRevenue(c), 0); },
    averageROI() {
      if (this.channels.length === 0) return 0;
      return this.channels.reduce((sum, c) => sum + this.getROI(c), 0) / this.channels.length;
    }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(n); },
    getRevenue(c) { return c.reach * (c.conversionRate / 100) * this.avgOrderValue; },
    getROI(c) { return c.cost === 0 ? 0 : ((this.getRevenue(c) - c.cost) / c.cost) * 100; },
    getPriority(c) {
      const roi = this.getROI(c);
      if (roi > 200) return 'عالية';
      if (roi > 50) return 'متوسطة';
      return 'منخفضة';
    },
    getPriorityClass(c) {
      const roi = this.getROI(c);
      if (roi > 200) return 'bg-green-100 text-green-800';
      if (roi > 50) return 'bg-yellow-100 text-yellow-800';
      return 'bg-red-100 text-red-800';
    },
    addChannel() {
      this.channels.push({ name: '', cost: 0, reach: 0, conversionRate: 0 });
    },
    saveData() {
      localStorage.setItem('channelComparison', JSON.stringify(this.channels));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
