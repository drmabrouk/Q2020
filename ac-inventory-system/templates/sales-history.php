<?php
global $wpdb;
$table_sales     = $wpdb->prefix . 'ac_is_sales';
$table_invoices  = $wpdb->prefix . 'ac_is_invoices';
$table_products  = $wpdb->prefix . 'ac_is_products';
$table_customers = $wpdb->prefix . 'ac_is_customers';
$table_staff     = $wpdb->prefix . 'ac_is_staff';

// Filters
$where = "1=1";
if ( ! empty( $_GET['sale_search'] ) ) {
    $search = '%' . $wpdb->esc_like( $_GET['sale_search'] ) . '%';
    $where .= $wpdb->prepare( " AND (c.name LIKE %s OR c.phone LIKE %s OR s.serial_number LIKE %s OR p.name LIKE %s OR p.barcode LIKE %s OR i.id = %d)",
        $search, $search, $search, $search, $search, intval($_GET['sale_search']) );
}

if ( ! empty( $_GET['date_from'] ) ) {
    $where .= $wpdb->prepare( " AND i.invoice_date >= %s", $_GET['date_from'] . ' 00:00:00' );
}
if ( ! empty( $_GET['date_to'] ) ) {
    $where .= $wpdb->prepare( " AND i.invoice_date <= %s", $_GET['date_to'] . ' 23:59:59' );
}
if ( ! empty( $_GET['branch_id'] ) ) {
    $where .= $wpdb->prepare( " AND i.branch_id = %d", $_GET['branch_id'] );
}
if ( ! empty( $_GET['operator_id'] ) ) {
    $where .= $wpdb->prepare( " AND i.operator_id = %s", $_GET['operator_id'] );
}

$sales = $wpdb->get_results("
    SELECT s.*, i.invoice_date, i.total_amount as invoice_total, c.name as customer_name, c.phone as customer_phone, p.name as product_name, i.operator_id
    FROM $table_sales s
    JOIN $table_invoices i ON s.invoice_id = i.id
    JOIN $table_products p ON s.product_id = p.id
    LEFT JOIN $table_customers c ON i.customer_id = c.id
    WHERE $where
    ORDER BY i.invoice_date DESC
");

$branches = AC_IS_Inventory::get_branches();
$staff_list = $wpdb->get_results( "SELECT id, name FROM $table_staff" );
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('سجل المبيعات والتفاصيل', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-search-filters" style="background:#fff; padding:20px; border:1px solid var(--ac-border); margin-bottom:25px;">
    <form method="get" class="ac-is-grid" style="grid-template-columns: repeat(3, 1fr); gap:15px;">
        <input type="hidden" name="ac_view" value="sales-history">

        <div class="ac-is-form-group">
            <label><?php _e('بحث شامل', 'ac-inventory-system'); ?></label>
            <input type="text" name="sale_search" value="<?php echo esc_attr($_GET['sale_search'] ?? ''); ?>" placeholder="<?php _e('رقم الفاتورة، العميل، الهاتف، المنتج...', 'ac-inventory-system'); ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('من تاريخ', 'ac-inventory-system'); ?></label>
            <input type="date" name="date_from" value="<?php echo esc_attr($_GET['date_from'] ?? ''); ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('إلى تاريخ', 'ac-inventory-system'); ?></label>
            <input type="date" name="date_to" value="<?php echo esc_attr($_GET['date_to'] ?? ''); ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الفرع', 'ac-inventory-system'); ?></label>
            <select name="branch_id">
                <option value=""><?php _e('كل الفروع', 'ac-inventory-system'); ?></option>
                <?php foreach($branches as $b): ?>
                    <option value="<?php echo $b->id; ?>" <?php selected($_GET['branch_id'] ?? '', $b->id); ?>><?php echo esc_html($b->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الموظف', 'ac-inventory-system'); ?></label>
            <select name="operator_id">
                <option value=""><?php _e('كل الموظفين', 'ac-inventory-system'); ?></option>
                <?php foreach($staff_list as $s): ?>
                    <option value="<?php echo $s->id; ?>" <?php selected($_GET['operator_id'] ?? '', $s->id); ?>><?php echo esc_html($s->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ac-is-form-group" style="display:flex; align-items:flex-end; gap:10px;">
            <button type="submit" class="ac-is-btn" style="flex:1; height:42px;"><?php _e('تطبيق الفلتر', 'ac-inventory-system'); ?></button>
            <a href="?ac_view=sales-history" class="ac-is-btn" style="background:#64748b; height:42px;"><?php _e('إعادة تعيين', 'ac-inventory-system'); ?></a>
        </div>
    </form>
</div>

<div style="background:#fff; border:1px solid var(--ac-border); overflow:hidden;">
    <table class="ac-is-table">
        <thead>
            <tr>
                <th><?php _e('رقم / تاريخ', 'ac-inventory-system'); ?></th>
                <th><?php _e('العميل', 'ac-inventory-system'); ?></th>
                <th><?php _e('المنتج والسيريال', 'ac-inventory-system'); ?></th>
                <th><?php _e('المبلغ', 'ac-inventory-system'); ?></th>
                <th><?php _e('الموظف المسؤول', 'ac-inventory-system'); ?></th>
                <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sales as $s):
                $operator_name = __('غير معروف', 'ac-inventory-system');
                if (strpos($s->operator_id, 'wp_') === 0) {
                    $wp_id = str_replace('wp_', '', $s->operator_id);
                    $user = get_userdata($wp_id);
                    if ($user) $operator_name = $user->display_name;
                } else {
                    $staff = $wpdb->get_row($wpdb->prepare("SELECT name FROM $table_staff WHERE id = %s", $s->operator_id));
                    if ($staff) $operator_name = $staff->name;
                }
            ?>
                <tr>
                    <td>
                        <strong>#<?php echo $s->invoice_id; ?></strong><br>
                        <small style="color:#64748b;"><?php echo date('Y-m-d H:i', strtotime($s->invoice_date)); ?></small>
                    </td>
                    <td>
                        <strong><?php echo esc_html($s->customer_name ?: __('عميل سريع', 'ac-inventory-system')); ?></strong><br>
                        <small><?php echo esc_html($s->customer_phone ?: '-'); ?></small>
                    </td>
                    <td>
                        <strong><?php echo esc_html($s->product_name); ?></strong><br>
                        <small>SN: <?php echo esc_html($s->serial_number ?: '-'); ?></small>
                    </td>
                    <td><span style="font-weight:700; color:var(--ac-primary);"><?php echo number_format($s->total_price, 2); ?> EGP</span></td>
                    <td><span class="ac-is-capsule capsule-info"><?php echo esc_html($operator_name); ?></span></td>
                    <td>
                        <div style="display:flex; gap:5px;">
                            <a href="<?php echo add_query_arg(array('ac_view' => 'invoice', 'invoice_id' => $s->invoice_id)); ?>" class="ac-is-btn" style="padding:4px 8px; font-size:0.75rem; background:#64748b;"><span class="dashicons dashicons-visibility"></span></a>
                            <?php if ( AC_IS_Auth::can_delete_records() ) : ?>
                                <button class="ac-is-btn ac-is-delete-invoice" data-id="<?php echo $s->invoice_id; ?>" style="padding:4px 8px; font-size:0.75rem; background:#ef4444;"><span class="dashicons dashicons-trash"></span></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($sales)) echo '<tr><td colspan="6" style="text-align:center; padding:40px;">'.__('لم يتم العثور على نتائج', 'ac-inventory-system').'</td></tr>'; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.ac-is-delete-invoice').on('click', function() {
        if (!confirm('<?php _e('هل أنت متأكد من حذف هذه الفاتورة؟ سيتم استرجاع المنتجات للمخزون وحذف السجل نهائياً.', 'ac-inventory-system'); ?>')) return;
        const id = $(this).data('id');
        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_delete_invoice',
            invoice_id: id,
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });
});
</script>
