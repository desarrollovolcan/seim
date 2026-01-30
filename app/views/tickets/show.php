<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h5 class="card-title mb-0">Detalle del ticket</h5>
                    <?php echo render_id_badge($ticket['id'] ?? null); ?>
                </div>
                <p class="mb-1"><strong>ID:</strong> <?php echo render_id_badge($ticket['id'] ?? null); ?></p>
                <p class="mb-1">
                    <strong>Cliente:</strong>
                    <a href="index.php?route=clients/show&id=<?php echo (int)($ticket['client_id'] ?? 0); ?>" class="link-reset">
                        <?php echo e($ticket['client_name'] ?? ''); ?>
                    </a>
                </p>
                <p class="mb-1"><strong>Asunto:</strong> <?php echo e($ticket['subject'] ?? ''); ?></p>
                <p class="mb-3"><strong>Prioridad:</strong> <?php echo e(ucfirst($ticket['priority'] ?? 'media')); ?></p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="index.php?route=projects&client_id=<?php echo (int)($ticket['client_id'] ?? 0); ?>" class="btn btn-outline-primary btn-sm">Ver proyectos</a>
                    <a href="index.php?route=invoices&client_id=<?php echo (int)($ticket['client_id'] ?? 0); ?>" class="btn btn-outline-success btn-sm">Ver facturas</a>
                </div>

                <form method="post" action="index.php?route=tickets/status">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="ticket_id" value="<?php echo (int)$ticket['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <?php
                            $statuses = ['abierto' => 'Abierto', 'en_progreso' => 'En progreso', 'pendiente' => 'Pendiente', 'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado'];
                            ?>
                            <?php foreach ($statuses as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo ($ticket['status'] ?? '') === $value ? 'selected' : ''; ?>>
                                    <?php echo e($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Asignar a</label>
                        <select name="assigned_user_id" class="form-select">
                            <option value="">Sin asignar</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo (int)$user['id']; ?>" <?php echo (int)($ticket['assigned_user_id'] ?? 0) === (int)$user['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($user['name'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Actualizar estado</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="card-title mb-0">Historial de mensajes</h5>
                    <small class="text-muted">Actualización automática</small>
                </div>
                <div class="flex-grow-1 overflow-auto mb-3" style="max-height: 420px;" id="ticketMessages" data-last-id="<?php echo !empty($messages) ? (int)end($messages)['id'] : 0; ?>">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <?php
                            $isUser = ($message['sender_type'] ?? '') === 'user';
                            $senderLabel = $message['sender_name'] ?? ($isUser ? 'Equipo' : 'Cliente');
                            $recipientLabel = $isUser ? 'Cliente' : 'Equipo';
                            ?>
                            <div class="border rounded-3 p-3 mb-2 bg-light bg-opacity-50">
                                <div class="row g-2 small text-muted">
                                    <div class="col-sm-4">
                                        <span class="fw-semibold text-dark">De:</span> <?php echo e($senderLabel); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="fw-semibold text-dark">Para:</span> <?php echo e($recipientLabel); ?>
                                    </div>
                                    <div class="col-sm-4 text-sm-end">
                                        <?php echo e($message['created_at'] ?? ''); ?>
                                    </div>
                                </div>
                                <div class="mt-2 bg-white border rounded-3 px-3 py-2">
                                    <?php echo nl2br(e($message['message'] ?? '')); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">Aún no hay mensajes.</div>
                    <?php endif; ?>
                </div>
                <form method="post" action="index.php?route=tickets/message">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="ticket_id" value="<?php echo (int)$ticket['id']; ?>">
                    <div class="mb-2">
                        <textarea name="message" class="form-control" rows="3" placeholder="Escribe tu respuesta..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const ticketMessages = document.getElementById('ticketMessages');
    const ticketId = <?php echo (int)($ticket['id'] ?? 0); ?>;

    const appendMessage = (message) => {
        const isUser = message.sender_type === 'user';
        const senderLabel = message.sender_name || (isUser ? 'Equipo' : 'Cliente');
        const recipientLabel = isUser ? 'Cliente' : 'Equipo';
        const wrapper = document.createElement('div');
        wrapper.className = 'border rounded-3 p-3 mb-2 bg-light bg-opacity-50';
        wrapper.innerHTML = `
            <div class="row g-2 small text-muted">
                <div class="col-sm-4"><span class="fw-semibold text-dark">De:</span> ${senderLabel}</div>
                <div class="col-sm-4"><span class="fw-semibold text-dark">Para:</span> ${recipientLabel}</div>
                <div class="col-sm-4 text-sm-end">${message.created_at || ''}</div>
            </div>
            <div class="mt-2 bg-white border rounded-3 px-3 py-2">${(message.message || '').replace(/\\n/g, '<br>')}</div>
        `;
        ticketMessages.appendChild(wrapper);
    };

    const refreshMessages = async () => {
        if (!ticketMessages || !ticketId) {
            return;
        }
        const lastId = Number(ticketMessages.dataset.lastId || 0);
        const response = await fetch(`index.php?route=tickets/messages&ticket_id=${ticketId}&since_id=${lastId}`);
        if (!response.ok) {
            return;
        }
        const payload = await response.json();
        if (!payload.messages || payload.messages.length === 0) {
            return;
        }
        payload.messages.forEach((message) => {
            appendMessage(message);
            ticketMessages.dataset.lastId = String(message.id || lastId);
        });
        ticketMessages.scrollTop = ticketMessages.scrollHeight;
    };

    if (ticketMessages) {
        setInterval(refreshMessages, 8000);
    }
</script>
