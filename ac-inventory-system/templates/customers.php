<?php
$customers = AC_IS_Customers::get_all_customers();
$can_manage = AC_IS_Auth::is_manager();
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('إدارة العملاء', 'ac-inventory-system'); ?></h2>
    <?php if($can_manage): ?>
        <button id="ac-is-add-customer-btn" class="ac-is-btn"><span class="dashicons dashicons-plus-alt" style="margin-left:8px;"></span><?php _e('إضافة عميل جديد', 'ac-inventory-system'); ?></button>
    <?php endif; ?>
</div>

<div class="ac-is-search-filters" style="margin-bottom:25px; display:flex; gap:15px; background:#fff; padding:20px; border:1px solid var(--ac-border);">
    <input type="text" id="ac-is-customer-search" placeholder="<?php _e('ابحث بالاسم، الهاتف، أو البريد...', 'ac-inventory-system'); ?>" style="flex:1; padding:12px; border:1px solid #ddd; border-radius:6px;">
</div>

<!-- Customer Modal -->
<div id="ac-is-customer-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10001; align-items:center; justify-content:center;">
    <div class="ac-is-card" style="width:100%; max-width:500px; padding:30px; position:relative;">
        <h3 id="modal-title"><?php _e('بيانات العميل', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-customer-form">
            <input type="hidden" name="id" id="customer-id">
            <div class="ac-is-form-group">
                <label><?php _e('الاسم بالكامل', 'ac-inventory-system'); ?></label>
                <input type="text" name="name" id="customer-name" required>
            </div>
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('الهاتف الأساسي', 'ac-inventory-system'); ?></label>
                    <input type="text" name="phone" id="customer-phone" required>
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('الهاتف الثانوي', 'ac-inventory-system'); ?></label>
                    <input type="text" name="phone_secondary" id="customer-phone-secondary">
                </div>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('البريد الإلكتروني', 'ac-inventory-system'); ?></label>
                <input type="email" name="email" id="customer-email">
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('العنوان', 'ac-inventory-system'); ?></label>
                <textarea name="address" id="customer-address" rows="2"></textarea>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" class="ac-is-btn" style="flex:1;"><?php _e('حفظ العميل', 'ac-inventory-system'); ?></button>
                <button type="button" id="close-modal" class="ac-is-btn" style="background:#64748b;"><?php _e('إلغاء', 'ac-inventory-system'); ?></button>
            </div>
        </form>
    </div>
</div>

<div style="background:#fff; border:1px solid var(--ac-border); overflow:hidden;">
    <table class="ac-is-table" id="ac-is-customer-table">
        <thead>
            <tr>
                <th><?php _e('العميل', 'ac-inventory-system'); ?></th>
                <th><?php _e('بيانات التواصل', 'ac-inventory-system'); ?></th>
                <th><?php _e('رؤى العميل', 'ac-inventory-system'); ?></th>
                <th><?php _e('صافي الربح', 'ac-inventory-system'); ?></th>
                <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $c):
                $profit_class = ($c->net_profit > 0) ? 'capsule-success' : 'capsule-warning';
            ?>
                <tr data-customer='<?php echo json_encode($c); ?>'>
                    <td><strong><?php echo esc_html($c->name); ?></strong></td>
                    <td>
                        <small><?php echo esc_html($c->phone); ?></small>
                        <?php if($c->phone_secondary): ?><br><small style="color:#64748b;"><?php echo esc_html($c->phone_secondary); ?></small><?php endif; ?>
                    </td>
                    <td>
                        <span class="ac-is-capsule capsule-primary" style="margin-bottom:4px;">
                            <?php _e('المشتريات:', 'ac-inventory-system'); ?> <?php echo number_format($c->total_revenue ?: 0, 2); ?> EGP
                        </span><br>
                        <span class="ac-is-capsule capsule-info">
                            <?php echo $c->total_invoices; ?> <?php _e('عملية', 'ac-inventory-system'); ?>
                        </span>
                    </td>
                    <td><span class="ac-is-capsule <?php echo $profit_class; ?>"><?php echo number_format($c->net_profit ?: 0, 2); ?> EGP</span></td>
                    <td>
                        <div style="display:flex; gap:5px;">
                            <a href="<?php echo add_query_arg(array('ac_view' => 'sales-history', 'sale_search' => $c->phone)); ?>" class="ac-is-btn" style="padding:4px 8px; font-size:0.75rem; background:#64748b;"><span class="dashicons dashicons-list-view"></span></a>
                            <?php if($can_manage): ?>
                                <button class="ac-is-btn ac-is-edit-customer" style="padding:4px 8px; font-size:0.75rem; background:#3b82f6;"><span class="dashicons dashicons-edit"></span></button>
                                <button class="ac-is-btn ac-is-delete-customer" style="padding:4px 8px; font-size:0.75rem; background:#ef4444;"><span class="dashicons dashicons-trash"></span></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($customers)) echo '<tr><td colspan="5" style="text-align:center; padding:40px;">'.__('لا يوجد عملاء مسجلين بعد', 'ac-inventory-system').'</td></tr>'; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    const modal = $('#ac-is-customer-modal');

    $('#ac-is-add-customer-btn').on('click', function() {
        $('#ac-is-customer-form')[0].reset();
        $('#customer-id').val('');
        $('#modal-title').text('<?php _e('إضافة عميل جديد', 'ac-inventory-system'); ?>');
        modal.css('display', 'flex');
    });

    $(document).on('click', '.ac-is-edit-customer', function() {
        const c = $(this).closest('tr').data('customer');
        $('#customer-id').val(c.id);
        $('#customer-name').val(c.name);
        $('#customer-phone').val(c.phone);
        $('#customer-phone-secondary').val(c.phone_secondary);
        $('#customer-email').val(c.email);
        $('#customer-address').val(c.address);
        $('#modal-title').text('<?php _e('تعديل بيانات العميل', 'ac-inventory-system'); ?>');
        modal.css('display', 'flex');
    });

    $('#close-modal').on('click', function() { modal.hide(); });

    $('#ac-is-customer-form').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize() + '&action=ac_is_save_customer&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });

    $(document).on('click', '.ac-is-delete-customer', function() {
        if (!confirm('<?php _e('هل أنت متأكد من حذف هذا العميل؟', 'ac-inventory-system'); ?>')) return;
        const id = $(this).closest('tr').data('customer').id;
        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_delete_customer',
            id: id,
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });

    $('#ac-is-customer-search').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('#ac-is-customer-table tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(query));
        });
    });
});
</script>
