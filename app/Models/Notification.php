<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public static function createForUser($userId, $type, $title, $message, $data = null)
    {
        return self::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function create($type, $title, $message, $data = null)
    {
        return self::query()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function getUnreadCount($userId = null)
    {
        $query = self::where('is_read', false);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->count();
    }

    public static function getRecent($limit = 5, $userId = null)
    {
        $query = self::where('is_read', false);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->latest()->take($limit)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
