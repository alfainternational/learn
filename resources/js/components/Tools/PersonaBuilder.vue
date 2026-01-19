<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">منشئ شخصية العميل</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشخصية</label>
          <input v-model="persona.name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="أحمد المدير">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">العمر</label>
            <input v-model.number="persona.age" type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="35">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الجنس</label>
            <select v-model="persona.gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="male">ذكر</option>
              <option value="female">أنثى</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">المسمى الوظيفي</label>
          <input v-model="persona.jobTitle" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="مدير تسويق">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الدخل الشهري</label>
          <select v-model="persona.income" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="low">أقل من 10,000 ريال</option>
            <option value="medium">10,000 - 25,000 ريال</option>
            <option value="high">25,000 - 50,000 ريال</option>
            <option value="vhigh">أكثر من 50,000 ريال</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الأهداف</label>
          <textarea v-model="persona.goals" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="3" placeholder="ما الذي يسعى لتحقيقه؟"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">التحديات</label>
          <textarea v-model="persona.challenges" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="3" placeholder="ما المشاكل التي يواجهها؟"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">القنوات المفضلة</label>
          <div class="flex flex-wrap gap-2">
            <label v-for="channel in channels" :key="channel" class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200">
              <input type="checkbox" v-model="persona.preferredChannels" :value="channel" class="rounded">
              <span class="text-sm">{{ channel }}</span>
            </label>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">معاينة الشخصية</h3>
        <div class="bg-white rounded-xl p-6 shadow">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 bg-indigo-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
              {{ persona.name ? persona.name[0] : '؟' }}
            </div>
            <div>
              <h4 class="text-xl font-bold text-gray-800">{{ persona.name || 'اسم الشخصية' }}</h4>
              <p class="text-gray-600">{{ persona.jobTitle || 'المسمى الوظيفي' }}</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-50 p-3 rounded-lg">
              <span class="text-xs text-gray-500">العمر</span>
              <p class="font-semibold">{{ persona.age || '-' }} سنة</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <span class="text-xs text-gray-500">الدخل</span>
              <p class="font-semibold">{{ incomeLabel }}</p>
            </div>
          </div>
          <div class="space-y-3">
            <div>
              <span class="text-xs text-gray-500 font-medium">الأهداف</span>
              <p class="text-sm text-gray-700">{{ persona.goals || 'لم يتم تحديدها' }}</p>
            </div>
            <div>
              <span class="text-xs text-gray-500 font-medium">التحديات</span>
              <p class="text-sm text-gray-700">{{ persona.challenges || 'لم يتم تحديدها' }}</p>
            </div>
            <div>
              <span class="text-xs text-gray-500 font-medium">القنوات</span>
              <div class="flex flex-wrap gap-1 mt-1">
                <span v-for="ch in persona.preferredChannels" :key="ch" class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">{{ ch }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="savePersona" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">حفظ الشخصية</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير PDF</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PersonaBuilder',
  data() {
    return {
      persona: {
        name: '',
        age: null,
        gender: 'male',
        jobTitle: '',
        income: 'medium',
        goals: '',
        challenges: '',
        preferredChannels: []
      },
      channels: ['فيسبوك', 'انستغرام', 'تويتر', 'لينكدإن', 'يوتيوب', 'تيك توك', 'البريد الإلكتروني', 'واتساب']
    }
  },
  computed: {
    incomeLabel() {
      const labels = { low: 'منخفض', medium: 'متوسط', high: 'مرتفع', vhigh: 'مرتفع جداً' };
      return labels[this.persona.income] || '-';
    }
  },
  methods: {
    savePersona() {
      localStorage.setItem('personaBuilder', JSON.stringify(this.persona));
      alert('تم حفظ الشخصية!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
