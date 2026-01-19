<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">نموذج العرض التجاري B2B</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">اسم شركتك</label>
            <input v-model="proposal.yourCompany" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="شركتك">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الشركة المستهدفة</label>
            <input v-model="proposal.clientCompany" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="العميل">
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عنوان العرض</label>
          <input v-model="proposal.title" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="عرض خدمات التسويق الرقمي">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">ملخص تنفيذي</label>
          <textarea v-model="proposal.summary" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="3" placeholder="نظرة عامة على العرض..."></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">التحديات التي نحلها</label>
          <textarea v-model="proposal.challenges" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="2" placeholder="المشاكل التي يواجهها العميل..."></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الحلول المقترحة</label>
          <div v-for="(sol, i) in proposal.solutions" :key="i" class="flex gap-2 mb-2">
            <input v-model="proposal.solutions[i]" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="الحل">
            <button @click="proposal.solutions.splice(i, 1)" class="text-red-500 hover:text-red-700">✕</button>
          </div>
          <button @click="proposal.solutions.push('')" class="text-blue-600 text-sm font-medium">+ إضافة حل</button>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">التسعير</label>
          <div v-for="(item, i) in proposal.pricing" :key="i" class="flex gap-2 mb-2">
            <input v-model="item.service" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="الخدمة">
            <input v-model.number="item.price" type="number" class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="السعر">
          </div>
          <button @click="proposal.pricing.push({ service: '', price: 0 })" class="text-blue-600 text-sm font-medium">+ إضافة بند</button>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">معاينة العرض</h3>
        <div class="bg-white rounded-xl p-6 shadow space-y-4 max-h-[600px] overflow-y-auto">
          <div class="text-center border-b pb-4">
            <h4 class="text-2xl font-bold text-indigo-800">{{ proposal.title || 'عنوان العرض' }}</h4>
            <p class="text-gray-600">من {{ proposal.yourCompany || '...' }} إلى {{ proposal.clientCompany || '...' }}</p>
            <p class="text-sm text-gray-500">{{ today }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-800 mb-2">الملخص التنفيذي</h5>
            <p class="text-sm text-gray-600">{{ proposal.summary || 'لم يتم إدخال الملخص' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-800 mb-2">التحديات</h5>
            <p class="text-sm text-gray-600">{{ proposal.challenges || 'لم يتم تحديد التحديات' }}</p>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-800 mb-2">الحلول</h5>
            <ul class="text-sm text-gray-600 space-y-1">
              <li v-for="(s, i) in proposal.solutions.filter(x => x)" :key="i" class="flex items-center gap-2">
                <span class="text-green-500">✓</span> {{ s }}
              </li>
            </ul>
          </div>
          
          <div>
            <h5 class="font-semibold text-gray-800 mb-2">التسعير</h5>
            <table class="w-full text-sm">
              <tr v-for="(item, i) in proposal.pricing.filter(x => x.service)" :key="i" class="border-b">
                <td class="py-2">{{ item.service }}</td>
                <td class="py-2 text-left font-medium">{{ formatNumber(item.price) }} ريال</td>
              </tr>
              <tr class="font-bold text-indigo-800">
                <td class="py-2">الإجمالي</td>
                <td class="py-2 text-left">{{ formatNumber(totalPrice) }} ريال</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveProposal" class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير PDF</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'B2BProposalTemplate',
  data() {
    return {
      proposal: {
        yourCompany: '',
        clientCompany: '',
        title: '',
        summary: '',
        challenges: '',
        solutions: [''],
        pricing: [{ service: '', price: 0 }]
      }
    }
  },
  computed: {
    today() { return new Date().toLocaleDateString('ar-SA'); },
    totalPrice() { return this.proposal.pricing.reduce((sum, item) => sum + (item.price || 0), 0); }
  },
  methods: {
    formatNumber(n) { return new Intl.NumberFormat('ar-SA').format(n); },
    saveProposal() {
      localStorage.setItem('b2bProposal', JSON.stringify(this.proposal));
      alert('تم حفظ العرض!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
