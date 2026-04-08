<?php
global $wpdb;
$table_sales = $wpdb->prefix . 'ac_is_sales';
$table_products = $wpdb->prefix . 'ac_is_products';
$table_staff = $wpdb->prefix . 'ac_is_staff';

$sales = $wpdb->get_results("
    SELECT s.*, p.name as product_name
    FROM $table_sales s
    JOIN $table_products p ON s.product_id = p.id
    ORDER BY s.sale_date DESC
");
?>
<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><?php _e('سجل المبيعات', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-search-filters" style="margin-bottom:20px; display:flex; gap:15px; flex-wrap: wrap; background:#f8fafc; padding:15px; border-radius:8px;">
    <form style="display:flex; gap:10px; width:100%;">
        <input type="hidden" name="ac_view" value="sales-history">
        <input type="text" name="sale_search" placeholder="<?php _e('ابحث بالاسم أو الرقم التسلسلي...', 'ac-inventory-system'); ?>" value="<?php echo isset($_GET['sale_search']) ? esc_attr($_GET['sale_search']) : ''; ?>" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:4px;">
        <button type="submit" class="ac-is-btn" style="padding: 10px 20px; background:#4a90e2;"><?php _e('بحث', 'ac-inventory-system'); ?></button>
    </form>
</div>

<table class="ac-is-table">
    <thead>
        <tr>
            <th><?php _e('التاريخ', 'ac-inventory-system'); ?></th>
            <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
            <th><?php _e('السيريال', 'ac-inventory-system'); ?></th>
            <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
            <th><?php _e('الإجمالي', 'ac-inventory-system'); ?></th>
            <th><?php _e('الموظف', 'ac-inventory-system'); ?></th>
            <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if($sales): foreach($sales as $sale):
            $operator_name = __('غير معروف', 'ac-inventory-system');
            if (strpos($sale->operator_id, 'wp_') === 0) {
                $wp_id = str_replace('wp_', '', $sale->operator_id);
                $user = get_userdata($wp_id);
                if ($user) $operator_name = $user->display_name;
            } else {
                $staff = $wpdb->get_row($wpdb->prepare("SELECT name FROM $table_staff WHERE id = %s", $sale->operator_id));
                if ($staff) $operator_name = $staff->name;
            }
        ?>
            <tr>
                <td><?php echo $sale->sale_date; ?></td>
                <td><?php echo esc_html($sale->product_name); ?></td>
                <td><?php echo esc_html($sale->serial_number ?: '-'); ?></td>
                <td><?php echo $sale->quantity; ?></td>
                <td><span style="font-weight:bold; color:var(--ac-primary);"><?php echo number_format($sale->total_price, 2); ?></span></td>
                <td><?php echo esc_html($operator_name); ?></td>
                <td>
                    <a href="<?php echo add_query_arg(array('ac_view' => 'invoice', 'invoice_id' => $sale->invoice_id)); ?>" class="ac-is-btn" style="padding: 5px 10px; font-size:0.8rem; background:#64748b;"><?php _e('فاتورة', 'ac-inventory-system'); ?></a>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="7" style="text-align:center;"><?php _e('لا توجد مبيعات مسجلة.', 'ac-inventory-system'); ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>
