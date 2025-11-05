<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileTestController extends Controller
{
    /**
     * Test file upload and retrieval
     */
    public function testFileOperations()
    {
        $results = [
            'spaces_configured' => false,
            'helper_functions' => false,
            'file_service' => false,
            'file_upload_test' => null,
            'file_retrieval_test' => null,
        ];

        try {
            // Test 1: Check if Spaces is configured
            $results['spaces_configured'] = !empty(config('filesystems.disks.spaces.key'));

            // Test 2: Check if helper functions are loaded
            $results['helper_functions'] = function_exists('file_service');

            // Test 3: Check if FileService can be instantiated
            $fileService = app(\App\Services\FileService::class);
            $results['file_service'] = $fileService !== null;

            // Test 4: Test file upload
            $testContent = "Test file content - " . now()->toDateTimeString();
            $testPath = 'test-files/test-' . time() . '.txt';
            
            $uploaded = file_service()->uploadContent($testContent, $testPath);
            $results['file_upload_test'] = $uploaded ? 'Success' : 'Failed';

            // Test 5: Test file retrieval
            if ($uploaded) {
                $fileUrl = file_url($testPath);
                $fileExists = file_exists_anywhere($testPath);
                $fileInfo = file_service()->getFileInfo($testPath);
                
                $results['file_retrieval_test'] = [
                    'url' => $fileUrl,
                    'exists' => $fileExists,
                    'info' => $fileInfo,
                ];

                // Clean up test file
                delete_file_anywhere($testPath);
            }

        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
        }

        return response()->json($results, 200);
    }

    /**
     * Display file upload test page
     */
    public function showTestPage()
    {
        return view('file-test');
    }

    /**
     * Handle file upload test
     */
    public function uploadTest(Request $request)
    {
        $request->validate([
            'test_file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('test_file');
            $filename = 'test-' . time() . '-' . $file->getClientOriginalName();
            
            // Upload to Spaces
            $path = upload_file($file, 'test-uploads', $filename);

            if (!$path) {
                return back()->withErrors(['error' => 'Failed to upload file to Spaces']);
            }

            // Get file info
            $fileInfo = file_service()->getFileInfo($path);
            $fileUrl = file_url($path);

            return back()->with([
                'success' => 'File uploaded successfully!',
                'file_path' => $path,
                'file_url' => $fileUrl,
                'file_info' => $fileInfo,
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Check file status (Spaces vs Local)
     */
    public function checkFileStatus(Request $request)
    {
        $path = $request->input('path');

        if (!$path) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        $fileInfo = file_service()->getFileInfo($path);

        return response()->json([
            'path' => $path,
            'info' => $fileInfo,
            'url' => file_url($path),
        ]);
    }
}


