<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);

        // Base views directory
        $baseDir = __DIR__ . "/../views/";

        // Resolve the view path (supports explicit subpaths and fallback recursive lookup)
        $viewPath = $this->resolveViewPath($baseDir, $view);
        if (!$viewPath || !file_exists($viewPath)) {
            $pretty = rtrim(str_replace(['.', '\\'], '/', $view), '/');
            throw new Exception("View file not found: '{$pretty}.view.php' under {$baseDir}");
        }

        ob_start();
        require($viewPath);
        $content = ob_get_clean();

        require($baseDir . "layouts/main.php");
    }

    /**
     * Resolve a view file path.
     * - Accepts explicit subpaths like "admin/adminDashboard" or "admin.adminDashboard".
     * - If only a bare name is provided (e.g., "adminDashboard"), searches recursively
     *   under the views directory for a matching "<name>.view.php" and returns the first match.
     */
    private function resolveViewPath(string $baseDir, string $view): ?string
    {
        // Normalize incoming view spec
        $normalized = trim(str_replace(['.', '\\'], '/', $view), '/');

        // 1) Try explicit/relative path first
        $candidate = $baseDir . $normalized . '.view.php';
        if (file_exists($candidate)) {
            return $candidate;
        }

        // 2) Fallback: search by filename anywhere under views
        $target = basename($normalized) . '.view.php';

        // Use SPL iterators for a lightweight recursive search
        if (class_exists('RecursiveDirectoryIterator')) {
            $flags = \FilesystemIterator::SKIP_DOTS;
            $dirIter = new \RecursiveDirectoryIterator($baseDir, $flags);
            $iter = new \RecursiveIteratorIterator($dirIter, \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iter as $fileInfo) {
                if ($fileInfo->isFile() && $fileInfo->getFilename() === $target) {
                    return $fileInfo->getPathname();
                }
            }
        }

        return null;
    }

    public function model($model)
    {
        if (!class_exists($model, false)) {
            $path = __DIR__ . "/../models/" . $model . ".php";
            if (file_exists($path)) {
                require_once $path;
            } else {
                throw new Exception("Model file not found: {$path}");
            }
        }
        return new $model();
    }

    function requireLogin($role)
{
    if (!isset($_SESSION['user'])) {
        echo "Access denied. Please log in as $role.";
        header("Refresh:2; url=/public/login"); // Wait 2s then redirect
        exit();
    }

    // if role mismatch
    if ($_SESSION['user']['role'] !== $role) {
        echo "Access denied. Please log in as $role.";
        header("Refresh:2; url=/public/login");
        exit();
    }
}
    

  
}
