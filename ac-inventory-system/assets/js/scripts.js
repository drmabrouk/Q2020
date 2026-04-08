jQuery(document).ready(function($) {
    // Multi-step Sales Flow Logic
    let cart = [];
    let currentStep = 1;
    let customerData = { is_quick: true };
    let salesMode = 'manual';

    function goToStep(step) {
        $('.ac-is-sale-step').hide();
        $(`#step-${step}`).fadeIn();
        $(`.step-item[data-step="${step}"]`).addClass('active').prevAll().addClass('complete');
        currentStep = step;

        if (step === 4) renderReview();
    }

    $('.ac-is-mode-select').on('click', function() {
        salesMode = $(this).data('mode');
        if (salesMode === 'scan') {
            $('#ac-is-reader-container').show();
            $('#ac-is-manual-search-box').hide();
            startScanner();
        } else {
            $('#ac-is-reader-container').hide();
            $('#ac-is-manual-search-box').show();
        }
        goToStep(2);
    });

    $('.ac-is-next-step').on('click', function() {
        const next = $(this).data('next');
        if (next == 3 && cart.length === 0) { alert('يرجى إضافة منتجات أولاً'); return; }
        goToStep(next);
    });

    $('#ac-is-quick-customer').on('click', function() {
        customerData = { is_quick: true, name: 'عميل سريع', phone: '-' };
        goToStep(4);
    });

    // Cart Logic
    function addProductToCart(product_id, serial = '', quantity = 1) {
        const option = $(`#ac-is-sale-product option[value="${product_id}"]`);
        if (!option.length) return false;

        cart.push({
            product_id: product_id,
            product_name: option.data('name'),
            quantity: quantity,
            serial_number: serial,
            unit_price: parseFloat(option.data('price')),
            total_price: (parseFloat(option.data('price')) * quantity).toFixed(2)
        });

        renderCart();
        return true;
    }

    $('#ac-is-add-to-list').on('click', function() {
        const pid = $('#ac-is-sale-product').val();
        if (!pid) return;
        addProductToCart(pid, $('#ac-is-sale-serial').val(), parseInt($('#ac-is-sale-qty').val()));
        $('#ac-is-sale-product').val('').trigger('change');
        $('#ac-is-sale-serial').val('');
        $('#ac-is-sale-qty').val(1);
    });

    function renderCart() {
        const body = $('#ac-is-cart-body');
        body.empty();
        let total = 0;
        cart.forEach((item, index) => {
            total += parseFloat(item.total_price);
            body.append(`<tr><td>${item.product_name}<br><small>${item.serial_number}</small></td><td>x${item.quantity}</td><td>${item.total_price}</td></tr>`);
        });
        $('#ac-is-cart-total').text(total.toFixed(2));
    }

    // Customer Lookup
    $('#ac-is-customer-phone').on('blur', function() {
        const phone = $(this).val();
        if (phone.length < 5) return;
        $.post(ac_is_ajax.ajax_url, { action: 'ac_is_get_customer', phone: phone, nonce: ac_is_ajax.nonce }, function(res) {
            if (res.success) {
                customerData = res.data;
                customerData.is_quick = false;
                $('#ac-is-customer-name').val(res.data.name);
                $('#ac-is-customer-address').val(res.data.address);
                $('#ac-is-customer-email').val(res.data.email);
            } else {
                customerData = { is_quick: false, phone: phone };
            }
            $('#customer-details-fields').slideDown();
        });
    });

    function renderReview() {
        // Build tables and info for Step 4
        $('#review-cart-table').html($('#ac-is-cart-body').html());
        $('#review-total').text($('#ac-is-cart-total').text());

        let custHtml = `<strong>العميل:</strong> ${$('#ac-is-customer-name').val() || customerData.name || '---'}<br>`;
        custHtml += `<strong>الهاتف:</strong> ${$('#ac-is-customer-phone').val() || customerData.phone}<br>`;
        $('#review-customer-info').html(custHtml);
    }

    $('#ac-is-finalize-sale').on('click', function() {
        const data = {
            action: 'ac_is_multi_sale',
            nonce: ac_is_ajax.nonce,
            items: cart,
            total_amount: $('#ac-is-cart-total').text(),
            branch_id: $('#ac-is-sale-branch').val(),
            customer_name: $('#ac-is-customer-name').val() || 'عميل سريع',
            customer_phone: $('#ac-is-customer-phone').val() || '-',
            customer_address: $('#ac-is-customer-address').val() || '',
            customer_email: $('#ac-is-customer-email').val() || '',
            send_email: 1
        };

        $.post(ac_is_ajax.ajax_url, data, function(res) {
            if (res.success) {
                window.location.href = window.location.href.split('&')[0] + '&ac_view=invoice&invoice_id=' + res.data.invoice_id + '&autoprint=1';
            }
        });
    });

    // Re-integrated Scanner (Html5Qrcode implementation)
    let html5QrCode;
    function startScanner() {
        if (!html5QrCode) html5QrCode = new Html5Qrcode("ac-is-reader");
        html5QrCode.start({ facingMode: "environment" }, { fps: 20, qrbox: 250 }, (text) => {
            if (addProductToCartByScan(text)) {
                $('.ac-is-scan-status').text('تم إضافة المنتج').css('color', 'green');
                if (window.navigator.vibrate) window.navigator.vibrate(100);
            }
        });
    }

    function addProductToCartByScan(query) {
        let found = false;
        $('#ac-is-sale-product option').each(function() {
            if ($(this).data('barcode') == query || $(this).data('serial') == query) {
                addProductToCart($(this).val(), $(this).data('serial'), 1);
                found = true; return false;
            }
        });
        return found;
    }

    // Reuse existing utility/logic...
    // [Including previous Fullscreen, Price Calc, and Auth logic below]

    // Real-time Sync & Visual Feedback
    const syncLoader = $('#ac-is-sync-loader');
    function showSync(text = 'جارٍ تحميل البيانات...') { syncLoader.find('.loader-text').text(text); syncLoader.fadeIn(200); }
    function hideSync() { syncLoader.find('.loader-text').text('تم التحديث بنجاح'); setTimeout(() => syncLoader.fadeOut(400), 1000); }

    $(document).ajaxStart(function() { if(currentStep !== 2) showSync(); });
    $(document).ajaxStop(function() { hideSync(); });

    $('#ac-is-refresh-btn').on('click', function() { location.reload(); });

    const systemRoot = document.getElementById('ac-is-system-root');
    const fullscreenBtn = $('#ac-is-fullscreen-btn');
    const unlockOverlay = $('#ac-is-unlock-overlay');
    const EXIT_PASSWORD = ac_is_ajax.fullscreen_password;

    fullscreenBtn.on('click', function() {
        if (!document.fullscreenElement) {
            if (systemRoot.requestFullscreen) systemRoot.requestFullscreen();
            else if (systemRoot.webkitRequestFullscreen) systemRoot.webkitRequestFullscreen();
        } else {
            unlockOverlay.css('display', 'flex').hide().fadeIn(300);
            $('#ac-is-unlock-pass').focus();
        }
    });

    $('#ac-is-unlock-submit').on('click', function() {
        if ($('#ac-is-unlock-pass').val() === EXIT_PASSWORD) {
            unlockOverlay.fadeOut(300, function() {
                if (document.exitFullscreen) document.exitFullscreen();
                $('#ac-is-unlock-pass').val('');
            });
        }
    });

    // Product Form calculations
    $('#original-price, #discount').on('input', function() {
        $('#final-price').val(($('#original-price').val() - $('#discount').val()).toFixed(2));
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

    $('#ac-is-logout-btn').on('click', function() {
        $.post(ac_is_ajax.ajax_url, { action: 'ac_is_logout', nonce: ac_is_ajax.nonce }, () => location.reload());
    });
});
