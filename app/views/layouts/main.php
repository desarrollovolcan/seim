<?php include __DIR__ . '/../../../partials/html.php'; ?>

<head>
    <?php $title = $title ?? 'GoCreative Ges'; include __DIR__ . '/../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../partials/head-css.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include __DIR__ . '/../partials/menu.php'; ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $pageTitle = $pageTitle ?? $title ?? ''; include __DIR__ . '/../../../partials/page-title.php'; ?>
                <?php
                $flashMessages = consume_flash();
                $flashClassMap = [
                    'success' => 'success',
                    'error' => 'danger',
                    'warning' => 'warning',
                    'info' => 'info',
                ];
                ?>
                <?php foreach ($flashMessages as $type => $messages): ?>
                    <?php $alertClass = $flashClassMap[$type] ?? 'info'; ?>
                    <?php foreach ((array)$messages as $message): ?>
                        <div class="alert alert-<?php echo e($alertClass); ?> alert-dismissible fade show" role="alert">
                            <?php echo e($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <?php
                $viewPath = __DIR__ . '/../' . $view . '.php';
                if (file_exists($viewPath)) {
                    include $viewPath;
                } else {
                    echo '<div class="alert alert-danger">Vista no encontrada.</div>';
                }
                ?>
            </div>
            <?php include __DIR__ . '/../../../partials/footer.php'; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../../../partials/footer-scripts.php'; ?>
    <?php ?>
</body>

</html>
