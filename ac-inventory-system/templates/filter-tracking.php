<?php
$filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : 'all';
$tracking_items = AC_IS_Filters::get_all_tracking( array('status' => $filter_status) );
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('متابعة صيانة فلاتر المياه', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:10px;">
        <a href="<?php echo add_query_arg('filter_status', 'all'); ?>" class="ac-is-btn <?php echo ($filter_status == 'all' ? '' : 'inactive'); ?>" style="<?php echo ($filter_status == 'all' ? '' : 'background:#64748b;'); ?>"><?php _e('الكل', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('filter_status', 'alert'); ?>" class="ac-is-btn <?php echo ($filter_status == 'alert' ? '' : 'inactive'); ?>" style="<?php echo ($filter_status == 'alert' ? 'background:#ef4444;' : 'background:#64748b;'); ?>"><?php _e('تنبيهات التغيير', 'ac-inventory-system'); ?></a>
    </div>
</div>

<div style="background:#fff; border:1px solid var(--ac-border); border-radius: 8px; overflow:hidden;">
    <table class="ac-is-table">
        <thead>
            <tr>
                <th><?php _e('العميل', 'ac-inventory-system'); ?></th>
                <th><?php _e('نوع الفلتر', 'ac-inventory-system'); ?></th>
                <th><?php _e('رقم الشمعة', 'ac-inventory-system'); ?></th>
                <th><?php _e('تاريخ التركيب', 'ac-inventory-system'); ?></th>
                <th><?php _e('تاريخ التغيير القادم', 'ac-inventory-system'); ?></th>
                <th><?php _e('الحالة', 'ac-inventory-system'); ?></th>
                <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $tracking_items ) : foreach ( $tracking_items as $item ) :
                $days_left = (strtotime($item->expiry_date) - time()) / 86400;
                $status_class = ($days_left <= 0) ? 'capsule-danger' : (($days_left <= 7) ? 'capsule-warning' : 'capsule-success');
                $status_text = ($days_left <= 0) ? __('منتهية الصلاحية', 'ac-inventory-system') : (($days_left <= 7) ? __('قرب موعد التغيير', 'ac-inventory-system') : __('نشطة', 'ac-inventory-system'));
            ?>
                <tr>
                    <td>
                        <strong><?php echo esc_html($item->customer_name); ?></strong><br>
                        <small style="color:#64748b;"><?php echo esc_html($item->customer_phone); ?></small>
                    </td>
                    <td><?php echo esc_html($item->product_name); ?></td>
                    <td><span class="ac-is-capsule capsule-primary"><?php _e('شمعة رقم', 'ac-inventory-system'); ?> <?php echo $item->stage_number; ?></span></td>
                    <td><?php echo date('Y-m-d', strtotime($item->installation_date)); ?></td>
                    <td><strong><?php echo date('Y-m-d', strtotime($item->expiry_date)); ?></strong></td>
                    <td><span class="ac-is-capsule <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    <td>
                        <button class="ac-is-btn ac-is-replace-candle" data-id="<?php echo $item->id; ?>" style="padding:6px 12px; font-size:0.8rem; background:#059669;">
                            <span class="dashicons dashicons-update" style="margin-left:5px;"></span><?php _e('تم التغيير', 'ac-inventory-system'); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; else : ?>
                <tr><td colspan="7" style="text-align:center; padding:40px; color:#94a3b8;"><?php _e('لا توجد بيانات متابعة حالياً.', 'ac-inventory-system'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.ac-is-replace-candle').on('click', function() {
        if (!confirm('<?php _e('هل تم تغيير الشمعة بالفعل؟ سيتم تحديث تاريخ التغيير القادم تلقائياً.', 'ac-inventory-system'); ?>')) return;
        const id = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span>');

        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_replace_candle',
            tracking_id: id,
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('خطأ في التحديث');
                btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> تم التغيير');
            }
        });
    });
});
</script>
