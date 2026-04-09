<div class="ac-is-dashboard" id="ac-is-system-root">
    <aside class="ac-is-sidebar">
        <div class="ac-is-sidebar-logo">
            <h2><?php
                global $wpdb;
                echo esc_html($wpdb->get_var("SELECT setting_value FROM {$wpdb->prefix}ac_is_settings WHERE setting_key = 'system_name'") ?: 'نظام البيع');
            ?></h2>
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
                <a href="<?php echo add_query_arg('ac_view', 'settings'); ?>" class="<?php echo (isset($_GET['ac_view']) && $_GET['ac_view'] == 'settings') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-admin-generic"></span> <?php _e('الإعدادات', 'ac-inventory-system'); ?>
                </a>
            <?php endif; ?>
        </nav>

        <div class="ac-is-sidebar-footer">
            <div class="ac-is-top-controls">
                <button id="ac-is-fullscreen-btn" class="control-btn" title="<?php _e('ملء الشاشة', 'ac-inventory-system'); ?>">
                    <span class="dashicons dashicons-fullscreen-alt"></span>
                </button>
                <button id="ac-is-refresh-btn" class="control-btn" title="<?php _e('تحديث البيانات', 'ac-inventory-system'); ?>">
                    <span class="dashicons dashicons-update"></span>
                </button>
                <button id="ac-is-logout-btn" class="control-btn logout" title="<?php _e('تسجيل الخروج', 'ac-inventory-system'); ?>">
                    <span class="dashicons dashicons-logout"></span>
                </button>
            </div>
        </div>
    </aside>

    <div id="ac-is-sync-loader" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); background:var(--ac-primary); color:#fff; padding:10px 20px; border-radius:30px; z-index:10000; box-shadow:0 4px 12px rgba(0,0,0,0.2); font-weight:600;">
        <span class="dashicons dashicons-update spin" style="margin-left:8px; vertical-align:middle;"></span>
        <span class="loader-text"><?php _e('جارٍ تحميل البيانات...', 'ac-inventory-system'); ?></span>
    </div>

    <!-- Scan Confirmation Overlay -->
    <div id="ac-is-scan-conf-overlay" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); padding:30px; border-radius:20px; z-index:10002; flex-direction:column; align-items:center; box-shadow:0 10px 40px rgba(0,0,0,0.2); border: 2px solid #059669; animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        <div style="width:80px; height:80px; background:#dcfce7; color:#059669; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
            <span class="dashicons dashicons-yes-alt" style="font-size:50px; width:50px; height:50px;"></span>
        </div>
        <h2 style="margin:0; color:#1e293b; font-size:1.4rem;"><?php _e('تم إضافة المنتج', 'ac-inventory-system'); ?></h2>
    </div>

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
