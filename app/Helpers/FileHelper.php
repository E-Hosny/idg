<?php

use App\Services\FileService;

if (!function_exists('file_service')) {
    /**
     * Get FileService instance
     * 
     * @return FileService
     */
    function file_service(): FileService
    {
        return app(FileService::class);
    }
}

if (!function_exists('upload_file')) {
    /**
     * Upload file to Spaces
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @param string $visibility
     * @return string|false
     */
    function upload_file($file, string $directory, ?string $filename = null, string $visibility = 'public')
    {
        return file_service()->upload($file, $directory, $filename, $visibility);
    }
}

if (!function_exists('file_url')) {
    /**
     * Get file URL from Spaces or local storage
     * 
     * @param string|null $path
     * @return string|null
     */
    function file_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        
        return file_service()->url($path);
    }
}

if (!function_exists('file_exists_anywhere')) {
    /**
     * Check if file exists in Spaces or local storage
     * 
     * @param string $path
     * @return bool
     */
    function file_exists_anywhere(string $path): bool
    {
        return file_service()->exists($path);
    }
}

if (!function_exists('delete_file_anywhere')) {
    /**
     * Delete file from both Spaces and local storage
     * 
     * @param string $path
     * @return bool
     */
    function delete_file_anywhere(string $path): bool
    {
        return file_service()->delete($path);
    }
}

if (!function_exists('download_file_anywhere')) {
    /**
     * Download file from Spaces or local storage
     * 
     * @param string $path
     * @param string|null $downloadName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    function download_file_anywhere(string $path, ?string $downloadName = null)
    {
        return file_service()->download($path, $downloadName);
    }
}

if (!function_exists('serve_file')) {
    /**
     * Serve file for viewing in browser
     * 
     * @param string $path
     * @return mixed
     */
    function serve_file(string $path)
    {
        return file_service()->serve($path);
    }
}

if (!function_exists('migrate_file_to_spaces')) {
    /**
     * Migrate file from local storage to Spaces
     * 
     * @param string $path
     * @return bool
     */
    function migrate_file_to_spaces(string $path): bool
    {
        return file_service()->migrateToSpaces($path);
    }
}


