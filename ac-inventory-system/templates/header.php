<div class="ac-is-dashboard">
    <nav class="ac-is-nav">
        <a href="<?php echo add_query_arg('ac_view', 'dashboard'); ?>"><?php _e('الرئيسية', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'inventory'); ?>"><?php _e('المخزون', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'sales'); ?>"><?php _e('عملية بيع', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'sales-history'); ?>"><?php _e('السجلات', 'ac-inventory-system'); ?></a>
        <a href="<?php echo add_query_arg('ac_view', 'reports'); ?>"><?php _e('التقارير', 'ac-inventory-system'); ?></a>
        <?php if ( current_user_can('manage_options') ) : ?>
            <a href="<?php echo add_query_arg('ac_view', 'branches'); ?>"><?php _e('الفروع', 'ac-inventory-system'); ?></a>
        <?php endif; ?>
    </nav>
    <div class="ac-is-content">
