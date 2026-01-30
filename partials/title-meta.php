<?php
$themeColor = '#6658dd';
$appName = 'SEIM';
$baseUrl = '';

if (function_exists('get_municipalidad')) {
    $municipalidad = get_municipalidad();
    $themeColor = $municipalidad['color_primary'] ?? $themeColor;
    $appName = $municipalidad['nombre'] ?? $appName;
}

if (function_exists('base_url')) {
    $baseUrl = rtrim(base_url(), '/');
}
?>
<meta charset="utf-8">
<title><?php echo ($title); ?> | UBold - Responsive Bootstrap 5 Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="UBold is a modern, responsive admin dashboard available on ThemeForest. Ideal for building CRM, CMS, project management tools, and custom web applications with a clean UI, flexible layouts, and rich features.">
<meta name="keywords" content="UBold, admin dashboard, ThemeForest, Bootstrap 5 admin, responsive admin, CRM dashboard, CMS admin, web app UI, admin theme, premium admin template">
<meta name="author" content="Coderthemes">
<meta name="theme-color" content="<?php echo htmlspecialchars($themeColor, ENT_QUOTES); ?>">
<meta name="application-name" content="<?php echo htmlspecialchars($appName, ENT_QUOTES); ?>">
<meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($appName, ENT_QUOTES); ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<!-- App favicon -->
<link rel="shortcut icon" href="assets/images/favicon.ico">
<link rel="apple-touch-icon" href="assets/images/logo.png">
<link rel="manifest" href="<?php echo $baseUrl; ?>/manifest.webmanifest">
