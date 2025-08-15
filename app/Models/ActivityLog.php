<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'metadata',
        'icon',
        'color'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new activity log entry
     */
    public static function logActivity($userId, $type, $description, $metadata = null, $icon = 'fas fa-circle', $color = 'blue')
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'icon' => $icon,
            'color' => $color
        ]);
    }

    /**
     * Get recent activities for a user
     */
    public static function getRecentActivities($userId, $limit = 5)
    {
        return self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
