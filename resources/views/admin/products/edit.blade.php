@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Product</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products') }}">Products</a></li>
                    <li class="breadcrumb-item active">Edit Product</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Information</h3>
            </div>
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price ({{ \App\Models\Currency::getDefaultCurrency()->symbol }} {{ \App\Models\Currency::getDefaultCurrency()->code }})</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock_quantity">Stock Quantity</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                                        <option value="{{ $category->slug }}" {{ $product->category == $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">Main Product Image</label>
                                @if($product->image)
                                    <div class="mb-2">
                                        <img src="{{ asset($product->image) }}" alt="Current image" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                @endif
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="gallery_images">Gallery Images</label>
                                @if($product->images && count($product->images) > 0)
                                    <div class="mb-2">
                                        <div class="row">
                                            @foreach($product->images as $index => $galleryImage)
                                                <div class="col-md-3 mb-2" id="gallery-image-{{ $index }}">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $galleryImage) }}" alt="Gallery image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                        <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: -5px; right: -5px; padding: 2px 6px;" onclick="removeGalleryImage({{ $index }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <input type="hidden" name="existing_gallery_images[]" value="{{ $galleryImage }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                        <label class="custom-file-label" for="gallery_images">Add more images</label>
                                    </div>
                                </div>
                                <small class="text-muted">You can select multiple images to add to the gallery</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <div id="editor" style="height: 300px;"></div>
                                <textarea class="form-control" id="description" name="description" style="display: none;" required>{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="specifications">Specifications (one per line)</label>
                                <textarea class="form-control" id="specifications" name="specifications" rows="4" placeholder="Weight: 3.5 carats&#10;Origin: Burma&#10;Certification: GIA Certified">{{ old('specifications', is_array($product->specifications) ? implode("\n", $product->specifications) : '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="features">Features (one per line)</label>
                                <textarea class="form-control" id="features" name="features" rows="4" placeholder="Authentic and certified product&#10;Free shipping on orders above ₹500&#10;7-day return policy&#10;Secure payment options">{{ old('features', is_array($product->features) ? implode("\n", $product->features) : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_stock" name="show_stock" value="1" {{ $product->show_stock ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_stock">Show Stock Quantity</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="{{ route('admin.products') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Initialize Quill editor
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
        ]
    }
});

// Set initial content
quill.root.innerHTML = {!! json_encode($product->description) !!};

// Update hidden textarea on form submit
document.querySelector('form').addEventListener('submit', function() {
    document.querySelector('#description').value = quill.root.innerHTML;
});

// Remove gallery image function with SweetAlert
function removeGalleryImage(index) {
    console.log('Removing image at index:', index); // Debug log

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to remove this image from the gallery?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC143C',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Hide the image div and mark input as deleted
            const imageDiv = document.getElementById('gallery-image-' + index);

            if (imageDiv) {
                const hiddenInput = imageDiv.querySelector('input[name="existing_gallery_images[]"]');

                imageDiv.style.display = 'none';
                if (hiddenInput) {
                    hiddenInput.name = 'deleted_gallery_images[]';
                }

                Swal.fire({
                    title: 'Removed!',
                    text: 'The image has been removed from the gallery.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                console.error('Image div not found:', 'gallery-image-' + index);
            }
        }
    });
}
</script>
@endsection
