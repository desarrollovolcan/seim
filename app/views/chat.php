<?php if (!empty($chatError)): ?>
    <div class="alert alert-danger"><?php echo e($chatError); ?></div>
<?php endif; ?>
<?php if (!empty($chatSuccess)): ?>
    <div class="alert alert-success"><?php echo e($chatSuccess); ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-xxl-4">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Conversaciones</h5>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#adminNewChat">
                        <i class="ti ti-plus me-1"></i>Nuevo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse mb-3" id="adminNewChat">
                    <div class="card card-body border">
                        <form method="post" action="chat.php">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="action" value="create_thread">
                            <div class="mb-3">
                                <label class="form-label">Cliente</label>
                                <select name="client_id" class="form-select" required>
                                    <option value="">Selecciona un cliente</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo (int)$client['id']; ?>">
                                            <?php echo e($client['name'] ?? ''); ?> · <?php echo e($client['email'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Asunto</label>
                                <input type="text" name="subject" class="form-control" placeholder="Ej. Planificación del proyecto" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensaje inicial</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Escribe el primer mensaje..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Crear conversación</button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($chatThreads)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($chatThreads as $thread): ?>
                            <?php $isActive = (int)$thread['id'] === $activeThreadId; ?>
                            <a href="chat.php?thread=<?php echo (int)$thread['id']; ?>" class="list-group-item list-group-item-action <?php echo $isActive ? 'active' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($thread['client_avatar'])): ?>
                                            <img src="<?php echo e($thread['client_avatar']); ?>" alt="Avatar cliente" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <?php echo e(strtoupper(substr($thread['client_name'] ?? 'C', 0, 1))); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?php echo e($thread['client_name'] ?? 'Cliente'); ?></div>
                                            <div class="text-muted fs-xs <?php echo $isActive ? 'text-white-50' : ''; ?>">
                                                <?php echo e($thread['subject'] ?? 'Conversación'); ?>
                                            </div>
                                            <div class="text-muted fs-xxs <?php echo $isActive ? 'text-white-50' : ''; ?>">
                                                <?php echo e($thread['last_message'] ?? 'Sin mensajes aún.'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($thread['last_message_at'])): ?>
                                        <span class="fs-xxs text-muted <?php echo $isActive ? 'text-white-50' : ''; ?>">
                                            <?php echo e($thread['last_message_at']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted fs-sm">Aún no existen conversaciones activas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xxl-8">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <?php if (!empty($activeThread)): ?>
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                        <div>
                            <h5 class="mb-1"><?php echo e($activeThread['client_name'] ?? 'Cliente'); ?></h5>
                            <div class="text-muted fs-xs"><?php echo e($activeThread['subject'] ?? 'Conversación'); ?></div>
                        </div>
                        <span class="badge bg-success-subtle text-success"><?php echo e(ucfirst($activeThread['status'] ?? 'abierto')); ?></span>
                    </div>
                <?php else: ?>
                    <div class="text-muted">Selecciona una conversación para comenzar.</div>
                <?php endif; ?>
            </div>

            <div class="card-body d-flex flex-column" style="min-height: 520px;">
                <?php if (!empty($activeThread)): ?>
                    <div class="flex-grow-1 overflow-auto mb-3" style="max-height: 420px;" id="chatMessages" data-last-id="<?php echo !empty($chatMessages) ? (int)end($chatMessages)['id'] : 0; ?>">
                        <?php if (!empty($chatMessages)): ?>
                            <?php foreach ($chatMessages as $message): ?>
                                <?php
                                $isUser = ($message['sender_type'] ?? '') === 'user';
                                $bubbleClasses = $isUser ? 'bg-primary text-white ms-auto' : 'bg-light';
                                ?>
                                <div class="d-flex mb-3 <?php echo $isUser ? 'justify-content-end' : 'justify-content-start'; ?>">
                                    <?php if (!$isUser): ?>
                                        <?php if (!empty($message['sender_avatar'])): ?>
                                            <img src="<?php echo e($message['sender_avatar']); ?>" alt="Avatar" class="rounded-circle me-2" style="width: 36px; height: 36px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                                <?php echo e(strtoupper(substr($message['sender_name'] ?? 'C', 0, 1))); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="p-3 rounded-3 <?php echo $bubbleClasses; ?>" style="max-width: 75%;">
                                        <div class="fw-semibold mb-1"><?php echo e($message['sender_name'] ?? ($isUser ? 'Equipo' : 'Cliente')); ?></div>
                                        <div><?php echo nl2br(e($message['message'] ?? '')); ?></div>
                                        <?php if (!empty($message['created_at'])): ?>
                                            <div class="fs-xxs mt-2 <?php echo $isUser ? 'text-white-50' : 'text-muted'; ?>">
                                                <?php echo e($message['created_at']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($isUser): ?>
                                        <?php if (!empty($message['sender_avatar'])): ?>
                                            <img src="<?php echo e($message['sender_avatar']); ?>" alt="Avatar" class="rounded-circle ms-2" style="width: 36px; height: 36px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center ms-2" style="width: 36px; height: 36px;">
                                                <?php echo e(strtoupper(substr($message['sender_name'] ?? 'E', 0, 1))); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted">Aún no hay mensajes en esta conversación.</div>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="chat.php?thread=<?php echo (int)$activeThreadId; ?>" class="mt-auto">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="action" value="send_message">
                        <input type="hidden" name="thread_id" value="<?php echo (int)$activeThreadId; ?>">
                        <div class="mb-2">
                            <textarea name="message" class="form-control" rows="3" placeholder="Escribe tu mensaje..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($activeThreadId)): ?>
    <script>
        const chatThreadId = <?php echo (int)$activeThreadId; ?>;
        const chatMessagesContainer = document.getElementById('chatMessages');
        const chatBellStorageKey = 'adminChatLastSeen';
        const initializeChatBell = () => {
            if (!chatMessagesContainer) {
                return;
            }
            const currentId = Number(chatMessagesContainer.dataset.lastId || 0);
            if (!localStorage.getItem(chatBellStorageKey)) {
                localStorage.setItem(chatBellStorageKey, String(currentId));
            }
        };

        const fetchChatMessages = async () => {
            if (!chatMessagesContainer) {
                return;
            }
            const lastId = Number(chatMessagesContainer.dataset.lastId || 0);
            const response = await fetch(`index.php?route=chat/messages&thread=${chatThreadId}&since=${lastId}`);
            if (!response.ok) {
                return;
            }
            const payload = await response.json();
            if (!payload.messages || payload.messages.length === 0) {
                return;
            }
            payload.messages.forEach((message) => {
                const isUser = message.sender_type === 'user';
                const wrapper = document.createElement('div');
                wrapper.className = `d-flex mb-3 ${isUser ? 'justify-content-end' : 'justify-content-start'}`;
                const bubble = document.createElement('div');
                bubble.className = `p-3 rounded-3 ${isUser ? 'bg-primary text-white ms-auto' : 'bg-light'}`;
                bubble.style.maxWidth = '75%';

                const name = document.createElement('div');
                name.className = 'fw-semibold mb-1';
                name.textContent = message.sender_name || (isUser ? 'Equipo' : 'Cliente');

                const text = document.createElement('div');
                text.textContent = message.message || '';

                const time = document.createElement('div');
                time.className = `fs-xxs mt-2 ${isUser ? 'text-white-50' : 'text-muted'}`;
                time.textContent = message.created_at || '';

                bubble.appendChild(name);
                bubble.appendChild(text);
                bubble.appendChild(time);

                const avatar = document.createElement('div');
                avatar.className = `rounded-circle ${isUser ? 'bg-primary-subtle text-primary ms-2' : 'bg-secondary-subtle text-secondary me-2'} d-flex align-items-center justify-content-center`;
                avatar.style.width = '36px';
                avatar.style.height = '36px';

                if (message.sender_avatar) {
                    const img = document.createElement('img');
                    img.src = message.sender_avatar;
                    img.alt = 'Avatar';
                    img.className = `rounded-circle ${isUser ? 'ms-2' : 'me-2'}`;
                    img.style.width = '36px';
                    img.style.height = '36px';
                    img.style.objectFit = 'cover';
                    if (isUser) {
                        wrapper.appendChild(bubble);
                        wrapper.appendChild(img);
                    } else {
                        wrapper.appendChild(img);
                        wrapper.appendChild(bubble);
                    }
                } else {
                    avatar.textContent = (message.sender_name || (isUser ? 'E' : 'C')).charAt(0).toUpperCase();
                    if (isUser) {
                        wrapper.appendChild(bubble);
                        wrapper.appendChild(avatar);
                    } else {
                        wrapper.appendChild(avatar);
                        wrapper.appendChild(bubble);
                    }
                }

                chatMessagesContainer.appendChild(wrapper);
                chatMessagesContainer.dataset.lastId = message.id;
            });
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        };

        initializeChatBell();
        setInterval(fetchChatMessages, 5000);

        const chatBellButton = document.getElementById('chatNotificationButton');
        if (chatBellButton) {
            chatBellButton.addEventListener('click', () => {
                const latest = Number(chatMessagesContainer?.dataset.lastId || 0);
                localStorage.setItem(chatBellStorageKey, String(latest));
            });
        }
    </script>
<?php endif; ?>
