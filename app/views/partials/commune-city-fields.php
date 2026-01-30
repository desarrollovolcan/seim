<?php
$communes = $communes ?? [];
$communeCityMap = $communeCityMap ?? [];
$communeValue = $communeValue ?? '';
$cityValue = $cityValue ?? '';
$communeName = $communeName ?? 'commune';
$cityName = $cityName ?? 'city';
$communeLabel = $communeLabel ?? 'Comuna';
$cityLabel = $cityLabel ?? 'Ciudad';
$communeSelectId = $communeSelectId ?? ('commune-' . uniqid());
$citySelectId = $citySelectId ?? ('city-' . uniqid());
?>
<div class="row g-2" data-commune-city-scope>
    <div class="col-md-6">
        <label class="form-label" for="<?php echo e($communeSelectId); ?>"><?php echo e($communeLabel); ?></label>
        <select name="<?php echo e($communeName); ?>" id="<?php echo e($communeSelectId); ?>" class="form-select" data-commune-select>
            <option value="">Selecciona comuna</option>
            <?php foreach ($communes as $communeOption): ?>
                <option value="<?php echo e($communeOption); ?>" <?php echo $communeOption === $communeValue ? 'selected' : ''; ?>>
                    <?php echo e($communeOption); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="<?php echo e($citySelectId); ?>"><?php echo e($cityLabel); ?></label>
        <select name="<?php echo e($cityName); ?>" id="<?php echo e($citySelectId); ?>" class="form-select" data-city-select data-selected="<?php echo e($cityValue); ?>">
            <option value="">Selecciona ciudad</option>
        </select>
    </div>
</div>

<script>
    (() => {
        const map = window.__chileCommuneCityMap ?? <?php echo json_encode($communeCityMap, JSON_UNESCAPED_UNICODE); ?>;
        window.__chileCommuneCityMap = map;

        const renderCities = (scope, selectedCity = '') => {
            const communeSelect = scope.querySelector('[data-commune-select]');
            const citySelect = scope.querySelector('[data-city-select]');
            if (!communeSelect || !citySelect) {
                return;
            }
            const commune = communeSelect.value;
            const cities = map?.[commune] ?? [];
            const currentSelected = selectedCity || citySelect.dataset.selected || '';

            citySelect.innerHTML = '<option value="">Selecciona ciudad</option>';
            cities.forEach((city) => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                if (city === currentSelected) {
                    option.selected = true;
                }
                citySelect.appendChild(option);
            });
            citySelect.disabled = cities.length === 0;
        };

        const initScope = (scope) => {
            if (scope.dataset.communeCityReady === 'true') {
                return;
            }
            scope.dataset.communeCityReady = 'true';
            const communeSelect = scope.querySelector('[data-commune-select]');
            const citySelect = scope.querySelector('[data-city-select]');
            if (!communeSelect || !citySelect) {
                return;
            }
            communeSelect.addEventListener('change', () => {
                citySelect.dataset.selected = '';
                renderCities(scope, '');
            });
            renderCities(scope, citySelect.dataset.selected || '');
        };

        document.querySelectorAll('[data-commune-city-scope]').forEach(initScope);

        window.syncCommuneCitySelects = () => {
            document.querySelectorAll('[data-commune-city-scope]').forEach((scope) => {
                scope.dataset.communeCityReady = 'false';
                const citySelect = scope.querySelector('[data-city-select]');
                if (citySelect) {
                    citySelect.dataset.selected = citySelect.value;
                }
                initScope(scope);
            });
        };
    })();
</script>
