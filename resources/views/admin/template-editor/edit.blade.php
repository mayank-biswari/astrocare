@extends('admin.layouts.app')

@section('title', 'Edit Template - ' . $filename)

@section('content')
<div class="content-fluid">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-code"></i> {{ $filename }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <button type="button" id="saveBtn" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.template-editor.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Template Files</h3>
                        </div>
                        <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                            <ul class="nav nav-pills flex-column">
                                @foreach($files as $file)
                                    <li class="nav-item">
                                        <a href="{{ route('admin.template-editor.edit', $file['name']) }}" 
                                           class="nav-link {{ $file['name'] === $filename ? 'active' : '' }}">
                                            <i class="fas fa-file-code mr-2"></i>
                                            {{ $file['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Editor Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Theme</label>
                                <select id="themeSelect" class="form-control form-control-sm">
                                    <option value="monokai">Monokai</option>
                                    <option value="github">GitHub</option>
                                    <option value="tomorrow">Tomorrow</option>
                                    <option value="twilight">Twilight</option>
                                    <option value="solarized_dark">Solarized Dark</option>
                                    <option value="solarized_light">Solarized Light</option>
                                    <option value="dracula">Dracula</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Font Size</label>
                                <select id="fontSizeSelect" class="form-control form-control-sm">
                                    <option value="12">12px</option>
                                    <option value="14" selected>14px</option>
                                    <option value="16">16px</option>
                                    <option value="18">18px</option>
                                    <option value="20">20px</option>
                                </select>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="wordWrapCheck" checked>
                                <label class="form-check-label" for="wordWrapCheck">Word Wrap</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Code Editor</h3>
                            <div class="card-tools">
                                <span class="badge badge-info">Ctrl+S to Save</span>
                                <span class="badge badge-secondary">Ctrl+F to Find</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="editor" style="height: 600px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.ace_editor {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ext-language_tools.js"></script>
<script>
let editor;
let hasUnsavedChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Ace Editor
    editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/php");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true,
        fontSize: 14,
        showPrintMargin: false,
        wrap: true
    });

    // Set initial content
    editor.setValue(@json($content), -1);

    // Track changes
    editor.session.on('change', function() {
        hasUnsavedChanges = true;
    });

    // Theme change
    document.getElementById('themeSelect').addEventListener('change', function() {
        editor.setTheme("ace/theme/" + this.value);
        localStorage.setItem('editorTheme', this.value);
    });

    // Font size change
    document.getElementById('fontSizeSelect').addEventListener('change', function() {
        editor.setFontSize(parseInt(this.value));
        localStorage.setItem('editorFontSize', this.value);
    });

    // Word wrap toggle
    document.getElementById('wordWrapCheck').addEventListener('change', function() {
        editor.session.setUseWrapMode(this.checked);
        localStorage.setItem('editorWordWrap', this.checked);
    });

    // Load saved preferences
    const savedTheme = localStorage.getItem('editorTheme');
    const savedFontSize = localStorage.getItem('editorFontSize');
    const savedWordWrap = localStorage.getItem('editorWordWrap');

    if (savedTheme) {
        document.getElementById('themeSelect').value = savedTheme;
        editor.setTheme("ace/theme/" + savedTheme);
    }
    if (savedFontSize) {
        document.getElementById('fontSizeSelect').value = savedFontSize;
        editor.setFontSize(parseInt(savedFontSize));
    }
    if (savedWordWrap !== null) {
        const wordWrap = savedWordWrap === 'true';
        document.getElementById('wordWrapCheck').checked = wordWrap;
        editor.session.setUseWrapMode(wordWrap);
    }

    // Save button
    document.getElementById('saveBtn').addEventListener('click', saveTemplate);

    // Keyboard shortcut Ctrl+S
    editor.commands.addCommand({
        name: 'save',
        bindKey: {win: 'Ctrl-S', mac: 'Command-S'},
        exec: function(editor) {
            saveTemplate();
        }
    });

    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});

function saveTemplate() {
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('{{ route("admin.template-editor.update", $filename) }}', {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            content: editor.getValue()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hasUnsavedChanges = false;
            Swal.fire({
                icon: 'success',
                title: 'Saved!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while saving the template.'
        });
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
    });
}
</script>
@endpush
