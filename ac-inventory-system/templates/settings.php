<?php
global $wpdb;
$staff = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ac_is_staff ORDER BY id DESC" );
$settings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ac_is_settings", OBJECT_K );
$fullscreen_pass = $settings['fullscreen_password']->setting_value ?? '123456789';
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('الإعدادات العامة للنظام', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-grid">
    <!-- Staff Management -->
    <div class="ac-is-card">
        <h3><?php _e('إدارة طاقم العمل', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-staff-form" style="margin-bottom:20px; padding:15px; background:#f8fafc; border:1px solid #e2e8f0;">
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('اسم المستخدم', 'ac-inventory-system'); ?></label>
                    <input type="text" name="staff_username" required>
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('كلمة المرور', 'ac-inventory-system'); ?></label>
                    <input type="password" name="staff_password" required>
                </div>
            </div>
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('الاسم بالكامل', 'ac-inventory-system'); ?></label>
                    <input type="text" name="staff_name" required>
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('الصلاحية', 'ac-inventory-system'); ?></label>
                    <select name="staff_role">
                        <option value="employee"><?php _e('موظف مبيعات', 'ac-inventory-system'); ?></option>
                        <option value="manager"><?php _e('مدير مبيعات', 'ac-inventory-system'); ?></option>
                        <option value="admin"><?php _e('مدير نظام', 'ac-inventory-system'); ?></option>
                    </select>
                </div>
            </div>
            <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('الراتب الأساسي', 'ac-inventory-system'); ?></label>
                    <input type="number" step="0.01" name="base_salary" value="0.00">
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('أيام العمل بالشهر', 'ac-inventory-system'); ?></label>
                    <input type="number" name="working_days" value="26">
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('ساعات العمل باليوم', 'ac-inventory-system'); ?></label>
                    <input type="number" name="working_hours" value="8">
                </div>
            </div>
            <button type="submit" class="ac-is-btn" style="width:100%; margin-top:10px;"><?php _e('إضافة موظف جديد', 'ac-inventory-system'); ?></button>
        </form>

        <table class="ac-is-table">
            <thead>
                <tr>
                    <th><?php _e('المستخدم', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الاسم', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الصلاحية', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الراتب', 'ac-inventory-system'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($staff as $s): ?>
                    <tr>
                        <td><?php echo esc_html($s->username); ?></td>
                        <td><?php echo esc_html($s->name); ?></td>
                        <td><?php
                            if($s->role == 'admin') _e('مدير نظام', 'ac-inventory-system');
                            elseif($s->role == 'manager') _e('مدير مبيعات', 'ac-inventory-system');
                            else _e('موظف', 'ac-inventory-system');
                        ?></td>
                        <td><?php echo number_format($s->base_salary, 2); ?></td>
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

    <!-- System Identity -->
    <div class="ac-is-card">
        <h3><?php _e('هوية النظام والشركة', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-system-settings-form">
            <div class="ac-is-form-group">
                <label><?php _e('اسم النظام (يظهر في القائمة)', 'ac-inventory-system'); ?></label>
                <input type="text" name="system_name" value="<?php echo esc_attr($settings['system_name']->setting_value ?? 'نظام البيع'); ?>" required>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('اسم الشركة / المؤسسة', 'ac-inventory-system'); ?></label>
                <input type="text" name="company_name" value="<?php echo esc_attr($settings['company_name']->setting_value ?? get_bloginfo('name')); ?>" required>
            </div>
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('رقم الهاتف للتواصل', 'ac-inventory-system'); ?></label>
                    <input type="text" name="company_phone" value="<?php echo esc_attr($settings['company_phone']->setting_value ?? ''); ?>">
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('البريد الإلكتروني', 'ac-inventory-system'); ?></label>
                    <input type="email" name="company_email" value="<?php echo esc_attr($settings['company_email']->setting_value ?? ''); ?>">
                </div>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('العنوان بالتفصيل', 'ac-inventory-system'); ?></label>
                <textarea name="company_address" rows="2"><?php echo esc_textarea($settings['company_address']->setting_value ?? ''); ?></textarea>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('رابط شعار الشركة (Logo URL)', 'ac-inventory-system'); ?></label>
                <input type="text" name="company_logo" value="<?php echo esc_attr($settings['company_logo']->setting_value ?? ''); ?>">
            </div>
            <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
            <div class="ac-is-form-group">
                <label><?php _e('كلمة مرور الخروج من ملء الشاشة', 'ac-inventory-system'); ?></label>
                <input type="text" name="fullscreen_password" value="<?php echo esc_attr($fullscreen_pass); ?>" required>
            </div>
            <button type="submit" class="ac-is-btn" style="background:#1e293b; width:100%; height:45px;"><?php _e('حفظ كافة الإعدادات', 'ac-inventory-system'); ?></button>
        </form>
    </div>

    <!-- Brand Management -->
    <div class="ac-is-card">
        <h3><?php _e('إدارة العلامات التجارية (البراندات)', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-brand-form" style="margin-bottom:20px; padding:15px; background:#f8fafc; border:1px solid #e2e8f0;">
            <input type="hidden" name="id" id="brand-id">
            <div class="ac-is-form-group">
                <label><?php _e('اسم البراند', 'ac-inventory-system'); ?></label>
                <input type="text" name="name" id="brand-name" required>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('شعار البراند (Logo)', 'ac-inventory-system'); ?></label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="logo_url" id="brand-logo-url">
                    <button type="button" class="ac-is-upload-btn ac-is-btn" style="background:#64748b;"><?php _e('رفع شعار', 'ac-inventory-system'); ?></button>
                </div>
            </div>
            <button type="submit" class="ac-is-btn" style="width:100%;"><?php _e('حفظ البراند', 'ac-inventory-system'); ?></button>
        </form>

        <?php $brands = AC_IS_Brands::get_brands(); ?>
        <table class="ac-is-table">
            <thead>
                <tr>
                    <th><?php _e('الشعار', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الاسم', 'ac-inventory-system'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($brands as $b): ?>
                    <tr data-brand='<?php echo json_encode($b); ?>'>
                        <td><?php if($b->logo_url): ?><img src="<?php echo esc_url($b->logo_url); ?>" style="height:30px;"><?php endif; ?></td>
                        <td><?php echo esc_html($b->name); ?></td>
                        <td>
                            <button class="ac-is-edit-brand" style="background:none; border:none; color:blue; cursor:pointer;"><span class="dashicons dashicons-edit"></span></button>
                            <button class="ac-is-delete-brand" data-id="<?php echo $b->id; ?>" style="background:none; border:none; color:red; cursor:pointer;"><span class="dashicons dashicons-trash"></span></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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

    // Brand logic
    $(document).on('click', '.ac-is-edit-brand', function() {
        const b = $(this).closest('tr').data('brand');
        $('#brand-id').val(b.id);
        $('#brand-name').val(b.name);
        $('#brand-logo-url').val(b.logo_url);
    });

    $('#ac-is-brand-form').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize() + '&action=ac_is_save_brand&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) location.reload();
        });
    });

    $('.ac-is-delete-brand').on('click', function() {
        if (!confirm('حذف هذا البراند؟')) return;
        $.post(ac_is_ajax.ajax_url, {
            action: 'ac_is_delete_brand',
            id: $(this).data('id'),
            nonce: ac_is_ajax.nonce
        }, function(response) {
            if (response.success) location.reload();
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
