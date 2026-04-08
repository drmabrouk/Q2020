<?php
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $product_id ? AC_IS_Inventory::get_product($product_id) : null;
$branches = AC_IS_Inventory::get_branches();
?>

<h2><?php echo $product ? __('تعديل منتج', 'ac-inventory-system') : __('إضافة منتج جديد', 'ac-inventory-system'); ?></h2>

<form id="ac-is-product-form">
    <input type="hidden" name="id" value="<?php echo $product_id; ?>">
    <div class="ac-is-form-group">
        <label><?php _e('اسم المنتج', 'ac-inventory-system'); ?></label>
        <input type="text" name="name" value="<?php echo $product ? esc_attr($product->name) : ''; ?>" required>
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('التصنيف', 'ac-inventory-system'); ?></label>
        <select name="category">
            <option value="ac" <?php selected($product ? $product->category : '', 'ac'); ?>><?php _e('مكيفات', 'ac-inventory-system'); ?></option>
            <option value="cooling" <?php selected($product ? $product->category : '', 'cooling'); ?>><?php _e('أنظمة تبريد', 'ac-inventory-system'); ?></option>
            <option value="filter" <?php selected($product ? $product->category : '', 'filter'); ?>><?php _e('فلاتر مياه', 'ac-inventory-system'); ?></option>
        </select>
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('السعر الأصلي', 'ac-inventory-system'); ?></label>
        <input type="number" step="0.01" name="original_price" value="<?php echo $product ? $product->original_price : '0.00'; ?>">
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('الخصم', 'ac-inventory-system'); ?></label>
        <input type="number" step="0.01" name="discount" value="<?php echo $product ? $product->discount : '0.00'; ?>">
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('السعر النهائي', 'ac-inventory-system'); ?></label>
        <input type="number" step="0.01" name="final_price" value="<?php echo $product ? $product->final_price : '0.00'; ?>">
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('كمية المخزون', 'ac-inventory-system'); ?></label>
        <input type="number" name="stock_quantity" value="<?php echo $product ? $product->stock_quantity : '0'; ?>">
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('الفرع', 'ac-inventory-system'); ?></label>
        <select name="branch_id">
            <?php foreach($branches as $branch): ?>
                <option value="<?php echo $branch->id; ?>" <?php selected($product ? $product->branch_id : '', $branch->id); ?>><?php echo esc_html($branch->name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="ac-is-form-group">
        <label><?php _e('رابط الصورة', 'ac-inventory-system'); ?></label>
        <input type="text" name="image_url" id="ac-is-image-url" value="<?php echo $product ? esc_url($product->image_url) : ''; ?>">
        <button type="button" class="ac-is-upload-btn ac-is-btn" style="background:#6c757d; margin-top:5px;"><?php _e('رفع صورة', 'ac-inventory-system'); ?></button>
    </div>
    <button type="submit" class="ac-is-btn"><?php _e('حفظ المنتج', 'ac-inventory-system'); ?></button>
</form>
