(function () {
    const STORAGE_PRODUCTS = 'pettyCashProducts';
    const STORAGE_RECEIPTS = 'pettyCashReceipts';

    const defaultProducts = [
        { id: 'P001', name: 'Papel A4', category: 'Librería', suggestedPrice: 18.50 },
        { id: 'P002', name: 'Lapicero azul', category: 'Librería', suggestedPrice: 2.20 },
        { id: 'P003', name: 'Alcohol 1L', category: 'Limpieza', suggestedPrice: 14.00 }
    ];

    const form = document.getElementById('pettyCashForm');
    const itemsBody = document.getElementById('receiptItemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const totalInput = document.getElementById('receiptTotal');
    const clearFormBtn = document.getElementById('clearFormBtn');
    const quickProductForm = document.getElementById('quickProductForm');

    function getProducts() {
        const products = JSON.parse(localStorage.getItem(STORAGE_PRODUCTS) || 'null');
        if (!products || !products.length) {
            localStorage.setItem(STORAGE_PRODUCTS, JSON.stringify(defaultProducts));
            return defaultProducts;
        }
        return products;
    }

    function setProducts(products) {
        localStorage.setItem(STORAGE_PRODUCTS, JSON.stringify(products));
    }

    function getReceipts() {
        return JSON.parse(localStorage.getItem(STORAGE_RECEIPTS) || '[]');
    }

    function setReceipts(receipts) {
        localStorage.setItem(STORAGE_RECEIPTS, JSON.stringify(receipts));
    }

    function buildProductOptions(selected = '') {
        const products = getProducts();
        const options = products.map((p) => {
            const selectedAttr = selected === p.id ? 'selected' : '';
            return `<option value="${p.id}" data-price="${p.suggestedPrice}" ${selectedAttr}>${p.name} (${p.category || 'General'})</option>`;
        });
        return `<option value="" disabled ${selected ? '' : 'selected'}>Selecciona producto...</option>${options.join('')}`;
    }

    function updateTotals() {
        let total = 0;
        itemsBody.querySelectorAll('tr').forEach((row) => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const subtotal = qty * price;
            row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
            total += subtotal;
        });
        totalInput.value = total.toFixed(2);
    }

    function addRow(data = {}) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><select class="form-select item-product" required>${buildProductOptions(data.productId || '')}</select></td>
            <td><input type="number" class="form-control item-qty" min="1" step="1" value="${data.quantity || 1}" required></td>
            <td><input type="number" class="form-control item-price" min="0" step="0.01" value="${data.unitPrice || 0}" required></td>
            <td><input type="text" class="form-control item-subtotal" value="0.00" readonly></td>
            <td><input type="text" class="form-control item-observation" placeholder="Observación por producto" value="${data.observation || ''}"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="ti ti-trash"></i></button></td>
        `;
        itemsBody.appendChild(row);

        const productSelect = row.querySelector('.item-product');
        const priceInput = row.querySelector('.item-price');

        productSelect.addEventListener('change', () => {
            const selected = productSelect.options[productSelect.selectedIndex];
            const suggestedPrice = parseFloat(selected.dataset.price || '0');
            if (!priceInput.value || parseFloat(priceInput.value) === 0) {
                priceInput.value = suggestedPrice.toFixed(2);
            }
            updateTotals();
        });

        row.querySelector('.item-qty').addEventListener('input', updateTotals);
        row.querySelector('.item-price').addEventListener('input', updateTotals);
        row.querySelector('.remove-row').addEventListener('click', () => {
            row.remove();
            updateTotals();
        });

        if (data.productId) {
            const selected = productSelect.options[productSelect.selectedIndex];
            if (selected && (!data.unitPrice || data.unitPrice === 0)) {
                priceInput.value = parseFloat(selected.dataset.price || '0').toFixed(2);
            }
        }

        updateTotals();
    }

    function refreshAllProductSelectors() {
        const currentSelections = Array.from(document.querySelectorAll('.item-product')).map((select) => select.value);
        document.querySelectorAll('.item-product').forEach((select, index) => {
            select.innerHTML = buildProductOptions(currentSelections[index]);
        });
    }

    addRowBtn.addEventListener('click', () => addRow());

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const rows = Array.from(itemsBody.querySelectorAll('tr'));
        if (!rows.length) {
            alert('Debes agregar al menos un producto.');
            return;
        }

        const items = rows.map((row) => {
            const productId = row.querySelector('.item-product').value;
            const product = getProducts().find((p) => p.id === productId);
            const quantity = parseFloat(row.querySelector('.item-qty').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-price').value) || 0;
            return {
                productId,
                productName: product ? product.name : 'N/A',
                quantity,
                unitPrice,
                subtotal: parseFloat((quantity * unitPrice).toFixed(2)),
                observation: row.querySelector('.item-observation').value.trim()
            };
        });

        const receipt = {
            id: Date.now(),
            number: document.getElementById('receiptNumber').value.trim(),
            date: document.getElementById('receiptDate').value,
            supplier: document.getElementById('receiptSupplier').value.trim(),
            currency: document.getElementById('receiptCurrency').value,
            total: parseFloat(totalInput.value || '0'),
            items,
            createdAt: new Date().toISOString()
        };

        const receipts = getReceipts();
        receipts.push(receipt);
        setReceipts(receipts);

        form.reset();
        itemsBody.innerHTML = '';
        addRow();
        alert('Boleta guardada correctamente. Revisa el listado para filtrar y exportar.');
    });

    clearFormBtn.addEventListener('click', () => {
        setTimeout(() => {
            itemsBody.innerHTML = '';
            addRow();
        }, 0);
    });

    quickProductForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const name = document.getElementById('quickProductName').value.trim();
        const category = document.getElementById('quickProductCategory').value.trim();
        const suggestedPrice = parseFloat(document.getElementById('quickProductPrice').value || '0');

        const products = getProducts();
        const newProduct = {
            id: `P${String(Date.now()).slice(-6)}`,
            name,
            category,
            suggestedPrice: parseFloat(suggestedPrice.toFixed(2))
        };

        products.push(newProduct);
        setProducts(products);
        refreshAllProductSelectors();
        quickProductForm.reset();

        const modalElement = document.getElementById('productModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    });

    document.getElementById('receiptDate').valueAsDate = new Date();
    getProducts();
    addRow();
})();
