<?php
$loginLogoSrc = login_logo_src($companySettings ?? []);
$hasCompanies = !empty($hasCompanies ?? $companies ?? []);
$companyLogos = $companyLogos ?? [];
?>

<div class="auth-box p-0 w-100">
    <div class="row w-100 g-0">
        <div class="col-xxl-4 col-xl-6">
            <div class="card border-0 mb-0">
                <div class="position-absolute top-0 end-0" style="width: 180px;">
                    <svg style="opacity: 0.08; width: 100%; height: auto;" width="600" height="560" viewBox="0 0 600 560" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_948_1464)">
                            <mask id="mask0_948_1464" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="600" height="1200">
                                <path d="M0 0L0 1200H600L600 0H0Z" fill="white" />
                            </mask>
                            <g mask="url(#mask0_948_1464)">
                                <path d="M537.448 166.697L569.994 170.892L550.644 189.578L537.448 166.697Z" fill="#FF4C3E" />
                            </g>
                            <mask id="mask1_948_1464" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="600" height="1200">
                                <path d="M0 0L0 1200H600L600 0H0Z" fill="white" />
                            </mask>
                            <g mask="url(#mask1_948_1464)">
                                <path d="M364.093 327.517L332.306 359.304C321.885 369.725 304.989 369.725 294.568 359.304L262.781 327.517C252.36 317.096 252.36 300.2 262.781 289.779L294.568 257.992C304.989 247.571 321.885 247.571 332.306 257.992L364.093 289.779C374.514 300.2 374.514 317.096 364.093 327.517Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                <path d="M417.828 85.0572L355.011 147.874C339.422 163.463 314.147 163.463 298.558 147.874L235.741 85.0572C220.152 69.4682 220.152 44.1931 235.741 28.6051L298.558 -34.2119C314.147 -49.8009 339.422 -49.8009 355.011 -34.2119L417.828 28.6051C433.417 44.1931 433.417 69.4682 417.828 85.0572Z" fill="#7b70ef" />
                                <path d="M619.299 318.084L563.736 373.647C548.147 389.236 522.872 389.236 507.283 373.647L451.72 318.084C436.131 302.495 436.131 277.22 451.72 261.631L507.283 206.068C522.872 190.479 548.147 190.479 563.736 206.068L619.299 261.631C634.888 277.221 634.888 302.495 619.299 318.084Z" fill="#089df1" />
                                <path d="M225.523 71.276L198.553 98.2459C186.21 110.589 166.198 110.589 153.854 98.2459L126.884 71.276C114.541 58.933 114.541 38.921 126.884 26.578L153.854 -0.392014C166.197 -12.735 186.209 -12.735 198.553 -0.392014L225.523 26.578C237.866 38.92 237.866 58.932 225.523 71.276Z" fill="#f7577e" />
                            </g>
                        </g>
                        <defs>
                            <clipPath id="clip0_948_1464">
                                <rect width="560" height="600" fill="white" transform="matrix(0 -1 1 0 0 560)" />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <div class="card-body min-vh-100 d-flex flex-column justify-content-center">
                    <div class="auth-brand mb-0 text-center">
                        <a href="index.php" class="logo-login">
                            <img src="<?php echo e($loginLogoSrc); ?>" alt="logo" height="28" data-login-logo>
                        </a>
                    </div>

                    <div class="mt-auto">
                        <div class="p-2 text-center">
                            <h3 class="fw-bold my-2">Acceso Administrador</h3>
                            <p class="text-muted mb-0">Ingresa tus credenciales para administrar la plataforma.</p>

                            <?php if (!empty($_SESSION['error'])): ?>
                                <div class="alert alert-danger text-start mt-3"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <?php if (!$hasCompanies): ?>
                                <div class="alert alert-warning text-start mt-3">No hay empresas activas configuradas. Contacta al administrador para continuar.</div>
                            <?php endif; ?>

                            <form class="mt-4" method="post" action="login.php">
                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                <fieldset <?php echo $hasCompanies ? '' : 'disabled'; ?>>
                                    <div class="app-search w-100 input-group rounded-pill mb-3">
                                        <select name="company_id" class="form-select py-2" required data-company-select>
                                            <option value="">Selecciona empresa</option>
                                            <?php foreach (($companies ?? []) as $company): ?>
                                                <?php $companyId = (int)$company['id']; ?>
                                                <option
                                                    value="<?php echo e((string)$companyId); ?>"
                                                    data-logo="<?php echo e($companyLogos[$companyId] ?? $loginLogoSrc); ?>"
                                                >
                                                    <?php echo e($company['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="text-muted small text-start mb-3">Selecciona la empresa donde administrarás la cuenta.</div>
                                    <div class="app-search w-100 input-group rounded-pill mb-3">
                                        <input type="email" name="email" class="form-control py-2" placeholder="Correo administrador" autocomplete="username" required>
                                        <i data-lucide="circle-user" class="app-search-icon text-muted"></i>
                                    </div>
                                    <div class="app-search w-100 input-group rounded-pill mb-2">
                                        <input type="password" name="password" class="form-control py-2" placeholder="Contraseña" autocomplete="current-password" required data-password-field>
                                        <button class="btn btn-outline-secondary" type="button" data-toggle-password>Mostrar</button>
                                        <i data-lucide="key-round" class="app-search-icon text-muted"></i>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary fw-semibold">Ingresar</button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>

                    <p class="text-center text-muted mt-auto mb-0">
                        © <script>document.write(new Date().getFullYear())</script> GoCreative.
                    </p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="h-100 position-relative card-side-img rounded-0 overflow-hidden">
                <div class="p-4 card-img-overlay auth-overlay d-flex align-items-end justify-content-center">
                    <div class="text-center text-white">
                        <h3 class="mb-2">Panel GoCreative</h3>
                        <p class="mb-0">Gestiona clientes, servicios y facturación desde un solo lugar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const companySelect = document.querySelector('[data-company-select]');
    const loginLogo = document.querySelector('[data-login-logo]');
    const updateLoginLogo = () => {
        if (!companySelect || !loginLogo) {
            return;
        }
        const selectedOption = companySelect.options[companySelect.selectedIndex];
        if (!selectedOption) {
            return;
        }
        const nextLogo = selectedOption.dataset.logo;
        if (nextLogo) {
            loginLogo.src = nextLogo;
        }
    };
    companySelect?.addEventListener('change', updateLoginLogo);
    updateLoginLogo();

    document.querySelector('[data-toggle-password]')?.addEventListener('click', (event) => {
        const button = event.currentTarget;
        const passwordInput = document.querySelector('[data-password-field]');
        if (!passwordInput || !button) {
            return;
        }
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        button.textContent = isPassword ? 'Ocultar' : 'Mostrar';
    });
</script>
