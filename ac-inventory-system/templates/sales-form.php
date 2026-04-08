<?php
$products = AC_IS_Inventory::get_products();
$branches = AC_IS_Inventory::get_branches();
?>
<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2><?php _e('تسجيل عملية بيع جديدة', 'ac-inventory-system'); ?></h2>
    <button type="button" id="ac-is-toggle-scanner" class="ac-is-btn" style="background:#4f46e5;">
        <span class="dashicons dashicons-camera" style="margin-left:8px;"></span> <?php _e('فتح الماسح الضوئي', 'ac-inventory-system'); ?>
    </button>
</div>

<div id="ac-is-reader" style="width:100%; max-width:600px; margin: 0 auto 25px; display:none; border-radius:12px; overflow:hidden;"></div>

<div class="ac-is-sales-search" style="margin-bottom:25px; padding:20px; background:#fff; border-radius:12px; border:1px solid var(--ac-border);">
    <label style="display:block; margin-bottom:10px; font-weight:600;"><?php _e('البحث السريع (باركود أو سيريال):', 'ac-inventory-system'); ?></label>
    <input type="text" id="ac-is-sale-product-search" placeholder="<?php _e('ادخل الباركود أو الرقم التسلسلي أو اسم المنتج...', 'ac-inventory-system'); ?>" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem;">
</div>

<form id="ac-is-sales-form" style="background:#fff; padding:30px; border-radius:12px; border:1px solid var(--ac-border);">
    <div class="ac-is-grid">
        <div class="ac-is-form-group">
            <label><?php _e('المنتج', 'ac-inventory-system'); ?></label>
            <select name="product_id" id="ac-is-sale-product" required style="height:46px;">
                <option value=""><?php _e('اختر المنتج', 'ac-inventory-system'); ?></option>
                <?php foreach($products as $product): ?>
                    <option value="<?php echo $product->id; ?>"
                            data-price="<?php echo $product->final_price; ?>"
                            data-stock="<?php echo $product->stock_quantity; ?>"
                            data-barcode="<?php echo esc_attr($product->barcode); ?>"
                            data-serial="<?php echo esc_attr($product->serial_number); ?>">
                        <?php echo esc_html($product->name); ?> (<?php echo $product->stock_quantity; ?> <?php _e('قطعة', 'ac-inventory-system'); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الرقم التسلسلي للقطعة', 'ac-inventory-system'); ?></label>
            <input type="text" name="serial_number" id="ac-is-sale-serial" placeholder="<?php _e('سيريال الوحدة المباعة', 'ac-inventory-system'); ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الكمية', 'ac-inventory-system'); ?></label>
            <input type="number" name="quantity" id="ac-is-sale-qty" min="1" value="1" required>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('السعر الإجمالي (EGP)', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="total_price" id="ac-is-sale-total" readonly style="background:#f8fafc; font-weight:700; color:var(--ac-primary);">
        </div>

        <div class="ac-is-form-group" style="grid-column: span 2;">
            <label><?php _e('الفرع', 'ac-inventory-system'); ?></label>
            <select name="branch_id" required>
                <?php foreach($branches as $branch): ?>
                    <option value="<?php echo $branch->id; ?>"><?php echo esc_html($branch->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div style="margin-top:30px; text-align: left; border-top: 1px solid #eee; padding-top: 20px;">
        <button type="submit" class="ac-is-btn" style="min-width:240px; font-size:1.1rem; background:#059669;"><?php _e('إتمام البيع وطباعة الفاتورة', 'ac-inventory-system'); ?></button>
    </div>
</form>
