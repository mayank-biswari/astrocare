<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsNotification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message',
        'lead_id', 'data', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CampaignLead::class, 'lead_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public static function createForLmsUsers(string $type, string $title, string $message, ?int $leadId = null, ?array $data = null): void
    {
        $lmsUsers = User::permission('access lms')->pluck('id');

        foreach ($lmsUsers as $userId) {
            static::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'lead_id' => $leadId,
                'data' => $data,
            ]);
        }
    }
}
