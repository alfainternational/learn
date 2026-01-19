<template>
  <div class="bg-white rounded-xl shadow-lg p-6 rtl" dir="rtl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">التقويم التسويقي</h2>
    
    <div class="flex justify-between items-center mb-6">
      <div class="flex gap-2">
        <button @click="prevMonth" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">السابق</button>
        <button @click="nextMonth" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">التالي</button>
      </div>
      <h3 class="text-xl font-semibold">{{ monthName }} {{ year }}</h3>
      <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ إضافة محتوى</button>
    </div>
    
    <div class="grid grid-cols-7 gap-1">
      <div v-for="day in weekDays" :key="day" class="p-2 text-center font-semibold text-gray-600 bg-gray-100">{{ day }}</div>
      <div v-for="(day, i) in calendarDays" :key="i" class="min-h-24 border border-gray-200 p-1" :class="day ? 'bg-white' : 'bg-gray-50'">
        <div v-if="day" class="h-full">
          <span class="text-sm font-medium">{{ day }}</span>
          <div class="mt-1 space-y-1">
            <div v-for="event in getEventsForDay(day)" :key="event.id" :class="getEventClass(event.type)" class="text-xs p-1 rounded truncate cursor-pointer" @click="editEvent(event)">
              {{ event.title }}
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Add Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">إضافة محتوى</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
            <input v-model="newEvent.title" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
            <input v-model.number="newEvent.day" type="number" min="1" max="31" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
            <select v-model="newEvent.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
              <option value="post">منشور</option>
              <option value="video">فيديو</option>
              <option value="story">ستوري</option>
              <option value="email">بريد</option>
              <option value="ad">إعلان</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">القناة</label>
            <input v-model="newEvent.channel" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="فيسبوك، انستغرام...">
          </div>
        </div>
        <div class="mt-6 flex gap-4">
          <button @click="addEvent" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">إضافة</button>
          <button @click="showAddModal = false" class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300">إلغاء</button>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <button @click="saveCalendar" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-medium">حفظ</button>
      <button @click="exportPDF" class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition font-medium">تصدير</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContentCalendar',
  data() {
    return {
      currentDate: new Date(),
      weekDays: ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
      events: [],
      showAddModal: false,
      newEvent: { title: '', day: 1, type: 'post', channel: '' }
    }
  },
  computed: {
    year() { return this.currentDate.getFullYear(); },
    month() { return this.currentDate.getMonth(); },
    monthName() {
      const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
      return months[this.month];
    },
    calendarDays() {
      const firstDay = new Date(this.year, this.month, 1).getDay();
      const daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
      const days = [];
      for (let i = 0; i < firstDay; i++) days.push(null);
      for (let i = 1; i <= daysInMonth; i++) days.push(i);
      return days;
    }
  },
  methods: {
    prevMonth() { this.currentDate = new Date(this.year, this.month - 1, 1); },
    nextMonth() { this.currentDate = new Date(this.year, this.month + 1, 1); },
    getEventsForDay(day) { return this.events.filter(e => e.day === day && e.month === this.month && e.year === this.year); },
    getEventClass(type) {
      const classes = { post: 'bg-blue-100 text-blue-800', video: 'bg-red-100 text-red-800', story: 'bg-purple-100 text-purple-800', email: 'bg-green-100 text-green-800', ad: 'bg-yellow-100 text-yellow-800' };
      return classes[type] || 'bg-gray-100 text-gray-800';
    },
    addEvent() {
      this.events.push({ ...this.newEvent, id: Date.now(), month: this.month, year: this.year });
      this.showAddModal = false;
      this.newEvent = { title: '', day: 1, type: 'post', channel: '' };
    },
    editEvent(event) { alert('تحرير: ' + event.title); },
    saveCalendar() {
      localStorage.setItem('contentCalendar', JSON.stringify(this.events));
      alert('تم الحفظ!');
    },
    exportPDF() { window.print(); }
  }
}
</script>
