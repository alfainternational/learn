<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">دليل الهوية البصرية</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">اسم العلامة التجارية</label>
          <input v-model="brand.name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="اسم العلامة">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الشعار النصي (Tagline)</label>
          <input v-model="brand.tagline" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="شعارك التسويقي">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الألوان الأساسية</label>
          <div class="flex gap-4">
            <div v-for="(color, i) in brand.primaryColors" :key="i" class="flex items-center gap-2">
              <input type="color" v-model="brand.primaryColors[i]" class="w-12 h-12 rounded cursor-pointer">
              <input type="text" v-model="brand.primaryColors[i]" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
            </div>
            <button @click="addPrimaryColor" class="w-12 h-12 border-2 border-dashed border-gray-300 rounded flex items-center justify-center text-gray-400 hover:border-gray-400">+</button>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الألوان الثانوية</label>
          <div class="flex gap-4">
            <div v-for="(color, i) in brand.secondaryColors" :key="i" class="flex items-center gap-2">
              <input type="color" v-model="brand.secondaryColors[i]" class="w-12 h-12 rounded cursor-pointer">
            </div>
            <button @click="addSecondaryColor" class="w-12 h-12 border-2 border-dashed border-gray-300 rounded flex items-center justify-center text-gray-400 hover:border-gray-400">+</button>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الخط الرئيسي</label>
          <select v-model="brand.primaryFont" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="Cairo">Cairo</option>
            <option value="Tajawal">Tajawal</option>
            <option value="Almarai">Almarai</option>
            <option value="Noto Kufi Arabic">Noto Kufi Arabic</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">القيم الأساسية</label>
          <div class="flex flex-wrap gap-2">
            <input v-model="valueInput" @keyup.enter="addValue" type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="أضف قيمة واضغط Enter">
          </div>
          <div class="flex flex-wrap gap-2 mt-2">
            <span v-for="(v, i) in brand.values" :key="i" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center gap-1">
              {{ v }}
              <button @click="removeValue(i)" class="text-blue-600 hover:text-blue-800">&times;</button>
            </span>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">معاينة الهوية</h3>
        <div class="bg-white rounded-xl p-6 shadow" :style="{ fontFamily: brand.primaryFont }">
          <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-lg flex items-center justify-center text-white text-2xl font-bold" :style="{ backgroundColor: brand.primaryColors[0] }">
              {{ brand.name ? brand.name[0] : 'ع' }}
            </div>
            <div>
              <h4 class="text-2xl font-bold" :style="{ color: brand.primaryColors[0] }">{{ brand.name || 'اسم العلامة' }}</h4>
              <p class="text-gray-600">{{ brand.tagline || 'الشعار التسويقي' }}</p>
            </div>
          </div>
          
          <div class="mb-6">
            <h5 class="text-sm font-medium text-gray-500 mb-2">لوحة الألوان</h5>
            <div class="flex gap-2">
              <div v-for="c in [...brand.primaryColors, ...brand.secondaryColors]" :key="c" class="w-12 h-12 rounded-lg shadow" :style="{ backgroundColor: c }"></div>
            </div>
          </div>
          
          <div class="mb-6">
            <h5 class="text-sm font-medium text-gray-500 mb-2">الخط</h5>
            <p class="text-3xl font-bold" :style="{ fontFamily: brand.primaryFont }">أبجد هوز حطي</p>
            <p class="text-xl" :style="{ fontFamily: brand.primaryFont }">1234567890</p>
          </div>
          
          <div>
            <h5 class="text-sm font-medium text-gray-500 mb-2">القيم</h5>
            <div class="flex flex-wrap gap-2">
              <span v-for="v in brand.values" :key="v" class="px-3 py-1 rounded-full text-sm text-white" :style="{ backgroundColor: brand.primaryColors[0] }">{{ v }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveGuide" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير PDF</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BrandIdentityGuide',
  data() {
    return {
      brand: {
        name: '',
        tagline: '',
        primaryColors: ['#3B82F6', '#1E40AF'],
        secondaryColors: ['#F59E0B', '#10B981'],
        primaryFont: 'Cairo',
        values: []
      },
      valueInput: ''
    }
  },
  methods: {
    addPrimaryColor() { this.brand.primaryColors.push('#000000'); },
    addSecondaryColor() { this.brand.secondaryColors.push('#888888'); },
    addValue() {
      if (this.valueInput.trim()) {
        this.brand.values.push(this.valueInput.trim());
        this.valueInput = '';
      }
    },
    removeValue(i) { this.brand.values.splice(i, 1); },
    saveGuide() {
      localStorage.setItem('brandIdentityGuide', JSON.stringify(this.brand));
      alert('تم حفظ دليل الهوية!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
