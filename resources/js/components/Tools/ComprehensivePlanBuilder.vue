<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">مخطط الخطة التسويقية الشاملة</h2>
    
    <div class="mb-6">
      <div class="flex gap-2 overflow-x-auto pb-2">
        <button v-for="(section, i) in sections" :key="i" @click="activeSection = i" :class="activeSection === i ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg font-medium whitespace-nowrap transition">
          {{ section.title }}
        </button>
      </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <component :is="'div'" v-for="(field, i) in currentFields" :key="i">
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ field.label }}</label>
          <textarea v-if="field.type === 'textarea'" v-model="plan[field.key]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" :rows="field.rows || 3" :placeholder="field.placeholder"></textarea>
          <input v-else-if="field.type === 'number'" v-model.number="plan[field.key]" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" :placeholder="field.placeholder">
          <select v-else-if="field.type === 'select'" v-model="plan[field.key]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option v-for="opt in field.options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
          <input v-else v-model="plan[field.key]" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" :placeholder="field.placeholder">
        </component>
        
        <div class="flex gap-2 mt-4">
          <button v-if="activeSection > 0" @click="activeSection--" class="flex-1 bg-gray-200 py-3 rounded-lg hover:bg-gray-300 transition">السابق</button>
          <button v-if="activeSection < sections.length - 1" @click="activeSection++" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">التالي</button>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">ملخص الخطة</h3>
        <div class="bg-white rounded-xl p-4 shadow max-h-[500px] overflow-y-auto space-y-4">
          <div class="border-b pb-3">
            <h4 class="font-bold text-lg text-indigo-800">{{ plan.businessName || 'اسم المشروع' }}</h4>
            <p class="text-sm text-gray-600">{{ plan.industry || 'المجال' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-700 text-sm mb-1">الرؤية</h5>
            <p class="text-sm text-gray-600">{{ plan.vision || '-' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-700 text-sm mb-1">الأهداف</h5>
            <p class="text-sm text-gray-600">{{ plan.goals || '-' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-700 text-sm mb-1">الجمهور المستهدف</h5>
            <p class="text-sm text-gray-600">{{ plan.targetAudience || '-' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-700 text-sm mb-1">القنوات</h5>
            <p class="text-sm text-gray-600">{{ plan.channels || '-' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-700 text-sm mb-1">الميزانية</h5>
            <p class="text-sm text-gray-600">{{ plan.budget ? formatNumber(plan.budget) + ' ريال' : '-' }}</p>
          </div>
          
          <div class="pt-3 border-t">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">نسبة الاكتمال</span>
              <span class="font-bold text-indigo-600">{{ completionPercentage }}%</span>
            </div>
            <div class="h-2 bg-gray-200 rounded-full mt-2 overflow-hidden">
              <div class="h-full bg-indigo-600 transition-all" :style="{ width: completionPercentage + '%' }"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="savePlan" class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition font-medium">حفظ الخطة</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير PDF</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ComprehensivePlanBuilder',
  data() {
    return {
      activeSection: 0,
      plan: {
        businessName: '', industry: '', vision: '', mission: '', goals: '',
        targetAudience: '', demographics: '', psychographics: '',
        competitors: '', swot: '', usp: '',
        channels: '', contentStrategy: '', campaignIdeas: '',
        budget: 0, timeline: '', kpis: ''
      },
      sections: [
        {
          title: 'المعلومات الأساسية',
          fields: [
            { key: 'businessName', label: 'اسم المشروع', type: 'text', placeholder: 'اسم شركتك أو مشروعك' },
            { key: 'industry', label: 'المجال', type: 'text', placeholder: 'مثال: تجارة إلكترونية' },
            { key: 'vision', label: 'الرؤية', type: 'textarea', placeholder: 'ما الذي تسعى لتحقيقه؟', rows: 2 },
            { key: 'mission', label: 'الرسالة', type: 'textarea', placeholder: 'كيف ستحقق رؤيتك؟', rows: 2 }
          ]
        },
        {
          title: 'الأهداف',
          fields: [
            { key: 'goals', label: 'الأهداف التسويقية', type: 'textarea', placeholder: 'حدد أهدافك بوضوح (SMART)', rows: 4 },
            { key: 'kpis', label: 'مؤشرات الأداء', type: 'textarea', placeholder: 'كيف ستقيس النجاح؟', rows: 3 }
          ]
        },
        {
          title: 'الجمهور',
          fields: [
            { key: 'targetAudience', label: 'الجمهور المستهدف', type: 'textarea', placeholder: 'من هم عملاؤك المثاليون؟', rows: 3 },
            { key: 'demographics', label: 'الديموغرافيا', type: 'textarea', placeholder: 'العمر، الجنس، الموقع، الدخل...', rows: 2 },
            { key: 'psychographics', label: 'السيكوغرافيا', type: 'textarea', placeholder: 'الاهتمامات، القيم، السلوكيات...', rows: 2 }
          ]
        },
        {
          title: 'التحليل التنافسي',
          fields: [
            { key: 'competitors', label: 'المنافسون', type: 'textarea', placeholder: 'من هم منافسوك الرئيسيون؟', rows: 3 },
            { key: 'swot', label: 'تحليل SWOT', type: 'textarea', placeholder: 'نقاط القوة، الضعف، الفرص، التهديدات', rows: 4 },
            { key: 'usp', label: 'القيمة الفريدة', type: 'textarea', placeholder: 'ما الذي يميزك؟', rows: 2 }
          ]
        },
        {
          title: 'الاستراتيجية',
          fields: [
            { key: 'channels', label: 'القنوات التسويقية', type: 'textarea', placeholder: 'ما القنوات التي ستستخدمها؟', rows: 2 },
            { key: 'contentStrategy', label: 'استراتيجية المحتوى', type: 'textarea', placeholder: 'نوع المحتوى وتكراره', rows: 3 },
            { key: 'campaignIdeas', label: 'أفكار الحملات', type: 'textarea', placeholder: 'حملات تسويقية مقترحة', rows: 3 }
          ]
        },
        {
          title: 'الميزانية والجدول',
          fields: [
            { key: 'budget', label: 'الميزانية الشهرية (ريال)', type: 'number', placeholder: '10000' },
            { key: 'timeline', label: 'الجدول الزمني', type: 'textarea', placeholder: 'المراحل والتواريخ المهمة', rows: 3 }
          ]
        }
      ]
    }
  },
  computed: {
    currentFields() { return this.sections[this.activeSection].fields; },
    completionPercentage() {
      const fields = Object.values(this.plan);
      const filled = fields.filter(v => v && v !== 0).length;
      return Math.round((filled / fields.length) * 100);
    }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(n); },
    savePlan() {
      localStorage.setItem('comprehensivePlan', JSON.stringify(this.plan));
      alert('تم حفظ الخطة!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
