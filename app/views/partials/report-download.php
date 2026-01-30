<?php
$reportTemplate = $reportTemplate ?? '';
$reportSource = $reportSource ?? '';
?>
<?php if ($reportTemplate !== '' && $reportSource !== ''): ?>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="report_template" value="<?php echo e($reportTemplate); ?>">
    <input type="hidden" name="report_source" value="<?php echo e($reportSource); ?>">
    <button type="submit" class="btn btn-outline-primary" formaction="index.php?route=reports/download" formmethod="post" formtarget="_blank">
        Descargar PDF
    </button>
<?php endif; ?>
