<?php
global $wpdb;
$staff = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ac_is_staff ORDER BY id DESC" );
$fullscreen_pass = $wpdb->get_var( "SELECT setting_value FROM {$wpdb->prefix}ac_is_settings WHERE setting_key = 'fullscreen_password'" );
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('الإعدادات / إدارة المستخدمين', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-grid">
    <!-- Staff Management -->
    <div class="ac-is-card">
        <h3><?php _e('إدارة طاقم العمل', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-staff-form" style="margin-bottom:20px; padding:15px; background:#f8fafc; border:1px solid #e2e8f0;">
            <div class="ac-is-form-group">
                <label><?php _e('اسم المستخدم', 'ac-inventory-system'); ?></label>
                <input type="text" name="staff_username" required>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('كلمة المرور', 'ac-inventory-system'); ?></label>
                <input type="password" name="staff_password" required>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('الاسم بالكامل', 'ac-inventory-system'); ?></label>
                <input type="text" name="staff_name" required>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('الصلاحية', 'ac-inventory-system'); ?></label>
                <select name="staff_role">
                    <option value="staff"><?php _e('موظف مبيعات', 'ac-inventory-system'); ?></option>
                    <option value="admin"><?php _e('مدير نظام', 'ac-inventory-system'); ?></option>
                </select>
            </div>
            <button type="submit" class="ac-is-btn" style="width:100%;"><?php _e('إضافة موظف جديد', 'ac-inventory-system'); ?></button>
        </form>

        <table class="ac-is-table">
            <thead>
                <tr>
                    <th><?php _e('المستخدم', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الاسم', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الصلاحية', 'ac-inventory-system'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($staff as $s): ?>
                    <tr>
                        <td><?php echo esc_html($s->username); ?></td>
                        <td><?php echo esc_html($s->name); ?></td>
                        <td><?php echo ($s->role == 'admin' ? __('مدير', 'ac-inventory-system') : __('موظف', 'ac-inventory-system')); ?></td>
                        <td>
                            <?php if($s->username != 'admin'): ?>
                                <button class="ac-is-delete-staff" data-id="<?php echo $s->id; ?>" style="background:none; border:none; color:red; cursor:pointer;"><span class="dashicons dashicons-trash"></span></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- System Settings -->
    <div class="ac-is-card">
        <h3><?php _e('إعدادات النظام', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-system-settings-form">
            <div class="ac-is-form-group">
                <label><?php _e('كلمة مرور الخروج من ملء الشاشة', 'ac-inventory-system'); ?></label>
                <input type="text" name="fullscreen_password" value="<?php echo esc_attr($fullscreen_pass); ?>" required>
                <p style="font-size:0.75rem; color:#64748b; margin-top:5px;"><?php _e('هذه هي كلمة المرور المطلوبة عند محاولة الخروج من وضع ملء الشاشة.', 'ac-inventory-system'); ?></p>
            </div>
            <button type="submit" class="ac-is-btn" style="background:#1e293b;"><?php _e('حفظ الإعدادات', 'ac-inventory-system'); ?></button>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add Staff
    $('#ac-is-staff-form').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize() + '&action=ac_is_add_staff&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('<?php _e('تمت إضافة الموظف بنجاح', 'ac-inventory-system'); ?>');
                location.reload();
            } else {
                alert(response.data);
            }
        });
    });

    // Delete Staff
    $('.ac-is-delete-staff').on('click', function() {
        if (!confirm('<?php _e('هل أنت متأكد من حذف هذا الموظف؟', 'ac-inventory-system'); ?>')) return;
        const id = $(this).data('id');
        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_delete_staff',
            id: id,
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });

    // Save System Settings
    $('#ac-is-system-settings-form').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize() + '&action=ac_is_save_settings&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('<?php _e('تم حفظ الإعدادات بنجاح', 'ac-inventory-system'); ?>');
                location.reload();
            }
        });
    });
});
</script>
