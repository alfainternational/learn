<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * عرض الشهادة
     */
    public function show($number)
    {
        $certificate = CourseCertificate::with(['user', 'course'])
            ->where('certificate_number', $number)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $certificate
        ]);
    }

    /**
     * إنشاء شهادة جديدة
     */
    public function generate($courseId)
    {
        $course = Course::findOrFail($courseId);
        $userId = auth()->id();

        // التحقق من إكمال الدورة
        $completionPercentage = $course->getCompletionPercentage($userId);
        if ($completionPercentage < 100) {
            return response()->json([
                'success' => false,
                'message' => 'يجب إكمال جميع الدروس للحصول على الشهادة',
                'data' => ['completion_percentage' => $completionPercentage]
            ], 400);
        }

        // التحقق من عدم وجود شهادة سابقة
        $existingCertificate = CourseCertificate::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existingCertificate) {
            return response()->json([
                'success' => true,
                'message' => 'لديك شهادة مسبقة لهذه الدورة',
                'data' => $existingCertificate
            ]);
        }

        // إنشاء شهادة جديدة
        $certificate = CourseCertificate::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'certificate_number' => 'CERT-' . strtoupper(Str::random(8)),
            'issued_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إصدار الشهادة بنجاح',
            'data' => $certificate->load(['user', 'course'])
        ], 201);
    }

    /**
     * تحميل PDF
     */
    public function download($number)
    {
        $certificate = CourseCertificate::with(['user', 'course'])
            ->where('certificate_number', $number)
            ->firstOrFail();

        // التحقق من أن المستخدم هو صاحب الشهادة
        if ($certificate->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتحميل هذه الشهادة'
            ], 403);
        }

        // TODO: إنشاء PDF باستخدام مكتبة مثل DomPDF
        // هذا placeholder للآن
        return response()->json([
            'success' => true,
            'message' => 'جاري تحميل الشهادة',
            'data' => [
                'download_url' => url("/api/v1/certificates/{$number}/pdf"),
                'certificate' => $certificate
            ]
        ]);
    }
}
