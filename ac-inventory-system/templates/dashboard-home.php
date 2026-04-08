<h2><?php _e('لوحة التحكم', 'ac-inventory-system'); ?></h2>

<div class="ac-is-summary-cards">
    <div class="ac-is-card">
        <h3><?php _e('إجمالي المنتجات', 'ac-inventory-system'); ?></h3>
        <div class="value"><?php 
            global $wpdb;
            echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ac_is_products"); 
        ?></div>
    </div>
    <div class="ac-is-card">
        <h3><?php _e('مبيعات اليوم', 'ac-inventory-system'); ?></h3>
        <div class="value"><?php 
            global $wpdb;
            echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ac_is_sales WHERE DATE(sale_date) = CURDATE()"); 
        ?></div>
    </div>
</div>

<div class="ac-is-quick-actions">
    <a href="<?php echo add_query_arg('ac_view', 'add-product'); ?>" class="ac-is-btn"><?php _e('إضافة منتج جديد', 'ac-inventory-system'); ?></a>
    <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="ac-is-btn" style="background:#007bff;"><?php _e('تسجيل عملية بيع', 'ac-inventory-system'); ?></a>
</div>
