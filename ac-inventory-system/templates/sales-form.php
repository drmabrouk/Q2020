<?php
$products = AC_IS_Inventory::get_products();
$branches = AC_IS_Inventory::get_branches();
?>
<h2><?php _e('تسجيل عملية بيع جديدة', 'ac-inventory-system'); ?></h2>

<div class="ac-is-sales-search" style="margin-bottom:20px; padding:15px; background:#f8fafc; border-radius:8px;">
    <label><?php _e('البحث السريع (باركود أو سيريال):', 'ac-inventory-system'); ?></label>
    <input type="text" id="ac-is-sale-product-search" placeholder="<?php _e('ادخل الباركود أو الرقم التسلسلي...', 'ac-inventory-system'); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
</div>

<form id="ac-is-sales-form">
    <div class="ac-is-grid">
        <div class="ac-is-form-group">
            <label><?php _e('المنتج', 'ac-inventory-system'); ?></label>
            <select name="product_id" id="ac-is-sale-product" required>
                <option value=""><?php _e('اختر المنتج', 'ac-inventory-system'); ?></option>
                <?php foreach($products as $product): ?>
                    <option value="<?php echo $product->id; ?>" data-price="<?php echo $product->final_price; ?>" data-stock="<?php echo $product->stock_quantity; ?>">
                        <?php echo esc_html($product->name); ?> (<?php _e('المتوفر:', 'ac-inventory-system'); ?> <?php echo $product->stock_quantity; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الرقم التسلسلي (اختياري)', 'ac-inventory-system'); ?></label>
            <input type="text" name="serial_number" id="ac-is-sale-serial" placeholder="<?php _e('الرقم التسلسلي للوحدة المباعة', 'ac-inventory-system'); ?>">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الكمية', 'ac-inventory-system'); ?></label>
            <input type="number" name="quantity" id="ac-is-sale-qty" min="1" value="1" required>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('السعر الإجمالي', 'ac-inventory-system'); ?></label>
            <input type="number" step="0.01" name="total_price" id="ac-is-sale-total" readonly>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الفرع', 'ac-inventory-system'); ?></label>
            <select name="branch_id" required>
                <?php foreach($branches as $branch): ?>
                    <option value="<?php echo $branch->id; ?>"><?php echo esc_html($branch->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div style="margin-top:20px; text-align: left;">
        <button type="submit" class="ac-is-btn" style="min-width:200px;"><?php _e('إتمام البيع وتوليد الفاتورة', 'ac-inventory-system'); ?></button>
    </div>
</form>
