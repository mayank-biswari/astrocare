<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceManagementController extends Controller
{
    /**
     * Display a listing of all services.
     */
    public function index()
    {
        $services = Service::ordered()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'type' => 'required|in:question,prediction,kundli,consultation,pooja,matching,custom',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'base_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'has_tiers' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'faq' => 'nullable|array',
            'faq.*.question' => 'nullable|string',
            'faq.*.answer' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'requires_auth' => 'boolean',
            'requires_captcha' => 'boolean',
            'requires_shipping' => 'boolean',
            'delivery_time' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['image', 'slug']);

        // Auto-generate slug from name if not provided
        $slug = $request->filled('slug') ? $request->slug : Str::slug($request->name);

        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;

        // Handle boolean fields (checkboxes)
        $data['has_tiers'] = $request->has('has_tiers');
        $data['requires_auth'] = $request->has('requires_auth');
        $data['requires_captcha'] = $request->has('requires_captcha');
        $data['requires_shipping'] = $request->has('requires_shipping');
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // Filter out empty features
        if (isset($data['features'])) {
            $data['features'] = array_values(array_filter($data['features']));
        }

        // Filter out empty FAQ items
        if (isset($data['faq'])) {
            $data['faq'] = array_values(array_filter($data['faq'], function ($item) {
                return !empty($item['question']) || !empty($item['answer']);
            }));
        }

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully!');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(int $id)
    {
        $service = Service::with(['tiers', 'formFields'])->findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service in the database.
     */
    public function update(Request $request, int $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:question,prediction,kundli,consultation,pooja,matching,custom',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'base_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'has_tiers' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'faq' => 'nullable|array',
            'faq.*.question' => 'nullable|string',
            'faq.*.answer' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'requires_auth' => 'boolean',
            'requires_captcha' => 'boolean',
            'requires_shipping' => 'boolean',
            'delivery_time' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['image', 'slug', '_token', '_method']);

        // Handle boolean fields (checkboxes)
        $data['has_tiers'] = $request->has('has_tiers');
        $data['requires_auth'] = $request->has('requires_auth');
        $data['requires_captcha'] = $request->has('requires_captcha');
        $data['requires_shipping'] = $request->has('requires_shipping');
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && \Storage::disk('public')->exists($service->image)) {
                \Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // Filter out empty features
        if (isset($data['features'])) {
            $data['features'] = array_values(array_filter($data['features']));
        }

        // Filter out empty FAQ items
        if (isset($data['faq'])) {
            $data['faq'] = array_values(array_filter($data['faq'], function ($item) {
                return !empty($item['question']) || !empty($item['answer']);
            }));
        }

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service from the database.
     */
    public function destroy(int $id)
    {
        $service = Service::findOrFail($id);

        // Delete associated image if exists
        if ($service->image && \Storage::disk('public')->exists($service->image)) {
            \Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully!');
    }

    /**
     * Toggle the active status of a service via AJAX.
     */
    public function toggleStatus(int $id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $service->is_active,
            'message' => 'Service status updated successfully!',
        ]);
    }

    // ─── Tier Management ───────────────────────────────────────────

    /**
     * Store a new tier for the specified service.
     */
    public function storeTier(Request $request, int $serviceId): RedirectResponse
    {
        $service = Service::findOrFail($serviceId);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'price', 'currency', 'features', 'sort_order']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        // Filter out empty features
        if (isset($data['features'])) {
            $data['features'] = array_values(array_filter($data['features']));
        }

        $service->tiers()->create($data);

        return redirect()->route('admin.services.edit', $serviceId)->with('success', 'Tier created successfully!');
    }

    /**
     * Update the specified tier for the given service.
     */
    public function updateTier(Request $request, int $serviceId, int $tierId): RedirectResponse
    {
        $service = Service::findOrFail($serviceId);
        $tier = $service->tiers()->findOrFail($tierId);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'price', 'currency', 'features', 'sort_order']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        // Filter out empty features
        if (isset($data['features'])) {
            $data['features'] = array_values(array_filter($data['features']));
        }

        $tier->update($data);

        return redirect()->route('admin.services.edit', $serviceId)->with('success', 'Tier updated successfully!');
    }

    /**
     * Delete the specified tier from the given service.
     */
    public function destroyTier(int $serviceId, int $tierId): RedirectResponse
    {
        $service = Service::findOrFail($serviceId);
        $tier = $service->tiers()->findOrFail($tierId);

        // Prevent deletion of the last active tier when has_tiers is enabled
        if ($service->has_tiers) {
            $activeTierCount = $service->tiers()->where('is_active', true)->count();

            if ($tier->is_active && $activeTierCount <= 1) {
                return redirect()->route('admin.services.edit', $serviceId)
                    ->with('error', 'Cannot delete the last active tier. At least one active tier is required when tiered pricing is enabled.');
            }
        }

        $tier->delete();

        return redirect()->route('admin.services.edit', $serviceId)->with('success', 'Tier deleted successfully!');
    }

    // ─── Form Field Management ─────────────────────────────────────

    /**
     * Store a new form field for the specified service.
     */
    public function storeField(Request $request, int $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        // Decode options if sent as JSON string
        if ($request->has('options') && is_string($request->input('options'))) {
            $request->merge(['options' => json_decode($request->input('options'), true)]);
        }

        $validated = $request->validate([
            'field_name' => 'required|string|max:100',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,email,tel,date,time,datetime,select,textarea,radio,checkbox,hidden,file',
            'placeholder' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*.value' => 'nullable|string',
            'options.*.label' => 'nullable|string',
            'validation_rules' => 'nullable|string|max:500',
            'is_required' => 'boolean',
            'section' => 'nullable|string|max:100',
            'section_label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'help_text' => 'nullable|string|max:500',
        ]);

        // Set default section if not provided
        if (empty($validated['section'])) {
            $validated['section'] = 'default';
        }

        // Filter out empty options
        if (isset($validated['options'])) {
            $validated['options'] = array_values(array_filter($validated['options'], function ($option) {
                return !empty($option['value']) || !empty($option['label']);
            }));
            if (empty($validated['options'])) {
                $validated['options'] = null;
            }
        }

        // Auto-assign sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $service->formFields()->max('sort_order') + 1;
        }

        // Handle boolean fields
        $validated['is_required'] = filter_var($request->input('is_required'), FILTER_VALIDATE_BOOLEAN);
        $validated['is_active'] = $request->has('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : true;

        $service->formFields()->create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Form field created successfully!']);
        }

        return redirect()->route('admin.services.edit', $serviceId)->with('success', 'Form field created successfully!');
    }

    /**
     * Update the specified form field for the given service.
     */
    public function updateField(Request $request, int $serviceId, int $fieldId)
    {
        $service = Service::findOrFail($serviceId);
        $field = $service->formFields()->findOrFail($fieldId);

        // Decode options if sent as JSON string
        if ($request->has('options') && is_string($request->input('options'))) {
            $request->merge(['options' => json_decode($request->input('options'), true)]);
        }

        $validated = $request->validate([
            'field_name' => 'required|string|max:100',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,email,tel,date,time,datetime,select,textarea,radio,checkbox,hidden,file',
            'placeholder' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*.value' => 'nullable|string',
            'options.*.label' => 'nullable|string',
            'validation_rules' => 'nullable|string|max:500',
            'is_required' => 'boolean',
            'section' => 'nullable|string|max:100',
            'section_label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'help_text' => 'nullable|string|max:500',
        ]);

        // Handle boolean fields
        $validated['is_required'] = filter_var($request->input('is_required'), FILTER_VALIDATE_BOOLEAN);
        $validated['is_active'] = $request->has('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : true;

        // Set default section if not provided
        if (empty($validated['section'])) {
            $validated['section'] = 'default';
        }

        // Filter out empty options
        if (isset($validated['options'])) {
            $validated['options'] = array_values(array_filter($validated['options'], function ($option) {
                return !empty($option['value']) || !empty($option['label']);
            }));
            if (empty($validated['options'])) {
                $validated['options'] = null;
            }
        }

        $field->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Form field updated successfully!']);
        }

        return redirect()->route('admin.services.edit', $serviceId)->with('success', 'Form field updated successfully!');
    }

    /**
     * Delete the specified form field from the given service.
     */
    public function destroyField(Request $request, int $serviceId, int $fieldId)
    {
        $service = Service::findOrFail($serviceId);
        $field = $service->formFields()->findOrFail($fieldId);

        $field->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Form field deleted successfully!']);
        }

        return redirect()->route('admin.services.edit', $serviceId)->with('success', 'Form field deleted successfully!');
    }

    /**
     * Reorder form fields for the specified service.
     *
     * Accepts a JSON array of field IDs in the desired order
     * and updates the sort_order accordingly.
     */
    public function reorderFields(Request $request, int $serviceId): JsonResponse
    {
        $service = Service::findOrFail($serviceId);

        $request->validate([
            'field_ids' => 'required|array',
            'field_ids.*' => 'integer',
        ]);

        $fieldIds = $request->input('field_ids');

        foreach ($fieldIds as $index => $fieldId) {
            $service->formFields()->where('id', $fieldId)->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fields reordered successfully!',
        ]);
    }
}
