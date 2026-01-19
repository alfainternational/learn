<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">محلل الكلمات المفتاحية</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الكلمة المفتاحية الرئيسية</label>
          <div class="flex gap-2">
            <input v-model="mainKeyword" type="text" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="مثال: تسويق رقمي" @keyup.enter="analyzeKeyword">
            <button @click="analyzeKeyword" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">تحليل</button>
          </div>
        </div>
        
        <div v-if="keywords.length > 0">
          <label class="block text-sm font-medium text-gray-700 mb-2">الكلمات ذات الصلة</label>
          <div class="bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto">
            <div v-for="(kw, i) in keywords" :key="i" class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
              <div class="flex items-center gap-3">
                <input type="checkbox" v-model="kw.selected" class="rounded">
                <span class="font-medium">{{ kw.keyword }}</span>
              </div>
              <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-500">{{ kw.volume }}</span>
                <span :class="difficultyColor(kw.difficulty)" class="px-2 py-1 rounded text-xs">{{ kw.difficulty }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">إضافة كلمات يدوياً</label>
          <textarea v-model="manualKeywords" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="3" placeholder="كلمة واحدة في كل سطر..."></textarea>
          <button @click="addManualKeywords" class="mt-2 text-blue-600 text-sm font-medium">+ إضافة للقائمة</button>
        </div>
      </div>
      
      <div class="space-y-4">
        <div class="bg-gradient-to-br from-green-50 to-teal-100 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">ملخص التحليل</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">إجمالي الكلمات</span>
              <p class="text-2xl font-bold text-gray-800">{{ keywords.length }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">المحددة</span>
              <p class="text-2xl font-bold text-blue-600">{{ selectedCount }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">سهلة المنافسة</span>
              <p class="text-2xl font-bold text-green-600">{{ easyCount }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg text-center">
              <span class="text-sm text-gray-600">حجم البحث الكلي</span>
              <p class="text-2xl font-bold text-purple-600">{{ formatNumber(totalVolume) }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white border border-gray-200 rounded-xl p-4">
          <h4 class="font-semibold text-gray-800 mb-3">الكلمات المحددة للتصدير</h4>
          <div class="flex flex-wrap gap-2">
            <span v-for="kw in selectedKeywords" :key="kw.keyword" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
              {{ kw.keyword }}
            </span>
            <span v-if="selectedKeywords.length === 0" class="text-gray-400 text-sm">لم تحدد أي كلمات بعد</span>
          </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <h4 class="font-medium text-yellow-800 mb-2">💡 نصائح SEO</h4>
          <ul class="text-sm text-yellow-700 space-y-1">
            <li>• ركز على كلمات ذات منافسة منخفضة في البداية</li>
            <li>• استخدم كلمات طويلة الذيل (Long-tail)</li>
            <li>• تأكد من ملاءمة الكلمة لنية المستخدم</li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveKeywords" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition font-medium">حفظ</button>
      <button @click="exportCSV" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير CSV</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'KeywordAnalyzer',
  data() {
    return {
      mainKeyword: '',
      manualKeywords: '',
      keywords: []
    }
  },
  computed: {
    selectedKeywords() { return this.keywords.filter(k => k.selected); },
    selectedCount() { return this.selectedKeywords.length; },
    easyCount() { return this.keywords.filter(k => k.difficulty === 'سهل').length; },
    totalVolume() { return this.keywords.reduce((sum, k) => sum + parseInt(k.volume.replace(/,/g, '')), 0); }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(n); },
    difficultyColor(d) {
      if (d === 'سهل') return 'bg-green-100 text-green-800';
      if (d === 'متوسط') return 'bg-yellow-100 text-yellow-800';
      return 'bg-red-100 text-red-800';
    },
    analyzeKeyword() {
      if (!this.mainKeyword) return;
      const difficulties = ['سهل', 'متوسط', 'صعب'];
      const suggestions = [
        this.mainKeyword,
        this.mainKeyword + ' للمبتدئين',
        'أفضل ' + this.mainKeyword,
        this.mainKeyword + ' 2024',
        'تعلم ' + this.mainKeyword,
        this.mainKeyword + ' مجاني',
        'دورة ' + this.mainKeyword,
        this.mainKeyword + ' احترافي'
      ];
      this.keywords = suggestions.map(kw => ({
        keyword: kw,
        volume: Math.floor(Math.random() * 50000).toLocaleString(),
        difficulty: difficulties[Math.floor(Math.random() * 3)],
        selected: false
      }));
    },
    addManualKeywords() {
      const newKws = this.manualKeywords.split('\n').filter(k => k.trim());
      newKws.forEach(kw => {
        this.keywords.push({ keyword: kw.trim(), volume: 'غير معروف', difficulty: 'متوسط', selected: true });
      });
      this.manualKeywords = '';
    },
    saveKeywords() {
      localStorage.setItem('keywordAnalysis', JSON.stringify(this.selectedKeywords));
      alert('تم الحفظ!');
    },
    exportCSV() {
      const csv = 'الكلمة,حجم البحث,الصعوبة\n' + this.selectedKeywords.map(k => `${k.keyword},${k.volume},${k.difficulty}`).join('\n');
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'keywords.csv';
      link.click();
    }
  }
}
</script>
