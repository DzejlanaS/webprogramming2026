<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if (is_file($file) || is_dir($file) && is_file(rtrim($file, '/') . '/index.html')) {
    return false;
}

if (str_starts_with($path, '/backend/')) {
    $_SERVER['SCRIPT_NAME'] = '/backend/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/backend/index.php';
    $_SERVER['PHP_SELF'] = '/backend/index.php';
    chdir(__DIR__ . '/backend');
    require __DIR__ . '/backend/index.php';
    return true;
}

return false;
