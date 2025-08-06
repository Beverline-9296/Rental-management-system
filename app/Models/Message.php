<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Property;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'message',
        'is_read',
        'read_at',
        'priority',
        'property_id',
        'message_type',
        'attachments',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'attachments' => 'array',
    ];
    
    /**
     * Get the sender of the message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    /**
     * Get the receiver of the message
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    /**
     * Get the property associated with the message
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
    
    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'normal' => 'blue',
            default => 'gray',
        };
    }
    
    /**
     * Get message type badge color
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->message_type) {
            'maintenance' => 'yellow',
            'lease' => 'green',
            'payment' => 'purple',
            'general' => 'blue',
            default => 'gray',
        };
    }
}
