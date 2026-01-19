<template>
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
       <button @click="$router.back()" class="flex items-center text-gray-500 hover:text-gray-700 mb-4 transition-colors">
         <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
         عودة للقائمة
       </button>
       <h2 class="text-3xl font-display font-bold text-gray-900">إنشاء خطة تسويقية جديدة</h2>
       <p class="text-gray-500 mt-2">اتبع الخطوات التالية لنقوم ببناء خطة محكمة لعملك باستخدام الذكاء الاصطناعي</p>
    </div>

    <!-- Steps Indicator -->
    <div class="mb-10">
        <div class="flex items-center justify-between relative">
            <!-- Progress Line -->
            <div class="absolute left-0 right-0 top-1/2 h-1 bg-gray-200 -z-10">
                <div class="h-full bg-primary-600 transition-all duration-500" :style="{ width: ((currentStep - 1) / 2 * 100) + '%' }"></div>
            </div>
            
            <div v-for="(step, index) in steps" :key="index" class="relative z-10 bg-gray-50 px-2 flex flex-col items-center" :class="{'text-primary-600': currentStep >= step.number, 'text-gray-400': currentStep < step.number}">
                <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg border-2 transition-all duration-300 shadow-md"
                    :class="[
                        currentStep > step.number ? 'bg-primary-600 border-primary-600 text-white' : '',
                        currentStep === step.number ? 'bg-white border-primary-600 text-primary-600 scale-110 shadow-xl ring-4 ring-primary-100' : '',
                        currentStep < step.number ? 'bg-white border-gray-300 text-gray-400' : ''
                    ]">
                    <component v-if="currentStep > step.number" :is="CheckIcon" class="w-7 h-7" />
                    <component v-else :is="step.icon" class="w-6 h-6" />
                </div>
                <span class="mt-2 text-xs font-medium text-center max-w-[100px] hidden sm:block">{{ step.label }}</span>
            </div>
        </div>
    </div>

    <!-- Form Steps -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        <!-- Step 1: Business Info -->
        <div v-if="currentStep === 1" class="space-y-6 animate-fade-in">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم المشروع / الشركة <span class="text-red-500">*</span></label>
                <input v-model="form.business_name" type="text" 
                    :class="['input', !form.business_name && currentStep > 1 ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : '']"
                    placeholder="مثال: متجر الهدايا المميزة">
                <p v-if="!form.business_name && currentStep > 1" class="text-xs text-red-500 mt-1">هذا الحقل مطلوب</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مجال العمل (الصناعة) <span class="text-red-500">*</span></label>
                <select v-model="form.industry" 
                    :class="['input', !form.industry && currentStep > 1 ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : '']">
                    <option value="" disabled>اختر المجال</option>
                    <option value="ecommerce">تجارة إلكترونية</option>
                    <option value="restaurant">مطاعم وكافيهات</option>
                    <option value="services">خدمات واستشارات</option>
                    <option value="real_estate">عقارات</option>
                    <option value="education">تعليم وتدريب</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وصف مختصر للمشروع</label>
                <textarea v-model="form.description" rows="4" class="input" placeholder="نبيع هدايا مخصصة للمناسبات مع توصيل سريع..."></textarea>
                <p class="text-xs text-gray-500 mt-2">كلما كان الوصف أدق، كانت نتائج الذكاء الاصطناعي أفضل.</p>
            </div>
        </div>

        <!-- Step 2: Target Audience -->
        <div v-if="currentStep === 2" class="space-y-6 animate-fade-in">
             <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 text-blue-700">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm">يمكنك استخدام الذكاء الاصطناعي لاحقا لاقتراح الجمهور المستهدف بدقة بناءً على وصف مشروعك.</p>
            </div>

            <div>
                 <label class="block text-sm font-medium text-gray-700 mb-2">من هم عملاؤك المثاليون؟ (اختياري)</label>
                 <textarea v-model="form.target_audience_input" rows="4" class="input" placeholder="مثال: النساء العاملات، محبو القهوة المختصة، الشركات الناشئة..."></textarea>
            </div>
            
            <div>
                 <label class="block text-sm font-medium text-gray-700 mb-2">رابط الموقع (اختياري)</label>
                 <input v-model="form.website" type="url" class="input" placeholder="https://example.com" dir="ltr">
            </div>
        </div>

        <!-- Step 3: Review -->
        <div v-if="currentStep === 3" class="space-y-6 animate-fade-in">
            <h3 class="text-lg font-bold text-gray-900 border-b pb-2">مراجعة البيانات</h3>
            
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-gray-500">اسم المشروع:</dt>
                    <dd class="font-medium">{{ form.business_name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">المجال:</dt>
                    <dd class="font-medium">{{ form.industry }}</dd>
                </div>
                <div>
                     <dt class="text-gray-500 mb-1">الوصف:</dt>
                     <dd class="bg-gray-50 p-3 rounded-lg text-sm">{{ form.description }}</dd>
                </div>
            </dl>

            <div class="bg-gradient-to-r from-primary-50 to-secondary-50 p-4 rounded-xl border border-primary-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-yellow-500 animate-pulse">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">جاهز للإطلاق؟</h4>
                        <p class="text-sm text-gray-600">عند الضغط على "إنشاء الخطة"، سيقوم مساعدك الذكي بتجهيز الهيكل الكامل للخطة.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-8 flex justify-between border-t pt-6">
            <button v-if="currentStep > 1" @click="currentStep--" class="btn bg-gray-100 text-gray-700 hover:bg-gray-200">
                السابق
            </button>
            <div v-else></div> <!-- Spacer -->

            <button v-if="currentStep < 3" @click="nextStep" :disabled="!isStepValid" 
                class="btn btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                التالي
            </button>
            
            <button v-if="currentStep === 3" @click="submit" :disabled="submitting"
                class="btn btn-secondary px-8 shadow-lg shadow-secondary-500/20">
                <span v-if="submitting">جاري الإنشاء...</span>
                <span v-else>إنشاء الخطة</span>
            </button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { 
    BuildingStorefrontIcon, 
    UserGroupIcon, 
    CheckCircleIcon,
    CheckIcon 
} from '@heroicons/vue/24/outline';

const router = useRouter();
const currentStep = ref(1);
const submitting = ref(false);

const steps = [
    { number: 1, label: 'بيانات المشروع', icon: BuildingStorefrontIcon },
    { number: 2, label: 'الجمهور المستهدف', icon: UserGroupIcon },
    { number: 3, label: 'المراجعة والإنشاء', icon: CheckCircleIcon }
];

const form = reactive({
    business_name: '',
    industry: '',
    description: '',
    target_audience_input: '',
    website: ''
});

const isStepValid = computed(() => {
    if (currentStep.value === 1) {
        return form.business_name && form.industry;
    }
    return true; // Step 2 is mostly optional
});

const nextStep = () => {
    if (isStepValid.value) {
        currentStep.value++;
    }
};

const submit = async () => {
    submitting.value = true;
    try {
        const payload = {
            ...form,
             // Map target_audience to expected array format by CreatePlanRequest if needed, or backend handles it
             // Backend CreatePlanRequest expects target_audience as array but our form is text for MVP simplicity
             // Let's adjust backend or just send simple data. Backend request: 'target_audience' => ['nullable', 'array']
             // I'll wrap it.
             target_audience: form.target_audience_input ? [form.target_audience_input] : []
        };
        const response = await axios.post('/plans', payload);
        
        // Redirect to the newly created plan
        router.push(`/plans/${response.data.data.id}`);
    } catch (error) {
        console.error(error);
        alert('حدث خطأ أثناء إنشاء الخطة. الرجاء المحاولة مرة أخرى.');
    } finally {
        submitting.value = false;
    }
};
</script>
