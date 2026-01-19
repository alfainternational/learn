<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">مولد الأفكار التسويقية بـ AI</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">مجال العمل</label>
          <input v-model="industry" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="مثال: التجارة الإلكترونية">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">المنتج/الخدمة</label>
          <input v-model="product" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="مثال: ملابس رياضية">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الجمهور المستهدف</label>
          <input v-model="audience" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="مثال: شباب 18-35">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">نوع المحتوى</label>
          <select v-model="contentType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="social">منشورات سوشيال ميديا</option>
            <option value="blog">مقالات مدونة</option>
            <option value="video">أفكار فيديو</option>
            <option value="campaign">حملات تسويقية</option>
            <option value="email">حملات بريد إلكتروني</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الهدف</label>
          <select v-model="goal" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="awareness">زيادة الوعي</option>
            <option value="engagement">زيادة التفاعل</option>
            <option value="leads">جمع العملاء المحتملين</option>
            <option value="sales">زيادة المبيعات</option>
            <option value="loyalty">بناء الولاء</option>
          </select>
        </div>
        <button @click="generateIdeas" :disabled="loading" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 transition font-medium disabled:opacity-50">
          {{ loading ? 'جاري التوليد...' : '✨ توليد الأفكار' }}
        </button>
      </div>
      
      <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">الأفكار المولدة</h3>
        <div v-if="ideas.length === 0" class="text-center text-gray-500 py-12">
          <span class="text-4xl">💡</span>
          <p class="mt-2">أدخل البيانات واضغط توليد</p>
        </div>
        <div v-else class="space-y-3 max-h-96 overflow-y-auto">
          <div v-for="(idea, i) in ideas" :key="i" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition cursor-pointer" @click="selectIdea(idea)">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-semibold text-gray-800">{{ idea.title }}</h4>
                <p class="text-sm text-gray-600 mt-1">{{ idea.description }}</p>
                <div class="flex gap-2 mt-2">
                  <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ idea.type }}</span>
                  <span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded">{{ idea.difficulty }}</span>
                </div>
              </div>
              <button @click.stop="saveIdea(idea)" class="text-gray-400 hover:text-yellow-500">⭐</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveAllIdeas" class="flex-1 bg-purple-600 text-white py-3 px-6 rounded-lg hover:bg-purple-700 transition font-medium">حفظ الكل</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AIIdeaGenerator',
  data() {
    return {
      industry: '',
      product: '',
      audience: '',
      contentType: 'social',
      goal: 'engagement',
      loading: false,
      ideas: []
    }
  },
  methods: {
    generateIdeas() {
      this.loading = true;
      setTimeout(() => {
        const templates = {
          social: [
            { title: 'تحدي الـ 30 يوم', description: `أطلق تحدي لجمهورك متعلق بـ ${this.product}`, type: 'تفاعلي', difficulty: 'سهل' },
            { title: 'قبل وبعد', description: 'اعرض نتائج استخدام منتجك بصور مقارنة', type: 'بصري', difficulty: 'سهل' },
            { title: 'سؤال وجواب مباشر', description: 'بث مباشر للإجابة على أسئلة المتابعين', type: 'تفاعلي', difficulty: 'متوسط' },
            { title: 'خلف الكواليس', description: 'اعرض كيف تصنع منتجاتك أو تقدم خدماتك', type: 'توعوي', difficulty: 'سهل' },
            { title: 'مسابقة مع جوائز', description: `مسابقة للفوز بـ ${this.product} مجاناً`, type: 'تفاعلي', difficulty: 'متوسط' }
          ],
          video: [
            { title: 'فيديو تعليمي', description: `كيفية استخدام ${this.product} بشكل صحيح`, type: 'تعليمي', difficulty: 'متوسط' },
            { title: 'مراجعة عملاء', description: 'شهادات حقيقية من عملاء سعداء', type: 'اجتماعي', difficulty: 'متوسط' },
            { title: 'يوم في حياة', description: 'يوم كامل مع فريق العمل', type: 'توعوي', difficulty: 'صعب' }
          ],
          campaign: [
            { title: 'حملة موسمية', description: 'استغل المناسبات والأعياد', type: 'ترويجي', difficulty: 'متوسط' },
            { title: 'برنامج إحالة', description: 'كافئ العملاء على جلب أصدقائهم', type: 'نمو', difficulty: 'متوسط' },
            { title: 'عرض حصري', description: 'خصم محدود لفترة قصيرة', type: 'ترويجي', difficulty: 'سهل' }
          ]
        };
        this.ideas = templates[this.contentType] || templates.social;
        this.loading = false;
      }, 1500);
    },
    selectIdea(idea) { alert('تم اختيار: ' + idea.title); },
    saveIdea(idea) { alert('تم حفظ: ' + idea.title); },
    saveAllIdeas() {
      localStorage.setItem('aiIdeas', JSON.stringify(this.ideas));
      alert('تم حفظ جميع الأفكار!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
