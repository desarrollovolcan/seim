<?php
$communes = $communes ?? [];
$communeValue = $communeValue ?? '';
$communeName = $communeName ?? 'commune';
$communeLabel = $communeLabel ?? 'Comuna';
$communeSelectId = $communeSelectId ?? ('commune-' . uniqid());
?>
<div class="row g-2">
    <div class="col-md-12">
        <label class="form-label" for="<?php echo e($communeSelectId); ?>"><?php echo e($communeLabel); ?></label>
        <select name="<?php echo e($communeName); ?>" id="<?php echo e($communeSelectId); ?>" class="form-select">
            <option value="">Selecciona comuna</option>
            <?php foreach ($communes as $communeOption): ?>
                <option value="<?php echo e($communeOption); ?>" <?php echo $communeOption === $communeValue ? 'selected' : ''; ?>>
                    <?php echo e($communeOption); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
