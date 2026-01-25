@extends('admin.layouts.app')

@section('title', 'Template Editor')

@section('content')
<div class="content-fluid">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Template Editor</h1>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createTemplateModal">
                        <i class="fas fa-plus"></i> Create New Template
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Template Files</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search templates...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    @if(count($files) > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Template Name</th>
                                    <th>Size</th>
                                    <th>Last Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="templateTable">
                                @foreach($files as $file)
                                    <tr class="template-row">
                                        <td>
                                            <i class="fas fa-file-code text-primary mr-2"></i>
                                            <strong class="template-name">{{ $file['name'] }}</strong>
                                        </td>
                                        <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                        <td>{{ date('M d, Y h:i A', $file['modified']) }}</td>
                                        <td>
                                            <a href="{{ route('admin.template-editor.edit', $file['name']) }}" class="btn btn-sm btn-info" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('admin.template-editor.download', $file['name']) }}" class="btn btn-sm btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteTemplate('{{ $file['name'] }}')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-3">No template files found. Create your first template!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Template</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="createTemplateForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="filename">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="filename" name="filename" placeholder="example-template.blade.php" required>
                        <small class="form-text text-muted">Must end with .blade.php</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.template-row');
    rows.forEach(row => {
        const templateName = row.querySelector('.template-name').textContent.toLowerCase();
        row.style.display = templateName.includes(searchTerm) ? '' : 'none';
    });
});

document.getElementById('createTemplateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    
    fetch('{{ route("admin.template-editor.create") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({filename: formData.get('filename')})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({icon: 'success', title: 'Success!', text: data.message, timer: 2000})
            .then(() => window.location.href = data.redirect || window.location.href);
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.message});
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus"></i> Create Template';
        }
    });
});

function deleteTemplate(filename) {
    Swal.fire({
        title: 'Delete Template?',
        text: `Delete "${filename}"? A backup will be created.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/template-editor/${filename}`, {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({icon: 'success', title: 'Deleted!', text: data.message, timer: 2000})
                    .then(() => window.location.reload());
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: data.message});
                }
            });
        }
    });
}
</script>
@endpush
