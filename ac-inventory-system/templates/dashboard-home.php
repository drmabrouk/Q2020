<?php
global $wpdb;
$table_products = $wpdb->prefix . 'ac_is_products';
$table_sales    = $wpdb->prefix . 'ac_is_sales';
$table_branches = $wpdb->prefix . 'ac_is_branches';

// Statistics
$total_products = $wpdb->get_var("SELECT COUNT(*) FROM $table_products");
$today_sales_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_sales WHERE DATE(sale_date) = CURDATE()");
$today_sales_total = $wpdb->get_var("SELECT SUM(total_price) FROM $table_sales WHERE DATE(sale_date) = CURDATE()") ?: 0;
$low_stock_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_products WHERE stock_quantity < 10");
$total_stock_qty = $wpdb->get_var("SELECT SUM(stock_quantity) FROM $table_products") ?: 0;

// Sales data for chart (last 7 days)
$chart_data = $wpdb->get_results("
    SELECT DATE(sale_date) as date, SUM(total_price) as total
    FROM $table_sales
    WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(sale_date)
    ORDER BY date ASC
");

// Top Products
$top_products = $wpdb->get_results("
    SELECT p.name, SUM(s.quantity) as total_qty
    FROM $table_sales s
    JOIN $table_products p ON s.product_id = p.id
    GROUP BY s.product_id
    ORDER BY total_qty DESC
    LIMIT 5
");

// Recent Activity
$recent_sales = $wpdb->get_results("
    SELECT s.*, p.name as product_name
    FROM $table_sales s
    JOIN $table_products p ON s.product_id = p.id
    ORDER BY s.sale_date DESC
    LIMIT 5
");
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2 style="font-weight:800; font-size:1.8rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('لوحة المعلومات / التقارير', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:15px;">
        <a href="<?php echo add_query_arg('ac_view', 'add-product'); ?>" class="ac-is-btn"><?php _e('إضافة منتج', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="ac-is-btn" style="background:#059669;"><?php _e('تسجيل بيع جديد', 'ac-inventory-system'); ?></a>
    </div>
</div>

<div class="ac-is-summary-cards">
    <div class="ac-is-card">
        <h3><?php _e('مبيعات اليوم', 'ac-inventory-system'); ?></h3>
        <div class="value"><?php echo number_format($today_sales_total, 2); ?> <small style="font-size:1rem; color:#64748b;">EGP</small></div>
        <div style="margin-top:10px; font-weight:600; color:#059669;">
            <span class="dashicons dashicons-cart" style="font-size:16px;"></span> <?php echo $today_sales_count; ?> <?php _e('عمليات مكتملة', 'ac-inventory-system'); ?>
        </div>
    </div>
    <div class="ac-is-card">
        <h3><?php _e('تنبيهات المخزون', 'ac-inventory-system'); ?></h3>
        <div class="value" style="color:var(--ac-danger-text);"><?php echo $low_stock_count; ?></div>
        <div style="margin-top:10px; font-weight:600; color:var(--ac-danger-text);">
            <span class="dashicons dashicons-warning" style="font-size:16px;"></span> <?php _e('منتجات قاربت على النفاد', 'ac-inventory-system'); ?>
        </div>
    </div>
    <div class="ac-is-card">
        <h3><?php _e('إجمالي المخزون', 'ac-inventory-system'); ?></h3>
        <div class="value"><?php echo number_format($total_stock_qty); ?></div>
        <div style="margin-top:10px; font-weight:600; color:#64748b;">
            <span class="dashicons dashicons-database" style="font-size:16px;"></span> <?php echo $total_products; ?> <?php _e('صنف مسجل', 'ac-inventory-system'); ?>
        </div>
    </div>
</div>

<div class="ac-is-grid" style="margin-top:40px;">
    <!-- Sales Chart -->
    <div class="ac-is-card" style="grid-column: span 2;">
        <h3><?php _e('تحليل المبيعات (آخر 7 أيام)', 'ac-inventory-system'); ?></h3>
        <canvas id="ac-is-sales-chart" height="100"></canvas>
    </div>

    <!-- Top Products -->
    <div class="ac-is-card">
        <h3><?php _e('الأكثر مبيعاً', 'ac-inventory-system'); ?></h3>
        <table class="ac-is-table">
            <thead>
                <tr>
                    <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($top_products as $tp): ?>
                    <tr>
                        <td><strong><?php echo esc_html($tp->name); ?></strong></td>
                        <td><span class="ac-is-capsule capsule-primary"><?php echo $tp->total_qty; ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($top_products)) echo '<tr><td colspan="2">'.__('لا توجد بيانات مبيعات بعد', 'ac-inventory-system').'</td></tr>'; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Activity -->
    <div class="ac-is-card">
        <h3><?php _e('آخر عمليات البيع', 'ac-inventory-system'); ?></h3>
        <div class="ac-is-recent-list">
            <?php foreach($recent_sales as $rs): ?>
                <div style="display:flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <strong><?php echo esc_html($rs->product_name); ?></strong><br>
                        <small style="color:#94a3b8;"><?php echo date('H:i - Y/m/d', strtotime($rs->sale_date)); ?></small>
                    </div>
                    <div style="text-align: left;">
                        <span style="font-weight:700; color:var(--ac-primary);"><?php echo number_format($rs->total_price, 2); ?> EGP</span><br>
                        <a href="<?php echo add_query_arg(array('ac_view' => 'invoice', 'sale_id' => $rs->id)); ?>" style="font-size:0.75rem; text-decoration:none; color:#64748b;"><?php _e('تفاصيل الفاتورة', 'ac-inventory-system'); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('ac-is-sales-chart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($d){ return $d->date; }, $chart_data)); ?>,
                datasets: [{
                    label: '<?php _e('المبيعات', 'ac-inventory-system'); ?>',
                    data: <?php echo json_encode(array_map(function($d){ return (float)$d->total; }, $chart_data)); ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
