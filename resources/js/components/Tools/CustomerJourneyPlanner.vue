<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">مخطط رحلة العميل</h2>
    
    <div class="overflow-x-auto">
      <div class="flex gap-4 min-w-max pb-4">
        <div v-for="(stage, index) in stages" :key="index" class="w-72 flex-shrink-0">
          <div class="bg-gradient-to-b from-blue-500 to-blue-600 text-white p-4 rounded-t-xl">
            <h3 class="font-bold text-lg">{{ stage.name }}</h3>
            <p class="text-blue-100 text-sm">{{ stage.description }}</p>
          </div>
          <div class="bg-gray-50 p-4 space-y-4 rounded-b-xl border border-t-0 border-gray-200">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">نقاط التواصل</label>
              <textarea v-model="journey[index].touchpoints" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" rows="2" placeholder="مثال: موقع الويب، إعلانات"></textarea>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">مشاعر العميل</label>
              <select v-model="journey[index].emotion" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="happy">😊 سعيد</option>
                <option value="neutral">😐 محايد</option>
                <option value="confused">😕 مرتبك</option>
                <option value="frustrated">😤 محبط</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">الإجراءات</label>
              <textarea v-model="journey[index].actions" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" rows="2" placeholder="ماذا يفعل العميل؟"></textarea>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">نقاط الألم</label>
              <textarea v-model="journey[index].painPoints" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" rows="2" placeholder="ما المشاكل؟"></textarea>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">الفرص</label>
              <textarea v-model="journey[index].opportunities" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" rows="2" placeholder="كيف نحسن التجربة؟"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveJourney" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">حفظ الرحلة</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير PDF</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomerJourneyPlanner',
  data() {
    return {
      stages: [
        { name: 'الوعي', description: 'اكتشاف المنتج/الخدمة' },
        { name: 'الاهتمام', description: 'البحث والمقارنة' },
        { name: 'القرار', description: 'اتخاذ قرار الشراء' },
        { name: 'الشراء', description: 'إتمام عملية الشراء' },
        { name: 'الولاء', description: 'التكرار والتوصية' }
      ],
      journey: Array(5).fill(null).map(() => ({
        touchpoints: '',
        emotion: 'neutral',
        actions: '',
        painPoints: '',
        opportunities: ''
      }))
    }
  },
  methods: {
    saveJourney() {
      localStorage.setItem('customerJourney', JSON.stringify(this.journey));
      alert('تم حفظ رحلة العميل!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
