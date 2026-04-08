jQuery(document).ready(function($) {
    // Real-time Sync & Visual Feedback
    const syncLoader = $('#ac-is-sync-loader');

    function showSync(text = 'جارٍ تحميل البيانات...') {
        syncLoader.find('.loader-text').text(text);
        syncLoader.fadeIn(200);
    }

    function hideSync(success = true) {
        if (success) {
            syncLoader.find('.loader-text').text('تم التحديث بنجاح');
            setTimeout(() => syncLoader.fadeOut(400), 1000);
        } else {
            syncLoader.fadeOut(200);
        }
    }

    $(document).ajaxStart(function() { showSync(); });
    $(document).ajaxStop(function() { hideSync(); });

    $('#ac-is-refresh-btn').on('click', function() {
        location.reload();
    });

    // Multi-Product Cart Logic
    let cart = [];

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

    $('#ac-is-add-to-list').on('click', function() {
        const product_id = $('#ac-is-sale-product').val();
        if (!product_id) { alert('يرجى اختيار منتج'); return; }

        const serial = $('#ac-is-sale-serial').val();
        const quantity = parseInt($('#ac-is-sale-qty').val());

        if (addProductToCart(product_id, serial, quantity)) {
            // Reset product selection
            $('#ac-is-sale-product').val('').trigger('change');
            $('#ac-is-sale-serial').val('');
            $('#ac-is-sale-qty').val(1);
            $('#ac-is-sale-product-search').val('');
        }
    });

    function renderCart() {
        const body = $('#ac-is-cart-body');
        body.empty();
        let total = 0;

        if (cart.length === 0) {
            body.append('<tr class="empty-cart"><td colspan="4" style="text-align:center;">لم يتم اختيار منتجات</td></tr>');
        } else {
            cart.forEach((item, index) => {
                total += parseFloat(item.total_price);
                body.append(`
                    <tr>
                        <td><strong>${item.product_name}</strong><br><small>${item.serial_number || ''}</small></td>
                        <td>${item.quantity}</td>
                        <td>${item.total_price}</td>
                        <td><button type="button" class="ac-is-remove-item" data-index="${index}" style="background:none; border:none; color:red; cursor:pointer;"><span class="dashicons dashicons-no-alt"></span></button></td>
                    </tr>
                `);
            });
        }
        $('#ac-is-cart-total').text(total.toFixed(2));
    }

    $(document).on('click', '.ac-is-remove-item', function() {
        const index = $(this).data('index');
        cart.splice(index, 1);
        renderCart();
    });

    // Customer Lookup Logic
    $('#ac-is-customer-phone').on('blur', function() {
        const phone = $(this).val();
        if (phone.length < 5) return;

        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_get_customer',
            phone: phone,
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) {
                const c = response.data;
                $('#ac-is-customer-name').val(c.name);
                $('#ac-is-customer-address').val(c.address);
                $('#ac-is-customer-email').val(c.email);
                $('#customer-details-fields').slideDown();
                $('#ac-is-customer-phone').css('border-color', '#059669');
            } else {
                $('#ac-is-customer-phone').css('border-color', '#cbd5e1');
            }
        });
    });

    // Submit Multi-Product Sale
    $('#ac-is-multi-sale-form').on('submit', function(e) {
        e.preventDefault();
        if (cart.length === 0) { alert('يرجى إضافة منتجات أولاً'); return; }

        const formData = $(this).serializeArray();
        const data = {
            action: 'ac_is_multi_sale',
            nonce: ac_is_ajax.nonce,
            items: cart,
            total_amount: $('#ac-is-cart-total').text()
        };

        formData.forEach(item => data[item.name] = item.value);

        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('تم تسجيل الفاتورة بنجاح');
                window.location.href = window.location.href.split('&')[0] + '&ac_view=invoice&invoice_id=' + response.data.invoice_id + '&autoprint=1';
            } else {
                alert('فشل تسجيل العملية: ' + response.data);
            }
        });
    });

    // Camera Scanning Logic (Professional Implementation)
    let html5QrCode;
    let lastScannedCode = "";
    let lastScannedTime = 0;

    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("ac-is-reader");
        }

        const config = {
            fps: 30,
            qrbox: { width: 280, height: 160 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error("Unable to start scanner", err);
            $('.ac-is-scan-status').text("خطأ في تشغيل الكاميرا").css('color', 'red').show();
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        const now = Date.now();
        // Debounce logic: 3 seconds for same code
        if (decodedText === lastScannedCode && (now - lastScannedTime) < 3000) {
            return;
        }

        lastScannedCode = decodedText;
        lastScannedTime = now;

        // Visual feedback
        $('#ac-is-reader-container').addClass('scan-success-flash');
        setTimeout(() => $('#ac-is-reader-container').removeClass('scan-success-flash'), 500);

        if (window.navigator && window.navigator.vibrate) {
            window.navigator.vibrate(100);
        }

        processScannedBarcode(decodedText);
    }

    function onScanError(errorMessage) {
        // Just silent console error for continuous scanning
        // console.warn(`Scan error: ${errorMessage}`);
    }

    function processScannedBarcode(query) {
        let found = false;
        $('#ac-is-sale-product option').each(function() {
            const barcode = String($(this).data('barcode'));
            const serial = String($(this).data('serial'));

            if (barcode == query || serial == query) {
                const product_id = $(this).val();
                if (addProductToCart(product_id, serial || barcode, 1)) {
                    $('.ac-is-scan-status').text('تم إضافة: ' + $(this).data('name')).fadeIn().delay(2000).fadeOut();
                    found = true;
                }
                return false;
            }
        });

        if (!found) {
            $.post(ac_is_ajax.ajax_url, {
                action: 'ac_is_search_products',
                search: query,
                nonce: ac_is_ajax.nonce
            }, function(response) {
                if (response.success && response.data.length > 0) {
                    const p = response.data[0];
                    if (addProductToCart(p.id, p.serial_number || p.barcode, 1)) {
                        $('.ac-is-scan-status').text('تم إضافة: ' + p.name).fadeIn().delay(2000).fadeOut();
                    }
                } else {
                    $('.ac-is-scan-status').text('المنتج غير موجود: ' + query).css('color', 'red').fadeIn().delay(2000).fadeOut();
                }
            });
        }
    }

    $('.ac-is-mode-btn').on('click', function() {
        const mode = $(this).data('mode');
        $('.ac-is-mode-btn').removeClass('active').css({'background':'#fff', 'color':'inherit', 'border-color':'#ddd'});
        $(this).addClass('active').css({'background': 'var(--ac-primary)', 'color': '#fff', 'border-color': 'var(--ac-primary)'});

        if (mode === 'scan') {
            $('#ac-is-reader-container').show();
            $('#ac-is-manual-search').hide();
            startScanner();
        } else {
            $('#ac-is-reader-container').hide();
            $('#ac-is-manual-search').show();
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.error(err));
            }
        }
    });

    // Utility: Generate Barcode
    function generateBarcode(id, value) {
        if (!value) return;
        JsBarcode(id, value, {
            format: "CODE128",
            width: 2,
            height: 40,
            displayValue: true
        });
    }

    // Fullscreen Logic
    const systemRoot = document.getElementById('ac-is-system-root');
    const fullscreenBtn = $('#ac-is-fullscreen-btn');
    const unlockOverlay = $('#ac-is-unlock-overlay');
    const EXIT_PASSWORD = ac_is_ajax.fullscreen_password;

    fullscreenBtn.on('click', function() {
        if (!document.fullscreenElement) {
            enterFullscreen();
        } else {
            showUnlockOverlay();
        }
    });

    function enterFullscreen() {
        if (systemRoot.requestFullscreen) {
            systemRoot.requestFullscreen();
        } else if (systemRoot.webkitRequestFullscreen) {
            systemRoot.webkitRequestFullscreen();
        } else if (systemRoot.msRequestFullscreen) {
            systemRoot.msRequestFullscreen();
        }
    }

    function exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    function showUnlockOverlay() {
        unlockOverlay.css('display', 'flex').hide().fadeIn(300);
        $('#ac-is-unlock-pass').focus();
    }

    $('#ac-is-unlock-submit').on('click', function() {
        const pass = $('#ac-is-unlock-pass').val();
        if (pass === EXIT_PASSWORD) {
            unlockOverlay.fadeOut(300, function() {
                exitFullscreen();
                $('#ac-is-unlock-pass').val('');
                $('#ac-is-unlock-error').hide();
            });
        } else {
            $('#ac-is-unlock-error').shake().show();
        }
    });

    $('#ac-is-unlock-pass').on('keypress', function(e) {
        if (e.which == 13) $('#ac-is-unlock-submit').click();
    });

    // Strict Fullscreen Security
    $(document).on('keydown', function(e) {
        if (document.fullscreenElement) {
            if (e.key === "Escape" || e.keyCode === 27) {
                e.preventDefault();
                showUnlockOverlay();
                return false;
            }
        }
    });

    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            if (unlockOverlay.is(':hidden') && fullscreenBtn.data('active') === 'true') {
                enterFullscreen();
                showUnlockOverlay();
            } else if (unlockOverlay.is(':hidden')) {
                fullscreenBtn.find('.btn-text').text('ملء الشاشة');
                fullscreenBtn.find('.dashicons').removeClass('dashicons-screenoptions').addClass('dashicons-fullscreen-alt');
                fullscreenBtn.data('active', 'false');
            }
        } else {
            fullscreenBtn.find('.btn-text').text('إنهاء وضع ملء الشاشة');
            fullscreenBtn.find('.dashicons').removeClass('dashicons-fullscreen-alt').addClass('dashicons-screenoptions');
            fullscreenBtn.data('active', 'true');
        }
    });

    $.fn.shake = function() {
        this.each(function(i) {
            $(this).css({ "position": "relative" });
            for (var x = 1; x <= 3; x++) {
                $(this).animate({ left: -10 }, 50).animate({ left: 10 }, 50).animate({ left: 0 }, 50);
            }
        });
        return this;
    }

    // Barcode Preview in Product Form
    $('#ac-is-barcode-input').on('input', function() {
        generateBarcode("#barcode-svg", $(this).val());
        if ($(this).val()) $('#barcode-preview').show();
    });

    if ($('#ac-is-barcode-input').val()) {
        generateBarcode("#barcode-svg", $('#ac-is-barcode-input').val());
        $('#barcode-preview').show();
    }

    $('#generate-barcode').on('click', function() {
        var randomBarcode = 'AC-' + Math.floor(Math.random() * 100000000);
        $('#ac-is-barcode-input').val(randomBarcode).trigger('input');
    });

    // Auto calculate final price
    $('#original-price, #discount').on('input', function() {
        var original = parseFloat($('#original-price').val()) || 0;
        var discount = parseFloat($('#discount').val()) || 0;
        $('#final-price').val((original - discount).toFixed(2));
    });

    // Save branch
    $('#ac-is-branch-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=ac_is_save_branch&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('تم حفظ الفرع');
                location.reload();
            }
        });
    });

    // Save product
    $('#ac-is-product-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=ac_is_save_product&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('تم الحفظ بنجاح');
                window.location.href = window.location.href.split('&')[0] + '&ac_view=inventory';
            } else {
                alert('حدث خطأ');
            }
        });
    });

    // Delete product
    $(document).on('click', '.ac-is-delete-product', function(e) {
        e.preventDefault();
        if (!confirm('هل أنت متأكد من الحذف؟')) return;
        var id = $(this).data('id');
        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_delete_product',
            id: id,
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });

    // Print Barcode Button (Sticker Only)
    $(document).on('click', '.ac-is-print-barcode', function(e) {
        e.preventDefault();
        var barcode = $(this).data('barcode');
        var name = $(this).data('name');
        var serial = $(this).data('serial');
        if (!barcode) { alert('لا يوجد باركود لهذا المنتج'); return; }

        $('#print-product-name').text(name + (serial ? ' (' + serial + ')' : ''));
        generateBarcode("#print-barcode-svg", barcode);

        $('body').addClass('ac-is-printing-sticker');
        window.print();

        setTimeout(function() {
            $('body').removeClass('ac-is-printing-sticker');
        }, 1000);
    });

    // Inventory Real-time Search & Filter (Improved Visual Feedback)
    var searchTimeout;
    $('#ac-is-inventory-search, #ac-is-inventory-category').on('input change', function() {
        clearTimeout(searchTimeout);
        $('#ac-is-inventory-table-body').css('opacity', '0.5');
        searchTimeout = setTimeout(function() {
            var search = $('#ac-is-inventory-search').val();
            var category = $('#ac-is-inventory-category').val();

            $.post(ac_is_ajax.ajax_url, {
                action: 'ac_is_search_products',
                search: search,
                category: category,
                nonce: ac_is_ajax.nonce
            }, function(response) {
                $('#ac-is-inventory-table-body').css('opacity', '1');
                if (response.success) {
                    renderInventory(response.data);
                }
            });
        }, 300);
    });

    function renderInventory(products) {
        var html = '';
        if (products.length === 0) {
            html = '<tr><td colspan="7" style="text-align:center; padding:40px;">لا توجد منتجات.</td></tr>';
        } else {
            $.each(products, function(i, p) {
                var stockClass = (p.stock_quantity < 5) ? 'capsule-danger' : ((p.stock_quantity < 15) ? 'capsule-warning' : 'capsule-success');
                var categoryName = (p.category == 'ac') ? 'مكيفات' : ((p.category == 'cooling') ? 'تبريد' : 'فلاتر');
                html += `<tr>
                    <td>${p.image_url ? '<img src="' + p.image_url + '" class="ac-is-product-img" style="border-radius:4px; max-width:50px;">' : ''}</td>
                    <td><strong>${p.name}</strong><br><span class="ac-is-capsule capsule-primary">${categoryName}</span> ${p.subcategory ? '<small style="color:#666;"> (' + p.subcategory + ')</small>' : ''}</td>
                    <td><small>B: ${p.barcode || 'N/A'}</small><br><small>S/N: ${p.serial_number || 'N/A'}</small></td>
                    <td><span style="font-weight:bold; color:var(--ac-primary);">${parseFloat(p.final_price).toFixed(2)} EGP</span>${p.discount > 0 ? '<br><del style="font-size:0.8rem; color:#999;">' + parseFloat(p.original_price).toFixed(2) + '</del> <span class="ac-is-capsule capsule-danger" style="font-size:0.7rem; padding: 1px 5px;">' + parseFloat(p.discount).toFixed(2) + '-</span>' : ''}</td>
                    <td><span class="ac-is-capsule ${stockClass}">${p.stock_quantity}</span></td>
                    <td>${p.branch_id}</td>
                    <td><div style="display:flex; gap:5px;">
                        <a href="?ac_view=edit-product&id=${p.id}" class="ac-is-btn" style="padding: 4px 8px; font-size:0.75rem; background:#3b82f6;">تعديل</a>
                        <button class="ac-is-btn ac-is-print-barcode" data-barcode="${p.barcode}" data-name="${p.name}" data-serial="${p.serial_number}" style="padding: 4px 8px; font-size:0.75rem; background:#64748b;">باركود</button>
                        <button class="ac-is-delete-product ac-is-btn" data-id="${p.id}" style="padding: 4px 8px; font-size:0.75rem; background:#ef4444;">حذف</button>
                    </div></td>
                </tr>`;
            });
        }
        $('#ac-is-inventory-table-body').html(html);
    }

    // Independent Logout
    $('#ac-is-logout-btn').on('click', function(e) {
        e.preventDefault();
        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_logout',
            nonce: ac_is_ajax.nonce
        }, function() {
            location.reload();
        });
    });
});
