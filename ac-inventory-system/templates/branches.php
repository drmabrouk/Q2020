<?php
$branches = AC_IS_Inventory::get_branches();
?>
<h2><?php _e('إدارة الفروع', 'ac-inventory-system'); ?></h2>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
    <div>
        <h3><?php _e('إضافة فرع جديد', 'ac-inventory-system'); ?></h3>
        <form id="ac-is-branch-form">
            <div class="ac-is-form-group">
                <label><?php _e('اسم الفرع', 'ac-inventory-system'); ?></label>
                <input type="text" name="name" required>
            </div>
            <div class="ac-is-form-group">
                <label><?php _e('الموقع', 'ac-inventory-system'); ?></label>
                <textarea name="location"></textarea>
            </div>
            <button type="submit" class="ac-is-btn"><?php _e('حفظ الفرع', 'ac-inventory-system'); ?></button>
        </form>
    </div>
    <div>
        <h3><?php _e('قائمة الفروع', 'ac-inventory-system'); ?></h3>
        <table class="ac-is-table">
            <thead>
                <tr>
                    <th><?php _e('الاسم', 'ac-inventory-system'); ?></th>
                    <th><?php _e('الموقع', 'ac-inventory-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if($branches): foreach($branches as $branch): ?>
                    <tr>
                        <td><?php echo esc_html($branch->name); ?></td>
                        <td><?php echo esc_html($branch->location); ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="2"><?php _e('لا توجد فروع.', 'ac-inventory-system'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
