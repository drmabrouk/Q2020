<?php
$daily = AC_IS_Reports::get_daily_sales();
$low_stock = AC_IS_Reports::get_stock_overview();
?>
<h2><?php _e('تقارير المبيعات والمخزون', 'ac-inventory-system'); ?></h2>

<div class="ac-is-report-section">
    <h3><?php _e('مبيعات آخر 7 أيام', 'ac-inventory-system'); ?></h3>
    <table class="ac-is-table">
        <thead>
            <tr>
                <th><?php _e('التاريخ', 'ac-inventory-system'); ?></th>
                <th><?php _e('الإجمالي', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($daily as $d): ?>
                <tr>
                    <td><?php echo $d->date; ?></td>
                    <td><?php echo $d->total; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="ac-is-report-section" style="margin-top:30px;">
    <h3><?php _e('تنبيهات انخفاض المخزون (أقل من 10 قطع)', 'ac-inventory-system'); ?></h3>
    <table class="ac-is-table">
        <thead>
            <tr>
                <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                <th><?php _e('الكمية المتبقية', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($low_stock as $ls): ?>
                <tr>
                    <td><?php echo esc_html($ls->name); ?></td>
                    <td style="color:red; font-weight:bold;"><?php echo $ls->stock_quantity; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="ac-is-export-actions" style="margin-top:30px;">
    <a href="<?php echo add_query_arg('ac_export', 'sales'); ?>" class="ac-is-btn" style="background:#28a745;"><?php _e('تصدير سجل المبيعات (CSV)', 'ac-inventory-system'); ?></a>
    <button onclick="window.print();" class="ac-is-btn" style="background:#6c757d;"><?php _e('طباعة التقرير', 'ac-inventory-system'); ?></button>
</div>
