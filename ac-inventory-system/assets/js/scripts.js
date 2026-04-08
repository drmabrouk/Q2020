jQuery(document).ready(function($) {
    // Shared State
    let cart = [];
    let salesMode = 'manual';
    let customerData = { is_quick: true };
    let html5QrCode;

    // --- Core Operations ---

    function addProductToCart(product_id, serial = '', quantity = 1) {
        const option = $(`#ac-is-sale-product option[value="${product_id}"]`);
        if (!option.length) return false;

        const product_name = option.data('name');
        const price = parseFloat(option.data('price'));
        const stock = parseInt(option.data('stock'));

        if (quantity > stock) {
            alert('الكمية المطلوبة أكبر من المتوفر');
            return false;
        }

        cart.push({
            product_id: product_id,
            product_name: product_name,
            quantity: quantity,
            serial_number: serial,
            unit_price: price,
            total_price: (price * quantity).toFixed(2)
        });

        renderCart();
        return true;
    }

    function renderCart() {
        const body = $('#ac-is-cart-body');
        body.empty();
        let total = 0;

        if (cart.length === 0) {
            body.append('<tr class="empty-cart"><td colspan="3" style="text-align:center; padding:40px; color:#94a3b8;">سلة المشتريات فارغة</td></tr>');
        } else {
            cart.forEach((item, index) => {
                total += parseFloat(item.total_price);
                body.append(`
                    <tr>
                        <td style="padding:12px;"><strong>${item.product_name}</strong><br><small>SN: ${item.serial_number || '-'}</small></td>
                        <td style="padding:12px;">x${item.quantity}</td>
                        <td style="padding:12px; text-align:left;">${item.total_price} EGP <button type="button" class="ac-is-remove-item" data-index="${index}" style="margin-right:10px; background:none; border:none; color:red; cursor:pointer;"><span class="dashicons dashicons-no-alt"></span></button></td>
                    </tr>
                `);
            });
        }
        $('#ac-is-cart-total').text(total.toFixed(2));
    }

    $(document).on('click', '.ac-is-remove-item', function() {
        cart.splice($(this).data('index'), 1);
        renderCart();
    });

    $('#ac-is-add-to-list').on('click', function() {
        const pid = $('#ac-is-sale-product').val();
        if (!pid) return;
        if (addProductToCart(pid, $('#ac-is-sale-serial').val(), parseInt($('#ac-is-sale-qty').val()))) {
            $('#ac-is-sale-product').val('').trigger('change');
            $('#ac-is-sale-serial').val('');
            $('#ac-is-sale-qty').val(1);
            $('#ac-is-sale-product-search').val('');
        }
    });

    // --- Mode Selection & Scanning ---

    $('.ac-is-mode-box').on('click', function() {
        salesMode = $(this).data('mode');
        $('.ac-is-mode-box').removeClass('active');
        $(this).addClass('active');

        if (salesMode === 'scan') {
            $('#ac-is-reader-container').show();
            $('#ac-is-manual-entry-area').hide();
            startScanner();
        } else {
            $('#ac-is-reader-container').hide();
            $('#ac-is-manual-entry-area').show();
            if (html5QrCode) html5QrCode.stop().catch(err => console.error(err));
        }
    });

    function startScanner() {
        if (!html5QrCode) html5QrCode = new Html5Qrcode("ac-is-reader");
        html5QrCode.start({ facingMode: "environment" }, { fps: 30, qrbox: { width: 250, height: 150 } }, (text) => {
            if (processScannedBarcode(text)) {
                showScanConfirmation();
                if (window.navigator.vibrate) window.navigator.vibrate(100);
            }
        });
    }

    function processScannedBarcode(query) {
        let found = false;
        $('#ac-is-sale-product option').each(function() {
            if ($(this).data('barcode') == query || $(this).data('serial') == query) {
                addProductToCart($(this).val(), $(this).data('serial'), 1);
                found = true; return false;
            }
        });
        return found;
    }

    function showScanConfirmation() {
        const overlay = $('#ac-is-scan-conf-overlay');
        overlay.css('display', 'flex').hide().fadeIn(200);
        setTimeout(() => overlay.fadeOut(300), 1200);
    }

    // --- Real-time Customer Recognition ---

    let customerTimeout;
    $('#ac-is-customer-phone').on('input', function() {
        clearTimeout(customerTimeout);
        const phone = $(this).val().trim();
        if (phone.length < 5) {
            $('#customer-details-fields').slideUp();
            return;
        }

        customerTimeout = setTimeout(() => {
            $.post(ac_is_ajax.ajax_url, {
                action: 'ac_is_get_customer',
                phone: phone,
                nonce: ac_is_ajax.nonce
            }, function(res) {
                if (res.success) {
                    const c = res.data;
                    $('#ac-is-customer-name').val(c.name);
                    $('#ac-is-customer-address').val(c.address);
                    $('#ac-is-customer-email').val(c.email);
                    customerData = c;
                    customerData.is_quick = false;
                    $('#ac-is-customer-phone').css('border-color', '#059669');
                } else {
                    $('#ac-is-customer-name').val('');
                    $('#ac-is-customer-address').val('');
                    $('#ac-is-customer-email').val('');
                    customerData = { is_quick: false, phone: phone };
                    $('#ac-is-customer-phone').css('border-color', 'var(--ac-primary)');
                }
                $('#customer-details-fields').slideDown();
            });
        }, 400);
    });

    // --- Finalize Sale ---

    $('#ac-is-finalize-sale').on('click', function() {
        if (cart.length === 0) { alert('يرجى إضافة منتجات أولاً'); return; }

        const data = {
            action: 'ac_is_multi_sale',
            nonce: ac_is_ajax.nonce,
            items: cart,
            total_amount: $('#ac-is-cart-total').text(),
            customer_name: $('#ac-is-customer-name').val() || 'عميل سريع',
            customer_phone: $('#ac-is-customer-phone').val() || '-',
            customer_address: $('#ac-is-customer-address').val() || '',
            customer_email: $('#ac-is-customer-email').val() || '',
            send_email: $('#ac-is-send-email').is(':checked') ? 1 : 0
        };

        $.post(ac_is_ajax.ajax_url, data, function(res) {
            if (res.success) {
                window.location.href = window.location.href.split('&')[0] + '&ac_view=invoice&invoice_id=' + res.data.invoice_id + '&autoprint=1';
            }
        });
    });

    // --- Infrastructure & Other Logic ---

    const syncLoader = $('#ac-is-sync-loader');
    function showSync(text = 'جارٍ تحميل البيانات...') { syncLoader.find('.loader-text').text(text); syncLoader.fadeIn(200); }
    function hideSync() { syncLoader.find('.loader-text').text('تم التحديث بنجاح'); setTimeout(() => syncLoader.fadeOut(400), 1000); }

    $(document).ajaxStart(function() { showSync(); });
    $(document).ajaxStop(function() { hideSync(); });

    $('#ac-is-refresh-btn').on('click', function() {
        showSync('جاري مسح التخزين المؤقت وتحديث البيانات...');
        if (window.sessionStorage) window.sessionStorage.clear();
        setTimeout(() => { window.location.reload(true); }, 500);
    });

    const systemRoot = document.getElementById('ac-is-system-root');
    const EXIT_PASSWORD = ac_is_ajax.fullscreen_password;

    $('#ac-is-fullscreen-btn').on('click', function() {
        if (!document.fullscreenElement) {
            if (systemRoot.requestFullscreen) systemRoot.requestFullscreen();
            else if (systemRoot.webkitRequestFullscreen) systemRoot.webkitRequestFullscreen();
        } else {
            $('#ac-is-unlock-overlay').css('display', 'flex').hide().fadeIn(300);
            $('#ac-is-unlock-pass').focus();
        }
    });

    $('#ac-is-unlock-submit').on('click', function() {
        if ($('#ac-is-unlock-pass').val() === EXIT_PASSWORD) {
            $('#ac-is-unlock-overlay').fadeOut(300, function() {
                if (document.exitFullscreen) document.exitFullscreen();
                $('#ac-is-unlock-pass').val('');
            });
        }
    });

    // Product calculations (Enhanced)
    $('#original-price, #discount').on('input', function() {
        const original = parseFloat($('#original-price').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        $('#final-price').val((original - (original * (discount/100))).toFixed(2));
    });

    // Barcode Sticker Printing (Professional)
    $(document).on('click', '.ac-is-print-barcode', function(e) {
        e.preventDefault();
        const barcode = $(this).data('barcode');
        const name = $(this).data('name');
        const serial = $(this).data('serial');
        if (!barcode) { alert('لا يوجد باركود لهذا المنتج'); return; }

        $('#print-product-name').text(name + (serial ? ' (' + serial + ')' : ''));
        generateBarcode("#print-barcode-svg", barcode);

        $('body').addClass('ac-is-printing-sticker');
        window.print();
        setTimeout(() => $('body').removeClass('ac-is-printing-sticker'), 1000);
    });

    // Product Barcode Generation (Enhanced)
    function generateBarcodeImage(value) {
        if (!value) return;

        $('#barcode-canvas-container').empty().append('<canvas id="barcode-canvas"></canvas>');
        JsBarcode("#barcode-canvas", value, {
            format: "CODE128",
            width: 3,
            height: 100,
            displayValue: true,
            fontSize: 20
        });
        $('#barcode-image-preview').fadeIn();
    }

    $('#ac-is-barcode-input').on('input', function() {
        generateBarcodeImage($(this).val());
    });

    if ($('#ac-is-barcode-input').val()) {
        generateBarcodeImage($('#ac-is-barcode-input').val());
    }

    $('#generate-barcode').on('click', function() {
        const randomBarcode = 'AC-' + Math.floor(Math.random() * 100000000);
        $('#ac-is-barcode-input').val(randomBarcode).trigger('input');
        if (!$('#ac-is-serial-input').val()) {
            $('#ac-is-serial-input').val(randomBarcode);
        }
    });

    // Product Save
    $('#ac-is-product-form').on('submit', function(e) {
        e.preventDefault();
        $.post(ac_is_ajax.ajax_url, $(this).serialize() + '&action=ac_is_save_product&nonce=' + ac_is_ajax.nonce, function(res) {
            if (res.success) window.location.href = '?ac_view=inventory';
        });
    });

    $(document).on('click', '.ac-is-delete-product', function(e) {
        if (!confirm('حذف؟')) return;
        $.post(ac_is_ajax.ajax_url, { action: 'ac_is_delete_product', id: $(this).data('id'), nonce: ac_is_ajax.nonce }, () => location.reload());
    });

    // Image Upload Handler
    $('.ac-is-upload-btn').on('click', function(e) {
        e.preventDefault();
        const frame = wp.media({ title: 'اختر صورة', multiple: false }).open();
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            const target = e.target.previousElementSibling;
            if (target && target.tagName === 'INPUT') {
                $(target).val(attachment.url);
            } else {
                $('#brand-logo-url').val(attachment.url);
            }
        });
    });

    // Logout
    $('#ac-is-logout-btn').on('click', function() {
        $.post(ac_is_ajax.ajax_url, { action: 'ac_is_logout', nonce: ac_is_ajax.nonce }, () => location.reload());
    });

    // Product Search
    $('#ac-is-sale-product-search').on('input', function() {
        const query = $(this).val().toLowerCase();
        if (query.length < 2) return;
        $('#ac-is-sale-product option').each(function() {
            const barcode = String($(this).data('barcode')).toLowerCase();
            const name = $(this).text().toLowerCase();
            if (barcode == query || name.includes(query)) {
                $('#ac-is-sale-product').val($(this).val()).trigger('change');
                return false;
            }
        });
    });

    if (window.location.search.indexOf('autoprint=1') > -1) {
        setTimeout(function() { window.print(); }, 1000);
    }
});
