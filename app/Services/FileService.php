<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class FileService
{
    /**
     * The primary disk for new uploads (Spaces)
     */
    const PRIMARY_DISK = 'spaces';
    
    /**
     * The fallback disk for old files (Local)
     */
    const FALLBACK_DISK = 'public';
    
    /**
     * Upload a file to Spaces
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @param string $visibility
     * @return string|false Path to the uploaded file or false on failure
     */
    public function upload(UploadedFile $file, string $directory, ?string $filename = null, string $visibility = 'public')
    {
        try {
            // Generate filename if not provided
            if (!$filename) {
                $filename = time() . '-' . $file->getClientOriginalName();
            }
            
            // Clean directory path
            $directory = trim($directory, '/');
            $fullPath = $directory . '/' . $filename;
            
            // Upload to Spaces
            $uploaded = Storage::disk(self::PRIMARY_DISK)->put(
                $fullPath,
                file_get_contents($file->getRealPath()),
                $visibility
            );
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload file to Spaces');
            }
            
            Log::info('File uploaded to Spaces', [
                'path' => $fullPath,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);
            
            return $fullPath;
            
        } catch (\Exception $e) {
            Log::error('File upload to Spaces failed', [
                'directory' => $directory,
                'filename' => $filename,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * Upload file content directly to Spaces
     * 
     * @param string $content
     * @param string $path
     * @param string $visibility
     * @return bool
     */
    public function uploadContent(string $content, string $path, string $visibility = 'public'): bool
    {
        try {
            $path = trim($path, '/');
            
            $uploaded = Storage::disk(self::PRIMARY_DISK)->put($path, $content, $visibility);
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload content to Spaces');
            }
            
            Log::info('Content uploaded to Spaces', ['path' => $path]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Content upload to Spaces failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * Check if file exists in Spaces or local storage
     * 
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        $path = trim($path, '/');
        
        // Check in Spaces first
        if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
            return true;
        }
        
        // Fallback to local storage
        if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get file URL - checks Spaces first, then local storage
     * 
     * @param string $path
     * @return string|null
     */
    public function url(string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        
        $path = trim($path, '/');
        
        try {
            // Check if file exists in Spaces
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                return Storage::disk(self::PRIMARY_DISK)->url($path);
            }
            
            // Fallback to local storage
            if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                // Use custom route to serve local files
                return url('/files/' . $path);
            }
            
            Log::warning('File not found in any storage', ['path' => $path]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error getting file URL', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Get file content - checks Spaces first, then local storage
     * 
     * @param string $path
     * @return string|null
     */
    public function get(string $path): ?string
    {
        $path = trim($path, '/');
        
        try {
            // Try Spaces first
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                return Storage::disk(self::PRIMARY_DISK)->get($path);
            }
            
            // Fallback to local storage
            if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                return Storage::disk(self::FALLBACK_DISK)->get($path);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error reading file', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Delete file from both Spaces and local storage
     * 
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $path = trim($path, '/');
        $deleted = false;
        
        try {
            // Delete from Spaces if exists
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                Storage::disk(self::PRIMARY_DISK)->delete($path);
                $deleted = true;
                Log::info('File deleted from Spaces', ['path' => $path]);
            }
            
            // Delete from local storage if exists
            if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                Storage::disk(self::FALLBACK_DISK)->delete($path);
                $deleted = true;
                Log::info('File deleted from local storage', ['path' => $path]);
            }
            
            return $deleted;
            
        } catch (\Exception $e) {
            Log::error('Error deleting file', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Download file response - checks Spaces first, then local storage
     * 
     * @param string $path
     * @param string|null $downloadName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function download(string $path, ?string $downloadName = null)
    {
        $path = trim($path, '/');
        
        try {
            // Try Spaces first
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                return Storage::disk(self::PRIMARY_DISK)->download($path, $downloadName);
            }
            
            // Fallback to local storage
            if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                return Storage::disk(self::FALLBACK_DISK)->download($path, $downloadName);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error downloading file', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Migrate a file from local storage to Spaces
     * 
     * @param string $path
     * @return bool
     */
    public function migrateToSpaces(string $path): bool
    {
        $path = trim($path, '/');
        
        try {
            // Check if file exists in local storage
            if (!Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                Log::warning('File not found in local storage for migration', ['path' => $path]);
                return false;
            }
            
            // Check if already in Spaces
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                Log::info('File already exists in Spaces', ['path' => $path]);
                return true;
            }
            
            // Get file content from local storage
            $content = Storage::disk(self::FALLBACK_DISK)->get($path);
            
            // Upload to Spaces
            $uploaded = Storage::disk(self::PRIMARY_DISK)->put($path, $content, 'public');
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload to Spaces during migration');
            }
            
            Log::info('File migrated to Spaces', ['path' => $path]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('File migration failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Get file location info (for debugging)
     * 
     * @param string $path
     * @return array
     */
    public function getFileInfo(string $path): array
    {
        $path = trim($path, '/');
        
        $info = [
            'path' => $path,
            'in_spaces' => false,
            'in_local' => false,
            'url' => null,
            'size' => null,
        ];
        
        try {
            // Check Spaces
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                $info['in_spaces'] = true;
                $info['size'] = Storage::disk(self::PRIMARY_DISK)->size($path);
                $info['url'] = Storage::disk(self::PRIMARY_DISK)->url($path);
            }
            
            // Check local
            if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                $info['in_local'] = true;
                if (!$info['size']) {
                    $info['size'] = Storage::disk(self::FALLBACK_DISK)->size($path);
                }
                if (!$info['url']) {
                    $info['url'] = url('/files/' . $path);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error getting file info', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
        
        return $info;
    }
    
    /**
     * Serve file response for viewing in browser
     * 
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response|null
     */
    public function serve(string $path)
    {
        $path = trim($path, '/');
        
        try {
            // Try Spaces first
            if (Storage::disk(self::PRIMARY_DISK)->exists($path)) {
                // For Spaces, redirect to the public URL
                $url = Storage::disk(self::PRIMARY_DISK)->url($path);
                return redirect($url);
            }
            
            // Fallback to local storage
            if (Storage::disk(self::FALLBACK_DISK)->exists($path)) {
                $filePath = Storage::disk(self::FALLBACK_DISK)->path($path);
                
                // Determine content type
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $contentType = $this->getContentType($extension);
                
                return response()->file($filePath, [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                ]);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error serving file', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Get content type based on file extension
     * 
     * @param string $extension
     * @return string
     */
    private function getContentType(string $extension): string
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        
        return $contentTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}


