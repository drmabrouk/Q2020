<?php
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $product_id ? AC_IS_Inventory::get_product($product_id) : null;
$brands = AC_IS_Brands::get_brands();
?>

<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2><?php echo $product ? __('تعديل بيانات الصنف', 'ac-inventory-system') : __('إضافة صنف جديد للمخزون', 'ac-inventory-system'); ?></h2>
    <a href="<?php echo add_query_arg('ac_view', 'inventory'); ?>" class="ac-is-btn" style="background:#64748b;"><?php _e('العودة للمخزون', 'ac-inventory-system'); ?></a>
</div>

<form id="ac-is-product-form" class="ac-is-form" style="background:#fff; padding:30px; border-radius:12px; border:1px solid var(--ac-border);">
    <input type="hidden" name="id" value="<?php echo $product_id; ?>">

    <div class="ac-is-grid">
        <div class="ac-is-form-group">
            <label><?php _e('اسم المنتج / الصنف', 'ac-inventory-system'); ?></label>
            <input type="text" name="name" placeholder="<?php _e('أدخل الاسم التجاري', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->name) : ''; ?>" required>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('التصنيف الرئيسي', 'ac-inventory-system'); ?></label>
            <select name="category" id="ac-is-category-select">
                <option value="ac" <?php selected($product ? $product->category : '', 'ac'); ?>><?php _e('مكيفات (Air Conditioners)', 'ac-inventory-system'); ?></option>
                <option value="filter" <?php selected($product ? $product->category : '', 'filter'); ?>><?php _e('فلاتر مياه (Water Filters)', 'ac-inventory-system'); ?></option>
                <option value="cooling" <?php selected($product ? $product->category : '', 'cooling'); ?>><?php _e('أنظمة تبريد أخرى', 'ac-inventory-system'); ?></option>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('العلامة التجارية (البراند)', 'ac-inventory-system'); ?></label>
            <select name="brand_id">
                <option value=""><?php _e('--- اختر البراند ---', 'ac-inventory-system'); ?></option>
                <?php foreach($brands as $brand): ?>
                    <option value="<?php echo $brand->id; ?>" <?php selected($product ? $product->brand_id : '', $brand->id); ?>><?php echo esc_html($brand->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Dynamic Fields for AC -->
        <div class="ac-is-form-group category-field ac-only" style="display:none;">
            <label><?php _e('رقم الموديل (Model Number)', 'ac-inventory-system'); ?></label>
            <input type="text" name="model_number" value="<?php echo $product ? esc_attr($product->model_number) : ''; ?>">
        </div>

        <!-- Dynamic Fields for Filters -->
        <div class="ac-is-form-group category-field filter-only" style="display:none;">
            <label><?php _e('عدد المراحل (Stages)', 'ac-inventory-system'); ?></label>
            <select name="filter_stages">
                <option value=""><?php _e('اختر عدد المراحل', 'ac-inventory-system'); ?></option>
                <?php for($i=1; $i<=7; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php selected($product ? $product->filter_stages : '', $i); ?>><?php echo $i; ?> <?php _e('مراحل', 'ac-inventory-system'); ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('التصنيف الفرعي', 'ac-inventory-system'); ?></label>
            <input type="text" name="subcategory" placeholder="<?php _e('مثال: سبليت، مركزي، 5 مراحل', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->subcategory) : ''; ?>">
        </div>

        <hr style="grid-column: span 2; margin:10px 0; border:0; border-top:1px solid #eee;">

        <div class="ac-is-form-group">
            <label><?php _e('سعر الشراء / الجملة (Wholesale Price)', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="purchase_cost" id="purchase-cost" placeholder="0.00" value="<?php echo $product ? $product->purchase_cost : '0.00'; ?>" required>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('السعر المعروض للبيع (Sale Price)', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="original_price" id="original-price" placeholder="0.00" value="<?php echo $product ? $product->original_price : '0.00'; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الخصم (%)', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="discount" id="discount" placeholder="0.00" value="<?php echo $product ? $product->discount : '0.00'; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('السعر النهائي بعد الخصم (Final Price)', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="final_price" id="final-price" placeholder="0.00" value="<?php echo $product ? $product->final_price : '0.00'; ?>" readonly style="background:#f8fafc; font-weight:700; color:var(--ac-primary);">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الكمية الحالية بالمخزون', 'ac-inventory-system'); ?></label>
            <input type="number" name="stock_quantity" placeholder="0" value="<?php echo $product ? $product->stock_quantity : '0'; ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الباركود (Barcode)', 'ac-inventory-system'); ?></label>
            <div style="display:flex; gap:10px;">
                <input type="text" name="barcode" id="ac-is-barcode-input" placeholder="<?php _e('باركود الصنف', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->barcode) : ''; ?>">
                <button type="button" id="generate-barcode" class="ac-is-btn" style="padding: 10px; background:#475569;"><?php _e('توليد تلقائي', 'ac-inventory-system'); ?></button>
            </div>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الرقم التسلسلي (S/N)', 'ac-inventory-system'); ?></label>
            <input type="text" name="serial_number" id="ac-is-serial-input" placeholder="<?php _e('يترك فارغاً للتوليد من الباركود', 'ac-inventory-system'); ?>" value="<?php echo $product ? esc_attr($product->serial_number) : ''; ?>">
        </div>
    </div>

    <div id="barcode-image-preview" style="margin-top:30px; text-align: center; display:none; padding:20px; border:2px dashed #ddd; background:#f9f9f9;">
        <h4 style="margin-bottom:15px;"><?php _e('صورة الباركود (عالية الدقة)', 'ac-inventory-system'); ?></h4>
        <div id="barcode-canvas-container"></div>
        <p style="margin-top:10px; font-size:0.8rem; color:#64748b;"><?php _e('يمكنك حفظ هذه الصورة لاستخدامها في الملصقات', 'ac-inventory-system'); ?></p>
    </div>

    <div style="margin-top:30px; text-align: left; border-top: 1px solid #eee; padding-top: 20px;">
        <button type="submit" class="ac-is-btn" style="min-width:240px; font-size: 1.1rem;"><?php _e('حفظ بيانات الصنف', 'ac-inventory-system'); ?></button>
    </div>
</form>

<script>
jQuery(document).ready(function($) {
    function toggleFields() {
        const cat = $('#ac-is-category-select').val();
        $('.category-field').hide();
        if(cat === 'ac') $('.ac-only').show();
        if(cat === 'filter') $('.filter-only').show();
    }
    $('#ac-is-category-select').on('change', toggleFields);
    toggleFields();
});
</script>
