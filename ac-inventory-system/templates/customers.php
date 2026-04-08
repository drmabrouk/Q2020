<?php
$customers = AC_IS_Customers::get_all_customers();
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('إدارة العملاء', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-search-filters" style="margin-bottom:25px; display:flex; gap:15px; background:#fff; padding:20px; border:1px solid var(--ac-border);">
    <input type="text" id="ac-is-customer-search" placeholder="<?php _e('ابحث بالاسم، الهاتف، أو السيريال...', 'ac-inventory-system'); ?>" style="flex:1; padding:12px; border:1px solid #ddd; border-radius:6px;">
</div>

<div style="background:#fff; border:1px solid var(--ac-border); overflow:hidden;">
    <table class="ac-is-table" id="ac-is-customer-table">
        <thead>
            <tr>
                <th><?php _e('العميل', 'ac-inventory-system'); ?></th>
                <th><?php _e('بيانات التواصل', 'ac-inventory-system'); ?></th>
                <th><?php _e('إجمالي المشتريات', 'ac-inventory-system'); ?></th>
                <th><?php _e('صافي الربح', 'ac-inventory-system'); ?></th>
                <th><?php _e('العمليات', 'ac-inventory-system'); ?></th>
                <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $c):
                $profit_class = ($c->net_profit > 0) ? 'capsule-success' : 'capsule-warning';
            ?>
                <tr>
                    <td><strong><?php echo esc_html($c->name); ?></strong></td>
                    <td>
                        <small><?php echo esc_html($c->phone); ?></small><br>
                        <small style="color:#64748b;"><?php echo esc_html($c->email); ?></small>
                    </td>
                    <td><span style="font-weight:700; color:var(--ac-primary);"><?php echo number_format($c->total_revenue ?: 0, 2); ?> EGP</span></td>
                    <td><span class="ac-is-capsule <?php echo $profit_class; ?>"><?php echo number_format($c->net_profit ?: 0, 2); ?> EGP</span></td>
                    <td><?php echo $c->total_invoices; ?> <?php _e('فاتورة', 'ac-inventory-system'); ?></td>
                    <td>
                        <a href="<?php echo add_query_arg(array('ac_view' => 'sales-history', 'sale_search' => $c->phone)); ?>" class="ac-is-btn" style="padding:4px 8px; font-size:0.75rem; background:#64748b;"><?php _e('سجل الفواتير', 'ac-inventory-system'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($customers)) echo '<tr><td colspan="6" style="text-align:center; padding:40px;">'.__('لا يوجد عملاء مسجلين بعد', 'ac-inventory-system').'</td></tr>'; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('#ac-is-customer-search').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('#ac-is-customer-table tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(query));
        });
    });
});
</script>
