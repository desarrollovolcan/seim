<?php
require __DIR__ . '/app/bootstrap.php';

$size = (int)($_GET['size'] ?? 192);
$size = in_array($size, [192, 512], true) ? $size : 192;

try {
    $companySettings = login_company_settings($db);
} catch (Throwable $e) {
    log_message('error', 'Failed to load company settings for PWA icon: ' . $e->getMessage());
    $companySettings = [];
}

$logoPath = $companySettings['logo_color'] ?? 'assets/images/logo.png';
$maskable = isset($_GET['maskable']) && (string)$_GET['maskable'] === '1';

$resolveImage = function (string $path): array {
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return ['content' => @file_get_contents($path), 'path' => null];
    }
    $fullPath = $path;
    if (!str_starts_with($path, '/') && !preg_match('#^[A-Za-z]:\\\\#', $path)) {
        $fullPath = __DIR__ . '/' . ltrim($path, '/');
    }
    if (!file_exists($fullPath)) {
        return ['content' => null, 'path' => null];
    }
    return ['content' => null, 'path' => $fullPath];
};

$source = $resolveImage($logoPath);
$imageData = $source['content'];
$imagePath = $source['path'];

if ($imageData === null && $imagePath === null) {
    $imagePath = __DIR__ . '/assets/images/logo.png';
}

$imageInfo = $imagePath ? @getimagesize($imagePath) : ($imageData ? @getimagesizefromstring($imageData) : false);

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

if ($imageInfo === false || !function_exists('imagecreatetruecolor')) {
    if ($imagePath) {
        readfile($imagePath);
    } elseif ($imageData) {
        echo $imageData;
    }
    exit;
}

$mime = $imageInfo['mime'] ?? '';
$createImage = match ($mime) {
    'image/png' => 'imagecreatefrompng',
    'image/jpeg' => 'imagecreatefromjpeg',
    'image/webp' => 'imagecreatefromwebp',
    default => null,
};

if ($createImage === null || !function_exists($createImage)) {
    if ($imagePath) {
        readfile($imagePath);
    } elseif ($imageData) {
        echo $imageData;
    }
    exit;
}

$sourceImage = $imagePath ? @$createImage($imagePath) : @$createImage('data://image/' . explode('/', $mime)[1] . ';base64,' . base64_encode($imageData));

if (!$sourceImage) {
    if ($imagePath) {
        readfile($imagePath);
    } elseif ($imageData) {
        echo $imageData;
    }
    exit;
}

$sourceWidth = imagesx($sourceImage);
$sourceHeight = imagesy($sourceImage);

$canvas = imagecreatetruecolor($size, $size);
$background = imagecolorallocate($canvas, 255, 255, 255);
imagefilledrectangle($canvas, 0, 0, $size, $size, $background);

$scale = min($size / $sourceWidth, $size / $sourceHeight);
$targetWidth = (int)round($sourceWidth * $scale);
$targetHeight = (int)round($sourceHeight * $scale);
$offsetX = (int)(($size - $targetWidth) / 2);
$offsetY = (int)(($size - $targetHeight) / 2);

if ($maskable) {
    $padding = (int)round($size * 0.1);
    $available = $size - ($padding * 2);
    $scale = min($available / $sourceWidth, $available / $sourceHeight);
    $targetWidth = (int)round($sourceWidth * $scale);
    $targetHeight = (int)round($sourceHeight * $scale);
    $offsetX = (int)(($size - $targetWidth) / 2);
    $offsetY = (int)(($size - $targetHeight) / 2);
}

imagecopyresampled(
    $canvas,
    $sourceImage,
    $offsetX,
    $offsetY,
    0,
    0,
    $targetWidth,
    $targetHeight,
    $sourceWidth,
    $sourceHeight
);

imagepng($canvas);
imagedestroy($canvas);
imagedestroy($sourceImage);
