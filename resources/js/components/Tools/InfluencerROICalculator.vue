<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">حاسبة ROI التسويق التأثيري</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">اسم المؤثر</label>
          <input v-model="campaign.influencer" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اسم المؤثر">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عدد المتابعين</label>
          <input v-model.number="campaign.followers" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="100000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">معدل التفاعل (%)</label>
          <input v-model.number="campaign.engagementRate" type="number" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="3.5">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">تكلفة الحملة (ريال)</label>
          <input v-model.number="campaign.cost" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="10000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عدد المنشورات</label>
          <input v-model.number="campaign.posts" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="3">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">معدل التحويل المتوقع (%)</label>
          <input v-model.number="campaign.conversionRate" type="number" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="1">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">متوسط قيمة الطلب (ريال)</label>
          <input v-model.number="campaign.avgOrderValue" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="300">
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-pink-50 to-purple-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">تحليل الحملة</h3>
        <div class="space-y-4">
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">الوصول المتوقع</span>
            <p class="text-2xl font-bold text-gray-800">{{ formatNumber(expectedReach) }}</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">التفاعلات المتوقعة</span>
            <p class="text-2xl font-bold text-blue-600">{{ formatNumber(expectedEngagement) }}</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">المبيعات المتوقعة</span>
            <p class="text-2xl font-bold text-green-600">{{ formatNumber(expectedSales) }} طلب</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">الإيرادات المتوقعة</span>
            <p class="text-2xl font-bold text-purple-600">{{ formatNumber(expectedRevenue) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">ROI المتوقع</span>
            <p class="text-3xl font-bold" :class="roi >= 0 ? 'text-green-600' : 'text-red-600'">{{ roi.toFixed(1) }}%</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">تكلفة لكل تفاعل (CPE)</span>
            <p class="text-xl font-bold text-orange-600">{{ cpe.toFixed(2) }} ريال</p>
          </div>
          <div class="bg-white p-4 rounded-lg">
            <span class="text-sm text-gray-600">التقييم</span>
            <p class="text-xl font-bold" :class="ratingColor">{{ rating }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveCampaign" class="flex-1 bg-pink-600 text-white py-3 px-6 rounded-lg hover:bg-pink-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InfluencerROICalculator',
  data() {
    return {
      campaign: {
        influencer: '',
        followers: 100000,
        engagementRate: 3.5,
        cost: 10000,
        posts: 3,
        conversionRate: 1,
        avgOrderValue: 300
      }
    }
  },
  computed: {
    expectedReach() { return this.campaign.followers * this.campaign.posts * 0.3; },
    expectedEngagement() { return this.expectedReach * (this.campaign.engagementRate / 100); },
    expectedSales() { return Math.round(this.expectedEngagement * (this.campaign.conversionRate / 100)); },
    expectedRevenue() { return this.expectedSales * this.campaign.avgOrderValue; },
    roi() { return this.campaign.cost > 0 ? ((this.expectedRevenue - this.campaign.cost) / this.campaign.cost) * 100 : 0; },
    cpe() { return this.expectedEngagement > 0 ? this.campaign.cost / this.expectedEngagement : 0; },
    rating() {
      if (this.roi >= 200) return 'ممتاز! 🌟';
      if (this.roi >= 100) return 'جيد جداً';
      if (this.roi >= 50) return 'جيد';
      if (this.roi >= 0) return 'مقبول';
      return 'غير مربح ❌';
    },
    ratingColor() {
      if (this.roi >= 100) return 'text-green-600';
      if (this.roi >= 50) return 'text-yellow-600';
      return 'text-red-600';
    }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(Math.round(n)); },
    saveCampaign() {
      localStorage.setItem('influencerCampaign', JSON.stringify(this.campaign));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
