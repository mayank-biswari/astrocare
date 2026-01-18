<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\PoojaService;
use App\Models\Order;
use App\Models\Consultation;
use App\Models\User;
use App\Models\Category;
use App\Models\Language;
use App\Models\PaymentGateway;
use App\Models\Currency;
use App\Models\CmsCategory;
use App\Models\CmsPageType;
use App\Models\ContactSubmission;
use App\Models\ContactSetting;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'consultations' => Consultation::count(),
            'contact_submissions' => \App\Models\ContactSubmission::count(),
            'unread_notifications' => \App\Models\Notification::getUnreadCount(),
            'cms_pages' => \App\Models\CmsPage::count(),
            'recent_contacts' => \App\Models\ContactSubmission::latest()->take(5)->get(),
            'recent_notifications' => \App\Models\Notification::latest()->take(5)->get()
        ];
        return view('admin.dashboard', compact('stats'));
    }

    // Products Management
    public function products(Request $request)
    {
        $query = Product::query();
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->status !== null) {
            $query->where('is_active', $request->status);
        }
        
        $products = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        return view('admin.products.create');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $galleryImages = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryImages[] = $file->store('products', 'public');
            }
        }

        $specifications = null;
        if ($request->specifications) {
            $specifications = array_filter(array_map('trim', explode("\n", $request->specifications)));
        }

        $features = null;
        if ($request->features) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'description' => $request->description,
            'specifications' => $specifications,
            'features' => $features,
            'image' => $imagePath ? '/storage/' . $imagePath : null,
            'images' => $galleryImages,
            'slug' => \Str::slug($request->name),
            'stock_quantity' => $request->stock_quantity ?? 0,
            'show_stock' => $request->has('show_stock'),
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully!');
    }

    public function editProduct(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $specifications = null;
        if ($request->specifications) {
            $specifications = array_filter(array_map('trim', explode("\n", $request->specifications)));
        }

        $features = null;
        if ($request->features) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        $updateData = [
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'description' => $request->description,
            'specifications' => $specifications,
            'features' => $features,
            'slug' => \Str::slug($request->name),
            'stock_quantity' => $request->stock_quantity ?? 0,
            'show_stock' => $request->has('show_stock'),
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $updateData['image'] = '/storage/' . $imagePath;
        }

        // Handle gallery images
        $existingImages = $request->input('existing_gallery_images', []);
        $deletedImages = $request->input('deleted_gallery_images', []);
        $newImages = [];
        
        // Delete files from storage
        if (!empty($deletedImages)) {
            foreach ($deletedImages as $deletedImage) {
                \Storage::disk('public')->delete('products/' . $deletedImage);
            }
        }
        
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $newImages[] = $file->store('products', 'public');
            }
        }
        
        // Remove deleted images from existing images array
        $existingImages = array_diff($existingImages, $deletedImages);
        
        $updateData['images'] = array_merge($existingImages, $newImages);

        $product->update($updateData);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully!');
    }

    // Orders Management
    public function orders(Request $request)
    {
        $query = Order::with('user');
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $orders = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        
        return redirect()->route('admin.orders')->with('success', 'Order status updated successfully!');
    }

    public function viewOrder($id)
    {
        $order = Order::with('user')->findOrFail($id);
        return view('admin.orders.view', compact('order'));
    }

    // Users Management
    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user->update(['role' => $request->role]);
        return redirect()->route('admin.users')->with('success', 'User role updated successfully!');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('site', 'public');
            \App\Models\SiteSetting::set('site_logo', '/storage/' . $logoPath);
        }

        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('site', 'public');
            \App\Models\SiteSetting::set('site_icon', '/storage/' . $iconPath);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }

    // Admin Users Management
    public function admins()
    {
        $admins = User::where('role', 'admin')->latest()->paginate(10);
        $users = User::where('role', 'user')->latest()->get();
        return view('admin.admins.index', compact('admins', 'users'));
    }

    public function promoteToAdmin(User $user)
    {
        $user->update(['role' => 'admin']);
        return redirect()->route('admin.admins')->with('success', 'User promoted to admin successfully!');
    }

    public function demoteFromAdmin(User $user)
    {
        $user->update(['role' => 'user']);
        return redirect()->route('admin.admins')->with('success', 'Admin demoted to user successfully!');
    }

    // Categories Management
    public function categories()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // Save translations
        if ($request->translations) {
            foreach ($request->translations as $langCode => $translation) {
                if (!empty($translation['name'])) {
                    $category->translations()->create([
                        'language_code' => $langCode,
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // Update translations
        $category->translations()->delete();
        if ($request->translations) {
            foreach ($request->translations as $langCode => $translation) {
                if (!empty($translation['name'])) {
                    $category->translations()->create([
                        'language_code' => $langCode,
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    // Consultations Management
    public function consultations(Request $request)
    {
        $query = Consultation::with('user');
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->search) {
            $query->whereHas('user', function($userQuery) use ($request) {
                $userQuery->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $consultations = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.consultations.index', compact('consultations'));
    }

    public function viewConsultation($id)
    {
        $consultation = Consultation::with('user')->findOrFail($id);
        return view('admin.consultations.view', compact('consultation'));
    }

    public function updateConsultationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled',
            'suggestions' => 'required_if:status,completed|nullable|string',
            'remedies' => 'required_if:status,completed|nullable|string',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string'
        ]);

        $consultation = Consultation::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        if ($request->status === 'completed') {
            $updateData['suggestions'] = $request->suggestions;
            $updateData['remedies'] = $request->remedies;
        } elseif ($request->status === 'cancelled') {
            $updateData['cancellation_reason'] = $request->cancellation_reason;
        }
        
        $consultation->update($updateData);
        
        return redirect()->route('admin.consultations')->with('success', 'Consultation status updated successfully!');
    }

    // Languages Management
    public function languages()
    {
        $languages = Language::orderBy('sort_order')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:languages',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
        ]);

        Language::create([
            'code' => $request->code,
            'name' => $request->name,
            'native_name' => $request->native_name,
            'is_active' => $request->has('is_active'),
            'sort_order' => Language::max('sort_order') + 1
        ]);

        return redirect()->route('admin.languages')->with('success', 'Language added successfully!');
    }

    public function updateLanguage(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,' . $id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
        ]);

        $language->update([
            'code' => $request->code,
            'name' => $request->name,
            'native_name' => $request->native_name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.languages')->with('success', 'Language updated successfully!');
    }

    public function setDefaultLanguage($id)
    {
        Language::where('is_default', true)->update(['is_default' => false]);
        Language::findOrFail($id)->update(['is_default' => true]);
        
        return redirect()->route('admin.languages')->with('success', 'Default language updated successfully!');
    }

    public function deleteLanguage($id)
    {
        $language = Language::findOrFail($id);
        
        if ($language->is_default) {
            return redirect()->route('admin.languages')->with('error', 'Cannot delete default language!');
        }
        
        $language->delete();
        return redirect()->route('admin.languages')->with('success', 'Language deleted successfully!');
    }

    // Currencies Management
    public function currencies()
    {
        $currencies = Currency::orderBy('sort_order')->get();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function updateCurrency(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);
        
        $request->validate([
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        $currency->update([
            'exchange_rate' => $request->exchange_rate,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.currencies')->with('success', 'Currency updated successfully!');
    }

    public function setDefaultCurrency($id)
    {
        Currency::where('is_default', true)->update(['is_default' => false]);
        Currency::findOrFail($id)->update(['is_default' => true]);
        
        return redirect()->route('admin.currencies')->with('success', 'Default currency updated successfully!');
    }

    // Payment Gateways Management
    public function paymentGateways()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        return view('admin.payment-gateways.index', compact('gateways'));
    }

    public function updatePaymentGateway(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        
        $gateway->update([
            'is_active' => $request->has('is_active'),
            'is_test_mode' => $request->has('is_test_mode'),
            'credentials' => $request->credentials ?? $gateway->credentials,
        ]);

        return redirect()->route('admin.payment-gateways')->with('success', 'Payment gateway updated successfully!');
    }

    // CMS Management
    public function cmsPages(Request $request)
    {
        $query = \App\Models\CmsPage::with(['category', 'pageType']);
        
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
        }
        
        if ($request->status !== null) {
            $query->where('is_published', $request->status);
        }
        
        if ($request->category) {
            $query->where('cms_category_id', $request->category);
        }
        
        if ($request->page_type) {
            $query->where('cms_page_type_id', $request->page_type);
        }
        
        $pages = $query->latest()->paginate(10)->appends($request->query());
        $categories = \App\Models\CmsCategory::where('is_active', true)->get();
        $pageTypes = \App\Models\CmsPageType::where('is_active', true)->get();
        
        return view('admin.cms.index', compact('pages', 'categories', 'pageTypes'));
    }

    public function createCmsPage()
    {
        $categories = CmsCategory::where('is_active', true)->get();
        $pageTypes = CmsPageType::where('is_active', true)->get();
        $languages = Language::getActiveLanguages();
        return view('admin.cms.create', compact('categories', 'pageTypes', 'languages'));
    }

    public function storeCmsPage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'language_code' => 'required|exists:languages,code'
        ]);

        $data = [
            'title' => $request->title,
            'body' => $request->body,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'cms_category_id' => $request->cms_category_id,
            'cms_page_type_id' => $request->cms_page_type_id,
            'custom_fields' => $request->custom_fields,
            'language_code' => $request->language_code,
            'is_published' => $request->has('is_published'),
            'allow_comments' => $request->has('allow_comments')
        ];
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cms', 'public');
        }

        $page = \App\Models\CmsPage::create($data);
        
        // Save translations for other languages
        if ($request->translations) {
            foreach ($request->translations as $langCode => $translation) {
                if (!empty($translation['title']) && $langCode !== $request->language_code) {
                    $page->translations()->create([
                        'language_code' => $langCode,
                        'title' => $translation['title'],
                        'body' => $translation['body'],
                        'meta_title' => $translation['meta_title'],
                        'meta_description' => $translation['meta_description'],
                        'meta_keywords' => $translation['meta_keywords']
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.cms.pages')->with('success', 'Page created successfully!');
    }

    public function editCmsPage($id)
    {
        $page = \App\Models\CmsPage::with('translations')->findOrFail($id);
        $categories = CmsCategory::where('is_active', true)->get();
        $pageTypes = CmsPageType::where('is_active', true)->get();
        $languages = Language::getActiveLanguages();
        return view('admin.cms.edit', compact('page', 'categories', 'pageTypes', 'languages'));
    }

    public function updateCmsPage(Request $request, $id)
    {
        $page = \App\Models\CmsPage::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cms_pages,slug,' . $id,
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'language_code' => 'required|exists:languages,code'
        ]);

        $data = [
            'title' => $request->title,
            'slug' => $request->slug,
            'body' => $request->body,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'cms_category_id' => $request->cms_category_id,
            'cms_page_type_id' => $request->cms_page_type_id,
            'custom_fields' => $request->custom_fields,
            'language_code' => $request->language_code,
            'is_published' => $request->has('is_published'),
            'allow_comments' => $request->has('allow_comments')
        ];
        
        // Handle image deletion
        if ($request->has('delete_image') && $page->image) {
            \Storage::disk('public')->delete($page->image);
            $data['image'] = null;
        }
        
        if ($request->hasFile('image')) {
            if ($page->image) {
                \Storage::disk('public')->delete($page->image);
            }
            $data['image'] = $request->file('image')->store('cms', 'public');
        }

        $page->update($data);
        
        // Update translations
        $page->translations()->delete();
        if ($request->translations) {
            foreach ($request->translations as $langCode => $translation) {
                if (!empty($translation['title']) && $langCode !== $request->language_code) {
                    $page->translations()->create([
                        'language_code' => $langCode,
                        'title' => $translation['title'],
                        'body' => $translation['body'],
                        'meta_title' => $translation['meta_title'],
                        'meta_description' => $translation['meta_description'],
                        'meta_keywords' => $translation['meta_keywords']
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.cms.pages')->with('success', 'Page updated successfully!');
    }

    public function deleteCmsPage($id)
    {
        \App\Models\CmsPage::findOrFail($id)->delete();
        return redirect()->route('admin.cms.pages')->with('success', 'Page deleted successfully!');
    }

    public function cmsComments()
    {
        $comments = \App\Models\CmsComment::with('page')->latest()->paginate(20);
        return view('admin.cms.comments', compact('comments'));
    }

    public function approveComment($id)
    {
        $comment = \App\Models\CmsComment::findOrFail($id);
        $comment->update(['is_approved' => !$comment->is_approved]);
        return redirect()->back()->with('success', 'Comment status updated!');
    }

    // CMS Categories Management
    public function cmsCategories()
    {
        $categories = CmsCategory::latest()->paginate(10);
        return view('admin.cms.categories', compact('categories'));
    }

    public function storeCmsCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cms_categories',
            'description' => 'nullable|string'
        ]);

        CmsCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.cms.categories')->with('success', 'Category created successfully!');
    }

    public function updateCmsCategory(Request $request, $id)
    {
        $category = CmsCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:cms_categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.cms.categories')->with('success', 'Category updated successfully!');
    }

    public function deleteCmsCategory($id)
    {
        CmsCategory::findOrFail($id)->delete();
        return redirect()->route('admin.cms.categories')->with('success', 'Category deleted successfully!');
    }

    // CMS Page Types Management
    public function cmsPageTypes(Request $request)
    {
        $query = CmsPageType::query();
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        if ($request->status !== null) {
            $query->where('is_active', $request->status);
        }
        
        $pageTypes = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.cms.page-types', compact('pageTypes'));
    }

    public function createCmsPageType()
    {
        return view('admin.cms.create-page-type');
    }

    public function storeCmsPageType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cms_page_types',
            'description' => 'nullable|string'
        ]);

        $customFields = [];
        if ($request->custom_fields) {
            foreach ($request->custom_fields as $field) {
                $customField = [
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => (bool)$field['required']
                ];
                
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    $customField['options'] = array_map('trim', explode(',', $field['options']));
                }
                
                $customFields[] = $customField;
            }
        }

        $fieldsConfig = [
            'show_comments' => $request->has('fields_config.show_comments'),
            'show_posted_date' => $request->has('fields_config.show_posted_date'),
            'show_author' => $request->has('fields_config.show_author'),
            'show_rating' => $request->has('fields_config.show_rating'),
            'custom_fields' => $customFields
        ];

        CmsPageType::create([
            'name' => $request->name,
            'description' => $request->description,
            'fields_config' => $fieldsConfig,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.cms.page-types')->with('success', 'Page type created successfully!');
    }

    public function editCmsPageType($id)
    {
        $pageType = CmsPageType::findOrFail($id);
        return view('admin.cms.edit-page-type', compact('pageType'));
    }

    public function updateCmsPageType(Request $request, $id)
    {
        $pageType = CmsPageType::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:cms_page_types,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $customFields = [];
        if ($request->custom_fields) {
            foreach ($request->custom_fields as $field) {
                $customField = [
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => (bool)$field['required']
                ];
                
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    $customField['options'] = array_map('trim', explode(',', $field['options']));
                }
                
                $customFields[] = $customField;
            }
        }

        $fieldsConfig = [
            'show_comments' => $request->has('fields_config.show_comments'),
            'show_posted_date' => $request->has('fields_config.show_posted_date'),
            'show_author' => $request->has('fields_config.show_author'),
            'show_rating' => $request->has('fields_config.show_rating'),
            'custom_fields' => $customFields
        ];

        $pageType->update([
            'name' => $request->name,
            'description' => $request->description,
            'fields_config' => $fieldsConfig,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.cms.page-types')->with('success', 'Page type updated successfully!');
    }

    public function deleteCmsPageType($id)
    {
        CmsPageType::findOrFail($id)->delete();
        return redirect()->route('admin.cms.page-types')->with('success', 'Page type deleted successfully!');
    }

    // Notifications Management
    public function notifications()
    {
        $notifications = \App\Models\Notification::latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        $notification = \App\Models\Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
        
        if ($notification->data && isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }
        
        return redirect()->route('admin.notifications');
    }

    public function markAllNotificationsRead()
    {
        \App\Models\Notification::where('is_read', false)->update(['is_read' => true]);
        return redirect()->back()->with('success', 'All notifications marked as read!');
    }

    // Footer Settings
    public function footerSettings()
    {
        return view('admin.footer.settings');
    }

    public function updateFooterSettings(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'copyright_text' => 'nullable|string'
        ]);

        foreach ($request->except(['_token']) as $key => $value) {
            \App\Models\FooterSetting::set($key, $value);
        }

        return redirect()->route('admin.footer.settings')->with('success', 'Footer settings updated successfully!');
    }

    // Contact Management
    public function contactSubmissions(Request $request)
    {
        $query = ContactSubmission::query();
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
        }
        
        if ($request->status !== null) {
            $query->where('is_read', $request->status);
        }
        
        $submissions = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.contact.submissions', compact('submissions'));
    }

    public function viewContactSubmission($id)
    {
        $submission = ContactSubmission::findOrFail($id);
        $submission->update(['is_read' => true]);
        return view('admin.contact.view', compact('submission'));
    }

    public function deleteContactSubmission($id)
    {
        ContactSubmission::findOrFail($id)->delete();
        return redirect()->route('admin.contact.submissions')->with('success', 'Submission deleted successfully!');
    }

    public function contactSettings()
    {
        return view('admin.contact.settings');
    }

    public function updateContactSettings(Request $request)
    {
        $request->validate([
            'admin_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
            'business_hours' => 'nullable|string'
        ]);

        ContactSetting::set('admin_email', $request->admin_email);
        ContactSetting::set('contact_phone', $request->contact_phone);
        ContactSetting::set('contact_address', $request->contact_address);
        ContactSetting::set('business_hours', $request->business_hours);
        ContactSetting::set('show_contact_info', $request->has('show_contact_info'));

        return redirect()->route('admin.contact.settings')->with('success', 'Contact settings updated successfully!');
    }

    // Lists Management
    public function productLists(Request $request)
    {
        $query = \App\Models\AdminList::where('type', 'products');
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $lists = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.lists.products', compact('lists'));
    }

    public function pageLists(Request $request)
    {
        $query = \App\Models\AdminList::where('type', 'pages');
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $lists = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.lists.pages', compact('lists'));
    }

    public function createList($type)
    {
        if (!in_array($type, ['products', 'pages'])) {
            abort(404);
        }
        
        $items = $type === 'products' ? Product::all() : \App\Models\CmsPage::all();
        $categories = $type === 'products' ? Category::where('is_active', true)->get() : CmsCategory::where('is_active', true)->get();
        $pageTypes = $type === 'pages' ? CmsPageType::where('is_active', true)->get() : collect();
        
        return view('admin.lists.create', compact('type', 'items', 'categories', 'pageTypes'));
    }

    public function storeList(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:products,pages',
            'method' => 'required|in:sql,manual,query_builder',
            'description' => 'nullable|string'
        ]);

        $configuration = [];
        
        switch ($request->method) {
            case 'sql':
                $request->validate(['sql_query' => 'required|string']);
                $configuration['sql'] = $request->sql_query;
                break;
                
            case 'manual':
                $request->validate(['selected_ids' => 'required|array']);
                $configuration['selected_ids'] = $request->selected_ids;
                break;
                
            case 'query_builder':
                $configuration['filters'] = $request->filters ?? [];
                break;
        }

        \App\Models\AdminList::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'method' => $request->method,
            'configuration' => $configuration,
            'is_active' => $request->has('is_active'),
            'is_template' => $request->has('is_template'),
            'template_name' => $request->template_name,
            'template_category' => $request->template_category,
            'create_page' => $request->has('create_page'),
            'page_title' => $request->page_title,
            'page_slug' => $request->page_slug,
            'page_description' => $request->page_description,
            'items_per_page' => $request->items_per_page ?: 12
        ]);

        $route = $request->type === 'products' ? 'admin.lists.products' : 'admin.lists.pages';
        return redirect()->route($route)->with('success', 'List created successfully!');
    }

    public function editList(\App\Models\AdminList $list)
    {
        $items = $list->type === 'products' ? Product::all() : \App\Models\CmsPage::all();
        $categories = $list->type === 'products' ? Category::where('is_active', true)->get() : CmsCategory::where('is_active', true)->get();
        $pageTypes = $list->type === 'pages' ? CmsPageType::where('is_active', true)->get() : collect();
        
        return view('admin.lists.edit', compact('list', 'items', 'categories', 'pageTypes'));
    }

    public function updateList(Request $request, \App\Models\AdminList $list)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'method' => 'required|in:sql,manual,query_builder',
            'description' => 'nullable|string'
        ]);

        $configuration = [];
        
        switch ($request->method) {
            case 'sql':
                $request->validate(['sql_query' => 'required|string']);
                $configuration['sql'] = $request->sql_query;
                break;
                
            case 'manual':
                $request->validate(['selected_ids' => 'required|array']);
                $configuration['selected_ids'] = $request->selected_ids;
                break;
                
            case 'query_builder':
                $configuration['filters'] = $request->filters ?? [];
                break;
        }

        $list->update([
            'name' => $request->name,
            'description' => $request->description,
            'method' => $request->method,
            'configuration' => $configuration,
            'is_active' => $request->has('is_active'),
            'is_template' => $request->has('is_template'),
            'create_page' => $request->has('create_page'),
            'page_title' => $request->page_title,
            'page_slug' => $request->page_slug,
            'page_description' => $request->page_description,
            'items_per_page' => $request->items_per_page ?: 12
        ]);

        $route = $list->type === 'products' ? 'admin.lists.products' : 'admin.lists.pages';
        return redirect()->route($route)->with('success', 'List updated successfully!');
    }

    public function deleteList(\App\Models\AdminList $list)
    {
        $list->delete();
        $route = $list->type === 'products' ? 'admin.lists.products' : 'admin.lists.pages';
        return redirect()->route($route)->with('success', 'List deleted successfully!');
    }

    // Templates Management
    public function templates(Request $request)
    {
        $query = \App\Models\AdminList::where('is_template', true);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('template_name', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->category) {
            $query->where('template_category', $request->category);
        }
        
        $templates = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.lists.templates', compact('templates'));
    }

    public function deleteTemplate(\App\Models\AdminList $template)
    {
        $template->delete();
        return redirect()->route('admin.lists.templates')->with('success', 'Template deleted successfully!');
    }

    // Dynamic Pages Management
    public function dynamicPages(Request $request)
    {
        $query = \App\Models\DynamicPage::query();
        
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('system_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->status !== null) {
            $query->where('is_published', $request->status);
        }
        
        $pages = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.dynamic-pages.index', compact('pages'));
    }

    public function createDynamicPage()
    {
        $lists = \App\Models\AdminList::where('is_active', true)->get();
        return view('admin.dynamic-pages.create', compact('lists'));
    }

    public function storeDynamicPage(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255|unique:dynamic_pages',
            'sections' => 'nullable|array'
        ]);

        // Process sections to handle checkbox values
        $sections = $request->sections ?? [];
        foreach ($sections as &$section) {
            // Handle checkboxes - if not present, they are false
            $section['show_dots'] = isset($section['show_dots']) && $section['show_dots'] === 'on';
            $section['show_arrows'] = isset($section['show_arrows']) && $section['show_arrows'] === 'on';
            $section['auto_rotate'] = isset($section['auto_rotate']) && $section['auto_rotate'] === 'on';
            $section['make_clickable'] = isset($section['make_clickable']) && $section['make_clickable'] === 'on';
            $section['show_read_more'] = isset($section['show_read_more']) && $section['show_read_more'] === 'on';
        }

        \App\Models\DynamicPage::create([
            'system_name' => $request->system_name,
            'title' => $request->title,
            'url' => $request->url,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'sections' => $sections,
            'custom_css' => $request->custom_css,
            'custom_js' => $request->custom_js,
            'external_css' => array_filter($request->external_css ?? []),
            'external_js' => array_filter($request->external_js ?? []),
            'is_published' => $request->has('is_published')
        ]);

        return redirect()->route('admin.dynamic-pages.index')->with('success', 'Dynamic page created successfully!');
    }

    public function editDynamicPage($id)
    {
        $page = \App\Models\DynamicPage::findOrFail($id);
        $lists = \App\Models\AdminList::where('is_active', true)->get();
        return view('admin.dynamic-pages.edit', compact('page', 'lists'));
    }

    public function updateDynamicPage(Request $request, $id)
    {
        $page = \App\Models\DynamicPage::findOrFail($id);
        
        $request->validate([
            'system_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255|unique:dynamic_pages,url,' . $id,
            'sections' => 'nullable|array'
        ]);

        // Process sections to handle checkbox values
        $sections = $request->sections ?? [];
        foreach ($sections as &$section) {
            // Handle checkboxes - if not present, they are false
            $section['show_dots'] = isset($section['show_dots']) && $section['show_dots'] === 'on';
            $section['show_arrows'] = isset($section['show_arrows']) && $section['show_arrows'] === 'on';
            $section['auto_rotate'] = isset($section['auto_rotate']) && $section['auto_rotate'] === 'on';
            $section['make_clickable'] = isset($section['make_clickable']) && $section['make_clickable'] === 'on';
            $section['show_read_more'] = isset($section['show_read_more']) && $section['show_read_more'] === 'on';
        }

        $page->update([
            'system_name' => $request->system_name,
            'title' => $request->title,
            'url' => $request->url,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'sections' => $sections,
            'custom_css' => $request->custom_css,
            'custom_js' => $request->custom_js,
            'external_css' => array_filter($request->external_css ?? []),
            'external_js' => array_filter($request->external_js ?? []),
            'is_published' => $request->has('is_published')
        ]);

        return redirect()->route('admin.dynamic-pages.index')->with('success', 'Dynamic page updated successfully!');
    }

    public function deleteDynamicPage($id)
    {
        \App\Models\DynamicPage::findOrFail($id)->delete();
        return redirect()->route('admin.dynamic-pages.index')->with('success', 'Dynamic page deleted successfully!');
    }

    public function previewList(Request $request)
    {
        $configuration = [];
        
        switch ($request->method) {
            case 'sql':
                try {
                    $results = collect(\DB::select($request->sql_query));
                    return response()->json(['count' => $results->count(), 'results' => $results->take(5)]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid SQL query: ' . $e->getMessage()], 400);
                }
                
            case 'manual':
                $count = count($request->selected_ids ?? []);
                return response()->json(['count' => $count]);
                
            case 'query_builder':
                $model = $request->type === 'products' ? Product::query() : \App\Models\CmsPage::query();
                
                foreach ($request->filters ?? [] as $filter) {
                    if (empty($filter['field']) || empty($filter['operator']) || $filter['value'] === '') {
                        continue;
                    }
                    
                    $field = $filter['field'];
                    $operator = $filter['operator'];
                    $value = $filter['value'];
                    
                    // Handle specific field mappings
                    if ($request->type === 'pages' && $field === 'page_type_id') {
                        $field = 'cms_page_type_id';
                    }
                    
                    if ($operator === 'like') {
                        $model->where($field, 'like', '%' . $value . '%');
                    } else {
                        $model->where($field, $operator, $value);
                    }
                }
                
                $results = $model->get();
                return response()->json(['count' => $results->count(), 'results' => $results->take(5)]);
        }
        
        return response()->json(['count' => 0]);
    }
}