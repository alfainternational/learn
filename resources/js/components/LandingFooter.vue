<template>
  <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
    <div class="container-custom">
      <div class="grid md:grid-cols-4 gap-12 mb-12">
        <!-- Brand -->
        <div class="col-span-1 md:col-span-1">
          <router-link :to="{ name: 'home' }" class="flex items-center gap-2 mb-6">
            <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg flex items-center justify-center text-white font-bold shadow-lg">
              خ
            </div>
            <span class="font-display font-black text-xl text-white">خطّط</span>
          </router-link>
          <p class="text-sm leading-relaxed text-gray-400 mb-6">
            منصتك الذكية لبناء خطط تسويقية متكاملة. نساعدك في تحويل أفكارك إلى استراتيجيات قابلة للتنفيذ ونتائج ملموسة.
          </p>
          <div class="flex items-center gap-4">
            <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary-600 transition-colors">
              <span class="sr-only">Twitter</span>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
            </a>
            <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary-600 transition-colors">
              <span class="sr-only">LinkedIn</span>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path><circle cx="4" cy="4" r="2"></circle></svg>
            </a>
          </div>
        </div>

        <!-- Links -->
        <div>
          <h4 class="text-white font-bold mb-6">المنتج</h4>
          <ul class="space-y-4 text-sm">
            <li><a href="#features" class="hover:text-primary-500 transition-colors">الميزات</a></li>
            <li><a href="#pricing" class="hover:text-primary-500 transition-colors">الأسعار</a></li>
            <li><a href="#how-it-works" class="hover:text-primary-500 transition-colors">كيف يعمل</a></li>
            <li><router-link to="/templates" class="hover:text-primary-500 transition-colors">القوالب</router-link></li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h4 class="text-white font-bold mb-6">الدعم</h4>
          <ul class="space-y-4 text-sm">
            <li><a href="#" class="hover:text-primary-500 transition-colors">مركز المساعدة</a></li>
            <li><a href="#" class="hover:text-primary-500 transition-colors">اتصل بنا</a></li>
            <li><a href="#" class="hover:text-primary-500 transition-colors">الأسئلة الشائعة</a></li>
            <li><a href="#" class="hover:text-primary-500 transition-colors">سياسة الخصوصية</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h4 class="text-white font-bold mb-6">ابقى على اطلاع</h4>
          <p class="text-sm text-gray-400 mb-4">اشترك في نشرتنا البريدية للحصول على نصائح تسويقية مجانية.</p>
          <form class="flex gap-2" @submit.prevent>
            <input type="email" placeholder="بريدك الإلكتروني" class="bg-gray-800 border-none rounded-lg px-4 py-2 text-sm w-full focus:ring-2 focus:ring-primary-500">
            <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
          </form>
        </div>
      </div>

      <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-sm text-gray-500">© 2026 منصة خطّط. جميع الحقوق محفوظة.</p>
        <div class="flex gap-6 text-sm text-gray-500">
          <a href="#" class="hover:text-white transition-colors">الشروط والأحكام</a>
          <a href="#" class="hover:text-white transition-colors">سياسة الاستخدام</a>
        </div>
      </div>
    </div>
  </footer>
</template>
