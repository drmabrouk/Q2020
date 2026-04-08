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

// Top Products
$top_products = $wpdb->get_results("
    SELECT p.name, SUM(s.quantity) as total_qty
    FROM $table_sales s
    JOIN $table_products p ON s.product_id = p.id
    GROUP BY s.product_id
    ORDER BY total_qty DESC
    LIMIT 5
");

// Branch Performance
$branch_stats = $wpdb->get_results("
    SELECT b.name, SUM(s.total_price) as total_sales
    FROM $table_branches b
    LEFT JOIN $table_sales s ON b.id = s.branch_id
    GROUP BY b.id
    ORDER BY total_sales DESC
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

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2><?php _e('لوحة المعلومات / التقارير', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:10px;">
        <a href="<?php echo add_query_arg('ac_view', 'add-product'); ?>" class="ac-is-btn"><?php _e('إضافة منتج', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="ac-is-btn" style="background:#059669;"><?php _e('تسجيل بيع', 'ac-inventory-system'); ?></a>
    </div>
</div>

<div class="ac-is-summary-cards">
    <div class="ac-is-card">
        <h3><?php _e('مبيعات اليوم', 'ac-inventory-system'); ?></h3>
        <div class="value"><?php echo number_format($today_sales_total, 2); ?> <small>EGP</small></div>
        <small><?php echo $today_sales_count; ?> <?php _e('عمليات بيع', 'ac-inventory-system'); ?></small>
    </div>
    <div class="ac-is-card">
        <h3><?php _e('تنبيهات المخزون', 'ac-inventory-system'); ?></h3>
        <div class="value" style="color:var(--ac-danger-text);"><?php echo $low_stock_count; ?></div>
        <small><?php _e('منتجات منخفضة', 'ac-inventory-system'); ?></small>
    </div>
    <div class="ac-is-card">
        <h3><?php _e('إجمالي المنتجات', 'ac-inventory-system'); ?></h3>
        <div class="value"><?php echo $total_products; ?></div>
        <small><?php _e('منتج مسجل', 'ac-inventory-system'); ?></small>
    </div>
</div>

<div class="ac-is-grid" style="margin-top:30px;">
    <!-- Top Products -->
    <div class="ac-is-report-section" style="background:#fff; padding:20px; border-radius:12px; border:1px solid var(--ac-border);">
        <h3><?php _e('الأكثر مبيعاً', 'ac-inventory-system'); ?></h3>
        <table class="ac-is-table" style="margin-top:10px;">
            <thead>
                <tr>
                    <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($top_products as $tp): ?>
                    <tr>
                        <td><?php echo esc_html($tp->name); ?></td>
                        <td><span class="ac-is-capsule capsule-primary"><?php echo $tp->total_qty; ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($top_products)) echo '<tr><td colspan="2">'.__('لا توجد بيانات', 'ac-inventory-system').'</td></tr>'; ?>
            </tbody>
        </table>
    </div>

    <!-- Branch Performance -->
    <div class="ac-is-report-section" style="background:#fff; padding:20px; border-radius:12px; border:1px solid var(--ac-border);">
        <h3><?php _e('أداء الفروع', 'ac-inventory-system'); ?></h3>
        <table class="ac-is-table" style="margin-top:10px;">
            <thead>
                <tr>
                    <th><?php _e('الفرع', 'ac-inventory-system'); ?></th>
                    <th><?php _e('المبيعات', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($branch_stats as $bs): ?>
                    <tr>
                        <td><?php echo esc_html($bs->name); ?></td>
                        <td><strong><?php echo number_format($bs->total_sales ?: 0, 2); ?> <small>EGP</small></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="ac-is-report-section" style="margin-top:30px; background:#fff; padding:20px; border-radius:12px; border:1px solid var(--ac-border);">
    <h3><?php _e('آخر عمليات البيع', 'ac-inventory-system'); ?></h3>
    <table class="ac-is-table" style="margin-top:10px;">
        <thead>
            <tr>
                <th><?php _e('التاريخ', 'ac-inventory-system'); ?></th>
                <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                <th><?php _e('الإجمالي', 'ac-inventory-system'); ?></th>
                <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($recent_sales as $rs): ?>
                <tr>
                    <td><?php echo $rs->sale_date; ?></td>
                    <td><?php echo esc_html($rs->product_name); ?></td>
                    <td><?php echo number_format($rs->total_price, 2); ?> EGP</td>
                    <td><a href="<?php echo add_query_arg(array('ac_view' => 'invoice', 'sale_id' => $rs->id)); ?>" class="ac-is-btn" style="padding:4px 10px; font-size:0.8rem; background:#64748b;"><?php _e('فاتورة', 'ac-inventory-system'); ?></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
