<template>
  <div class="min-h-screen font-sans">
    <LandingNavbar />

    <!-- Hero Section (Enhanced) -->
    <section class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 text-white overflow-hidden pt-32 pb-20 md:pt-40 md:pb-32">
      <!-- Background Elements -->
      <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
      </div>
      <div class="absolute top-20 right-0 w-96 h-96 bg-secondary-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow animation-delay-2000"></div>

      <div class="container-custom relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <!-- Text Content -->
          <div class="text-center lg:text-right space-y-8 animate-fade-in">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              <span class="text-sm font-medium">الخيار الأول لأكثر من 500 مسوق</span>
            </div>
            
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-black leading-tight tracking-tight">
              خطط لتسويقك
              <br/>
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary-200 to-white">بذكاء وسرعة</span>
            </h1>
            
            <p class="text-xl md:text-2xl text-primary-100 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
              لا مزيد من التخبط أو إهدار الميزانية. احصل على خطة تسويقية متكاملة، مخصصة لعملك، ومدعومة بالذكاء الاصطناعي في دقائق.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
              <router-link to="/register" class="btn btn-secondary text-lg px-8 py-4 shadow-xl shadow-secondary-900/10 hover:shadow-2xl hover:scale-105 transition-all duration-300">
                ابدأ رحلتك مجاناً
              </router-link>
              <button @click="scrollToVideo" class="btn btn-ghost border-2 border-white/20 hover:bg-white/10 text-lg px-8 py-4 backdrop-blur-sm">
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                شاهد الفيديو
              </button>
            </div>
            
            <div class="pt-4 flex items-center justify-center lg:justify-start gap-4 text-sm text-primary-100/80">
              <div class="flex items-center gap-1"><svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> بدون بطاقة ائتمان</div>
              <div class="flex items-center gap-1"><svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> تجربة مجانية لمدة 7 أيام</div>
            </div>
          </div>
          
          <!-- Hero Visual -->
          <div class="relative lg:h-[600px] flex items-center justify-center animate-slide-up hover:scale-[1.02] transition-transform duration-500">
            <div class="relative z-10 w-full max-w-lg">
                <!-- Abstract Dashboard UI Representation -->
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border border-white/50">
                    <div class="flex items-center justify-between mb-8 border-b pb-4">
                        <div class="flex gap-2">
                           <div class="w-3 h-3 rounded-full bg-red-400"></div>
                           <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                           <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="h-2 w-24 bg-gray-200 rounded-full"></div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-1/3 h-24 bg-primary-50 rounded-xl p-3 flex flex-col justify-between">
                                <div class="w-8 h-8 bg-primary-100 rounded-lg"></div>
                                <div class="h-2 w-16 bg-primary-200 rounded-full"></div>
                            </div>
                             <div class="w-1/3 h-24 bg-secondary-50 rounded-xl p-3 flex flex-col justify-between">
                                <div class="w-8 h-8 bg-secondary-100 rounded-lg"></div>
                                <div class="h-2 w-16 bg-secondary-200 rounded-full"></div>
                            </div>
                             <div class="w-1/3 h-24 bg-gray-50 rounded-xl p-3 flex flex-col justify-between">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg"></div>
                                <div class="h-2 w-16 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                        <div class="h-32 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center text-gray-400">
                             مخطط النمو المتوقع
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">اكتمال الخطة</span>
                                <span class="text-primary-600 font-bold">85%</span>
                            </div>
                            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full w-[85%] bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      
       <!-- Wave Separator -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
          <path d="M0 120L48 110C96 100 192 80 288 75C384 70 480 80 576 85C672 90 768 90 864 85C960 80 1056 70 1152 65C1248 60 1344 60 1392 60L1440 60V120H1392C1344 120 1248 120 1152 120C1056 120 960 120 864 120C768 120 672 120 576 120C480 120 384 120 288 120C192 120 96 120 48 120H0Z" fill="#F9FAFB"/>
        </svg>
      </div>
    </section>

    <!-- Pain Points Section (New) -->
    <section class="section bg-gray-50" id="pain-points">
      <div class="container-custom">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-3xl md:text-5xl font-display font-black mb-6">
            هل تعاني من <span class="text-red-500">فوضى التسويق</span>؟
          </h2>
          <p class="text-xl text-gray-600">
            أصحاب الأعمال يخسرون الملايين سنوياً بسبب غياب التخطيط
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="card bg-white p-8 border-t-4 border-red-400 group hover:shadow-xl transition-all">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-gray-900">إهدار الميزانية</h3>
            <p class="text-gray-600">تصرف الأموال على إعلانات لا تحقق عائداً لأنك لا تستهدف الجمهور الصحيح.</p>
          </div>

          <div class="card bg-white p-8 border-t-4 border-red-400 group hover:shadow-xl transition-all">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-gray-900">ضياع الوقت</h3>
            <p class="text-gray-600">تقضي ساعات في "التفكير" بما يجب نشره بدلاً من التركيز على إدارة عملك.</p>
          </div>

          <div class="card bg-white p-8 border-t-4 border-red-400 group hover:shadow-xl transition-all">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-gray-900">تخبط النتائج</h3>
            <p class="text-gray-600">نتائج متذبذبة وغير متوقعة لغياب الاستراتيجية الواضحة والقياس الدقيق.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works Section (New) -->
    <section class="section bg-white relative overflow-hidden" id="how-it-works">
      <div class="container-custom">
        <div class="text-center mb-20">
            <h2 class="text-3xl md:text-5xl font-display font-black mb-6">
            كيف يحول <span class="text-gradient">خطّط</span> عملك؟
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            منهجية علمية مبسطة في 3 خطوات عملية
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-12 relative">
            <!-- Connecting Line -->
            <div class="hidden md:block absolute top-12 right-0 left-0 h-0.5 bg-gray-100 z-0"></div>

            <!-- Step 1 -->
            <div class="relative z-10 text-center group">
                <div class="w-24 h-24 bg-white border-4 border-primary-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg group-hover:border-primary-500 transition-colors duration-300">
                    <span class="text-4xl font-black text-primary-200 group-hover:text-primary-600 transition-colors">1</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">التشخيص الذكي</h3>
                <p class="text-gray-600 leading-relaxed">
                    أجب على أسئلة محددة عن عملك. نظامنا الذكي يحلل وضعك الحالي ويحدد الفرص الضائعة بدقة.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="relative z-10 text-center group">
                <div class="w-24 h-24 bg-white border-4 border-secondary-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg group-hover:border-secondary-500 transition-colors duration-300">
                    <span class="text-4xl font-black text-secondary-200 group-hover:text-secondary-600 transition-colors">2</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">التخطيط بالذكاء الاصطناعي</h3>
                <p class="text-gray-600 leading-relaxed">
                    يقوم AI ببناء خطة مخصصة: من تحديد الجمهور المثالي إلى صياغة الرسائل ورزنامة المحتوى.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="relative z-10 text-center group">
                <div class="w-24 h-24 bg-white border-4 border-green-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg group-hover:border-green-500 transition-colors duration-300">
                    <span class="text-4xl font-black text-green-200 group-hover:text-green-600 transition-colors">3</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">التنفيذ والنتائج</h3>
                <p class="text-gray-600 leading-relaxed">
                    حمّل خطتك كملف PDF احترافي. ابدأ التنفيذ بخطوات واضحة وراقب مؤشرات الأداء وهي ترتفع.
                </p>
            </div>
        </div>
      </div>
    </section>

    <!-- Features Section (Refined) -->
    <section class="section bg-gray-50" id="features">
        <div class="container-custom">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-bold uppercase tracking-wider text-sm">ميزات تنافسية</span>
                <h2 class="text-3xl md:text-5xl font-display font-black mt-2 mb-6">أدوات المحترفين بين يديك</h2>
            </div>
            
            <div class="flex flex-col gap-20">
                <!-- Feature Block 1 -->
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="order-2 md:order-1">
                        <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold mb-4">بناء شخصية العميل بدقة الليزر</h3>
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">
                            لا تخمن من هو عميلك. نساعدك في بناء "أفاتار" دقيق لعملائك: مخاوفهم، رغباتهم، وأين يتواجدون، لتوجيه رسائلك بدقة.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                تحليل ديموغرافي ونفسي
                            </li>
                            <li class="flex items-center gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                تحديد نقاط الألم (Pain Points)
                            </li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2 bg-gradient-to-br from-primary-50 to-primary-100 rounded-3xl p-8 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                        <!-- Placeholder for feature image -->
                         <div class="aspect-video bg-white rounded-xl shadow-lg flex items-center justify-center text-primary-200">
                            صورة توضيحية لواجهة تحديد الجمهور
                        </div>
                    </div>
                </div>

                <!-- Feature Block 2 -->
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="bg-gradient-to-br from-secondary-50 to-secondary-100 rounded-3xl p-8 transform -rotate-2 hover:rotate-0 transition-transform duration-500">
                        <!-- Placeholder for feature image -->
                        <div class="aspect-video bg-white rounded-xl shadow-lg flex items-center justify-center text-secondary-200">
                            صورة توضيحية لواجهة خطة المحتوى
                        </div>
                    </div>
                    <div>
                        <div class="w-16 h-16 bg-secondary-100 rounded-2xl flex items-center justify-center text-secondary-600 mb-6">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold mb-4">رزنامة محتوى جاهزة للنشر</h3>
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">
                            وداعاً لـ "ماذا أنشر اليوم؟". خطّط يولد لك أفكار محتوى لشهر كامل، موزعة حسب مراحل وعي العميل (Funnel).
                        </p>
                         <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                30 فكرة محتوى حصرية
                            </li>
                            <li class="flex items-center gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                تنوع بين التعليمي، البيعي، والتفاعلي
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section (New) -->
    <section class="section bg-white" id="testimonials">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-display font-black mb-6">قصص نجاح ملهمة</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Review 1 -->
                <div class="bg-gray-50 rounded-2xl p-8 relative">
                    <div class="text-yellow-400 flex mb-4">★★★★★</div>
                    <p class="text-gray-700 mb-6 leading-relaxed">"كنت تائهاً في التسويق وأخسر المال. خطّط رتب أفكاري وأعطاني خارطة طريق واضحة. مبيعاتي زادت 40% في أول شهر."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                        <div>
                            <div class="font-bold">أحمد العتيبي</div>
                            <div class="text-sm text-gray-500">متجر إلكتروني</div>
                        </div>
                    </div>
                </div>
                 <!-- Review 2 -->
                <div class="bg-gray-50 rounded-2xl p-8 relative">
                    <div class="text-yellow-400 flex mb-4">★★★★★</div>
                    <p class="text-gray-700 mb-6 leading-relaxed">"أفضل استثمار لعملي الناشئ. بدلاً من توظيف وكالة بآلاف الريالات، حصلت على خطة احترافية بنفس الجودة."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                        <div>
                            <div class="font-bold">سارة محمد</div>
                            <div class="text-sm text-gray-500">مصممة جرافيك</div>
                        </div>
                    </div>
                </div>
                 <!-- Review 3 -->
                <div class="bg-gray-50 rounded-2xl p-8 relative">
                    <div class="text-yellow-400 flex mb-4">★★★★★</div>
                    <p class="text-gray-700 mb-6 leading-relaxed">"سهولة الاستخدام مذهلة. والذكاء الاصطناعي يفهم السوق السعودي بشكل مخيف! أنصح به كل رائد أعمال."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                        <div>
                            <div class="font-bold">فهد الدوسري</div>
                            <div class="text-sm text-gray-500">صاحب كوفي شوب</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Logos -->
            <div class="mt-16 pt-8 border-t border-gray-100">
                <p class="text-center text-gray-400 mb-8 text-sm">شركات نساعدها على النمو</p>
                <div class="flex flex-wrap justify-center gap-12 opacity-50 grayscale">
                    <!-- Placeholders for logos -->
                    <div class="h-8 font-display font-bold text-xl">SALLA</div>
                    <div class="h-8 font-display font-bold text-xl">ZID</div>
                    <div class="h-8 font-display font-bold text-xl">SHOPIFY</div>
                    <div class="h-8 font-display font-bold text-xl">WOO</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section (Existing - Refined) -->
    <section class="section bg-gray-900 text-white" id="pricing">
      <!-- ... (Keeping existing pricing content structure but wrapped nicely) ... -->
      <div class="container-custom">
        <div class="text-center mb-16">
          <span class="text-secondary-400 font-bold uppercase tracking-wider text-sm">للمستقبل</span>
          <h2 class="text-3xl md:text-5xl font-display font-black mt-2 mb-4">
            استثمار بسيط، عائد ضخم
          </h2>
          <p class="text-xl text-gray-400">
            اختر الخطة التي تناسب حجم عملك الحالي
          </p>
        </div>

        <!-- Pricing Cards (Reusing Logic from previous file but ensuring dark mode compatibility) -->
        <div class="grid md:grid-cols-4 gap-6">
            <!-- Free -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 hover:border-gray-500 transition-colors">
                <div class="mb-4">
                    <h3 class="text-xl font-bold">مجاني</h3>
                     <div class="text-3xl font-bold mt-2">0 <span class="text-sm text-gray-400 font-normal">ريال</span></div>
                </div>
                <ul class="space-y-3 mb-6 text-sm text-gray-300">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> خطة واحدة</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> تجربة AI محدودة</li>
                </ul>
                <router-link to="/register" class="btn bg-gray-700 hover:bg-gray-600 text-white w-full">ابدأ الآن</router-link>
            </div>
            
            <!-- Basic -->
             <div class="bg-gray-800 rounded-2xl p-6 border border-primary-500/30 relative">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-primary-400">أساسي</h3>
                     <div class="text-3xl font-bold mt-2">99 <span class="text-sm text-gray-400 font-normal">ريال/شهر</span></div>
                </div>
                <ul class="space-y-3 mb-6 text-sm text-gray-300">
                     <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> 3 خطط</li>
                     <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> AI غير محدود</li>
                </ul>
                <router-link to="/register" class="btn btn-primary w-full">اختر الباقة</router-link>
            </div>

            <!-- Pro -->
             <div class="bg-gray-800 rounded-2xl p-6 border-2 border-secondary-500 relative transform md:-translate-y-4 shadow-2xl shadow-secondary-900/50">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-secondary-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase">الأكثر مبيعاً</div>
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-secondary-400">احترافي</h3>
                     <div class="text-3xl font-bold mt-2">299 <span class="text-sm text-gray-400 font-normal">ريال/شهر</span></div>
                </div>
                <ul class="space-y-3 mb-6 text-sm text-gray-300">
                     <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> 10 خطط</li>
                     <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> تصدير لكل الصيغ</li>
                     <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> دعم فني VIP</li>
                </ul>
                <router-link to="/register" class="btn btn-secondary w-full">اختر الباقة</router-link>
            </div>

            <!-- Enterprise -->
             <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 hover:border-gray-500 transition-colors">
                <div class="mb-4">
                    <h3 class="text-xl font-bold">شركات</h3>
                     <div class="text-3xl font-bold mt-2">999 <span class="text-sm text-gray-400 font-normal">ريال</span></div>
                </div>
                 <ul class="space-y-3 mb-6 text-sm text-gray-300">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> كل شيء مفتوح</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> API Access</li>
                </ul>
                <router-link to="/register" class="btn bg-white text-gray-900 hover:bg-gray-100 w-full">تواصل معنا</router-link>
            </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section (New) -->
     <section class="section bg-white" id="faq">
        <div class="container-custom max-w-4xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-display font-black mb-6">الأسئلة الشائعة</h2>
            </div>
            
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer">
                    <h3 class="font-bold text-lg mb-2">هل أحتاج لخبرة في التسويق لاستخدام المنصة؟</h3>
                    <p class="text-gray-600">أبداً! صُممت منصة خطّط لتكون مساعدك الذكي. هي ستقودك خطوة بخطوة، والذكاء الاصطناعي سيكتب لك المحتوى.</p>
                </div>
                 <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer">
                    <h3 class="font-bold text-lg mb-2">هل يمكنني تغيير خطتي لاحقاً؟</h3>
                    <p class="text-gray-600">نعم، يمكنك الترقية أو العودة للخطة المجانية في أي وقت بضغطة زر من لوحة التحكم.</p>
                </div>
                 <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer">
                    <h3 class="font-bold text-lg mb-2">كيف تعمل ميزة الذكاء الاصطناعي؟</h3>
                    <p class="text-gray-600">نستخدم نماذج متطورة (Gemini) مدربة على أفضل الممارسات التسويقية. النظام يحلل إجاباتك ويولد نصوصاً مخصصة لعملك.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="section bg-gradient-to-br from-primary-600 to-primary-800 text-white text-center">
        <div class="container-custom">
            <h2 class="text-4xl font-display font-black mb-6">لا تضيع يوماً آخر بدون خطة</h2>
            <p class="text-xl mb-10 text-primary-100 max-w-2xl mx-auto">انضم الآن وابدأ في بناء إمبراطوريتك الرقمية</p>
            <router-link to="/register" class="btn btn-secondary text-lg px-12 py-5 shadow-2xl hover:scale-105 transition-transform">
                ابدأ تجربتك المجانية
            </router-link>
            <p class="mt-6 text-sm opacity-70">ضمان استرجاع الأموال لمدة 14 يوم</p>
        </div>
    </section>

    <LandingFooter />
  </div>
</template>

<script setup>
import LandingNavbar from '@/components/LandingNavbar.vue';
import LandingFooter from '@/components/LandingFooter.vue';

const scrollToVideo = () => {
    // Placeholder for video modal or scroll
    console.log('Video clicked');
};
</script>
