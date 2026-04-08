<?php
global $wpdb;
$table_sales = $wpdb->prefix . 'ac_is_sales';
$table_products = $wpdb->prefix . 'ac_is_products';
$sales = $wpdb->get_results("
    SELECT s.*, p.name as product_name, u.display_name as operator_name 
    FROM $table_sales s
    JOIN $table_products p ON s.product_id = p.id
    JOIN {$wpdb->users} u ON s.operator_id = u.ID
    ORDER BY s.sale_date DESC
");
?>
<h2><?php _e('سجل المبيعات', 'ac-inventory-system'); ?></h2>

<table class="ac-is-table">
    <thead>
        <tr>
            <th><?php _e('التاريخ', 'ac-inventory-system'); ?></th>
            <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
            <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
            <th><?php _e('الإجمالي', 'ac-inventory-system'); ?></th>
            <th><?php _e('الموظف', 'ac-inventory-system'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if($sales): foreach($sales as $sale): ?>
            <tr>
                <td><?php echo $sale->sale_date; ?></td>
                <td><?php echo esc_html($sale->product_name); ?></td>
                <td><?php echo $sale->quantity; ?></td>
                <td><?php echo $sale->total_price; ?></td>
                <td><?php echo esc_html($sale->operator_name); ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="5"><?php _e('لا توجد مبيعات مسجلة.', 'ac-inventory-system'); ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>
