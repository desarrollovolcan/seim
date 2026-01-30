<?php
$documents = $documents ?? [];
$categories = $categories ?? [];
$counts = $counts ?? [
    'all' => 0,
    'favorites' => 0,
    'recent' => 0,
    'downloads' => 0,
    'trash' => 0,
    'shared' => 0,
];
$activeFilter = $activeFilter ?? 'all';
$activeCategoryId = $activeCategoryId ?? null;
$users = $users ?? [];
$documentCount = count($documents);
$recentDocuments = array_slice($documents, 0, 4);
$formatSize = static function (int $bytes): string {
    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / 1024 / 1024, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
};
$fileIcon = static function (string $extension): string {
    return match (strtoupper($extension)) {
        'PDF' => 'ti ti-file-type-pdf',
        'XLS', 'XLSX', 'CSV' => 'ti ti-file-spreadsheet',
        'PPT', 'PPTX' => 'ti ti-file-type-ppt',
        'DOC', 'DOCX' => 'ti ti-file-type-doc',
        'PNG', 'JPG', 'JPEG', 'WEBP', 'GIF' => 'ti ti-photo',
        'MP3', 'WAV' => 'ti ti-music',
        'MP4', 'MOV' => 'ti ti-video',
        default => 'ti ti-file-text',
    };
};
$userInitials = static function (string $name): string {
    $name = trim($name);
    if ($name === '') {
        return 'U';
    }
    return strtoupper(mb_substr_safe($name, 0, 1));
};
$filterLink = static function (string $filter = 'all', ?int $categoryId = null): string {
    $params = ['route=documents', 'filter=' . urlencode($filter)];
    if ($categoryId) {
        $params[] = 'category=' . urlencode((string)$categoryId);
    }
    return 'index.php?' . implode('&', $params);
};
?>

<div class="outlook-box gap-1 documents-compact">
    <div class="offcanvas-lg offcanvas-start outlook-left-menu outlook-left-menu-md" tabindex="-1" id="documentsSidebarOffcanvas">
        <div class="card h-100 mb-0 rounded-0 border-0" data-simplebar>
            <div class="card-body p-3">
                <button type="button" class="btn btn-danger btn-sm fw-medium w-100" data-bs-toggle="modal" data-bs-target="#documentsUploadModal">Subir archivos</button>

                <div class="list-group list-group-flush list-custom mt-2">
                    <a href="<?php echo e($filterLink('all')); ?>" class="list-group-item list-group-item-action py-2 <?php echo $activeFilter === 'all' && !$activeCategoryId ? 'active' : ''; ?>">
                        <i class="ti ti-folder me-1 opacity-75 align-middle"></i>
                        <span class="align-middle">Mis archivos</span>
                        <span class="badge align-middle bg-danger-subtle fs-xxs text-danger float-end"><?php echo e((string)($counts['all'] ?? $documentCount)); ?></span>
                    </a>

                    <a href="<?php echo e($filterLink('shared')); ?>" class="list-group-item list-group-item-action py-2 <?php echo $activeFilter === 'shared' ? 'active' : ''; ?>">
                        <i class="ti ti-share align-middle me-1 opacity-75"></i>
                        <span class="align-middle">Compartidos conmigo</span>
                        <span class="badge align-middle bg-light fs-xxs text-muted float-end"><?php echo e((string)($counts['shared'] ?? 0)); ?></span>
                    </a>

                    <a href="<?php echo e($filterLink('recent')); ?>" class="list-group-item list-group-item-action py-2 <?php echo $activeFilter === 'recent' ? 'active' : ''; ?>">
                        <i class="ti ti-clock align-middle me-1 opacity-75"></i>
                        <span class="align-middle">Recientes</span>
                        <span class="badge align-middle bg-light fs-xxs text-muted float-end"><?php echo e((string)($counts['recent'] ?? 0)); ?></span>
                    </a>

                    <a href="<?php echo e($filterLink('favorites')); ?>" class="list-group-item list-group-item-action py-2 <?php echo $activeFilter === 'favorites' ? 'active' : ''; ?>">
                        <i class="ti ti-star align-middle me-1 opacity-75"></i>
                        <span class="align-middle">Favoritos</span>
                        <span class="badge align-middle bg-light fs-xxs text-muted float-end"><?php echo e((string)($counts['favorites'] ?? 0)); ?></span>
                    </a>

                    <a href="<?php echo e($filterLink('downloads')); ?>" class="list-group-item list-group-item-action py-2 <?php echo $activeFilter === 'downloads' ? 'active' : ''; ?>">
                        <i class="ti ti-download align-middle me-1 opacity-75"></i>
                        <span class="align-middle">Descargas</span>
                        <span class="badge align-middle bg-light fs-xxs text-muted float-end"><?php echo e((string)($counts['downloads'] ?? 0)); ?></span>
                    </a>

                    <a href="<?php echo e($filterLink('trash')); ?>" class="list-group-item list-group-item-action py-2 <?php echo $activeFilter === 'trash' ? 'active' : ''; ?>">
                        <i class="ti ti-trash me-1 align-middle opacity-75"></i>
                        <span class="align-middle">Papelera</span>
                        <span class="badge align-middle bg-light fs-xxs text-muted float-end"><?php echo e((string)($counts['trash'] ?? 0)); ?></span>
                    </a>

                    <div class="list-group-item mt-2 d-flex align-items-center justify-content-between py-2">
                        <span class="align-middle">Categorías</span>
                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#documentsCategoryModal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>

                    <?php if (empty($categories)): ?>
                        <div class="list-group-item text-muted fs-xs">
                            Crea una categoría para organizar documentos.
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between gap-2 py-2 <?php echo $activeCategoryId === (int)$category['id'] ? 'active' : ''; ?>">
                                <a href="<?php echo e($filterLink('all', (int)$category['id'])); ?>" class="d-flex align-items-center gap-1 flex-grow-1 link-reset">
                                    <span class="rounded-circle d-inline-block me-1 align-middle" style="width: 10px; height: 10px; background-color: <?php echo e((string)$category['color']); ?>"></span>
                                    <span class="align-middle"><?php echo e((string)$category['name']); ?></span>
                                    <span class="badge align-middle bg-light fs-xxs text-muted ms-auto"><?php echo e((string)($category['total'] ?? 0)); ?></span>
                                </a>
                                <form method="post" action="index.php?route=documents/categories/delete">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo e((string)$category['id']); ?>">
                                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger" title="Eliminar categoría">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div data-table data-table-rows-per-page="8" class="card h-100 mb-0 rounded-0 flex-grow-1 border-0">
        <div class="card-header border-light justify-content-between py-2">
            <div class="d-flex gap-2">
                <div class="d-lg-none d-inline-flex gap-2">
                    <button class="btn btn-default btn-icon btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#documentsSidebarOffcanvas" aria-controls="documentsSidebarOffcanvas">
                        <i class="ti ti-menu-2 fs-lg"></i>
                    </button>
                </div>

                <div class="app-search">
                    <input data-table-search type="search" class="form-control form-control-sm" placeholder="Buscar archivos...">
                    <i data-lucide="search" class="app-search-icon text-muted"></i>
                </div>
                <button data-table-delete-selected class="btn btn-danger btn-sm d-none">Eliminar</button>
            </div>

            <div class="d-flex align-items-center gap-2 documents-filter-bar">
                <span class="me-2 fw-semibold fs-xs text-muted">Filtrar por:</span>

                <div class="app-search">
                    <select data-table-filter="type" class="form-select form-control form-control-sm my-1 my-md-0">
                        <option value="">Tipo de archivo</option>
                                <option value="PDF">PDF</option>
                                <option value="DOC">DOC</option>
                                <option value="XLS">XLS</option>
                                <option value="PPT">PPT</option>
                                <option value="JPG">JPG</option>
                            </select>
                            <i data-lucide="file" class="app-search-icon text-muted"></i>
                        </div>

                <div>
                    <select data-table-set-rows-per-page class="form-select form-control form-control-sm my-1 my-md-0">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body pt-3" style="height: calc(100% - 86px);" data-simplebar data-simplebar-md>
            <div class="row g-2 mb-2">
                <?php if (empty($recentDocuments)): ?>
                    <div class="col-12">
                        <div class="border border-dashed rounded-3 p-3 text-center text-muted fs-sm">
                            Sube documentos para comenzar a organizar tu biblioteca.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentDocuments as $document): ?>
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card border border-dashed mb-0 documents-compact-card">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="flex-shrink-0 avatar-sm bg-light bg-opacity-50 text-muted rounded-2">
                                            <i class="<?php echo e($fileIcon($document['extension'] ?? '')); ?> fs-18 avatar-title"></i>
                                        </div>
                                        <div class="flex-grow-1 documents-card-body">
                                            <h5 class="mb-1 fs-xs text-truncate documents-card-title">
                                                <a href="<?php echo e($document['download_url']); ?>" class="link-reset documents-card-link">
                                                    <?php echo e($document['name']); ?>
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-0 fs-xs text-truncate documents-card-meta">
                                                <?php echo e($formatSize($document['size'] ?? 0)); ?>
                                                <?php if (!empty($document['category']['name'])): ?>
                                                    · <?php echo e((string)$document['category']['name']); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="dropdown flex-shrink-0 text-muted">
                                            <a href="#" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="<?php echo e($document['download_url']); ?>" class="dropdown-item"><i class="ti ti-download me-1"></i> Descargar</a>
                                                <?php $sharedUsersJson = htmlspecialchars(json_encode($document['shared_with'] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#documentsShareModal" data-document-id="<?php echo e((string)$document['id']); ?>" data-shared-users="<?php echo $sharedUsersJson; ?>">
                                                    <i class="ti ti-share me-1"></i> Compartir
                                                </button>
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#documentsCategoryAssignModal" data-document-id="<?php echo e((string)$document['id']); ?>">
                                                    <i class="ti ti-tag me-1"></i> Cambiar categoría
                                                </button>
                                                <?php if ($activeFilter === 'trash'): ?>
                                                    <form method="post" action="index.php?route=documents/restore" class="px-3">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                        <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                        <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                        <button type="submit" class="btn btn-link dropdown-item text-success p-0"><i class="ti ti-restore me-1"></i> Restaurar</button>
                                                    </form>
                                                    <form method="post" action="index.php?route=documents/purge" class="px-3">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                        <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                        <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                        <button type="submit" class="btn btn-link dropdown-item text-danger p-0"><i class="ti ti-trash me-1"></i> Eliminar definitivamente</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="post" action="index.php?route=documents/favorite" class="px-3">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                        <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                        <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                        <button type="submit" class="btn btn-link dropdown-item p-0">
                                                            <i class="ti ti-star me-1"></i>
                                                            <?php echo $document['is_favorite'] ? 'Quitar favorito' : 'Agregar a favoritos'; ?>
                                                        </button>
                                                    </form>
                                                    <form method="post" action="index.php?route=documents/delete" class="px-3">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                        <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                        <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                        <button type="submit" class="btn btn-link dropdown-item text-danger p-0"><i class="ti ti-trash me-1"></i> Enviar a papelera</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="row g-2">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" data-table-sort>
                            <thead class="bg-light-subtle text-muted fs-xs">
                                <tr>
                                    <th data-column="name">Nombre</th>
                                    <th data-column="type">Tipo</th>
                                    <th data-column="category">Categoría</th>
                                    <th data-column="size">Tamaño</th>
                                    <th data-column="shared">Compartido con</th>
                                    <th data-column="updated">Última actualización</th>
                                    <th class="text-end" data-column="actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documents)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay documentos cargados todavía.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($documents as $document): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-xs bg-light text-muted rounded-2">
                                                        <i class="<?php echo e($fileIcon($document['extension'] ?? '')); ?> fs-16 avatar-title"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?php echo e($document['name']); ?></div>
                                                        <div class="text-muted fs-xs">
                                                            <?php if (!empty($document['category']['name'])): ?>
                                                                <?php echo e((string)$document['category']['name']); ?>
                                                            <?php else: ?>
                                                                Sin categoría
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo e($document['extension']); ?></td>
                                            <td>
                                                <?php if (!empty($document['category']['name'])): ?>
                                                    <span class="badge rounded-pill" style="background-color: <?php echo e((string)$document['category']['color']); ?>;">
                                                        <?php echo e((string)$document['category']['name']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted fs-xs">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($formatSize($document['size'] ?? 0)); ?></td>
                                            <td>
                                                <?php $sharedUsers = $document['shared_with'] ?? []; ?>
                                                <?php if (empty($sharedUsers)): ?>
                                                    <span class="text-muted fs-xs">-</span>
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <?php foreach ($sharedUsers as $sharedUser): ?>
                                                            <?php $sharedName = (string)($sharedUser['name'] ?? ''); ?>
                                                            <?php if (!empty($sharedUser['avatar_path'])): ?>
                                                                <img src="<?php echo e((string)$sharedUser['avatar_path']); ?>"
                                                                     alt="<?php echo e($sharedName); ?>"
                                                                     class="rounded-circle"
                                                                     style="width: 28px; height: 28px; object-fit: cover;"
                                                                     title="<?php echo e($sharedName); ?>">
                                                            <?php else: ?>
                                                                <span class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-semibold"
                                                                      style="width: 28px; height: 28px;"
                                                                      title="<?php echo e($sharedName); ?>">
                                                                    <?php echo e($userInitials($sharedName)); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($document['updated_at']); ?></td>
                                            <td class="text-end">
                                                <div class="dropdown actions-dropdown">
                                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="<?php echo e($document['download_url']); ?>">
                                                                Descargar
                                                            </a>
                                                        </li>
                                                        <?php if ($activeFilter === 'trash'): ?>
                                                            <li>
                                                                <form method="post" action="index.php?route=documents/restore">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                                    <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                                    <button type="submit" class="dropdown-item dropdown-item-button text-success">Restaurar</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="post" action="index.php?route=documents/purge">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                                    <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar definitivamente</button>
                                                                </form>
                                                            </li>
                                                        <?php else: ?>
                                                            <li>
                                                                <?php $sharedUsersJson = htmlspecialchars(json_encode($document['shared_with'] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>
                                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#documentsShareModal" data-document-id="<?php echo e((string)$document['id']); ?>" data-shared-users="<?php echo $sharedUsersJson; ?>">
                                                                    Compartir
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#documentsCategoryAssignModal" data-document-id="<?php echo e((string)$document['id']); ?>">
                                                                    Cambiar categoría
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <form method="post" action="index.php?route=documents/favorite">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                                    <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                                    <button type="submit" class="dropdown-item dropdown-item-button">
                                                                        <?php echo $document['is_favorite'] ? 'Quitar favorito' : 'Agregar a favoritos'; ?>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="post" action="index.php?route=documents/delete">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                                    <input type="hidden" name="id" value="<?php echo e((string)$document['id']); ?>">
                                                                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                                                                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger">Enviar a papelera</button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
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

<div class="modal fade" id="documentsUploadModal" tabindex="-1" aria-labelledby="documentsUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="index.php?route=documents/store" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentsUploadModalLabel">Subir documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Archivos</label>
                        <input type="file" name="documents[]" class="form-control" multiple required>
                        <small class="text-muted">Máximo 10MB por archivo.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select">
                            <option value="">Sin categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo e((string)$category['id']); ?>"><?php echo e((string)$category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="documentsCategoryModal" tabindex="-1" aria-labelledby="documentsCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="index.php?route=documents/categories/store">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentsCategoryModalLabel">Nueva categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Marketing" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" class="form-control form-control-color" value="#6c757d" title="Selecciona un color">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="documentsCategoryAssignModal" tabindex="-1" aria-labelledby="documentsCategoryAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="index.php?route=documents/categories/assign">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentsCategoryAssignModalLabel">Asignar categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select">
                            <option value="">Sin categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo e((string)$category['id']); ?>"><?php echo e((string)$category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="documentsShareModal" tabindex="-1" aria-labelledby="documentsShareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="index.php?route=documents/share">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentsShareModalLabel">Compartir documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="redirect_filter" value="<?php echo e((string)$activeFilter); ?>">
                    <input type="hidden" name="redirect_category" value="<?php echo e((string)$activeCategoryId); ?>">
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Selecciona un usuario</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo e((string)$user['id']); ?>"><?php echo e((string)$user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="border-top pt-3 mt-3" data-share-list data-empty-text="Aún no se ha compartido con nadie."></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Compartir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const assignModal = document.getElementById('documentsCategoryAssignModal');
    const shareModal = document.getElementById('documentsShareModal');

    if (assignModal) {
        assignModal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            const documentId = trigger?.getAttribute('data-document-id');
            assignModal.querySelector('input[name="id"]').value = documentId || '';
        });
    }

    if (shareModal) {
        shareModal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            const documentId = trigger?.getAttribute('data-document-id');
            const shareList = shareModal.querySelector('[data-share-list]');
            shareModal.querySelector('input[name="id"]').value = documentId || '';
            if (!shareList) {
                return;
            }
            const sharedUsersRaw = trigger?.getAttribute('data-shared-users') || '[]';
            let sharedUsers = [];
            try {
                sharedUsers = JSON.parse(sharedUsersRaw);
                if (!Array.isArray(sharedUsers)) {
                    sharedUsers = [];
                }
            } catch (error) {
                sharedUsers = [];
            }
            shareList.innerHTML = '';
            if (sharedUsers.length === 0) {
                const emptyText = shareList.getAttribute('data-empty-text') || 'Sin usuarios compartidos.';
                const emptyItem = document.createElement('p');
                emptyItem.className = 'text-muted mb-0';
                emptyItem.textContent = emptyText;
                shareList.appendChild(emptyItem);
                return;
            }
            const csrfToken = shareModal.querySelector('input[name="csrf_token"]')?.value || '';
            const redirectFilter = shareModal.querySelector('input[name="redirect_filter"]')?.value || '';
            const redirectCategory = shareModal.querySelector('input[name="redirect_category"]')?.value || '';

            const createHiddenInput = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                return input;
            };

            sharedUsers.forEach((sharedUser) => {
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center justify-content-between gap-2 border rounded-2 px-2 py-1 mb-2';

                const name = document.createElement('span');
                name.className = 'fw-medium';
                name.textContent = sharedUser?.name || 'Usuario';

                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'index.php?route=documents/unshare';
                form.className = 'ms-auto';
                form.appendChild(createHiddenInput('csrf_token', csrfToken));
                form.appendChild(createHiddenInput('id', documentId || ''));
                form.appendChild(createHiddenInput('user_id', sharedUser?.id ? String(sharedUser.id) : ''));
                form.appendChild(createHiddenInput('redirect_filter', redirectFilter));
                form.appendChild(createHiddenInput('redirect_category', redirectCategory));

                const button = document.createElement('button');
                button.type = 'submit';
                button.className = 'btn btn-sm btn-outline-danger';
                button.textContent = 'Dejar de compartir';
                form.appendChild(button);

                item.appendChild(name);
                item.appendChild(form);
                shareList.appendChild(item);
            });
        });
    }
});
</script>

<style>
    .documents-compact .list-group-item {
        font-size: 0.85rem;
    }

    .documents-compact .documents-compact-card .dropdown-toggle {
        font-size: 1rem;
    }

    .documents-compact .table td,
    .documents-compact .table th {
        padding-top: 0.55rem;
        padding-bottom: 0.55rem;
    }

    .documents-compact .table td {
        font-size: 0.85rem;
    }

    .documents-compact .actions-dropdown .btn {
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
    }

    .documents-compact-card .documents-card-body {
        min-width: 0;
    }

    .documents-compact-card .documents-card-link {
        display: block;
        max-width: 100%;
    }

    .documents-compact-card .documents-card-title,
    .documents-compact-card .documents-card-meta {
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .documents-filter-bar {
        flex-wrap: wrap;
    }

    .documents-filter-bar .app-search {
        min-width: 180px;
    }

    .documents-filter-bar .app-search .form-select {
        padding-left: 2.5rem;
    }
</style>
