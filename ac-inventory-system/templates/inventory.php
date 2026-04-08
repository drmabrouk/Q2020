<?php
$products = AC_IS_Inventory::get_products();
?>
<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center;">
    <h2><?php _e('قائمة المخزون', 'ac-inventory-system'); ?></h2>
    <a href="<?php echo add_query_arg('ac_view', 'add-product'); ?>" class="ac-is-btn"><?php _e('إضافة منتج', 'ac-inventory-system'); ?></a>
</div>

<table class="ac-is-table">
    <thead>
        <tr>
            <th><?php _e('الصورة', 'ac-inventory-system'); ?></th>
            <th><?php _e('الاسم', 'ac-inventory-system'); ?></th>
            <th><?php _e('السعر', 'ac-inventory-system'); ?></th>
            <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
            <th><?php _e('الفرع', 'ac-inventory-system'); ?></th>
            <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if ( $products ) : foreach ( $products as $product ) : ?>
            <tr>
                <td><?php if($product->image_url): ?><img src="<?php echo esc_url($product->image_url); ?>" class="ac-is-product-img"><?php endif; ?></td>
                <td><?php echo esc_html( $product->name ); ?></td>
                <td><?php echo esc_html( $product->final_price ); ?></td>
                <td><?php echo esc_html( $product->stock_quantity ); ?></td>
                <td><?php 
                    $branches = AC_IS_Inventory::get_branches();
                    foreach($branches as $b) {
                        if($b->id == $product->branch_id) {
                            echo esc_html($b->name);
                            break;
                        }
                    }
                ?></td>
                <td>
                    <a href="<?php echo add_query_arg( array('ac_view' => 'edit-product', 'id' => $product->id) ); ?>"><?php _e('تعديل', 'ac-inventory-system'); ?></a> |
                    <a href="#" class="ac-is-delete-product" data-id="<?php echo $product->id; ?>" style="color:red;"><?php _e('حذف', 'ac-inventory-system'); ?></a>
                </td>
            </tr>
        <?php endforeach; else : ?>
            <tr><td colspan="6"><?php _e('لا توجد منتجات.', 'ac-inventory-system'); ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>
