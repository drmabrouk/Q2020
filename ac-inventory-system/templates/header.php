<div class="ac-is-dashboard" id="ac-is-system-root">
    <aside class="ac-is-sidebar">
        <div class="ac-is-sidebar-logo">
            <h2>نظام البيع</h2>
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
            <a href="<?php echo add_query_arg('ac_view', 'customers'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'customers') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-groups"></span> <?php _e('إدارة العملاء', 'ac-inventory-system'); ?>
            </a>
            <a href="<?php echo add_query_arg('ac_view', 'payroll'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'payroll') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-money-alt"></span> <?php _e('المرتبات', 'ac-inventory-system'); ?>
            </a>
            <?php if ( AC_IS_Auth::is_admin() ) : ?>
                <a href="<?php echo add_query_arg('ac_view', 'branches'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'branches') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-store"></span> <?php _e('الفروع', 'ac-inventory-system'); ?>
                </a>
                <a href="<?php echo add_query_arg('ac_view', 'settings'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'settings') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-admin-generic"></span> <?php _e('الإعدادات', 'ac-inventory-system'); ?>
                </a>
            <?php endif; ?>
            <a href="#" id="ac-is-logout-btn">
                <span class="dashicons dashicons-logout"></span> <?php _e('تسجيل الخروج', 'ac-inventory-system'); ?>
            </a>
        </nav>

        <div class="ac-is-sidebar-footer" style="padding: 15px; border-top: 1px solid var(--ac-sidebar-hover);">
            <button id="ac-is-fullscreen-btn" class="ac-is-btn" style="width:100%; background:#475569; padding: 10px; font-size: 0.85rem;">
                <span class="dashicons dashicons-fullscreen-alt" style="margin-left:8px;"></span>
                <span class="btn-text"><?php _e('ملء الشاشة', 'ac-inventory-system'); ?></span>
            </button>
        </div>
    </aside>
    <main class="ac-is-main-content">
        <div class="ac-is-content-inner">

<div id="ac-is-unlock-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.95); z-index:9999; align-items:center; justify-content:center; flex-direction:column; color:#fff;">
    <h2 style="margin-bottom:20px;"><?php _e('النظام مغلق - يرجى إدخال كلمة المرور للخروج', 'ac-inventory-system'); ?></h2>
    <div style="display:flex; gap:10px;">
        <input type="password" id="ac-is-unlock-pass" placeholder="********" style="padding:15px; border-radius:8px; border:none; font-size:1.2rem; text-align:center;">
        <button id="ac-is-unlock-submit" class="ac-is-btn" style="background:var(--ac-primary); font-size:1.1rem;"><?php _e('فك القفل', 'ac-inventory-system'); ?></button>
    </div>
    <p id="ac-is-unlock-error" style="color:var(--ac-danger-text); margin-top:15px; display:none;"><?php _e('كلمة المرور غير صحيحة', 'ac-inventory-system'); ?></p>
</div>
