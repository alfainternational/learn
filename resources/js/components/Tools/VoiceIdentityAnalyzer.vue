<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">محلل الهوية الصوتية للعلامة التجارية</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">اسم العلامة التجارية</label>
          <input v-model="brand.name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اسم العلامة">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3">نبرة الصوت</label>
          <div class="space-y-3">
            <div v-for="tone in tones" :key="tone.key" class="flex items-center justify-between">
              <span class="text-sm text-gray-600">{{ tone.label }}</span>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">{{ tone.min }}</span>
                <input type="range" v-model.number="brand.tones[tone.key]" min="0" max="100" class="w-32">
                <span class="text-xs text-gray-400">{{ tone.max }}</span>
                <span class="w-8 text-center text-sm font-medium">{{ brand.tones[tone.key] }}%</span>
              </div>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الكلمات المفتاحية للعلامة</label>
          <input v-model="keywordInput" @keyup.enter="addKeyword" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اضغط Enter لإضافة كلمة">
          <div class="flex flex-wrap gap-2 mt-2">
            <span v-for="(kw, i) in brand.keywords" :key="i" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center gap-1">
              {{ kw }}
              <button @click="removeKeyword(i)" class="text-blue-600 hover:text-blue-800">&times;</button>
            </span>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الشخصية</label>
          <div class="grid grid-cols-3 gap-2">
            <label v-for="p in personalities" :key="p" class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200">
              <input type="checkbox" v-model="brand.personality" :value="p" class="rounded">
              <span class="text-sm">{{ p }}</span>
            </label>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">ملخص الهوية الصوتية</h3>
        <div class="bg-white rounded-xl p-6 shadow space-y-4">
          <div class="text-center border-b pb-4">
            <h4 class="text-2xl font-bold text-purple-800">{{ brand.name || 'علامتك التجارية' }}</h4>
          </div>
          <div>
            <h5 class="font-medium text-gray-700 mb-2">نبرة الصوت</h5>
            <div class="space-y-2">
              <div v-for="tone in tones" :key="tone.key" class="flex items-center gap-2">
                <span class="text-xs w-20">{{ tone.label }}</span>
                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-purple-500" :style="{ width: brand.tones[tone.key] + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
          <div>
            <h5 class="font-medium text-gray-700 mb-2">الشخصية</h5>
            <div class="flex flex-wrap gap-1">
              <span v-for="p in brand.personality" :key="p" class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded">{{ p }}</span>
            </div>
          </div>
          <div>
            <h5 class="font-medium text-gray-700 mb-2">الكلمات المفتاحية</h5>
            <p class="text-sm text-gray-600">{{ brand.keywords.join(' • ') || 'لم تحدد بعد' }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveIdentity" class="flex-1 bg-purple-600 text-white py-3 px-6 rounded-lg hover:bg-purple-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VoiceIdentityAnalyzer',
  data() {
    return {
      brand: {
        name: '',
        tones: { formal: 50, friendly: 50, professional: 50, playful: 50 },
        keywords: [],
        personality: []
      },
      keywordInput: '',
      tones: [
        { key: 'formal', label: 'رسمي', min: 'غير رسمي', max: 'رسمي جداً' },
        { key: 'friendly', label: 'ودود', min: 'محايد', max: 'ودود جداً' },
        { key: 'professional', label: 'احترافي', min: 'بسيط', max: 'احترافي' },
        { key: 'playful', label: 'مرح', min: 'جاد', max: 'مرح' }
      ],
      personalities: ['موثوق', 'مبتكر', 'ودود', 'محترف', 'شاب', 'تقليدي', 'جريء', 'هادئ']
    }
  },
  methods: {
    addKeyword() {
      if (this.keywordInput.trim()) {
        this.brand.keywords.push(this.keywordInput.trim());
        this.keywordInput = '';
      }
    },
    removeKeyword(i) { this.brand.keywords.splice(i, 1); },
    saveIdentity() {
      localStorage.setItem('voiceIdentity', JSON.stringify(this.brand));
      alert('تم حفظ الهوية الصوتية!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
