<?php
// video_proxy.php - Stream videos from local or network (UNC) paths
session_start();

// Optional: Restrict access to logged-in users only
// if (!isset($_SESSION['admin'])) {
//     header("HTTP/1.1 403 Forbidden");
//     exit;
// }

if (!isset($_GET['path'])) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$path = $_GET['path'];

// Handle UNC paths (ensure backslashes are handled if coming from Windows)
// If the path starts with \\, it's a network path.
// PHP on Windows handles these natively if the user running Apache/PHP has permissions.

if (!file_exists($path)) {
    // Try to handle forward slashes if they were converted
    $path = str_replace('/', '\\', $path);
    if (!file_exists($path)) {
        header("HTTP/1.1 404 Not Found");
        echo "File not found: " . htmlspecialchars($path);
        exit;
    }
}

$size = filesize($path);
$fm = @fopen($path, 'rb');
if (!$fm) {
    header("HTTP/1.1 500 Internal Server Error");
    exit;
}

$begin = 0;
$end = $size - 1;

if (isset($_SERVER['HTTP_RANGE'])) {
    if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
        $begin = intval($matches[1]);
        if (!empty($matches[2])) {
            $end = intval($matches[2]);
        }
    }
}

if ($begin > 0 || $end < $size - 1) {
    header('HTTP/1.1 206 Partial Content');
} else {
    header('HTTP/1.1 200 OK');
}

header("Content-Type: video/mp4");
header('Accept-Ranges: bytes');
header('Content-Length: ' . ($end - $begin + 1));
header("Content-Disposition: inline");
header("Content-Range: bytes $begin-$end/$size");
header("Content-Transfer-Encoding: binary");
header("Connection: close");

$cur = $begin;
fseek($fm, $begin, 0);

while (!feof($fm) && $cur <= $end && (connection_status() == 0)) {
    echo fread($fm, min(1024 * 16, ($end - $cur) + 1));
    $cur += 1024 * 16;
}

fclose($fm);
?>
