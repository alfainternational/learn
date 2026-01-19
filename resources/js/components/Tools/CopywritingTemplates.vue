<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">قوالب النصوص التسويقية</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">نوع القالب</label>
          <select v-model="selectedTemplate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option v-for="t in templates" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">اسم المنتج/الخدمة</label>
          <input v-model="productName" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="منتجك">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الفائدة الرئيسية</label>
          <input v-model="mainBenefit" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="ما الفائدة؟">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الجمهور المستهدف</label>
          <input v-model="targetAudience" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="من تستهدف؟">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الدعوة للإجراء (CTA)</label>
          <input v-model="cta" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اشترِ الآن">
        </div>
        <button @click="generateCopy" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">توليد النص</button>
      </div>
      
      <div class="lg:col-span-2 space-y-4">
        <div class="bg-gray-50 rounded-xl p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800">النص الناتج</h3>
            <button @click="copyToClipboard" class="text-blue-600 hover:text-blue-800 text-sm font-medium">نسخ</button>
          </div>
          <textarea v-model="generatedCopy" class="w-full h-64 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="النص سيظهر هنا..."></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-medium text-yellow-800 mb-2">نصائح</h4>
            <ul class="text-sm text-yellow-700 space-y-1">
              <li>• استخدم أرقام محددة</li>
              <li>• ركز على الفوائد لا المميزات</li>
              <li>• اخلق إحساس بالإلحاح</li>
            </ul>
          </div>
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-medium text-green-800 mb-2">كلمات قوية</h4>
            <div class="flex flex-wrap gap-1">
              <span v-for="w in powerWords" :key="w" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-green-200" @click="insertWord(w)">{{ w }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveCopy" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CopywritingTemplates',
  data() {
    return {
      selectedTemplate: 'aida',
      productName: '',
      mainBenefit: '',
      targetAudience: '',
      cta: '',
      generatedCopy: '',
      templates: [
        { id: 'aida', name: 'AIDA - انتباه، اهتمام، رغبة، إجراء' },
        { id: 'pas', name: 'PAS - مشكلة، تحريك، حل' },
        { id: 'fab', name: 'FAB - ميزة، فائدة، منفعة' },
        { id: 'social', name: 'منشور سوشيال ميديا' },
        { id: 'email', name: 'بريد إلكتروني تسويقي' }
      ],
      powerWords: ['مجاني', 'حصري', 'محدود', 'الآن', 'اكتشف', 'سري', 'مضمون', 'فوري', 'جديد', 'أنت']
    }
  },
  methods: {
    generateCopy() {
      const templates = {
        aida: `🎯 انتباه!\nهل أنت من ${this.targetAudience}؟\n\n💡 اهتمام:\n${this.productName} يقدم لك ${this.mainBenefit}\n\n❤️ رغبة:\nتخيل حياتك بعد استخدام ${this.productName}...\n\n👉 إجراء:\n${this.cta}`,
        pas: `😫 المشكلة:\nهل تعاني من...؟\n\n😰 التحريك:\nهذه المشكلة تؤثر على ${this.targetAudience} يومياً...\n\n✅ الحل:\n${this.productName} - ${this.mainBenefit}\n\n${this.cta}`,
        fab: `✨ الميزة:\n${this.productName}\n\n🎁 الفائدة:\n${this.mainBenefit}\n\n🏆 المنفعة:\nستحصل على نتائج مذهلة!\n\n${this.cta}`,
        social: `🔥 ${this.productName}\n\n${this.mainBenefit}\n\nمثالي لـ ${this.targetAudience}\n\n👇 ${this.cta}\n\n#تسويق #${this.productName.replace(/\s/g, '')}`,
        email: `الموضوع: ${this.mainBenefit} - خاص بـ ${this.targetAudience}\n\nمرحباً،\n\nهل تبحث عن ${this.mainBenefit}؟\n\n${this.productName} هو الحل الأمثل لك.\n\n${this.cta}\n\nمع أطيب التحيات`
      };
      this.generatedCopy = templates[this.selectedTemplate] || '';
    },
    copyToClipboard() {
      navigator.clipboard.writeText(this.generatedCopy);
      alert('تم النسخ!');
    },
    insertWord(w) { this.generatedCopy += ' ' + w; },
    saveCopy() {
      localStorage.setItem('copywritingTemplate', JSON.stringify({ template: this.selectedTemplate, copy: this.generatedCopy }));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
