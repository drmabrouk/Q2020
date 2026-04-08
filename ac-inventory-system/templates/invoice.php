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

<div class="ac-is-header-flex no-print" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-weight:800;"><?php _e('عرض ومعاينة الفاتورة', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:12px;">
        <button onclick="window.print();" class="ac-is-btn" style="background:#1e293b;"><span class="dashicons dashicons-printer" style="margin-left:8px;"></span><?php _e('طباعة فورية', 'ac-inventory-system'); ?></button>
        <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="ac-is-btn" style="background:#059669;"><?php _e('عملية بيع جديدة', 'ac-inventory-system'); ?></a>
    </div>
</div>

<?php if($sale): ?>
<div class="invoice-container" style="background:#fff; font-family: 'Segoe UI', Tahoma, sans-serif;">
    <div class="invoice-header" style="border-bottom: 4px solid #0f172a; padding-bottom: 20px; margin-bottom: 30px;">
        <div style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin:0; font-size:2rem; font-weight:900; color:#0f172a;"><?php echo get_bloginfo('name'); ?></h1>
                <p style="margin:5px 0; font-weight:700; color:#64748b;"><?php echo $branch_name; ?></p>
            </div>
            <div style="text-align: left;">
                <h2 style="margin:0; color:#2563eb; font-weight:800;"><?php _e('فاتورة ضريبية مبسطة', 'ac-inventory-system'); ?></h2>
                <p style="margin:5px 0; font-weight:600;">#<?php echo str_pad($sale->id, 8, '0', STR_PAD_LEFT); ?></p>
                <p style="margin:0; font-size:0.9rem; color:#64748b;"><?php echo date('H:i | Y/m/d', strtotime($sale->sale_date)); ?></p>
            </div>
        </div>
    </div>

    <div class="invoice-details" style="margin-bottom:40px;">
        <table class="invoice-table" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding:15px; text-align:right; font-weight:800;"><?php _e('البيان / المنتج', 'ac-inventory-system'); ?></th>
                    <th style="padding:15px; text-align:right; font-weight:800;"><?php _e('السيريال / الباركود', 'ac-inventory-system'); ?></th>
                    <th style="padding:15px; text-align:center; font-weight:800;"><?php _e('الكمية', 'ac-inventory-system'); ?></th>
                    <th style="padding:15px; text-align:left; font-weight:800;"><?php _e('الإجمالي', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding:20px 15px;">
                        <span style="font-size:1.1rem; font-weight:700; color:#0f172a;"><?php echo esc_html($sale->product_name); ?></span>
                    </td>
                    <td style="padding:20px 15px;">
                        <code style="background:#f1f5f9; padding:2px 5px; border-radius:3px; font-size:0.9rem;"><?php echo esc_html($sale->serial_number ?: $sale->product_barcode); ?></code>
                    </td>
                    <td style="padding:20px 15px; text-align:center; font-weight:700;"><?php echo $sale->quantity; ?></td>
                    <td style="padding:20px 15px; text-align:left; font-weight:800; color:#2563eb; font-size:1.1rem;"><?php echo number_format($sale->total_price, 2); ?> EGP</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="invoice-summary" style="display:flex; justify-content: space-between; align-items: flex-end; padding:20px; background:#f8fafc; border-radius:0;">
        <div class="invoice-qr">
            <svg id="invoice-barcode-svg"></svg>
            <script>
                jQuery(document).ready(function($) {
                    JsBarcode("#invoice-barcode-svg", "<?php echo str_pad($sale->id, 10, '0', STR_PAD_LEFT); ?>", {
                        format: "CODE128",
                        width: 1.5,
                        height: 40,
                        displayValue: true,
                        fontSize: 10
                    });
                });
            </script>
        </div>
        <div style="text-align: left;">
            <p style="margin:0; color:#64748b; font-weight:600;"><?php _e('المبلغ الإجمالي المستحق', 'ac-inventory-system'); ?></p>
            <h2 style="margin:5px 0 0 0; font-size:2.2rem; font-weight:900; color:#0f172a;"><?php echo number_format($sale->total_price, 2); ?> <small style="font-size:1rem; color:#64748b;">EGP</small></h2>
        </div>
    </div>

    <div class="invoice-footer" style="margin-top:50px; text-align: center; border-top: 2px dashed #e2e8f0; padding-top: 30px;">
        <p style="font-weight:700; color:#0f172a; margin-bottom:10px;"><?php _e('إقرار استلام العميل', 'ac-inventory-system'); ?></p>
        <p style="font-size:0.85rem; color:#64748b; max-width:600px; margin: 0 auto; line-height:1.6;">
            <?php _e('أقر أنا المستلم بأنني قد تسلمت الأصناف المذكورة أعلاه بحالة جيدة ومطابقة للمواصفات المطلوبة، وأن الشركة غير مسؤولة عن سوء الاستخدام بعد الاستلام.', 'ac-inventory-system'); ?>
        </p>
        <div style="margin-top:40px; display:flex; justify-content: space-around;">
            <p>___________________<br><?php _e('توقيع المستلم', 'ac-inventory-system'); ?></p>
            <p>___________________<br><?php _e('توقيع المحاسب', 'ac-inventory-system'); ?></p>
        </div>
        <p style="margin-top:50px; font-size:0.8rem; color:#94a3b8;"><?php _e('شكراً لثقتكم بنا - تم إصدار هذه الفاتورة إلكترونياً', 'ac-inventory-system'); ?></p>
    </div>
</div>
<?php else: ?>
    <p><?php _e('عذراً، لم يتم العثور على بيانات هذه الفاتورة.', 'ac-inventory-system'); ?></p>
<?php endif; ?>
