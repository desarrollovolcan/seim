<?php
$activityCodeValue = $activityCodeValue ?? '';
$activityCodeLabel = $activityCodeLabel ?? 'Código actividad';
$activityCodeName = $activityCodeName ?? 'activity_code';
$activityCodeHelp = $activityCodeHelp ?? 'Busca por código o descripción.';
$activityCodeOptions = $activityCodeOptions ?? [];
$activityCodeId = $activityCodeId ?? ('activity-code-' . uniqid());
$activityCodeListId = $activityCodeListId ?? ('activity-code-list-' . uniqid());
?>
<label class="form-label" for="<?php echo e($activityCodeId); ?>"><?php echo e($activityCodeLabel); ?></label>
<input
    type="text"
    name="<?php echo e($activityCodeName); ?>"
    id="<?php echo e($activityCodeId); ?>"
    class="form-control"
    list="<?php echo e($activityCodeListId); ?>"
    value="<?php echo e($activityCodeValue); ?>"
    placeholder="Ej: 620100"
>
<datalist id="<?php echo e($activityCodeListId); ?>">
    <?php foreach ($activityCodeOptions as $option): ?>
        <?php $code = (string)($option['code'] ?? ''); ?>
        <?php $name = (string)($option['name'] ?? ''); ?>
        <option value="<?php echo e($code); ?>"><?php echo e(trim($code . ' - ' . $name)); ?></option>
    <?php endforeach; ?>
</datalist>
<div class="form-text"><?php echo e($activityCodeHelp); ?></div>
