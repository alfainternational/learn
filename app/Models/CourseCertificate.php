<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'final_score',
        'issue_date',
        'certificate_url'
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = 'CERT-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Get the user that owns the certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the certificate.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate certificate URL.
     */
    public function generateCertificateUrl(): string
    {
        return url("/certificates/{$this->certificate_number}");
    }
}
