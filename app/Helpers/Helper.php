<?php
namespace App\Helpers;
class Helper{
    public static function deleteDirectory($dir) {
        // Check if the directory exists
        if (!file_exists($dir)) {
            return false; // Directory doesn't exist, nothing to delete
        }
        // Get the list of files and subdirectories in the directory
        $files = array_diff(scandir($dir), ['.', '..']);

        // Loop through each file and subdirectory
        foreach ($files as $file) {
            $path = $dir . '/' . $file;

            // If it's a directory, recursively delete it
            if (is_dir($path)) {
                static::deleteDirectory($path);
            } else {
                // If it's a file, delete it
                unlink($path);
            }
        }
        // After all contents are deleted, remove the directory itself
        return rmdir($dir);
    }
}
