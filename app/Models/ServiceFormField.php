<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFormField extends Model
{
    protected $fillable = [
        'service_id', 'field_name', 'field_label', 'field_type',
        'placeholder', 'options', 'validation_rules', 'is_required',
        'section', 'section_label', 'sort_order', 'is_active', 'help_text',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    /**
     * Get the service that owns this form field.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    /**
     * Scope to only active form fields.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Convert stored validation_rules string to Laravel rules array.
     *
     * e.g., "required|string|max:255" → ['required', 'string', 'max:255']
     *
     * @return array
     */
    public function getValidationRulesArray(): array
    {
        if (empty($this->validation_rules)) {
            return [];
        }

        return explode('|', $this->validation_rules);
    }
}
