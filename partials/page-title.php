<?php 
$displayTitle = $pageTitle ?? $title ?? '';
if (!empty($displayTitle)) : 
?>
    <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
        <h4 class="mb-0 fw-bold fs-18 text-dark"><?php echo htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8'); ?></h4>
        <ol class="breadcrumb mb-0 py-0 fs-12">
            <li class="breadcrumb-item"><a href="index.php" class="text-secondary text-decoration-none">SEIM</a></li>
            <?php if (!empty($subtitle)) : ?>
                <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-secondary text-decoration-none"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active text-muted" aria-current="page"><?php echo htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </div>
<?php endif; ?>