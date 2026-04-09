<?php
global $wpdb;
$table_products = $wpdb->prefix . 'ac_is_products';
$table_sales    = $wpdb->prefix . 'ac_is_sales';
$table_invoices = $wpdb->prefix . 'ac_is_invoices';

// Statistics
$total_products = $wpdb->get_var("SELECT COUNT(*) FROM $table_products");
$today_sales_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_invoices WHERE DATE(invoice_date) = CURDATE()");
$today_sales_total = $wpdb->get_var("SELECT SUM(total_amount) FROM $table_invoices WHERE DATE(invoice_date) = CURDATE()") ?: 0;
$low_stock_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_products WHERE stock_quantity < 10");
$total_stock_qty = $wpdb->get_var("SELECT SUM(stock_quantity) FROM $table_products") ?: 0;

// Monthly Sales data for chart (Last 6 months)
$chart_data = $wpdb->get_results("
    SELECT DATE_FORMAT(invoice_date, '%Y-%m') as month, SUM(total_amount) as total
    FROM $table_invoices
    WHERE invoice_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(invoice_date, '%Y-%m')
    ORDER BY month ASC
");

// Recent Activity
$recent_sales = $wpdb->get_results("
    SELECT i.*, c.name as customer_name
    FROM $table_invoices i
    LEFT JOIN {$wpdb->prefix}ac_is_customers c ON i.customer_id = c.id
    ORDER BY i.invoice_date DESC
    LIMIT 8
");
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('لوحة المعلومات / التقارير', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:10px;">
        <a href="<?php echo add_query_arg('ac_view', 'add-product'); ?>" class="ac-is-btn"><?php _e('إضافة منتج', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="ac-is-btn" style="background:#059669;"><?php _e('بيع جديد', 'ac-inventory-system'); ?></a>
    </div>
</div>

<!-- Horizontal Professional Metrics -->
<div class="ac-is-metrics-row">
    <div class="ac-is-metric-card" style="border-right-color: #059669;">
        <div class="ac-is-metric-icon" style="background: #ecfdf5; color: #059669;">
            <span class="dashicons dashicons-cart"></span>
        </div>
        <div class="ac-is-metric-content">
            <div class="ac-is-metric-title"><?php _e('مبيعات اليوم', 'ac-inventory-system'); ?></div>
            <div class="ac-is-metric-value"><?php echo number_format($today_sales_total, 2); ?> <small style="font-size:0.8rem;">EGP</small></div>
            <div style="font-size: 0.75rem; color: #059669; font-weight: 600;"><?php echo $today_sales_count; ?> <?php _e('عملية', 'ac-inventory-system'); ?></div>
        </div>
    </div>

    <div class="ac-is-metric-card" style="border-right-color: #dc2626;">
        <div class="ac-is-metric-icon" style="background: #fef2f2; color: #dc2626;">
            <span class="dashicons dashicons-warning"></span>
        </div>
        <div class="ac-is-metric-content">
            <div class="ac-is-metric-title"><?php _e('تنبيهات المخزون', 'ac-inventory-system'); ?></div>
            <div class="ac-is-metric-value" style="color: #dc2626;"><?php echo $low_stock_count; ?></div>
            <div style="font-size: 0.75rem; color: #64748b;"><?php _e('منتجات منخفضة', 'ac-inventory-system'); ?></div>
        </div>
    </div>

    <div class="ac-is-metric-card" style="border-right-color: #2563eb;">
        <div class="ac-is-metric-icon" style="background: #eff6ff; color: #2563eb;">
            <span class="dashicons dashicons-database"></span>
        </div>
        <div class="ac-is-metric-content">
            <div class="ac-is-metric-title"><?php _e('إجمالي المخزون', 'ac-inventory-system'); ?></div>
            <div class="ac-is-metric-value"><?php echo number_format($total_stock_qty); ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;"><?php echo $total_products; ?> <?php _e('صنف مسجل', 'ac-inventory-system'); ?></div>
        </div>
    </div>
</div>

<div class="ac-is-grid" style="grid-template-columns: 1.5fr 1fr; gap:20px; align-items: stretch;">
    <!-- Monthly Sales Chart -->
    <div class="ac-is-card" style="display:flex; flex-direction:column;">
        <h3><?php _e('تحليل المبيعات الشهري', 'ac-inventory-system'); ?></h3>
        <div style="flex-grow:1; display:flex; align-items:center;">
            <canvas id="ac-is-sales-chart" height="150"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="ac-is-card" style="display:flex; flex-direction:column;">
        <h3><?php _e('آخر عمليات البيع', 'ac-inventory-system'); ?></h3>
        <div class="ac-is-recent-list" style="flex-grow:1;">
            <?php foreach($recent_sales as $rs): ?>
                <div style="display:flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                    <div style="font-size:0.8rem;">
                        <strong>#<?php echo $rs->id; ?> - <?php echo esc_html($rs->customer_name ?: __('عميل سريع', 'ac-inventory-system')); ?></strong><br>
                        <small style="color:#94a3b8; font-size:0.7rem;"><?php echo date('H:i - d/m', strtotime($rs->invoice_date)); ?></small>
                    </div>
                    <div style="text-align: left;">
                        <span style="font-weight:700; color:var(--ac-primary); font-size:0.85rem;"><?php echo number_format($rs->total_amount, 2); ?></span><br>
                        <a href="<?php echo add_query_arg(array('ac_view' => 'invoice', 'invoice_id' => $rs->id)); ?>" style="font-size:0.7rem; text-decoration:none; color:#64748b;"><?php _e('عرض', 'ac-inventory-system'); ?></a>
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
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function($d){ return $d->month; }, $chart_data)); ?>,
                datasets: [{
                    label: '<?php _e('المبيعات', 'ac-inventory-system'); ?>',
                    data: <?php echo json_encode(array_map(function($d){ return (float)$d->total; }, $chart_data)); ?>,
                    backgroundColor: '#2563eb',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
