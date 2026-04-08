jQuery(document).ready(function($) {
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
    const EXIT_PASSWORD = '123456789';

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

    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            if (unlockOverlay.is(':hidden')) {
                enterFullscreen();
                showUnlockOverlay();
            }
            fullscreenBtn.find('.btn-text').text('ملء الشاشة');
            fullscreenBtn.find('.dashicons').removeClass('dashicons-screenoptions').addClass('dashicons-fullscreen-alt');
        } else {
            fullscreenBtn.find('.btn-text').text('إنهاء وضع ملء الشاشة');
            fullscreenBtn.find('.dashicons').removeClass('dashicons-fullscreen-alt').addClass('dashicons-screenoptions');
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

        // Remove class after print dialog is closed
        setTimeout(function() {
            $('body').removeClass('ac-is-printing-sticker');
        }, 1000);
    });

    // Inventory Real-time Search & Filter (Improved Visual Feedback)
    var searchTimeout;
    $('#ac-is-inventory-search, #ac-is-inventory-category').on('input change', function() {
        clearTimeout(searchTimeout);
        $('#ac-is-inventory-table-body').css('opacity', '0.5'); // Visual feedback
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
        }, 300); // Faster search
    });

    function renderInventory(products) {
        var html = '';
        if (products.length === 0) {
            html = '<tr><td colspan="7" style="text-align:center; padding:40px;">لا توجد منتجات.</td></tr>';
        } else {
            $.each(products, function(i, p) {
                var stockClass = (p.stock_quantity < 5) ? 'capsule-danger' : ((p.stock_quantity < 15) ? 'capsule-warning' : 'capsule-success');
                var categoryName = (p.category == 'ac') ? 'مكيفات' : ((p.category == 'cooling') ? 'تبريد' : 'فلاتر');
                html += '<tr>' +
                    '<td>' + (p.image_url ? '<img src="' + p.image_url + '" class="ac-is-product-img" style="border-radius:4px; max-width:50px;">' : '') + '</td>' +
                    '<td><strong>' + p.name + '</strong><br><span class="ac-is-capsule capsule-primary">' + categoryName + '</span> ' + (p.subcategory ? '<small style="color:#666;"> (' + p.subcategory + ')</small>' : '') + '</td>' +
                    '<td><small>B: ' + (p.barcode || 'N/A') + '</small><br><small>S/N: ' + (p.serial_number || 'N/A') + '</small></td>' +
                    '<td><span style="font-weight:bold; color:var(--ac-primary);">' + parseFloat(p.final_price).toFixed(2) + ' EGP</span>' + (p.discount > 0 ? '<br><del style="font-size:0.7rem; color:#999;">' + parseFloat(p.original_price).toFixed(2) + '</del> <span class="ac-is-capsule capsule-danger" style="font-size:0.7rem; padding: 1px 5px;">' + parseFloat(p.discount).toFixed(2) + '-</span>' : '') + '</td>' +
                    '<td><span class="ac-is-capsule ' + stockClass + '">' + p.stock_quantity + '</span></td>' +
                    '<td>' + p.branch_id + '</td>' +
                    '<td><div style="display:flex; gap:5px;">' +
                        '<a href="?ac_view=edit-product&id=' + p.id + '" class="ac-is-btn" style="padding: 4px 8px; font-size:0.75rem; background:#3b82f6;">تعديل</a>' +
                        '<button class="ac-is-btn ac-is-print-barcode" data-barcode="' + p.barcode + '" data-name="' + p.name + '" data-serial="' + p.serial_number + '" style="padding: 4px 8px; font-size:0.75rem; background:#64748b;">باركود</button>' +
                        '<button class="ac-is-delete-product ac-is-btn" data-id="' + p.id + '" style="padding: 4px 8px; font-size:0.75rem; background:#ef4444;">حذف</button>' +
                    '</div></td>' +
                '</tr>';
            });
        }
        $('#ac-is-inventory-table-body').html(html);
    }

    // Sales Mode Toggles
    $('.ac-is-mode-btn').on('click', function() {
        var mode = $(this).data('mode');
        $('.ac-is-mode-btn').removeClass('active').css({'background':'#fff', 'color':'inherit', 'border-color':'#ddd'});
        $(this).addClass('active').css({'background': 'var(--ac-primary)', 'color': '#fff', 'border-color': 'var(--ac-primary)'});

        if (mode === 'scan') {
            $('#ac-is-reader-container').show();
            $('#ac-is-manual-search').hide();
            startScanner();
        } else {
            $('#ac-is-reader-container').hide();
            $('#ac-is-manual-search').show();
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
        }
    });

    // Camera Scanning Logic
    var html5QrcodeScanner;
    function startScanner() {
        html5QrcodeScanner = new Html5QrcodeScanner("ac-is-reader", {
            fps: 30,
            qrbox: {width: 250, height: 150},
            aspectRatio: 1.0
        });
        html5QrcodeScanner.render(onScanSuccess);
    }

    function onScanSuccess(decodedText, decodedResult) {
        $('#ac-is-sale-product-search').val(decodedText).trigger('input');
        if (window.navigator && window.navigator.vibrate) {
            window.navigator.vibrate(100);
        }
    }

    // Sale search product (Enhanced Live Search)
    $('#ac-is-sale-product-search').on('input', function() {
        var query = $(this).val().trim();
        if (query.length < 2) return;

        var found = false;
        $('#ac-is-sale-product option').each(function() {
            var barcode = String($(this).data('barcode'));
            var serial = String($(this).data('serial'));
            var name = $(this).text().toLowerCase();

            if (barcode == query || serial == query || name.includes(query.toLowerCase())) {
                $('#ac-is-sale-product').val($(this).val()).trigger('change');
                if (serial == query || barcode == query) {
                    $('#ac-is-sale-serial').val(serial || barcode);
                }
                $(this).parent().css('background', '#f0fdf4'); // Instant feedback
                found = true;
                return false;
            }
        });

        if (!found && query.length > 5) {
            $.post(ac_is_ajax.ajax_url, {
                action: 'ac_is_search_products',
                search: query,
                nonce: ac_is_ajax.nonce
            }, function(response) {
                if (response.success && response.data.length > 0) {
                    var p = response.data[0];
                    $('#ac-is-sale-product').val(p.id).trigger('change');
                    $('#ac-is-sale-serial').val(p.serial_number || p.barcode);
                }
            });
        }
    });

    // Sale price calculation
    $('#ac-is-sale-product, #ac-is-sale-qty').on('change input', function() {
        var product = $('#ac-is-sale-product option:selected');
        var price = parseFloat(product.data('price')) || 0;
        var qty = parseInt($('#ac-is-sale-qty').val()) || 0;
        $('#ac-is-sale-total').val((price * qty).toFixed(2));
    });

    // Record sale
    $('#ac-is-sales-form').on('submit', function(e) {
        e.preventDefault();
        var product = $('#ac-is-sale-product option:selected');
        if (!product.val()) { alert('يرجى اختيار منتج'); return; }

        var stock = parseInt(product.data('stock'));
        var qty = parseInt($('#ac-is-sale-qty').val());

        if (qty > stock) {
            alert('الكمية المطلوبة أكبر من المتوفر في المخزون');
            return;
        }

        var data = $(this).serialize() + '&action=ac_is_record_sale&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('تم تسجيل البيع بنجاح');
                window.location.href = window.location.href.split('&')[0] + '&ac_view=invoice&sale_id=' + response.data.sale_id + '&autoprint=1';
            } else {
                alert('فشل تسجيل البيع: ' + response.data);
            }
        });
    });

    if (window.location.search.indexOf('autoprint=1') > -1) {
        setTimeout(function() { window.print(); }, 1000);
    }
});
