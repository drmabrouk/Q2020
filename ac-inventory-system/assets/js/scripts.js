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

    // Print Barcode Button
    $(document).on('click', '.ac-is-print-barcode', function(e) {
        e.preventDefault();
        var barcode = $(this).data('barcode');
        var name = $(this).data('name');
        var serial = $(this).data('serial');
        if (!barcode) { alert('لا يوجد باركود لهذا المنتج'); return; }

        $('#print-product-name').text(name + (serial ? ' (' + serial + ')' : ''));
        generateBarcode("#print-barcode-svg", barcode);
        window.print();
    });

    // Inventory Real-time Search & Filter
    var searchTimeout;
    $('#ac-is-inventory-search, #ac-is-inventory-category').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            var search = $('#ac-is-inventory-search').val();
            var category = $('#ac-is-inventory-category').val();

            $.post(ac_is_ajax.ajax_url, {
                action: 'ac_is_search_products',
                search: search,
                category: category,
                nonce: ac_is_ajax.nonce
            }, function(response) {
                if (response.success) {
                    renderInventory(response.data);
                }
            });
        }, 500);
    });

    function renderInventory(products) {
        var html = '';
        if (products.length === 0) {
            html = '<tr><td colspan="7" style="text-align:center;">لا توجد منتجات.</td></tr>';
        } else {
            $.each(products, function(i, p) {
                var stockClass = (p.stock_quantity < 5) ? 'capsule-danger' : ((p.stock_quantity < 15) ? 'capsule-warning' : 'capsule-success');
                var categoryName = (p.category == 'ac') ? 'مكيفات' : ((p.category == 'cooling') ? 'تبريد' : 'فلاتر');
                html += '<tr>' +
                    '<td>' + (p.image_url ? '<img src="' + p.image_url + '" class="ac-is-product-img" style="border-radius:4px; max-width:60px;">' : '') + '</td>' +
                    '<td><strong>' + p.name + '</strong><br><span class="ac-is-capsule capsule-primary">' + categoryName + '</span> ' + (p.subcategory ? '<small style="color:#666;"> (' + p.subcategory + ')</small>' : '') + '</td>' +
                    '<td><small>B: ' + (p.barcode || 'N/A') + '</small><br><small>S/N: ' + (p.serial_number || 'N/A') + '</small></td>' +
                    '<td><span style="font-weight:bold; color:var(--ac-primary);">' + parseFloat(p.final_price).toFixed(2) + '</span>' + (p.discount > 0 ? '<br><del style="font-size:0.8rem; color:#999;">' + parseFloat(p.original_price).toFixed(2) + '</del> <span class="ac-is-capsule capsule-danger" style="font-size:0.7rem; padding: 1px 5px;">' + parseFloat(p.discount).toFixed(2) + '-</span>' : '') + '</td>' +
                    '<td><span class="ac-is-capsule ' + stockClass + '">' + p.stock_quantity + '</span></td>' +
                    '<td>' + p.branch_id + '</td>' +
                    '<td><div style="display:flex; gap:5px;">' +
                        '<a href="?ac_view=edit-product&id=' + p.id + '" class="ac-is-btn" style="padding: 5px 10px; font-size:0.8rem; background:#4a90e2;">تعديل</a>' +
                        '<button class="ac-is-btn ac-is-print-barcode" data-barcode="' + p.barcode + '" data-name="' + p.name + '" data-serial="' + p.serial_number + '" style="padding: 5px 10px; font-size:0.8rem; background:#6c757d;">باركود</button>' +
                        '<button class="ac-is-delete-product ac-is-btn" data-id="' + p.id + '" style="padding: 5px 10px; font-size:0.8rem; background:#e53e3e;">حذف</button>' +
                    '</div></td>' +
                '</tr>';
            });
        }
        $('#ac-is-inventory-table-body').html(html);
    }

    // Camera Scanning Logic
    var html5QrcodeScanner;
    $('#ac-is-toggle-scanner').on('click', function() {
        if ($('#ac-is-reader').is(':visible')) {
            $('#ac-is-reader').hide();
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
        } else {
            $('#ac-is-reader').show();
            startScanner();
        }
    });

    function startScanner() {
        html5QrcodeScanner = new Html5QrcodeScanner("ac-is-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    }

    function onScanSuccess(decodedText, decodedResult) {
        $('#ac-is-sale-product-search').val(decodedText).trigger('input');
        html5QrcodeScanner.clear();
        $('#ac-is-reader').hide();
    }

    // Sale search product by barcode/serial/name
    $('#ac-is-sale-product-search').on('input', function() {
        var query = $(this).val();
        if (query.length < 3) return;

        // Local search first
        var found = false;
        $('#ac-is-sale-product option').each(function() {
            var barcode = $(this).data('barcode');
            var serial = $(this).data('serial');
            var name = $(this).text();

            if (barcode == query || serial == query || name.includes(query)) {
                $('#ac-is-sale-product').val($(this).val()).trigger('change');
                if (serial == query) $('#ac-is-sale-serial').val(serial);
                found = true;
                return false;
            }
        });

        if (!found) {
            // AJAX search fallback
            $.post(ac_is_ajax.ajax_url, {
                action: 'ac_is_search_products',
                search: query,
                nonce: ac_is_ajax.nonce
            }, function(response) {
                if (response.success && response.data.length > 0) {
                    var p = response.data[0];
                    $('#ac-is-sale-product').val(p.id).trigger('change');
                    if (p.serial_number == query) $('#ac-is-sale-serial').val(p.serial_number);
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
                window.location.href = window.location.href.split('&')[0] + '&ac_view=invoice&sale_id=' + response.data.sale_id;
            } else {
                alert('فشل تسجيل البيع: ' + response.data);
            }
        });
    });

    // Simple media uploader
    $('.ac-is-upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'رفع صورة المنتج',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#ac-is-image-url').val(image_url);
        });
    });
});
