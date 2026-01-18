@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Media Manager</h1>
        <div>
            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload"></i> Upload File
            </button>
            <button class="btn btn-success" data-toggle="modal" data-target="#folderModal">
                <i class="fas fa-folder-plus"></i> New Folder
            </button>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.media') }}">Media</a></li>
            @if($folder)
                @php
                    $parts = explode('/', $folder);
                    $path = '';
                @endphp
                @foreach($parts as $part)
                    @php $path .= ($path ? '/' : '') . $part; @endphp
                    <li class="breadcrumb-item"><a href="{{ route('admin.media', ['folder' => $path]) }}">{{ $part }}</a></li>
                @endforeach
            @endif
        </ol>
    </nav>

    <!-- Folders -->
    @if(count($folders) > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Folders</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($folders as $dir)
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <div class="text-center folder-item">
                        <a href="{{ route('admin.media', ['folder' => $dir['path']]) }}" class="text-decoration-none">
                            <i class="fas fa-folder fa-4x text-warning"></i>
                            <p class="mt-2 mb-0 small">{{ $dir['name'] }}</p>
                        </a>
                        <button class="btn btn-sm btn-danger mt-1" onclick="deleteFolder('{{ $dir['path'] }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Files -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Files</h5>
        </div>
        <div class="card-body">
            @if(count($files) > 0)
            <div class="row">
                @foreach($files as $file)
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <div class="card file-item">
                        <div class="card-body text-center p-2">
                            @if(str_starts_with($file['type'], 'image/'))
                                <img src="{{ $file['url'] }}" class="img-fluid mb-2" style="max-height: 100px; object-fit: cover;">
                            @else
                                <i class="fas fa-file fa-4x text-secondary mb-2"></i>
                            @endif
                            <p class="mb-1 small text-truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
                            <small class="text-muted">{{ number_format($file['size'] / 1024, 2) }} KB</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-info" onclick="copyUrl('{{ $file['url'] }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteFile('{{ $file['path'] }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-muted">No files in this folder</p>
            @endif
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="folder" value="{{ $folder }}">
                    <div class="mb-3">
                        <label class="form-label">Select File</label>
                        <input type="file" class="form-control" name="file" required>
                        <small class="text-muted">Max size: 10MB</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- New Folder Modal -->
<div class="modal fade" id="folderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Folder</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="folderForm">
                    @csrf
                    <input type="hidden" name="parent" value="{{ $folder }}">
                    <div class="mb-3">
                        <label class="form-label">Folder Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            
            fetch('{{ route('admin.media.upload') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    $('#uploadModal').modal('hide');
                    Swal.fire('Success!', 'File uploaded successfully', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', 'Upload failed', 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Upload';
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Upload failed: ' + error, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Upload';
            });
        });
    }

    const folderForm = document.getElementById('folderForm');
    if (folderForm) {
        folderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('{{ route('admin.media.folder.create') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    $('#folderModal').modal('hide');
                    Swal.fire('Success!', 'Folder created successfully', 'success').then(() => location.reload());
                }
            });
        });
    }
});

function deleteFile(path) {
    Swal.fire({
        title: 'Delete File?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('admin.media.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ path: path })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Deleted!', 'File has been deleted.', 'success').then(() => location.reload());
                }
            });
        }
    });
}

function deleteFolder(path) {
    Swal.fire({
        title: 'Delete Folder?',
        text: "This will delete the folder and all its contents!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('admin.media.folder.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ path: path })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Deleted!', 'Folder has been deleted.', 'success').then(() => location.reload());
                }
            });
        }
    });
}

function copyUrl(url) {
    // Fallback for older browsers
    if (!navigator.clipboard) {
        const textArea = document.createElement('textarea');
        textArea.value = url;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'URL copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        } catch (err) {
            Swal.fire('Error!', 'Failed to copy URL', 'error');
        }
        document.body.removeChild(textArea);
        return;
    }
    
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'URL copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    }).catch(err => {
        Swal.fire('Error!', 'Failed to copy URL', 'error');
    });
}
</script>
@endpush
