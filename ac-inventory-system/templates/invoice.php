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

<div class="ac-is-header-flex no-print" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2><?php _e('عرض الفاتورة', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:10px;">
        <button onclick="window.print();" class="ac-is-btn" style="background:#64748b;"><span class="dashicons dashicons-printer" style="margin-left:8px;"></span><?php _e('طباعة الفاتورة', 'ac-inventory-system'); ?></button>
        <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="ac-is-btn" style="background:#059669;"><?php _e('بيع جديد', 'ac-inventory-system'); ?></a>
    </div>
</div>

<?php if($sale): ?>
<div class="invoice-container">
    <div class="invoice-header">
        <div>
            <h1><?php echo get_bloginfo('name'); ?></h1>
            <p><?php echo $branch_name; ?></p>
        </div>
        <div style="text-align: left;">
            <h2 style="margin:0;"><?php _e('فاتورة مبيعات', 'ac-inventory-system'); ?></h2>
            <p><?php _e('رقم:', 'ac-inventory-system'); ?> #<?php echo str_pad($sale->id, 6, '0', STR_PAD_LEFT); ?></p>
            <p><?php _e('التاريخ:', 'ac-inventory-system'); ?> <?php echo date('Y-m-d', strtotime($sale->sale_date)); ?></p>
        </div>
    </div>

    <div class="invoice-details">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الرقم التسلسلي / الباركود', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الإجمالي', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?php echo esc_html($sale->product_name); ?></strong>
                    </td>
                    <td>
                        <small>S/N: <?php echo esc_html($sale->serial_number ?: 'N/A'); ?></small><br>
                        <small>B: <?php echo esc_html($sale->product_barcode ?: 'N/A'); ?></small>
                    </td>
                    <td><?php echo $sale->quantity; ?></td>
                    <td><?php echo number_format($sale->total_price, 2); ?> EGP</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="invoice-footer" style="display:flex; justify-content: space-between; align-items: flex-end; margin-top: 40px;">
        <div class="invoice-barcode">
            <svg id="invoice-barcode-svg"></svg>
            <script>
                jQuery(document).ready(function($) {
                    JsBarcode("#invoice-barcode-svg", "INV-<?php echo $sale->id; ?>", {
                        format: "CODE128",
                        width: 1,
                        height: 30,
                        displayValue: true
                    });
                });
            </script>
        </div>
        <div class="invoice-total">
            <p style="font-size:1.2rem; border-top: 2px solid #333; padding-top: 10px;">
                <strong><?php _e('الإجمالي النهائي:', 'ac-inventory-system'); ?></strong>
                <span style="color:var(--ac-primary);"><?php echo number_format($sale->total_price, 2); ?> EGP</span>
            </p>
        </div>
    </div>

    <div style="margin-top: 60px; text-align: center; border-top: 1px dashed #ddd; padding-top: 20px;">
        <p><?php _e('شكراً لتعاملكم معنا', 'ac-inventory-system'); ?></p>
    </div>
</div>
<?php else: ?>
    <p><?php _e('عذراً، الفاتورة غير موجودة.', 'ac-inventory-system'); ?></p>
<?php endif; ?>
