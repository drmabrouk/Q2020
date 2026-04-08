<?php
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $product_id ? AC_IS_Inventory::get_product($product_id) : null;
$branches = AC_IS_Inventory::get_branches();
?>

<h2><?php echo $product ? __('تعديل منتج', 'ac-inventory-system') : __('إضافة منتج جديد', 'ac-inventory-system'); ?></h2>

<form id="ac-is-product-form" class="ac-is-form">
    <input type="hidden" name="id" value="<?php echo $product_id; ?>">

    <div class="ac-is-grid">
        <div class="ac-is-form-group">
            <label><?php _e('اسم المنتج', 'ac-inventory-system'); ?></label>
            <input type="text" name="name" placeholder="<?php _e('أدخل اسم المنتج هنا', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->name) : ''; ?>" required>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('التصنيف الرئيسي', 'ac-inventory-system'); ?></label>
            <select name="category" id="ac-is-category">
                <option value="ac" <?php selected($product ? $product->category : '', 'ac'); ?>><?php _e('مكيفات', 'ac-inventory-system'); ?></option>
                <option value="cooling" <?php selected($product ? $product->category : '', 'cooling'); ?>><?php _e('أنظمة تبريد', 'ac-inventory-system'); ?></option>
                <option value="filter" <?php selected($product ? $product->category : '', 'filter'); ?>><?php _e('فلاتر مياه', 'ac-inventory-system'); ?></option>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('التصنيف الفرعي', 'ac-inventory-system'); ?></label>
            <input type="text" name="subcategory" placeholder="<?php _e('مثال: سبليت، مركزي، 5 مراحل', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->subcategory) : ''; ?>">
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
            <label><?php _e('السعر الأصلي', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="original_price" id="original-price" placeholder="0.00" value="<?php echo $product ? $product->original_price : '0.00'; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الخصم', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="discount" id="discount" placeholder="0.00" value="<?php echo $product ? $product->discount : '0.00'; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('السعر النهائي', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="final_price" id="final-price" placeholder="0.00" value="<?php echo $product ? $product->final_price : '0.00'; ?>" readonly>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('كمية المخزون', 'ac-inventory-system'); ?></label>
            <input type="number" name="stock_quantity" placeholder="0" value="<?php echo $product ? $product->stock_quantity : '0'; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الرقم التسلسلي (Serial Number)', 'ac-inventory-system'); ?></label>
            <input type="text" name="serial_number" placeholder="<?php _e('أدخل الرقم التسلسلي الفريد', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->serial_number) : ''; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الباركود (Barcode)', 'ac-inventory-system'); ?></label>
            <div style="display:flex; gap:10px;">
                <input type="text" name="barcode" id="ac-is-barcode-input" placeholder="<?php _e('أدخل أو ولد باركود', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->barcode) : ''; ?>">
                <button type="button" id="generate-barcode" class="ac-is-btn" style="padding: 10px;"><?php _e('توليد', 'ac-inventory-system'); ?></button>
            </div>
            <div id="barcode-preview" style="margin-top:10px;">
                <svg id="barcode-svg"></svg>
            </div>
        </div>

        <div class="ac-is-form-group" style="grid-column: span 2;">
            <label><?php _e('رابط الصورة', 'ac-inventory-system'); ?></label>
            <div style="display:flex; gap:10px;">
                <input type="text" name="image_url" id="ac-is-image-url" placeholder="https://..." value="<?php echo $product ? esc_url($product->image_url) : ''; ?>">
                <button type="button" class="ac-is-upload-btn ac-is-btn" style="background:#6c757d;"><?php _e('رفع صورة', 'ac-inventory-system'); ?></button>
            </div>
        </div>
    </div>

    <div style="margin-top:20px; text-align: left;">
        <button type="submit" class="ac-is-btn" style="min-width:200px;"><?php _e('حفظ المنتج', 'ac-inventory-system'); ?></button>
    </div>
</form>
