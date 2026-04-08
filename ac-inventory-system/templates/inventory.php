<?php
$products = AC_IS_Inventory::get_products();
?>
<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><?php _e('إدارة المخزون', 'ac-inventory-system'); ?></h2>
    <a href="<?php echo add_query_arg('ac_view', 'add-product'); ?>" class="ac-is-btn"><?php _e('إضافة منتج جديد', 'ac-inventory-system'); ?></a>
</div>

<div class="ac-is-search-filters" style="margin-bottom:20px; display:flex; gap:15px; flex-wrap: wrap; background:#f8fafc; padding:15px; border-radius:8px;">
    <input type="text" id="ac-is-inventory-search" placeholder="<?php _e('ابحث بالاسم، الباركود أو السيريال...', 'ac-inventory-system'); ?>" style="flex:1; min-width:250px; padding:10px; border:1px solid #ddd; border-radius:4px;">
    <select id="ac-is-inventory-category" style="padding:10px; border:1px solid #ddd; border-radius:4px;">
        <option value=""><?php _e('كل التصنيفات', 'ac-inventory-system'); ?></option>
        <option value="ac"><?php _e('مكيفات', 'ac-inventory-system'); ?></option>
        <option value="cooling"><?php _e('أنظمة تبريد', 'ac-inventory-system'); ?></option>
        <option value="filter"><?php _e('فلاتر مياه', 'ac-inventory-system'); ?></option>
    </select>
</div>

<table class="ac-is-table">
    <thead>
        <tr>
            <th><?php _e('الصورة', 'ac-inventory-system'); ?></th>
            <th><?php _e('الاسم والتصنيف', 'ac-inventory-system'); ?></th>
            <th><?php _e('الباركود والسيريال', 'ac-inventory-system'); ?></th>
            <th><?php _e('السعر والخصم', 'ac-inventory-system'); ?></th>
            <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
            <th><?php _e('الفرع', 'ac-inventory-system'); ?></th>
            <th><?php _e('إجراءات', 'ac-inventory-system'); ?></th>
        </tr>
    </thead>
    <tbody id="ac-is-inventory-table-body">
        <?php if ( $products ) : foreach ( $products as $product ) :
            $stock_class = ($product->stock_quantity < 5) ? 'capsule-danger' : (($product->stock_quantity < 15) ? 'capsule-warning' : 'capsule-success');
            $category_name = ($product->category == 'ac') ? __('مكيفات', 'ac-inventory-system') : (($product->category == 'cooling') ? __('تبريد', 'ac-inventory-system') : __('فلاتر', 'ac-inventory-system'));
        ?>
            <tr data-id="<?php echo $product->id; ?>">
                <td><?php if($product->image_url): ?><img src="<?php echo esc_url($product->image_url); ?>" class="ac-is-product-img" style="border-radius:4px; max-width:60px;"><?php endif; ?></td>
                <td>
                    <strong><?php echo esc_html( $product->name ); ?></strong><br>
                    <span class="ac-is-capsule capsule-primary"><?php echo $category_name; ?></span>
                    <?php if($product->subcategory): ?><small style="color:#666;"> (<?php echo esc_html($product->subcategory); ?>)</small><?php endif; ?>
                </td>
                <td>
                    <small>B: <?php echo esc_html($product->barcode ? $product->barcode : 'N/A'); ?></small><br>
                    <small>S/N: <?php echo esc_html($product->serial_number ? $product->serial_number : 'N/A'); ?></small>
                </td>
                <td>
                    <span style="font-weight:bold; color:var(--ac-primary);"><?php echo number_format($product->final_price, 2); ?></span><br>
                    <?php if($product->discount > 0): ?>
                        <del style="font-size:0.8rem; color:#999;"><?php echo number_format($product->original_price, 2); ?></del>
                        <span class="ac-is-capsule capsule-danger" style="font-size:0.7rem; padding: 1px 5px;"><?php echo number_format($product->discount, 2); ?>-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="ac-is-capsule <?php echo $stock_class; ?>"><?php echo esc_html( $product->stock_quantity ); ?></span>
                </td>
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
                    <div style="display:flex; gap:5px;">
                        <a href="<?php echo add_query_arg( array('ac_view' => 'edit-product', 'id' => $product->id) ); ?>" class="ac-is-btn" style="padding: 5px 10px; font-size:0.8rem; background:#4a90e2;"><?php _e('تعديل', 'ac-inventory-system'); ?></a>
                        <button class="ac-is-btn ac-is-print-barcode" data-barcode="<?php echo esc_attr($product->barcode); ?>" data-name="<?php echo esc_attr($product->name); ?>" style="padding: 5px 10px; font-size:0.8rem; background:#6c757d;"><?php _e('باركود', 'ac-inventory-system'); ?></button>
                        <button class="ac-is-delete-product ac-is-btn" data-id="<?php echo $product->id; ?>" style="padding: 5px 10px; font-size:0.8rem; background:#e53e3e;"><?php _e('حذف', 'ac-inventory-system'); ?></button>
                    </div>
                </td>
            </tr>
        <?php endforeach; else : ?>
            <tr><td colspan="7" style="text-align:center;"><?php _e('لا توجد منتجات.', 'ac-inventory-system'); ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="barcode-print-section" style="display:none;">
    <div class="barcode-sticker">
        <div class="product-name" id="print-product-name"></div>
        <svg id="print-barcode-svg"></svg>
    </div>
</div>
