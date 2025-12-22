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

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'consultations' => Consultation::count(),
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
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
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
}