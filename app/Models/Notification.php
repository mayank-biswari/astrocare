<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public static function create($type, $title, $message, $data = null)
    {
        return self::query()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function getUnreadCount()
    {
        return self::where('is_read', false)->count();
    }

    public static function getRecent($limit = 5)
    {
        return self::where('is_read', false)->latest()->take($limit)->get();
    }
}