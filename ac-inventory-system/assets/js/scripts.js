jQuery(document).ready(function($) {
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
    $('.ac-is-delete-product').on('click', function(e) {
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
                $('#receipt-content').html(
                    '<p>رقم الفاتورة: ' + response.data.sale_id + '</p>' +
                    '<p>المنتج: ' + product.text() + '</p>' +
                    '<p>الكمية: ' + qty + '</p>' +
                    '<p>الإجمالي: ' + $('#ac-is-sale-total').val() + '</p>'
                );
                $('#ac-is-receipt').show();
                $('#ac-is-sales-form')[0].reset();
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
