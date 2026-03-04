(function () {
    const STORAGE_RECEIPTS = 'pettyCashReceipts';

    function getReceipts() {
        return JSON.parse(localStorage.getItem(STORAGE_RECEIPTS) || '[]');
    }

    function formatItems(items) {
        return items.map((item) => `${item.productName} x${item.quantity}${item.observation ? ` (${item.observation})` : ''}`).join(' | ');
    }

    const table = $('#pettyCashTable').DataTable({
        data: [],
        columns: [
            { data: 'number' },
            { data: 'date' },
            { data: 'supplier' },
            { data: 'currency' },
            { data: 'itemCount' },
            { data: 'total' },
            { data: 'detail' }
        ],
        responsive: true,
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Listado_Caja_Chica',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ],
        language: {
            emptyTable: 'No hay registros de caja chica. Crea una boleta primero.'
        }
    });

    function refreshTable() {
        const rows = getReceipts().map((receipt) => ({
            number: receipt.number,
            date: receipt.date,
            supplier: receipt.supplier,
            currency: receipt.currency,
            itemCount: receipt.items.length,
            total: Number(receipt.total || 0).toFixed(2),
            detail: formatItems(receipt.items)
        }));

        table.clear().rows.add(rows).draw();
    }

    $.fn.dataTable.ext.search.push(function (_settings, data) {
        const fromDate = document.getElementById('filterFromDate').value;
        const toDate = document.getElementById('filterToDate').value;
        const supplier = document.getElementById('filterSupplier').value.trim().toLowerCase();

        const rowDate = data[1];
        const rowSupplier = (data[2] || '').toLowerCase();

        if (supplier && !rowSupplier.includes(supplier)) {
            return false;
        }

        if (fromDate && rowDate < fromDate) {
            return false;
        }

        if (toDate && rowDate > toDate) {
            return false;
        }

        return true;
    });

    ['filterFromDate', 'filterToDate', 'filterSupplier'].forEach((id) => {
        document.getElementById(id).addEventListener('input', function () {
            table.draw();
        });
    });

    document.getElementById('clearFiltersBtn').addEventListener('click', function () {
        document.getElementById('filterFromDate').value = '';
        document.getElementById('filterToDate').value = '';
        document.getElementById('filterSupplier').value = '';
        table.draw();
    });

    refreshTable();
})();
