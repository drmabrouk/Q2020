<?php
$products = AC_IS_Inventory::get_products();
$branches = AC_IS_Inventory::get_branches();
?>
<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-weight:800; font-size:1.8rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('تسجيل عملية بيع جديدة', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-sales-modes" style="display:flex; gap:20px; margin-bottom:30px;">
    <button type="button" class="ac-is-mode-btn ac-is-btn active" data-mode="manual" style="flex:1; background:#fff; color:var(--ac-sidebar-bg) !important; border: 2px solid var(--ac-sidebar-bg);">
        <span class="dashicons dashicons-edit" style="margin-left:8px;"></span> <?php _e('إدخال يدوي', 'ac-inventory-system'); ?>
    </button>
    <button type="button" class="ac-is-mode-btn ac-is-btn" data-mode="scan" id="ac-is-toggle-scanner" style="flex:1; background:#fff; color:var(--ac-primary) !important; border: 2px solid var(--ac-primary);">
        <span class="dashicons dashicons-camera" style="margin-left:8px;"></span> <?php _e('مسح الباركود (كاميرا)', 'ac-inventory-system'); ?>
    </button>
</div>

<div id="ac-is-reader-container" style="display:none; margin-bottom:30px;">
    <div id="ac-is-reader" style="width:100%; max-width:600px; margin: 0 auto; border: 4px solid var(--ac-primary); border-radius:0;"></div>
    <div style="text-align:center; margin-top:15px;">
        <p style="color:var(--ac-primary); font-weight:700;"><?php _e('جاري المسح... وجه الكاميرا نحو الباركود', 'ac-inventory-system'); ?></p>
    </div>
</div>

<div id="ac-is-manual-search" class="ac-is-card" style="margin-bottom:30px; border-right: 5px solid var(--ac-primary);">
    <label style="display:block; margin-bottom:12px; font-weight:700; font-size:1.1rem;"><?php _e('ابحث عن منتج:', 'ac-inventory-system'); ?></label>
    <input type="text" id="ac-is-sale-product-search" placeholder="<?php _e('ادخل الباركود، الرقم التسلسلي، أو اسم المنتج للبحث السريع...', 'ac-inventory-system'); ?>" style="width:100%; padding:15px; border:2px solid #e2e8f0; border-radius:6px; font-size:1.1rem;">
</div>

<form id="ac-is-sales-form" class="ac-is-card">
    <div class="ac-is-grid">
        <div class="ac-is-form-group">
            <label><?php _e('المنتج المختار', 'ac-inventory-system'); ?></label>
            <select name="product_id" id="ac-is-sale-product" required style="height:52px; font-weight:700;">
                <option value=""><?php _e('--- اختر المنتج ---', 'ac-inventory-system'); ?></option>
                <?php foreach($products as $product): ?>
                    <option value="<?php echo $product->id; ?>"
                            data-price="<?php echo $product->final_price; ?>"
                            data-stock="<?php echo $product->stock_quantity; ?>"
                            data-barcode="<?php echo esc_attr($product->barcode); ?>"
                            data-serial="<?php echo esc_attr($product->serial_number); ?>">
                        <?php echo esc_html($product->name); ?> (<?php echo $product->stock_quantity; ?> <?php _e('متاح', 'ac-inventory-system'); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('تأكيد الرقم التسلسلي (S/N)', 'ac-inventory-system'); ?></label>
            <input type="text" name="serial_number" id="ac-is-sale-serial" placeholder="<?php _e('الرقم التسلسلي للوحدة المباعة', 'ac-inventory-system'); ?>" style="height:52px;">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الكمية', 'ac-inventory-system'); ?></label>
            <input type="number" name="quantity" id="ac-is-sale-qty" min="1" value="1" required style="height:52px;">
        </div>

        <div class="ac-is-form-group">
            <label><?php _e('الإجمالي المستحق', 'ac-inventory-system'); ?></label>
            <div style="position:relative;">
                <input type="number" step="0.01" name="total_price" id="ac-is-sale-total" readonly style="height:52px; padding-right:60px; font-size:1.4rem; font-weight:800; color:var(--ac-primary); background:#f8fafc; border:2px solid var(--ac-primary);">
                <span style="position:absolute; right:15px; top:12px; font-weight:700; color:var(--ac-primary);">EGP</span>
            </div>
        </div>

        <div class="ac-is-form-group" style="grid-column: span 2;">
            <label><?php _e('الفرع البائع', 'ac-inventory-system'); ?></label>
            <select name="branch_id" required style="height:52px;">
                <?php foreach($branches as $branch): ?>
                    <option value="<?php echo $branch->id; ?>"><?php echo esc_html($branch->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div style="margin-top:40px; text-align: left; border-top: 2px solid #f1f5f9; padding-top: 30px;">
        <button type="submit" class="ac-is-btn" style="min-width:300px; font-size:1.2rem; background:#059669; height:60px;">
            <span class="dashicons dashicons-yes-alt" style="margin-left:10px;"></span> <?php _e('إتمام العملية وطباعة الفاتورة', 'ac-inventory-system'); ?>
        </button>
    </div>
</form>
