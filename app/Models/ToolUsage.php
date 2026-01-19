<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolUsage extends Model
{
    use HasFactory;

    protected $table = 'tool_usage';

    protected $fillable = [
        'user_id',
        'lesson_tool_id',
        'input_data',
        'output_data',
        'saved_result',
        'used_at'
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'saved_result' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the usage.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tool that owns the usage.
     */
    public function tool()
    {
        return $this->belongsTo(LessonTool::class, 'lesson_tool_id');
    }
}
