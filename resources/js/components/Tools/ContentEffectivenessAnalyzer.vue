<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">محلل فعالية المحتوى</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">رابط أو عنوان المحتوى</label>
          <input v-model="content.title" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اسم المحتوى">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">نوع المحتوى</label>
          <select v-model="content.type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="post">منشور</option>
            <option value="video">فيديو</option>
            <option value="article">مقال</option>
            <option value="story">ستوري</option>
            <option value="reel">ريلز</option>
          </select>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المشاهدات</label>
            <input v-model.number="content.views" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الإعجابات</label>
            <input v-model.number="content.likes" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">التعليقات</label>
            <input v-model.number="content.comments" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المشاركات</label>
            <input v-model.number="content.shares" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">النقرات</label>
            <input v-model.number="content.clicks" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">التحويلات</label>
            <input v-model.number="content.conversions" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-green-50 to-teal-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">تحليل الأداء</h3>
        <div class="space-y-4">
          <div class="bg-white p-4 rounded-lg">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-600">معدل التفاعل</span>
              <span class="text-xl font-bold" :class="engagementColor">{{ engagementRate.toFixed(2) }}%</span>
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-gradient-to-r from-green-400 to-green-600" :style="{ width: Math.min(engagementRate * 10, 100) + '%' }"></div>
            </div>
          </div>
          
          <div class="bg-white p-4 rounded-lg">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-600">معدل النقر (CTR)</span>
              <span class="text-xl font-bold text-blue-600">{{ ctr.toFixed(2) }}%</span>
            </div>
          </div>
          
          <div class="bg-white p-4 rounded-lg">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-600">معدل التحويل</span>
              <span class="text-xl font-bold text-purple-600">{{ conversionRate.toFixed(2) }}%</span>
            </div>
          </div>
          
          <div class="bg-white p-4 rounded-lg">
            <div class="flex justify-between items-center">
              <span class="text-gray-600">تقييم المحتوى</span>
              <span class="text-2xl font-bold" :class="scoreColor">{{ contentScore }}/100</span>
            </div>
            <p class="text-sm text-gray-500 mt-2">{{ scoreDescription }}</p>
          </div>
        </div>
        
        <div class="mt-4 bg-white p-4 rounded-lg">
          <h4 class="font-medium text-gray-800 mb-2">التوصيات</h4>
          <ul class="text-sm text-gray-600 space-y-1">
            <li v-for="(rec, i) in recommendations" :key="i" class="flex items-start gap-2">
              <span class="text-green-500">✓</span>
              <span>{{ rec }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveAnalysis" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition font-medium">حفظ التحليل</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContentEffectivenessAnalyzer',
  data() {
    return {
      content: {
        title: '',
        type: 'post',
        views: 0,
        likes: 0,
        comments: 0,
        shares: 0,
        clicks: 0,
        conversions: 0
      }
    }
  },
  computed: {
    totalEngagement() { return this.content.likes + this.content.comments + this.content.shares; },
    engagementRate() { return this.content.views === 0 ? 0 : (this.totalEngagement / this.content.views) * 100; },
    ctr() { return this.content.views === 0 ? 0 : (this.content.clicks / this.content.views) * 100; },
    conversionRate() { return this.content.clicks === 0 ? 0 : (this.content.conversions / this.content.clicks) * 100; },
    contentScore() {
      let score = 0;
      if (this.engagementRate > 5) score += 30; else if (this.engagementRate > 2) score += 20; else if (this.engagementRate > 1) score += 10;
      if (this.ctr > 3) score += 30; else if (this.ctr > 1) score += 20; else if (this.ctr > 0.5) score += 10;
      if (this.conversionRate > 5) score += 40; else if (this.conversionRate > 2) score += 25; else if (this.conversionRate > 1) score += 15;
      return Math.min(score, 100);
    },
    scoreColor() {
      if (this.contentScore >= 70) return 'text-green-600';
      if (this.contentScore >= 40) return 'text-yellow-600';
      return 'text-red-600';
    },
    engagementColor() {
      if (this.engagementRate >= 5) return 'text-green-600';
      if (this.engagementRate >= 2) return 'text-yellow-600';
      return 'text-red-600';
    },
    scoreDescription() {
      if (this.contentScore >= 70) return 'أداء ممتاز! استمر بهذا النهج';
      if (this.contentScore >= 40) return 'أداء جيد مع مجال للتحسين';
      return 'يحتاج تحسين كبير';
    },
    recommendations() {
      const recs = [];
      if (this.engagementRate < 2) recs.push('حسّن جودة المحتوى لزيادة التفاعل');
      if (this.ctr < 1) recs.push('استخدم عناوين أكثر جاذبية');
      if (this.conversionRate < 2) recs.push('راجع صفحة الهبوط والعرض');
      if (this.content.shares < this.content.likes * 0.1) recs.push('أضف محتوى قابل للمشاركة');
      if (recs.length === 0) recs.push('أداء ممتاز! حافظ على هذا المستوى');
      return recs;
    }
  },
  methods: {
    saveAnalysis() {
      localStorage.setItem('contentAnalysis', JSON.stringify(this.content));
      alert('تم حفظ التحليل!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
