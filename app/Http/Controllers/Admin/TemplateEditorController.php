<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TemplateEditorController extends Controller
{
    protected $templatesPath = 'resources/views/dynamic-pages/custom-templates';
    protected $allowedExtensions = ['blade.php', 'php'];

    /**
     * Display list of template files
     */
    public function index()
    {
        $templatesPath = base_path($this->templatesPath);
        
        // Create directory if it doesn't exist
        if (!File::exists($templatesPath)) {
            File::makeDirectory($templatesPath, 0755, true);
        }

        $files = $this->getTemplateFiles($templatesPath);
        
        return view('admin.template-editor.index', compact('files'));
    }

    /**
     * Show the editor for a specific file
     */
    public function edit($filename)
    {
        if (!$this->isValidFilename($filename)) {
            return redirect()->route('admin.template-editor.index')
                ->with('error', 'Invalid filename.');
        }

        $filePath = base_path($this->templatesPath . '/' . $filename);

        if (!File::exists($filePath)) {
            return redirect()->route('admin.template-editor.index')
                ->with('error', 'File not found.');
        }

        $content = File::get($filePath);
        $files = $this->getTemplateFiles(base_path($this->templatesPath));

        return view('admin.template-editor.edit', compact('filename', 'content', 'files'));
    }

    /**
     * Update the template file
     */
    public function update(Request $request, $filename)
    {
        if (!$this->isValidFilename($filename)) {
            return response()->json(['success' => false, 'message' => 'Invalid filename.'], 400);
        }

        $request->validate([
            'content' => 'required|string'
        ]);

        $filePath = base_path($this->templatesPath . '/' . $filename);

        if (!File::exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        try {
            // Create backup before updating
            $backupPath = base_path($this->templatesPath . '/backups');
            
            // If filename contains subdirectory, preserve structure in backups
            $backupFile = $backupPath . '/' . $filename . '.' . time() . '.backup';
            $backupDir = dirname($backupFile);
            
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            File::copy($filePath, $backupFile);

            // Update the file
            File::put($filePath, $request->content);

            // Keep only last 5 backups
            $this->cleanupBackups($backupPath, $filename);

            return response()->json([
                'success' => true, 
                'message' => 'Template updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error updating template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new template file
     */
    public function create(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|regex:/^[a-zA-Z0-9_-]+\.blade\.php$/',
            'content' => 'nullable|string'
        ]);

        $filename = $request->filename;
        $filePath = base_path($this->templatesPath . '/' . $filename);

        if (File::exists($filePath)) {
            return response()->json([
                'success' => false, 
                'message' => 'File already exists.'
            ], 400);
        }

        try {
            $content = $request->content ?? "{{-- New Template: {$filename} --}}\n\n@extends('layouts.app')\n\n@section('content')\n<div class=\"container mx-auto px-4 py-8\">\n    <h1>New Template</h1>\n</div>\n@endsection\n";
            
            File::put($filePath, $content);

            return response()->json([
                'success' => true, 
                'message' => 'Template created successfully!',
                'redirect' => route('admin.template-editor.edit', $filename)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error creating template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a template file
     */
    public function destroy($filename)
    {
        if (!$this->isValidFilename($filename)) {
            return response()->json(['success' => false, 'message' => 'Invalid filename.'], 400);
        }

        $filePath = base_path($this->templatesPath . '/' . $filename);

        if (!File::exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        try {
            // Create backup before deleting
            $backupPath = base_path($this->templatesPath . '/backups/deleted');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $backupFile = $backupPath . '/' . $filename . '.' . time() . '.deleted';
            File::copy($filePath, $backupFile);

            // Delete the file
            File::delete($filePath);

            return response()->json([
                'success' => true, 
                'message' => 'Template deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of template files
     */
    protected function getTemplateFiles($path)
    {
        if (!File::exists($path)) {
            return [];
        }

        $files = File::allFiles($path);
        $templates = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Skip backup files and hidden files
            if (Str::startsWith($filename, '.') || Str::contains($relativePath, ['.backup', 'backups/'])) {
                continue;
            }

            // Only include allowed extensions
            $isAllowed = false;
            foreach ($this->allowedExtensions as $ext) {
                if (Str::endsWith($filename, $ext)) {
                    $isAllowed = true;
                    break;
                }
            }

            if ($isAllowed) {
                $templates[] = [
                    'name' => $relativePath,
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'path' => $file->getPathname()
                ];
            }
        }

        // Sort by name
        usort($templates, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $templates;
    }

    /**
     * Validate filename
     */
    protected function isValidFilename($filename)
    {
        // Prevent directory traversal outside templates directory
        if (Str::contains($filename, ['..'])) {
            return false;
        }

        // Check allowed extensions
        foreach ($this->allowedExtensions as $ext) {
            if (Str::endsWith($filename, $ext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cleanup old backups
     */
    protected function cleanupBackups($backupPath, $filename)
    {
        $backups = File::glob($backupPath . '/' . $filename . '.*.backup');
        
        if (count($backups) > 5) {
            // Sort by modification time
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Delete oldest backups
            $toDelete = array_slice($backups, 0, count($backups) - 5);
            foreach ($toDelete as $backup) {
                File::delete($backup);
            }
        }
    }

    /**
     * Download a template file
     */
    public function download($filename)
    {
        if (!$this->isValidFilename($filename)) {
            return redirect()->route('admin.template-editor.index')
                ->with('error', 'Invalid filename.');
        }

        $filePath = base_path($this->templatesPath . '/' . $filename);

        if (!File::exists($filePath)) {
            return redirect()->route('admin.template-editor.index')
                ->with('error', 'File not found.');
        }

        return response()->download($filePath);
    }
}
