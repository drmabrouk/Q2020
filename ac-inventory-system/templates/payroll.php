<?php
$month = isset($_GET['payroll_month']) ? sanitize_text_field($_GET['payroll_month']) : date('Y-m');
$payroll_data = AC_IS_Payroll::get_staff_payroll($month);

global $wpdb;
// Hide admin from staff selection
$staff_list = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ac_is_staff WHERE username != 'admin' ORDER BY name ASC" );
$can_edit_salary = AC_IS_Auth::is_admin() || AC_IS_Auth::is_manager();
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('نظام المرتبات والحضور', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-grid" style="grid-template-columns: 1fr 2fr; gap:20px;">
    <!-- Attendance Entry -->
    <div class="ac-is-card">
        <h3><?php _e('تسجيل حضور وانصراف', 'ac-inventory-system'); ?></h3>
        <p style="font-size:0.8rem; color:#64748b; margin-bottom:15px;"><?php _e('الدوام الرسمي: 09:00 ص - 10:00 م', 'ac-inventory-system'); ?></p>
        <form id="ac-is-attendance-form">
            <div class="ac-is-form-group">
                <label><?php _e('الموظف', 'ac-inventory-system'); ?></label>
                <select name="staff_id" required>
                    <?php foreach($staff_list as $s): ?>
                        <option value="<?php echo $s->id; ?>"><?php echo esc_html($s->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('التاريخ', 'ac-inventory-system'); ?></label>
                <input type="date" name="work_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('الحضور', 'ac-inventory-system'); ?></label>
                    <input type="time" name="check_in" value="09:00">
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('الانصراف', 'ac-inventory-system'); ?></label>
                    <input type="time" name="check_out" value="22:00">
                </div>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('الحالة', 'ac-inventory-system'); ?></label>
                <select name="status">
                    <option value="present"><?php _e('حاضر', 'ac-inventory-system'); ?></option>
                    <option value="absent"><?php _e('غائب', 'ac-inventory-system'); ?></option>
                    <option value="leave"><?php _e('إجازة', 'ac-inventory-system'); ?></option>
                </select>
            </div>
            <button type="submit" class="ac-is-btn" style="width:100%; background:#1e293b;"><?php _e('حفظ السجل', 'ac-inventory-system'); ?></button>
        </form>
    </div>

    <!-- Payroll Summary -->
    <div class="ac-is-card">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:15px;">
            <h3><?php _e('ملخص مرتبات شهر:', 'ac-inventory-system'); ?> <?php echo $month; ?></h3>
            <form method="get">
                <input type="hidden" name="ac_view" value="payroll">
                <input type="month" name="payroll_month" value="<?php echo $month; ?>" onchange="this.form.submit()" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
            </form>
        </div>

        <table class="ac-is-table">
            <thead>
                <tr>
                    <th><?php _e('الموظف', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الراتب', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الحضور', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الخصومات', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الصافي', 'ac-inventory-system'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($payroll_data as $p): ?>
                    <tr>
                        <td><strong><?php echo esc_html($p->name); ?></strong></td>
                        <td><?php echo number_format($p->base_salary, 2); ?></td>
                        <td><span class="ac-is-capsule capsule-primary"><?php echo $p->days_present; ?> <?php _e('يوم', 'ac-inventory-system'); ?></span></td>
                        <td><span style="color:red;"><?php echo number_format($p->deductions, 2); ?></span></td>
                        <td><span style="font-weight:800; color:#059669;"><?php echo number_format($p->net_salary, 2); ?> EGP</span></td>
                        <td>
                            <?php if($can_edit_salary): ?>
                                <a href="<?php echo add_query_arg('ac_view', 'settings'); ?>" title="<?php _e('تعديل الراتب', 'ac-inventory-system'); ?>"><span class="dashicons dashicons-edit"></span></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#ac-is-attendance-form').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize() + '&action=ac_is_record_attendance&nonce=' + ac_is_ajax.nonce;
        $.post(ac_is_ajax.ajax_url, data, function(response) {
            if (response.success) {
                alert('<?php _e('تم تسجيل السجل بنجاح', 'ac-inventory-system'); ?>');
                location.reload();
            }
        });
    });
});
</script>
