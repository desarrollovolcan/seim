<?php

class PaymentsController extends Controller
{
    public function buttons(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $invoices = $this->db->fetchAll(
            'SELECT invoices.id,
                    MAX(invoices.numero) as numero,
                    MAX(invoices.fecha_emision) as fecha_emision,
                    MAX(invoices.fecha_vencimiento) as fecha_vencimiento,
                    MAX(invoices.total) as total,
                    MAX(invoices.estado) as estado,
                    MAX(clients.name) as client_name,
                    COALESCE(SUM(payments.monto), 0) as paid_total,
                    MAX(invoices.total) - COALESCE(SUM(payments.monto), 0) as pending_total
             FROM invoices
             JOIN clients ON invoices.client_id = clients.id
             LEFT JOIN payments ON payments.invoice_id = invoices.id
             WHERE invoices.company_id = :company_id AND invoices.deleted_at IS NULL
             GROUP BY invoices.id
             HAVING pending_total > 0
             ORDER BY MAX(invoices.fecha_vencimiento) ASC',
            ['company_id' => $companyId]
        );

        $this->render('payments/buttons', [
            'title' => 'Botones de pago',
            'pageTitle' => 'Botones de pago',
            'invoices' => $invoices,
        ]);
    }

    public function paid(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $payments = $this->db->fetchAll(
            'SELECT payments.*, invoices.numero as invoice_number, invoices.total as invoice_total, clients.name as client_name
             FROM payments
             JOIN invoices ON payments.invoice_id = invoices.id
             JOIN clients ON invoices.client_id = clients.id
             WHERE invoices.company_id = :company_id
             ORDER BY payments.fecha_pago DESC, payments.id DESC',
            ['company_id' => $companyId]
        );

        $this->render('payments/paid', [
            'title' => 'Pagos realizados',
            'pageTitle' => 'Pagos realizados',
            'payments' => $payments,
        ]);
    }

    public function pending(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $pendingInvoices = $this->db->fetchAll(
            'SELECT invoices.id,
                    MAX(invoices.numero) as numero,
                    MAX(invoices.fecha_emision) as fecha_emision,
                    MAX(invoices.fecha_vencimiento) as fecha_vencimiento,
                    MAX(invoices.total) as total,
                    MAX(invoices.estado) as estado,
                    MAX(clients.name) as client_name,
                    COALESCE(SUM(payments.monto), 0) as paid_total,
                    MAX(invoices.total) - COALESCE(SUM(payments.monto), 0) as pending_total
             FROM invoices
             JOIN clients ON invoices.client_id = clients.id
             LEFT JOIN payments ON payments.invoice_id = invoices.id
             WHERE invoices.company_id = :company_id AND invoices.deleted_at IS NULL
             GROUP BY invoices.id
             HAVING pending_total > 0
             ORDER BY MAX(invoices.fecha_vencimiento) ASC',
            ['company_id' => $companyId]
        );

        $this->render('payments/pending', [
            'title' => 'Pagos pendientes',
            'pageTitle' => 'Pagos pendientes',
            'pendingInvoices' => $pendingInvoices,
        ]);
    }
}
