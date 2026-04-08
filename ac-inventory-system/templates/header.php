<div class="ac-is-dashboard">
    <aside class="ac-is-sidebar">
        <div class="ac-is-sidebar-logo">
            <h2>AC System</h2>
        </div>
        <nav class="ac-is-sidebar-nav">
            <a href="<?php echo add_query_arg('ac_view', 'dashboard'); ?>" class="<?php echo (!isset($_GET['ac_view']) || $_GET['ac_view'] == 'dashboard') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-dashboard"></span> <?php _e('لوحة المعلومات', 'ac-inventory-system'); ?>
            </a>
            <a href="<?php echo add_query_arg('ac_view', 'inventory'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'inventory') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-database"></span> <?php _e('إدارة المخزون', 'ac-inventory-system'); ?>
            </a>
            <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'sales') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-cart"></span> <?php _e('تسجيل بيع', 'ac-inventory-system'); ?>
            </a>
            <a href="<?php echo add_query_arg('ac_view', 'sales-history'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'sales-history') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-list-view"></span> <?php _e('سجل المبيعات', 'ac-inventory-system'); ?>
            </a>
            <?php if ( current_user_can('manage_options') ) : ?>
                <a href="<?php echo add_query_arg('ac_view', 'branches'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'branches') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-store"></span> <?php _e('الفروع', 'ac-inventory-system'); ?>
                </a>
            <?php endif; ?>
        </nav>
    </aside>
    <main class="ac-is-main-content">
        <div class="ac-is-content-inner">
