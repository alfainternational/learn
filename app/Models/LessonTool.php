<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTool extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'tool_type',
        'title',
        'description',
        'tool_config',
        'example_data',
        'component_name',
        'is_active'
    ];

    protected $casts = [
        'tool_config' => 'array',
        'example_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the lesson that owns the tool.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get usage records for this tool.
     */
    public function usageRecords()
    {
        return $this->hasMany(ToolUsage::class);
    }

    /**
     * Get usage count for this tool.
     */
    public function getUsageCount(): int
    {
        return $this->usageRecords()->count();
    }
}
