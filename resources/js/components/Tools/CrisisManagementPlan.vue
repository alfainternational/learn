<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">خطة إدارة الأزمات</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشركة/العلامة</label>
          <input v-model="plan.company" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اسم الشركة">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">نوع الأزمة</label>
          <select v-model="plan.crisisType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="reputation">أزمة سمعة</option>
            <option value="product">مشكلة منتج</option>
            <option value="service">مشكلة خدمة</option>
            <option value="social">أزمة سوشيال ميديا</option>
            <option value="legal">مشكلة قانونية</option>
            <option value="other">أخرى</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الخطورة</label>
          <div class="flex gap-2">
            <button v-for="level in [1,2,3,4,5]" :key="level" @click="plan.severity = level" :class="plan.severity >= level ? severityColors[level] : 'bg-gray-200'" class="flex-1 py-2 rounded-lg font-medium transition">{{ level }}</button>
          </div>
          <p class="text-sm text-gray-500 mt-1">{{ severityLabels[plan.severity] }}</p>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">وصف الأزمة</label>
          <textarea v-model="plan.description" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="3" placeholder="صف الأزمة بالتفصيل..."></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">فريق الأزمات</label>
          <div v-for="(member, i) in plan.team" :key="i" class="flex gap-2 mb-2">
            <input v-model="member.name" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="الاسم">
            <input v-model="member.role" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="الدور">
            <input v-model="member.phone" type="text" class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="الهاتف">
          </div>
          <button @click="addTeamMember" class="text-blue-600 text-sm font-medium">+ إضافة عضو</button>
        </div>
      </div>
      
      <div class="space-y-4">
        <div class="bg-gradient-to-br from-red-50 to-orange-100 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">خطة الاستجابة</h3>
          
          <div class="space-y-4">
            <div v-for="(step, i) in responseSteps" :key="i" class="bg-white p-4 rounded-lg">
              <div class="flex items-center gap-3 mb-2">
                <span class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center font-bold">{{ i + 1 }}</span>
                <h4 class="font-semibold text-gray-800">{{ step.title }}</h4>
                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ step.timeframe }}</span>
              </div>
              <textarea v-model="plan.responses[i]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" rows="2" :placeholder="step.placeholder"></textarea>
            </div>
          </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <h4 class="font-medium text-yellow-800 mb-2">⚠️ نصائح مهمة</h4>
          <ul class="text-sm text-yellow-700 space-y-1">
            <li>• لا تتجاهل الأزمة أبداً</li>
            <li>• تصرف بسرعة ولكن بحكمة</li>
            <li>• كن صادقاً وشفافاً</li>
            <li>• وثّق كل شيء</li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="savePlan" class="flex-1 bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 transition font-medium">حفظ الخطة</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير PDF</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CrisisManagementPlan',
  data() {
    return {
      plan: {
        company: '',
        crisisType: 'reputation',
        severity: 3,
        description: '',
        team: [{ name: '', role: '', phone: '' }],
        responses: ['', '', '', '']
      },
      severityColors: { 1: 'bg-green-500 text-white', 2: 'bg-yellow-400', 3: 'bg-orange-500 text-white', 4: 'bg-red-500 text-white', 5: 'bg-red-700 text-white' },
      severityLabels: { 1: 'منخفض جداً', 2: 'منخفض', 3: 'متوسط', 4: 'مرتفع', 5: 'حرج' },
      responseSteps: [
        { title: 'الاستجابة الفورية', timeframe: '0-1 ساعة', placeholder: 'ماذا ستفعل فوراً؟' },
        { title: 'التواصل الداخلي', timeframe: '1-2 ساعة', placeholder: 'كيف ستبلغ الفريق؟' },
        { title: 'البيان الرسمي', timeframe: '2-4 ساعات', placeholder: 'ما الرسالة للجمهور؟' },
        { title: 'المتابعة', timeframe: '24-48 ساعة', placeholder: 'خطوات المتابعة' }
      ]
    }
  },
  methods: {
    addTeamMember() { this.plan.team.push({ name: '', role: '', phone: '' }); },
    savePlan() {
      localStorage.setItem('crisisPlan', JSON.stringify(this.plan));
      alert('تم حفظ خطة الأزمات!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
