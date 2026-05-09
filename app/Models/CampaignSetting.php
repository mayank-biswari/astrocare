<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignSetting extends Model
{
    use HasFactory;

    protected $fillable = ['source', 'key', 'value'];

    /**
     * Get the notification email configured for a given campaign source.
     */
    public static function getNotificationEmail(string $source): ?string
    {
        $setting = self::where('source', $source)
            ->where('key', 'notification_email')
            ->first();

        return $setting?->value;
    }

    /**
     * Set (or update) the notification email for a given campaign source.
     */
    public static function setNotificationEmail(string $source, string $email): self
    {
        return self::updateOrCreate(
            ['source' => $source, 'key' => 'notification_email'],
            ['value' => $email]
        );
    }
}
