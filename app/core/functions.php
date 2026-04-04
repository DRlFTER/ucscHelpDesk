<?php
function component($name, $data = [])
{
    $path = __DIR__ . "/../views/components/{$name}.component.php";

    if (file_exists($path)) {
        extract($data);
        require $path;
    } else {
        throw new Exception("Component '{$name}' not found at {$path}");
    }
}

if (!function_exists('time_ago')) {
    function time_ago($datetime): string
    {
        if ($datetime === null || $datetime === '') {
            return '';
        }

        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime((string)$datetime);
        if ($timestamp === false) {
            return '';
        }

        $diff = time() - $timestamp;
        if ($diff < 0) {
            $diff = 0;
        }

        if ($diff < 5) {
            return 'just now';
        }

        $units = [
            'yr'  => 365 * 24 * 60 * 60,
            'mo'  => 30 * 24 * 60 * 60,
            'wk'  => 7 * 24 * 60 * 60,
            'day' => 24 * 60 * 60,
            'hr'  => 60 * 60,
            'min' => 60,
            's'   => 1,
        ];

        foreach ($units as $label => $secs) {
            if ($diff >= $secs) {
                $val = (int) floor($diff / $secs);
                return $val . ' ' . $label . ' ago';
            }
        }

        return 'just now';
    }
}

/**
 * Handle File Uploads (Tickets, Forums, etc.)
 * Creates folder based on entity_type, moves the file securely, and returns file metadata.
 * 
 * @param array $fileArray Example: $_FILES['attachment']
 * @param string $entityType Directory mapping: 'ticket', 'forum', 'kb', etc.
 * @param array $allowedTypes Optional array of allowed mime types
 * @param int $maxSize Max file size in bytes
 * @return array|false Returns array of metadata on success, false on failure
 */
function handle_upload($fileArray, $entityType = 'global', $allowedTypes = [], $maxSize = 10485760) // 10MB default
{
    if (!isset($fileArray['error']) || is_array($fileArray['error'])) {
        return false;
    }

    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($fileArray['size'] > $maxSize) {
        return false;
    }

    // Set paths
    // Assumes this file is in app/core/ and public/uploads is 2 levels up
    $uploadBaseDir = __DIR__ . '/../../public/uploads/';
    $targetDir = $uploadBaseDir . $entityType . '/';

    // Create target directory if it doesn't exist safely
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Prepare safe and robust file name
    $originalName = basename($fileArray['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    // Prevent execution risks by blacklisting dangerous extensions
    $dangerous = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'cgi', 'exe', 'pl', 'asp', 'js', 'sh', 'py'];
    if (in_array($extension, $dangerous)) {
        return false;
    }

    // Build the unique filename
    $uniqueName = uniqid() . '_' . time() . '.' . $extension;
    $targetFile = $targetDir . $uniqueName;

    // Move file
    if (move_uploaded_file($fileArray['tmp_name'], $targetFile)) {
        // Fallback MIME test on saved file to handle edge cases
        $fileMime = mime_content_type($targetFile);
        if (!empty($allowedTypes) && !in_array($fileMime, $allowedTypes)) {
            unlink($targetFile);
            return false;
        }

        return [
            'file_name' => $originalName,
            'file_path' => 'uploads/' . $entityType . '/' . $uniqueName, // Path relative to public folder
            'file_type' => $fileMime ?: 'application/octet-stream',
            'file_size' => (int) $fileArray['size']
        ];
    }

    return false;
}
