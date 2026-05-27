<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'short_description', 'description',
        'image', 'icon', 'base_price', 'currency', 'has_tiers',
        'features', 'faq', 'meta_title', 'meta_description', 'meta_keywords',
        'requires_auth', 'requires_captcha', 'requires_shipping',
        'delivery_time', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
        'base_price' => 'decimal:2',
        'has_tiers' => 'boolean',
        'requires_auth' => 'boolean',
        'requires_captcha' => 'boolean',
        'requires_shipping' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    /**
     * Get the pricing tiers for this service.
     */
    public function tiers(): HasMany
    {
        return $this->hasMany(ServiceTier::class);
    }

    /**
     * Get the configured form fields for this service.
     */
    public function formFields(): HasMany
    {
        return $this->hasMany(ServiceFormField::class);
    }

    /**
     * Get the submissions for this service.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(ServiceSubmission::class);
    }

    /**
     * Get the consultations for this service (legacy relationship).
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    /**
     * Scope to only active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order services by sort_order ascending.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // ─── Methods ───────────────────────────────────────────────────

    /**
     * Build Laravel validation rules array from configured form fields.
     *
     * Reads all active form fields for this service and constructs
     * a validation rules array suitable for Laravel's Validator.
     *
     * @return array<string, array<string>>
     */
    public function buildValidationRules(): array
    {
        $fields = $this->formFields()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];

            // Add required/nullable rule
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Parse stored validation rules if present
            if (!empty($field->validation_rules)) {
                $parsedRules = array_filter(
                    array_map('trim', explode('|', $field->validation_rules))
                );

                // Merge parsed rules, avoiding duplicate required/nullable
                foreach ($parsedRules as $rule) {
                    if (!in_array($rule, ['required', 'nullable'])) {
                        $fieldRules[] = $rule;
                    }
                }
            } else {
                // Apply default rules based on field type when no explicit rules set
                switch ($field->field_type) {
                    case 'email':
                        $fieldRules[] = 'email';
                        break;
                    case 'tel':
                        $fieldRules[] = 'string';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                    case 'time':
                        $fieldRules[] = 'string';
                        break;
                    case 'datetime':
                        $fieldRules[] = 'date';
                        break;
                    case 'file':
                        $fieldRules[] = 'file';
                        $fieldRules[] = 'max:10240';
                        break;
                    case 'checkbox':
                        // Checkboxes can be arrays
                        break;
                    case 'select':
                    case 'radio':
                        if (!empty($field->options)) {
                            $validValues = collect($field->options)->pluck('value')->implode(',');
                            if ($validValues) {
                                $fieldRules[] = 'in:' . $validValues;
                            }
                        }
                        break;
                    default:
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:255';
                        break;
                }
            }

            $rules[$field->field_name] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Get the display price for frontend rendering.
     *
     * Returns formatted price string. For tiered services, shows the
     * price range (e.g., "₹299 - ₹999"). For flat-price services,
     * shows the base price (e.g., "₹499").
     *
     * @return string
     */
    public function getDisplayPrice(): string
    {
        if ($this->has_tiers) {
            $activeTiers = $this->tiers()->where('is_active', true)->orderBy('price', 'asc')->get();

            if ($activeTiers->isEmpty()) {
                return formatPrice($this->base_price, $this->currency ?? 'INR');
            }

            $minPrice = $activeTiers->first()->price;
            $maxPrice = $activeTiers->last()->price;

            if ($minPrice == $maxPrice) {
                return formatPrice($minPrice, $this->currency ?? 'INR');
            }

            return formatPrice($minPrice, $this->currency ?? 'INR') . ' - ' . formatPrice($maxPrice, $this->currency ?? 'INR');
        }

        return formatPrice($this->base_price, $this->currency ?? 'INR');
    }
}
