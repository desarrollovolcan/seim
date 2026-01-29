<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('usuarios', 'view');

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$editingId = null;
$viewRecord = null;
$empresas = load_empresas();
$roles = [];
$userEmpresas = [];
$viewEmpresas = [];

$fields = [
    'nombre' => '',
    'apellido' => '',
    'rut' => '',
    'correo' => '',
    'telefono' => '',
    'username' => '',
    'rol' => '',
    'cargo' => '',
    'fecha_nacimiento' => '',
    'direccion' => '',
    'estado' => '1',
];

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$viewId]);
        $viewRecord = $stmt->fetch() ?: null;
        if ($viewRecord) {
            try {
                $stmt = db()->prepare(
                    'SELECT e.nombre, e.razon_social
                     FROM user_empresas ue
                     INNER JOIN empresas e ON e.id = ue.empresa_id
                     WHERE ue.user_id = ?
                     ORDER BY e.nombre'
                );
                $stmt->execute([$viewId]);
                $viewEmpresas = $stmt->fetchAll();
            } catch (Exception $e) {
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$editingId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['nombre'] = (string) ($record['nombre'] ?? '');
            $fields['apellido'] = (string) ($record['apellido'] ?? '');
            $fields['rut'] = (string) ($record['rut'] ?? '');
            $fields['correo'] = (string) ($record['correo'] ?? '');
            $fields['telefono'] = (string) ($record['telefono'] ?? '');
            $fields['username'] = (string) ($record['username'] ?? '');
            $fields['rol'] = (string) ($record['rol'] ?? '');
            $fields['cargo'] = (string) ($record['cargo'] ?? '');
            $fields['fecha_nacimiento'] = (string) ($record['fecha_nacimiento'] ?? '');
            $fields['direccion'] = (string) ($record['direccion'] ?? '');
            $fields['estado'] = (string) ((int) ($record['estado'] ?? 1));
        }

        try {
            $stmt = db()->prepare('SELECT empresa_id FROM user_empresas WHERE user_id = ?');
            $stmt->execute([$editingId]);
            $userEmpresas = array_map('intval', array_column($stmt->fetchAll(), 'empresa_id'));
        } catch (Exception $e) {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } else {
        $action = (string) ($_POST['action'] ?? 'create');
        $recordId = (int) ($_POST['id'] ?? 0);

        if ($action === 'delete' && $recordId > 0) {
            if (!has_permission('usuarios', 'delete')) {
                $errors[] = 'No tienes permisos para eliminar usuarios.';
            } else {
                try {
                    $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
                    $stmt->execute([$recordId]);
                    $_SESSION['usuario_flash'] = 'Usuario eliminado correctamente.';
                    redirect('usuario.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo eliminar el usuario.';
                }
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }
            $password = (string) ($_POST['password'] ?? '');
            $empresaIds = $_POST['empresa_ids'] ?? [];
            $empresaIds = array_values(array_unique(array_filter(array_map('intval', (array) $empresaIds))));
            $userEmpresas = $empresaIds;

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }
            if ($fields['apellido'] === '') {
                $errors[] = 'El apellido es obligatorio.';
            }
            if ($fields['rut'] === '') {
                $errors[] = 'El RUT es obligatorio.';
            }
            if ($fields['correo'] === '' || !filter_var($fields['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Debes ingresar un correo válido.';
            }
            if ($fields['telefono'] === '') {
                $errors[] = 'El teléfono es obligatorio.';
            }
            if ($fields['username'] === '') {
                $errors[] = 'El usuario es obligatorio.';
            }
            if (!$empresaIds) {
                $errors[] = 'Debes asociar el usuario a al menos una empresa.';
            }
            if ($action === 'create' && $password === '') {
                $errors[] = 'La contraseña es obligatoria.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        if (!has_permission('usuarios', 'edit')) {
                            throw new RuntimeException('Sin permisos para editar.');
                        }
                        $query = 'UPDATE users SET rut = ?, nombre = ?, apellido = ?, cargo = ?, fecha_nacimiento = ?, correo = ?, telefono = ?, direccion = ?, username = ?, rol = ?, estado = ?';
                        $params = [
                            $fields['rut'],
                            $fields['nombre'],
                            $fields['apellido'],
                            $fields['cargo'] !== '' ? $fields['cargo'] : null,
                            $fields['fecha_nacimiento'] !== '' ? $fields['fecha_nacimiento'] : null,
                            $fields['correo'],
                            $fields['telefono'],
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $fields['username'],
                            $fields['rol'] !== '' ? $fields['rol'] : null,
                            (int) ($fields['estado'] === '1'),
                        ];

                        if ($password !== '') {
                            $query .= ', password_hash = ?';
                            $params[] = password_hash($password, PASSWORD_DEFAULT);
                        }

                        $query .= ' WHERE id = ?';
                        $params[] = $recordId;

                        $stmt = db()->prepare($query);
                        $stmt->execute($params);

                        $stmtDelete = db()->prepare('DELETE FROM user_empresas WHERE user_id = ?');
                        $stmtDelete->execute([$recordId]);
                        $stmtInsert = db()->prepare('INSERT INTO user_empresas (user_id, empresa_id) VALUES (?, ?)');
                        foreach ($empresaIds as $empresaId) {
                            $stmtInsert->execute([$recordId, $empresaId]);
                        }

                        $_SESSION['usuario_flash'] = 'Usuario actualizado correctamente.';
                    } else {
                        if (!has_permission('usuarios', 'create')) {
                            throw new RuntimeException('Sin permisos para crear.');
                        }
                        $stmt = db()->prepare(
                            'INSERT INTO users (empresa_id, rut, nombre, apellido, cargo, fecha_nacimiento, correo, telefono, direccion, username, rol, password_hash, password_locked, is_superadmin, estado)
                             VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?)'
                        );
                        $stmt->execute([
                            $fields['rut'],
                            $fields['nombre'],
                            $fields['apellido'],
                            $fields['cargo'] !== '' ? $fields['cargo'] : null,
                            $fields['fecha_nacimiento'] !== '' ? $fields['fecha_nacimiento'] : null,
                            $fields['correo'],
                            $fields['telefono'],
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $fields['username'],
                            $fields['rol'] !== '' ? $fields['rol'] : null,
                            password_hash($password, PASSWORD_DEFAULT),
                            (int) ($fields['estado'] === '1'),
                        ]);

                        $userId = (int) db()->lastInsertId();
                        $stmtInsert = db()->prepare('INSERT INTO user_empresas (user_id, empresa_id) VALUES (?, ?)');
                        foreach ($empresaIds as $empresaId) {
                            $stmtInsert->execute([$userId, $empresaId]);
                        }

                        $_SESSION['usuario_flash'] = 'Usuario registrado correctamente.';
                    }

                    redirect('usuario.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar el usuario. Revisa que el correo, RUT y usuario no estén duplicados.';
                }
            }
        }
    }
}

if (isset($_SESSION['usuario_flash'])) {
    $successMessage = (string) $_SESSION['usuario_flash'];
    unset($_SESSION['usuario_flash']);
}

$usuarios = [];
try {
    $roles = db()->query('SELECT nombre FROM roles ORDER BY nombre')->fetchAll();
    $usuarios = db()->query(
        'SELECT id, nombre, apellido, correo, telefono, username, rol, estado, fecha_creacion
         FROM users
         ORDER BY fecha_creacion DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de usuarios.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Usuario'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Gestión'; $title = 'Usuario'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Input Example</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitch">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if ($successMessage !== '') : ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <?php if ($errors) : ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error) : ?>
                                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if ($viewRecord) : ?>
                                    <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                                        <h6 class="fw-semibold mb-2">Detalle de usuario</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Apellido:</span> <?php echo htmlspecialchars($viewRecord['apellido'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Correo:</span> <?php echo htmlspecialchars($viewRecord['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Teléfono:</span> <?php echo htmlspecialchars($viewRecord['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Usuario:</span> <?php echo htmlspecialchars($viewRecord['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Rol:</span> <?php echo htmlspecialchars($viewRecord['rol'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-8">
                                                <span class="text-muted">Empresas:</span>
                                                <?php if ($viewEmpresas) : ?>
                                                    <?php
                                                        $empresasNombres = array_map(
                                                            fn(array $empresa) => $empresa['razon_social'] ?: $empresa['nombre'],
                                                            $viewEmpresas
                                                        );
                                                    ?>
                                                    <?php echo htmlspecialchars(implode(', ', $empresasNombres), ENT_QUOTES, 'UTF-8'); ?>
                                                <?php else : ?>
                                                    —
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="usuario.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="<?php echo $editingId ? 'update' : 'create'; ?>">
                                    <?php if ($editingId) : ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $editingId; ?>">
                                    <?php endif; ?>

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fields['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Apellido</label>
                                            <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($fields['apellido'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">RUT</label>
                                            <input type="text" name="rut" class="form-control rut-field" value="<?php echo htmlspecialchars($fields['rut'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="12.345.678-9" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Correo</label>
                                            <div class="app-search">
                                                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($fields['correo'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="usuario@correo.com" required>
                                                <i data-lucide="mail" class="app-search-icon text-muted"></i>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($fields['telefono'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Usuario</label>
                                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($fields['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Rol</label>
                                            <select name="rol" class="form-select">
                                                <option value="">Selecciona</option>
                                                <?php foreach ($roles as $rol) : ?>
                                                    <?php $rolNombre = (string) ($rol['nombre'] ?? ''); ?>
                                                    <option value="<?php echo htmlspecialchars($rolNombre, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $fields['rol'] === $rolNombre ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($rolNombre, ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Cargo</label>
                                            <input type="text" name="cargo" class="form-control" value="<?php echo htmlspecialchars($fields['cargo'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cargo del usuario">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Empresas asociadas</label>
                                            <div class="row g-2">
                                                <?php foreach ($empresas as $empresa) : ?>
                                                    <?php
                                                        $empresaId = (int) ($empresa['id'] ?? 0);
                                                        $empresaNombre = $empresa['razon_social'] ?: $empresa['nombre'];
                                                        $checked = in_array($empresaId, $userEmpresas, true);
                                                    ?>
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="empresa_ids[]" value="<?php echo $empresaId; ?>" id="usuario-empresa-<?php echo $empresaId; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="usuario-empresa-<?php echo $empresaId; ?>">
                                                                <?php echo htmlspecialchars($empresaNombre, ENT_QUOTES, 'UTF-8'); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Fecha nacimiento</label>
                                            <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($fields['fecha_nacimiento'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Contraseña <?php echo $editingId ? '(opcional)' : ''; ?></label>
                                            <input type="password" name="password" class="form-control" <?php echo $editingId ? '' : 'required'; ?>>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Estado</label>
                                            <select name="estado" class="form-select">
                                                <option value="1" <?php echo $fields['estado'] === '1' ? 'selected' : ''; ?>>Activo</option>
                                                <option value="0" <?php echo $fields['estado'] === '0' ? 'selected' : ''; ?>>Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($fields['direccion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Dirección">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" <?php echo !has_permission('usuarios', $editingId ? 'edit' : 'create') ? 'disabled' : ''; ?>>
                                                <?php echo $editingId ? 'Actualizar usuario' : 'Guardar usuario'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="usuario.php" class="btn btn-outline-secondary">Cancelar edición</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Usuarios registrados</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Apellido</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                                <th>Usuario</th>
                                                <th>Rol</th>
                                                <th>Estado</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$usuarios) : ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($usuarios as $usuario) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['apellido'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['rol'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo (int) ($usuario['estado'] ?? 0) === 1 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
                                                                <?php echo (int) ($usuario['estado'] ?? 0) === 1 ? 'Activo' : 'Inactivo'; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars((string) ($usuario['fecha_creacion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="usuario.php?view=<?php echo (int) $usuario['id']; ?>">Ver</a>
                                                            <?php if (has_permission('usuarios', 'edit')) : ?>
                                                                <a class="btn btn-sm btn-outline-secondary" href="usuario.php?edit=<?php echo (int) $usuario['id']; ?>">Editar</a>
                                                            <?php endif; ?>
                                                            <?php if (has_permission('usuarios', 'delete')) : ?>
                                                                <form method="post" action="usuario.php" class="d-inline">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo (int) $usuario['id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Main Content -->
        <!-- ============================================================== -->

        <?php include('partials/footer.php'); ?>
    </div>
    <!-- End page -->

    <?php include('partials/footer-scripts.php'); ?>

    <script>
        function formatRut(value) {
            const clean = value.replace(/[^0-9kK]/g, '').toUpperCase();
            if (clean.length === 0) {
                return '';
            }
            const body = clean.slice(0, -1);
            const dv = clean.slice(-1);
            const reversed = body.split('').reverse();
            const grouped = [];
            for (let i = 0; i < reversed.length; i += 3) {
                grouped.push(reversed.slice(i, i + 3).reverse().join(''));
            }
            const formattedBody = grouped.reverse().join('.');
            return `${formattedBody}-${dv}`;
        }

        document.querySelectorAll('.rut-field').forEach((input) => {
            input.addEventListener('blur', (event) => {
                event.target.value = formatRut(event.target.value);
            });
        });
    </script>
</body>
</html>
