<?php

class Controller
{
    protected $timeout = 20;
    public function view($view, $data = [])
    {
        extract($data);

        $baseDir = __DIR__ . "/../views/";

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

    private function resolveViewPath(string $baseDir, string $view): ?string
    {
        $normalized = trim(str_replace(['.', '\\'], '/', $view), '/');

        $candidate = $baseDir . $normalized . '.view.php';
        if (file_exists($candidate)) {
            return $candidate;
        }

        $target = basename($normalized) . '.view.php';

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
        header("Location: " . ROOT . "login?denied=1");
        exit();
     }

     if (strtolower($_SESSION['user']['role'] ?? '') !== strtolower($role)) {
        header("Location: " . ROOT . "login?denied=$role");
        exit();
     }
    }
    

  
}
