<?php
$month = isset($_GET['payroll_month']) ? sanitize_text_field($_GET['payroll_month']) : date('Y-m');
$payroll_data = AC_IS_Payroll::get_staff_payroll($month);

global $wpdb;
$staff_list = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ac_is_staff ORDER BY name ASC" );
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('نظام المرتبات والحضور', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-grid" style="grid-template-columns: 1fr 2fr; gap:20px;">
    <!-- Attendance Entry -->
    <div class="ac-is-card">
        <h3><?php _e('تسجيل حضور وانصراف', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-attendance-form" style="margin-top:15px;">
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
                    <input type="time" name="check_in">
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('الانصراف', 'ac-inventory-system'); ?></label>
                    <input type="time" name="check_out">
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
                    <th><?php _e('الراتب الأساسي', 'ac-inventory-system'); ?></th>
                    <th><?php _e('أيام الحضور', 'ac-inventory-system'); ?></th>
                    <th><?php _e('إجمالي الساعات', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الخصومات', 'ac-inventory-system'); ?></th>
                    <th><?php _e('صافي المستحق', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($payroll_data as $p): ?>
                    <tr>
                        <td><strong><?php echo esc_html($p->name); ?></strong></td>
                        <td><?php echo number_format($p->base_salary, 2); ?></td>
                        <td><span class="ac-is-capsule capsule-primary"><?php echo $p->days_present; ?></span></td>
                        <td><?php echo $p->total_hours; ?> <?php _e('ساعة', 'ac-inventory-system'); ?></td>
                        <td><span style="color:red;"><?php echo number_format($p->deductions, 2); ?></span></td>
                        <td><span style="font-weight:800; color:#059669; font-size:1rem;"><?php echo number_format($p->net_salary, 2); ?> EGP</span></td>
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
