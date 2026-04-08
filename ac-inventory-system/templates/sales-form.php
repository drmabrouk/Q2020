<?php
$products = AC_IS_Inventory::get_products();
$branches = AC_IS_Inventory::get_branches();
?>
<h2><?php _e('تسجيل عملية بيع جديدة', 'ac-inventory-system'); ?></h2>

<form id="ac-is-sales-form">
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
    <button type="submit" class="ac-is-btn"><?php _e('إتمام البيع', 'ac-inventory-system'); ?></button>
</form>

<div id="ac-is-receipt" style="display:none; margin-top:30px; border:1px dashed #333; padding:20px;">
    <h3><?php _e('إيصال بيع', 'ac-inventory-system'); ?></h3>
    <div id="receipt-content"></div>
    <button onclick="window.print();" class="ac-is-btn" style="background:#6c757d;"><?php _e('طباعة', 'ac-inventory-system'); ?></button>
</div>
