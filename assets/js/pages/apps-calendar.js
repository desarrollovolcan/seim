/**
 * Template Name: UBold - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Apps Calendar
 */

class CalendarSchedule {
    constructor() {
        this.body = document.body;
        this.modal = new bootstrap.Modal(document.getElementById('event-modal'), {backdrop: 'static'});
        this.calendar = document.getElementById('calendar');
        this.formEvent = document.getElementById('forms-event');
        this.btnNewEvent = document.querySelectorAll('.btn-new-event');
        this.btnDeleteEvent = document.getElementById('btn-delete-event');
        this.btnSaveEvent = document.getElementById('btn-save-event');
        this.modalTitle = document.getElementById('modal-title');
        this.eventIdInput = document.getElementById('event-id');
        this.eventTitleInput = document.getElementById('event-title');
        this.eventTypeInput = document.getElementById('event-type');
        this.eventCategoryInput = document.getElementById('event-category');
        this.eventStartInput = document.getElementById('event-start');
        this.eventEndInput = document.getElementById('event-end');
        this.eventLocationInput = document.getElementById('event-location');
        this.eventReminderInput = document.getElementById('event-reminder');
        this.eventAllDayInput = document.getElementById('event-all-day');
        this.eventDescriptionInput = document.getElementById('event-description');
        this.eventAttendeesInput = document.getElementById('event-attendees');
        this.attendeesPreview = document.getElementById('event-attendees-preview');
        this.documentPreview = document.getElementById('event-documents-preview');
        this.documentCheckboxes = document.querySelectorAll('.calendar-document-checkbox');
        this.externalEventContainer = document.getElementById('external-events');
        this.eventTypesList = document.getElementById('event-types-list');
        this.eventTypesEmpty = document.getElementById('event-types-empty');
        this.eventTypesHelp = document.getElementById('event-types-help');
        this.typeModalElement = document.getElementById('event-type-modal');
        this.typeModal = this.typeModalElement ? new bootstrap.Modal(this.typeModalElement, {backdrop: 'static'}) : null;
        this.typeForm = document.getElementById('event-type-form');
        this.typeModalTitle = document.getElementById('event-type-modal-title');
        this.typeIdInput = document.getElementById('event-type-id');
        this.typeNameInput = document.getElementById('event-type-name');
        this.typeClassInput = document.getElementById('event-type-class');
        this.btnAddType = document.getElementById('btn-add-type');
        this.btnDeleteType = document.getElementById('btn-delete-type');
        this.btnSaveType = document.getElementById('btn-save-type');
        this.config = window.calendarConfig || {};
        this.calendarObj = null;
        this.selectedEvent = null;
        this.newEventData = null;
        this.pendingEvent = null;
        this.activeDocumentIds = [];
        this.eventTypes = Array.isArray(this.config.eventTypes)
            ? this.config.eventTypes.map(function (type) {
                return {
                    id: parseInt(type.id, 10),
                    name: type.name || '',
                    class_name: type.class_name || ''
                };
            })
            : [];
        this.typeClasses = Array.isArray(this.config.typeClasses) ? this.config.typeClasses : [];
    }

    init() {
        const today = new Date();
        const self = this;
        this.renderEventTypes();
        this.refreshTypeOptions();
        this.syncTypeColor();
        this.updateCreateState();

        if (this.eventTypesList) {
            new FullCalendar.Draggable(this.eventTypesList, {
                itemSelector: '.external-event',
                eventData: function (eventEl) {
                    return {
                        title: eventEl.getAttribute('data-name') || eventEl.innerText.trim(),
                        classNames: eventEl.getAttribute('data-class'),
                        extendedProps: {
                            type_id: parseInt(eventEl.getAttribute('data-type-id'), 10) || null,
                            type_name: eventEl.getAttribute('data-name') || ''
                        }
                    };
                }
            });
        }

        self.calendarObj = new FullCalendar.Calendar(self.calendar, {
            plugins: [],
            slotDuration: '00:30:00',
            slotMinTime: '07:00:00',
            slotMaxTime: '19:00:00',
            themeSystem: 'bootstrap',
            bootstrapFontAwesome: false,
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista',
                prev: 'Anterior',
                next: 'Siguiente'
            },
            initialView: 'dayGridMonth',
            handleWindowResize: true,
            height: window.innerHeight - 240,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: self.config.eventsUrl || [],
            editable: true,
            droppable: true,
            selectable: true,
            select: function (info) {
                self.onSelect(info);
            },
            dateClick: function (info) {
                self.onSelect(info);
            },
            eventClick: function (info) {
                self.onEventClick(info);
            },
            eventReceive: function (info) {
                if (!self.hasTypes()) {
                    info.event.remove();
                    alert('Debes crear un tipo de evento antes de agendar.');
                    return;
                }
                self.pendingEvent = info.event;
                self.selectedEvent = info.event;
                self.openModalWithEvent(info.event, true);
            },
            eventDrop: function (info) {
                self.persistEvent(info.event, false).catch(function () {
                    info.revert();
                });
            },
            eventResize: function (info) {
                self.persistEvent(info.event, false).catch(function () {
                    info.revert();
                });
            }
        });

        self.calendarObj.render();
        self.updateCreateState();

        self.btnNewEvent.forEach(function (btn) {
            btn.addEventListener('click', function () {
                self.onSelect({
                    date: new Date(),
                    allDay: true
                });
            });
        });

        if (self.eventTypeInput) {
            self.eventTypeInput.addEventListener('change', function () {
                self.syncTypeColor();
            });
        }

        if (self.eventAllDayInput) {
            self.eventAllDayInput.addEventListener('change', function () {
                self.syncAllDayInputs();
            });
        }

        self.documentCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                self.updateDocumentPreview();
            });
        });

        if (self.eventAttendeesInput) {
            self.eventAttendeesInput.addEventListener('change', function () {
                self.updateAttendeesPreview();
            });
        }

        self.formEvent?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = self.formEvent;

            if (form.checkValidity()) {
                self.saveEvent().catch(function (error) {
                    console.error(error);
                    alert('No pudimos guardar el evento.');
                });
            } else {
                e.stopPropagation();
                form.classList.add('was-validated');
            }
        });

        self.btnDeleteEvent?.addEventListener('click', function () {
            self.deleteEvent().catch(function (error) {
                console.error(error);
                alert('No pudimos eliminar el evento.');
            });
        });

        self.btnAddType?.addEventListener('click', function () {
            self.openTypeModal();
        });

        if (self.eventTypesList) {
            self.eventTypesList.addEventListener('click', function (event) {
                const target = event.target.closest('button');
                if (!target) {
                    return;
                }
                event.preventDefault();
                event.stopPropagation();
                const typeId = parseInt(target.getAttribute('data-type-id'), 10);
                if (Number.isNaN(typeId)) {
                    return;
                }
                const type = self.getTypeById(typeId);
                if (!type) {
                    return;
                }
                const action = target.getAttribute('data-action');
                if (action === 'edit') {
                    self.openTypeModal(type);
                }
                if (action === 'delete') {
                    self.requestDeleteType(type);
                }
            });
            self.eventTypesList.addEventListener('mousedown', function (event) {
                const target = event.target.closest('button');
                if (target) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        }

        self.typeForm?.addEventListener('submit', function (event) {
            event.preventDefault();
            const form = self.typeForm;
            if (form.checkValidity()) {
                self.saveType().catch(function (error) {
                    console.error(error);
                    alert('No pudimos guardar el tipo.');
                });
            } else {
                event.stopPropagation();
                form.classList.add('was-validated');
            }
        });

        self.btnDeleteType?.addEventListener('click', function () {
            const typeId = parseInt(self.typeIdInput?.value || '0', 10);
            const type = self.getTypeById(typeId);
            if (type) {
                self.requestDeleteType(type);
            }
        });

        const modalEl = document.getElementById('event-modal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                if (self.pendingEvent) {
                    self.pendingEvent.remove();
                    self.pendingEvent = null;
                }
            });
        }
    }

    onEventClick(info) {
        this.formEvent?.reset();
        this.formEvent.classList.remove('was-validated');
        this.newEventData = null;
        this.pendingEvent = null;
        this.btnDeleteEvent.style.display = 'block';
        this.modalTitle.text = 'Editar evento';
        this.selectedEvent = info.event;
        this.openModalWithEvent(info.event, false);
    }

    onSelect(info) {
        this.formEvent?.reset();
        this.formEvent?.classList.remove('was-validated');
        this.selectedEvent = null;
        this.pendingEvent = null;
        this.newEventData = info;
        this.btnDeleteEvent.style.display = 'none';
        this.modalTitle.text = 'Crear evento';
        if (!this.hasTypes()) {
            alert('Debes crear un tipo de evento antes de agendar.');
            this.calendarObj.unselect();
            return;
        }
        this.populateFormForNewEvent(info);
        this.modal.show();
        this.calendarObj.unselect();
    }

    openModalWithEvent(event, isNewFromDrop) {
        this.formEvent?.classList.remove('was-validated');
        if (isNewFromDrop) {
            this.btnDeleteEvent.style.display = 'none';
            this.modalTitle.text = 'Crear evento';
        } else {
            this.btnDeleteEvent.style.display = 'block';
            this.modalTitle.text = 'Editar evento';
        }
        this.populateFormFromEvent(event);
        this.modal.show();
    }

    populateFormForNewEvent(info) {
        if (this.eventIdInput) {
            this.eventIdInput.value = '';
        }
        if (this.eventTitleInput) {
            this.eventTitleInput.value = '';
        }
        const defaultType = this.eventTypes[0] || null;
        if (this.eventTypeInput) {
            this.eventTypeInput.value = defaultType ? String(defaultType.id) : '';
        }
        if (this.eventCategoryInput) {
            this.eventCategoryInput.value = defaultType ? defaultType.class_name : '';
        }
        const selection = this.normalizeSelection(info);
        if (this.eventAllDayInput) {
            this.eventAllDayInput.checked = selection.allDay === true;
        }
        this.setDateInputs(selection.start, selection.allDay === true, selection.end);
        if (this.eventLocationInput) {
            this.eventLocationInput.value = '';
        }
        if (this.eventReminderInput) {
            this.eventReminderInput.value = '';
        }
        if (this.eventDescriptionInput) {
            this.eventDescriptionInput.value = '';
        }
        this.resetDocumentSelection([]);
        this.resetAttendeeSelection([]);
    }

    populateFormFromEvent(event) {
        if (this.eventIdInput) {
            this.eventIdInput.value = event.id || '';
        }
        if (this.eventTitleInput) {
            this.eventTitleInput.value = event.title || '';
        }
        if (this.eventTypeInput) {
            const typeId = event.extendedProps?.type_id;
            const fallbackType = this.eventTypes[0]?.id || '';
            this.eventTypeInput.value = typeId !== null && typeId !== undefined ? String(typeId) : String(fallbackType);
        }
        if (this.eventCategoryInput) {
            const type = this.getTypeById(parseInt(this.eventTypeInput?.value || '0', 10));
            this.eventCategoryInput.value = type ? type.class_name : '';
        }
        if (this.eventAllDayInput) {
            this.eventAllDayInput.checked = event.allDay === true;
        }
        const endDate = event.allDay ? this.normalizeAllDayEndForForm(event.end) : event.end;
        this.setDateInputs(event.start, event.allDay === true, endDate);
        if (this.eventLocationInput) {
            this.eventLocationInput.value = event.extendedProps?.location || '';
        }
        if (this.eventReminderInput) {
            const reminder = event.extendedProps?.reminder_minutes;
            this.eventReminderInput.value = reminder !== null && reminder !== undefined ? String(reminder) : '';
        }
        if (this.eventDescriptionInput) {
            this.eventDescriptionInput.value = event.extendedProps?.description || '';
        }
        const docs = event.extendedProps?.documents || [];
        this.resetDocumentSelection(docs.map(function (doc) { return doc.id; }));
        const attendees = event.extendedProps?.attendees || [];
        this.resetAttendeeSelection(attendees.map(function (attendee) { return attendee.id; }));
    }

    setDateInputs(startDate, allDay, endDate = null) {
        if (!this.eventStartInput) {
            return;
        }
        const startValue = this.formatDateInput(startDate, allDay);
        this.eventStartInput.value = startValue || '';
        if (this.eventEndInput) {
            const endValue = this.formatDateInput(endDate || startDate, allDay);
            this.eventEndInput.value = endValue || '';
        }
        this.syncAllDayInputs();
    }

    formatDateInput(value, allDay) {
        if (!value) {
            return '';
        }
        const date = value instanceof Date ? value : new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '';
        }
        const offset = date.getTimezoneOffset();
        const localDate = new Date(date.getTime() - offset * 60000);
        const iso = localDate.toISOString();
        return allDay ? iso.slice(0, 10) + 'T00:00' : iso.slice(0, 16);
    }

    syncAllDayInputs() {
        if (!this.eventAllDayInput || !this.eventStartInput || !this.eventEndInput) {
            return;
        }
        if (this.eventAllDayInput.checked) {
            this.eventStartInput.type = 'date';
            this.eventEndInput.type = 'date';
            if (this.eventStartInput.value) {
                this.eventStartInput.value = this.eventStartInput.value.slice(0, 10);
            }
            if (this.eventEndInput.value) {
                this.eventEndInput.value = this.eventEndInput.value.slice(0, 10);
            }
        } else {
            this.eventStartInput.type = 'datetime-local';
            this.eventEndInput.type = 'datetime-local';
        }
    }

    normalizeSelection(info) {
        const start = info.start || info.date || new Date();
        let end = info.end || info.date || start;
        if (info.allDay && info.end) {
            const endDate = new Date(info.end);
            endDate.setDate(endDate.getDate() - 1);
            end = endDate;
        }
        return {
            start: start,
            end: end,
            allDay: info.allDay === true
        };
    }

    normalizeAllDayEndForForm(endDate) {
        if (!endDate) {
            return null;
        }
        const adjusted = new Date(endDate);
        adjusted.setDate(adjusted.getDate() - 1);
        return adjusted;
    }

    buildPayload() {
        const allDay = this.eventAllDayInput?.checked || false;
        const startRaw = this.eventStartInput?.value || '';
        const endRaw = this.eventEndInput?.value || '';
        const start = this.normalizeInputDate(startRaw, allDay);
        const end = this.normalizeInputDate(endRaw, allDay);
        const typeId = parseInt(this.eventTypeInput?.value || '0', 10);
        const type = this.getTypeById(typeId);

        return {
            id: this.eventIdInput?.value || null,
            title: this.eventTitleInput?.value || '',
            type_id: typeId || null,
            class_name: type ? type.class_name : this.eventCategoryInput?.value || '',
            start: start,
            end: end,
            all_day: allDay ? 1 : 0,
            location: this.eventLocationInput?.value || '',
            reminder_minutes: this.eventReminderInput?.value || null,
            description: this.eventDescriptionInput?.value || '',
            documents: this.getSelectedDocumentIds(),
            attendees: this.getSelectedAttendeeIds(),
            csrf_token: this.config.csrfToken
        };
    }

    normalizeInputDate(value, allDay) {
        if (!value) {
            return null;
        }
        if (allDay) {
            return value.length > 10 ? value.slice(0, 10) : value;
        }
        return value;
    }

    getSelectedDocumentIds() {
        const ids = [];
        this.documentCheckboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                ids.push(parseInt(checkbox.value, 10));
            }
        });
        return ids;
    }

    resetDocumentSelection(selectedIds) {
        this.activeDocumentIds = selectedIds || [];
        this.documentCheckboxes.forEach(function (checkbox) {
            const id = parseInt(checkbox.value, 10);
            checkbox.checked = selectedIds.includes(id);
        });
        this.updateDocumentPreview();
    }

    getSelectedAttendeeIds() {
        if (!this.eventAttendeesInput) {
            return [];
        }
        return Array.from(this.eventAttendeesInput.selectedOptions)
            .map(function (option) {
                return parseInt(option.value, 10);
            })
            .filter(function (id) {
                return !Number.isNaN(id) && id > 0;
            });
    }

    resetAttendeeSelection(selectedIds) {
        if (!this.eventAttendeesInput) {
            return;
        }
        const ids = selectedIds || [];
        Array.from(this.eventAttendeesInput.options).forEach(function (option) {
            const optionId = parseInt(option.value, 10);
            option.selected = ids.includes(optionId);
        });
        this.updateAttendeesPreview();
    }

    updateDocumentPreview() {
        if (!this.documentPreview) {
            return;
        }
        const selected = [];
        this.documentCheckboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                selected.push({
                    id: parseInt(checkbox.value, 10),
                    name: checkbox.getAttribute('data-document-name'),
                    url: checkbox.getAttribute('data-document-url')
                });
            }
        });
        if (selected.length === 0) {
            this.documentPreview.innerHTML = '<span class="text-muted fs-xs">Sin documentos adjuntos.</span>';
            return;
        }
        this.documentPreview.innerHTML = selected.map(function (doc) {
            return '<div class="d-flex align-items-center gap-2 fs-xs">' +
                '<i class="ti ti-file"></i>' +
                '<a class="link-reset" href="' + doc.url + '" target="_blank">' + doc.name + '</a>' +
                '</div>';
        }).join('');
    }

    updateAttendeesPreview() {
        if (!this.attendeesPreview || !this.eventAttendeesInput) {
            return;
        }
        const selected = Array.from(this.eventAttendeesInput.selectedOptions).map(function (option) {
            return option.getAttribute('data-user-name') || option.textContent.trim();
        }).filter(Boolean);

        if (selected.length === 0) {
            this.attendeesPreview.innerHTML = '<span class="text-muted fs-xs">Sin invitados asignados.</span>';
            return;
        }
        this.attendeesPreview.innerHTML = selected.map(function (name) {
            return '<div class="d-flex align-items-center gap-2 fs-xs">' +
                '<i class="ti ti-user"></i>' +
                '<span>' + name + '</span>' +
                '</div>';
        }).join('');
    }

    hasTypes() {
        return Array.isArray(this.eventTypes) && this.eventTypes.length > 0;
    }

    getTypeById(id) {
        if (!id) {
            return null;
        }
        return this.eventTypes.find(function (type) {
            return type.id === id;
        }) || null;
    }

    escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    renderEventTypes() {
        if (!this.eventTypesList) {
            return;
        }
        const self = this;
        this.eventTypesList.innerHTML = this.eventTypes.map(function (type) {
            const safeName = self.escapeHtml(type.name);
            return '<div class="external-event fc-event d-flex align-items-center gap-2 fw-semibold ' + type.class_name + '"' +
                ' data-type-id="' + type.id + '" data-class="' + type.class_name + '" data-name="' + safeName + '">' +
                '<span class="d-flex align-items-center flex-grow-1">' +
                '<i class="ti ti-circle-filled me-2"></i>' +
                '<span class="event-type-name">' + safeName + '</span>' +
                '</span>' +
                '<div class="d-flex gap-1">' +
                '<button type="button" class="btn btn-sm btn-light" data-action="edit" data-type-id="' + type.id + '">' +
                '<i class="ti ti-pencil"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-light" data-action="delete" data-type-id="' + type.id + '">' +
                '<i class="ti ti-trash"></i>' +
                '</button>' +
                '</div>' +
                '</div>';
        }).join('');
        if (this.eventTypesEmpty) {
            this.eventTypesEmpty.style.display = this.hasTypes() ? 'none' : 'block';
        }
    }

    refreshTypeOptions() {
        if (!this.eventTypeInput) {
            return;
        }
        const currentValue = this.eventTypeInput.value;
        this.eventTypeInput.innerHTML = '';
        if (!this.hasTypes()) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No hay tipos disponibles';
            this.eventTypeInput.appendChild(option);
            this.eventTypeInput.disabled = true;
            return;
        }
        this.eventTypeInput.disabled = false;
        this.eventTypes.forEach(function (type) {
            const option = document.createElement('option');
            option.value = String(type.id);
            option.textContent = type.name;
            if (String(type.id) === currentValue) {
                option.selected = true;
            }
            this.eventTypeInput.appendChild(option);
        }, this);
        if (!this.eventTypeInput.value && this.eventTypes[0]) {
            this.eventTypeInput.value = String(this.eventTypes[0].id);
        }
    }

    syncTypeColor() {
        if (!this.eventCategoryInput) {
            return;
        }
        const typeId = parseInt(this.eventTypeInput?.value || '0', 10);
        const type = this.getTypeById(typeId);
        this.eventCategoryInput.value = type ? type.class_name : '';
    }

    updateCreateState() {
        const disabled = !this.hasTypes();
        this.btnNewEvent.forEach(function (btn) {
            btn.disabled = disabled;
        });
        if (this.calendarObj) {
            this.calendarObj.setOption('selectable', !disabled);
            this.calendarObj.setOption('droppable', !disabled);
        }
        if (this.eventTypesHelp) {
            this.eventTypesHelp.textContent = disabled
                ? 'Crea un tipo para comenzar a agendar eventos.'
                : 'Arrastra un tipo de evento al calendario o haz clic en la fecha.';
        }
    }

    openTypeModal(type = null) {
        if (!this.typeModal) {
            return;
        }
        if (this.typeForm) {
            this.typeForm.reset();
            this.typeForm.classList.remove('was-validated');
        }
        if (type) {
            this.typeModalTitle.textContent = 'Editar tipo de evento';
            this.typeIdInput.value = String(type.id);
            this.typeNameInput.value = type.name;
            this.typeClassInput.value = type.class_name;
            this.btnDeleteType.style.display = 'inline-flex';
        } else {
            this.typeModalTitle.textContent = 'Nuevo tipo de evento';
            this.typeIdInput.value = '';
            this.typeNameInput.value = '';
            this.typeClassInput.value = this.typeClasses[0] || '';
            this.btnDeleteType.style.display = 'none';
        }
        this.typeModal.show();
    }

    async saveType() {
        const typeId = parseInt(this.typeIdInput?.value || '0', 10);
        const payload = {
            id: typeId || null,
            name: this.typeNameInput?.value || '',
            class_name: this.typeClassInput?.value || '',
            csrf_token: this.config.csrfToken
        };
        const url = typeId ? this.config.updateTypeUrl : this.config.storeTypeUrl;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al guardar el tipo');
        }
        const type = {
            id: parseInt(result.type.id, 10),
            name: result.type.name,
            class_name: result.type.class_name
        };
        if (typeId) {
            this.eventTypes = this.eventTypes.map(function (existing) {
                return existing.id === type.id ? type : existing;
            });
            this.syncEventsForType(type);
        } else {
            this.eventTypes.push(type);
        }
        this.eventTypes.sort(function (a, b) {
            return a.name.localeCompare(b.name, 'es', {sensitivity: 'base'});
        });
        this.renderEventTypes();
        this.refreshTypeOptions();
        this.syncTypeColor();
        this.updateCreateState();
        this.typeModal.hide();
    }

    requestDeleteType(type) {
        if (!confirm('¿Eliminar el tipo "' + type.name + '"?')) {
            return;
        }
        this.deleteType(type.id).catch(function (error) {
            console.error(error);
            alert('No pudimos eliminar el tipo.');
        });
    }

    async deleteType(typeId) {
        const response = await fetch(this.config.deleteTypeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: typeId,
                csrf_token: this.config.csrfToken
            })
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al eliminar el tipo');
        }
        this.eventTypes = this.eventTypes.filter(function (type) {
            return type.id !== typeId;
        });
        this.renderEventTypes();
        this.refreshTypeOptions();
        this.syncTypeColor();
        this.updateCreateState();
        if (this.typeModal) {
            this.typeModal.hide();
        }
    }

    syncEventsForType(type) {
        if (!this.calendarObj) {
            return;
        }
        this.calendarObj.getEvents().forEach(function (event) {
            if (event.extendedProps?.type_id === type.id) {
                event.setProp('classNames', type.class_name.split(' '));
                event.setExtendedProp('type_name', type.name);
            }
        });
    }

    async saveEvent() {
        const payload = this.buildPayload();
        if (!payload.title || !payload.start || !payload.type_id) {
            return;
        }
        const response = await fetch(this.config.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al guardar el evento');
        }
        const eventData = this.buildEventData(payload, result.id);

        if (this.selectedEvent) {
            this.applyEventUpdates(this.selectedEvent, eventData);
        } else if (this.pendingEvent) {
            this.applyEventUpdates(this.pendingEvent, eventData);
            this.pendingEvent = null;
        } else {
            this.calendarObj.addEvent(eventData);
        }

        this.selectedEvent = null;
        this.modal.hide();
    }

    buildEventData(payload, id) {
        const type = this.getTypeById(parseInt(payload.type_id || '0', 10));
        return {
            id: id,
            title: payload.title,
            start: payload.start,
            end: payload.end || null,
            allDay: payload.all_day === 1,
            className: type ? type.class_name : payload.class_name,
            extendedProps: {
                type_id: payload.type_id ? parseInt(payload.type_id, 10) : null,
                type_name: type ? type.name : '',
                location: payload.location,
                description: payload.description,
                reminder_minutes: payload.reminder_minutes !== null && payload.reminder_minutes !== '' ? parseInt(payload.reminder_minutes, 10) : null,
                documents: this.collectSelectedDocuments(),
                attendees: this.collectSelectedAttendees()
            }
        };
    }

    collectSelectedDocuments() {
        const docs = [];
        this.documentCheckboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                docs.push({
                    id: parseInt(checkbox.value, 10),
                    name: checkbox.getAttribute('data-document-name'),
                    download_url: checkbox.getAttribute('data-document-url')
                });
            }
        });
        return docs;
    }

    collectSelectedAttendees() {
        if (!this.eventAttendeesInput) {
            return [];
        }
        return Array.from(this.eventAttendeesInput.selectedOptions).map(function (option) {
            return {
                id: parseInt(option.value, 10),
                name: option.getAttribute('data-user-name') || option.textContent.trim()
            };
        }).filter(function (attendee) {
            return !Number.isNaN(attendee.id) && attendee.id > 0;
        });
    }

    applyEventUpdates(event, data) {
        event.setProp('title', data.title);
        event.setStart(data.start);
        event.setEnd(data.end);
        event.setAllDay(data.allDay);
        event.setProp('classNames', data.className.split(' '));
        event.setExtendedProp('type_id', data.extendedProps.type_id);
        event.setExtendedProp('type_name', data.extendedProps.type_name);
        event.setExtendedProp('location', data.extendedProps.location);
        event.setExtendedProp('description', data.extendedProps.description);
        event.setExtendedProp('reminder_minutes', data.extendedProps.reminder_minutes);
        event.setExtendedProp('documents', data.extendedProps.documents);
        event.setExtendedProp('attendees', data.extendedProps.attendees);
    }

    async deleteEvent() {
        if (!this.selectedEvent) {
            return;
        }
        const response = await fetch(this.config.deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: this.selectedEvent.id,
                csrf_token: this.config.csrfToken
            })
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al eliminar el evento');
        }
        this.selectedEvent.remove();
        this.selectedEvent = null;
        this.modal.hide();
    }

    async persistEvent(event, showModalOnError) {
        const payload = {
            id: event.id,
            title: event.title,
            type_id: event.extendedProps?.type_id || null,
            class_name: Array.isArray(event.classNames) ? event.classNames.join(' ') : event.classNames || '',
            start: event.startStr,
            end: event.endStr || null,
            all_day: event.allDay ? 1 : 0,
            location: event.extendedProps?.location || '',
            reminder_minutes: event.extendedProps?.reminder_minutes || null,
            description: event.extendedProps?.description || '',
            documents: (event.extendedProps?.documents || []).map(function (doc) { return doc.id; }),
            attendees: (event.extendedProps?.attendees || []).map(function (attendee) { return attendee.id; }),
            csrf_token: this.config.csrfToken
        };
        const response = await fetch(this.config.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            if (showModalOnError) {
                alert(result.message || 'No pudimos actualizar el evento.');
            }
            throw new Error(result.message || 'Error al actualizar el evento');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarElement = document.getElementById('calendar');
    if (calendarElement) {
        new CalendarSchedule().init();
    }
});
