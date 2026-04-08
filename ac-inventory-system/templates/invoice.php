<?php
$sale_id = isset($_GET['sale_id']) ? intval($_GET['sale_id']) : 0;
$sale = $sale_id ? AC_IS_Sales::get_sale($sale_id) : null;
$branches = AC_IS_Inventory::get_branches();
$branch_name = '';
if($sale) {
    foreach($branches as $b) {
        if($b->id == $sale->branch_id) {
            $branch_name = $b->name;
            break;
        }
    }
}
?>

<?php if($sale): ?>
<div class="invoice-container">
    <div class="invoice-header">
        <div>
            <h1><?php _e('فاتورة مبيعات', 'ac-inventory-system'); ?></h1>
            <p><?php _e('رقم الفاتورة:', 'ac-inventory-system'); ?> #<?php echo $sale->id; ?></p>
        </div>
        <div style="text-align: left;">
            <p><strong><?php echo get_bloginfo('name'); ?></strong></p>
            <p><?php echo $branch_name; ?></p>
            <p><?php _e('التاريخ:', 'ac-inventory-system'); ?> <?php echo $sale->sale_date; ?></p>
        </div>
    </div>

    <div class="invoice-details">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الرقم التسلسلي', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
                    <th><?php _e('السعر', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo esc_html($sale->product_name); ?></td>
                    <td><?php echo esc_html($sale->serial_number ?: 'N/A'); ?></td>
                    <td><?php echo $sale->quantity; ?></td>
                    <td><?php echo number_format($sale->total_price, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="invoice-total">
        <p><?php _e('الإجمالي النهائي:', 'ac-inventory-system'); ?> <?php echo number_format($sale->total_price, 2); ?> <?php _e('ريال', 'ac-inventory-system'); ?></p>
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <p><?php _e('شكراً لتعاملكم معنا', 'ac-inventory-system'); ?></p>
        <button onclick="window.print();" class="ac-is-btn" style="margin-top:20px; background:#6c757d;"><?php _e('طباعة الفاتورة', 'ac-inventory-system'); ?></button>
    </div>
</div>
<?php else: ?>
    <p><?php _e('عذراً، الفاتورة غير موجودة.', 'ac-inventory-system'); ?></p>
<?php endif; ?>
