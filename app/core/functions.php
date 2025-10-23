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
