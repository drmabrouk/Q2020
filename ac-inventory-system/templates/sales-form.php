<?php
$products = AC_IS_Inventory::get_products();
$branches = AC_IS_Inventory::get_branches();
?>

<div class="ac-is-sales-stepper" style="margin-bottom: 30px; display:flex; justify-content: space-between; position: relative;">
    <div class="step-item active" data-step="1"><span>1</span><small><?php _e('الوضع', 'ac-inventory-system'); ?></small></div>
    <div class="step-item" data-step="2"><span>2</span><small><?php _e('المنتجات', 'ac-inventory-system'); ?></small></div>
    <div class="step-item" data-step="3"><span>3</span><small><?php _e('العميل', 'ac-inventory-system'); ?></small></div>
    <div class="step-item" data-step="4"><span>4</span><small><?php _e('المراجعة', 'ac-inventory-system'); ?></small></div>
    <div class="step-item" data-step="5"><span>5</span><small><?php _e('الفاتورة', 'ac-inventory-system'); ?></small></div>
    <div class="step-line" style="position:absolute; top:18px; left:0; width:100%; height:2px; background:#e2e8f0; z-index:-1;"></div>
</div>

<!-- Step 1: Mode Selection -->
<div id="step-1" class="ac-is-sale-step">
    <div class="ac-is-header-flex" style="margin-bottom:20px;">
        <h2><?php _e('الخطوة 1: اختر وضع الإدخال', 'ac-inventory-system'); ?></h2>
    </div>
    <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:20px;">
        <button type="button" class="ac-is-mode-select ac-is-btn" data-mode="manual" style="height:150px; flex-direction:column; background:#fff; color:var(--ac-text) !important; border:2px solid var(--ac-border);">
            <span class="dashicons dashicons-edit" style="font-size:40px; width:40px; height:40px; margin-bottom:10px;"></span>
            <span style="font-size:1.2rem;"><?php _e('إدخال يدوي', 'ac-inventory-system'); ?></span>
        </button>
        <button type="button" class="ac-is-mode-select ac-is-btn" data-mode="scan" style="height:150px; flex-direction:column; background:#fff; color:var(--ac-primary) !important; border:2px solid var(--ac-primary);">
            <span class="dashicons dashicons-camera" style="font-size:40px; width:40px; height:40px; margin-bottom:10px;"></span>
            <span style="font-size:1.2rem;"><?php _e('مسح بالباركود', 'ac-inventory-system'); ?></span>
        </button>
    </div>
</div>

<!-- Step 2: Product Selection -->
<div id="step-2" class="ac-is-sale-step" style="display:none;">
    <div class="ac-is-header-flex" style="margin-bottom:20px;">
        <h2><?php _e('الخطوة 2: إضافة المنتجات', 'ac-inventory-system'); ?></h2>
    </div>
    <div class="ac-is-grid">
        <div class="ac-is-selection-area">
            <div id="ac-is-reader-container" style="display:none; margin-bottom:20px;">
                <div id="ac-is-reader"></div>
                <div class="ac-is-scan-overlay"><div class="ac-is-scan-frame"><div class="ac-is-scan-corners"></div></div></div>
                <div class="ac-is-scan-status"><?php _e('جاهز للمسح', 'ac-inventory-system'); ?></div>
            </div>
            <div id="ac-is-manual-search-box" class="ac-is-card" style="margin-bottom:20px;">
                <input type="text" id="ac-is-sale-product-search" placeholder="<?php _e('ابحث عن منتج...', 'ac-inventory-system'); ?>" style="width:100%;">
            </div>
            <div class="ac-is-card" style="padding:20px;">
                <div class="ac-is-form-group">
                    <label><?php _e('المنتج', 'ac-inventory-system'); ?></label>
                    <select id="ac-is-sale-product">
                        <option value=""><?php _e('اختر المنتج', 'ac-inventory-system'); ?></option>
                        <?php foreach($products as $product): ?>
                            <option value="<?php echo $product->id; ?>"
                                    data-name="<?php echo esc_attr($product->name); ?>"
                                    data-price="<?php echo $product->final_price; ?>"
                                    data-stock="<?php echo $product->stock_quantity; ?>"
                                    data-barcode="<?php echo esc_attr($product->barcode); ?>"
                                    data-serial="<?php echo esc_attr($product->serial_number); ?>">
                                <?php echo esc_html($product->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                    <div class="ac-is-form-group"><label><?php _e('السيريال', 'ac-inventory-system'); ?></label><input type="text" id="ac-is-sale-serial"></div>
                    <div class="ac-is-form-group"><label><?php _e('الكمية', 'ac-inventory-system'); ?></label><input type="number" id="ac-is-sale-qty" min="1" value="1"></div>
                </div>
                <button type="button" id="ac-is-add-to-list" class="ac-is-btn" style="width:100%; background:#1e293b;"><?php _e('إضافة للفاتورة', 'ac-inventory-system'); ?></button>
            </div>
        </div>
        <div class="ac-is-cart-area">
            <div class="ac-is-card" style="padding:20px; border-top: 4px solid var(--ac-primary);">
                <h3><?php _e('المنتجات المضافة', 'ac-inventory-system'); ?></h3>
                <table class="ac-is-table">
                    <tbody id="ac-is-cart-body"></tbody>
                    <tfoot><tr><th colspan="2"><?php _e('الإجمالي', 'ac-inventory-system'); ?></th><th><span id="ac-is-cart-total">0.00</span></th></tr></tfoot>
                </table>
                <button type="button" class="ac-is-next-step ac-is-btn" data-next="3" style="width:100%; margin-top:15px;"><?php _e('متابعة لبيانات العميل', 'ac-inventory-system'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Step 3: Customer Identification -->
<div id="step-3" class="ac-is-sale-step" style="display:none;">
    <div class="ac-is-header-flex" style="margin-bottom:20px;">
        <h2><?php _e('الخطوة 3: بيانات العميل', 'ac-inventory-system'); ?></h2>
    </div>
    <div class="ac-is-card" style="max-width:600px; margin:0 auto; padding:30px;">
        <div class="ac-is-form-group">
            <label><?php _e('رقم الهاتف', 'ac-inventory-system'); ?></label>
            <input type="text" id="ac-is-customer-phone" placeholder="01xxxxxxxxx">
        </div>
        <div id="customer-details-fields" style="display:none; border-top:1px solid #eee; padding-top:20px; margin-top:20px;">
            <div class="ac-is-form-group"><label><?php _e('اسم العميل', 'ac-inventory-system'); ?></label><input type="text" id="ac-is-customer-name"></div>
            <div class="ac-is-form-group"><label><?php _e('العنوان', 'ac-inventory-system'); ?></label><input type="text" id="ac-is-customer-address"></div>
            <div class="ac-is-form-group"><label><?php _e('البريد الإلكتروني', 'ac-inventory-system'); ?></label><input type="email" id="ac-is-customer-email"></div>
        </div>
        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="button" class="ac-is-next-step ac-is-btn" data-next="4" style="flex:1; background:#059669;"><?php _e('مراجعة الطلب', 'ac-inventory-system'); ?></button>
            <button type="button" id="ac-is-quick-customer" class="ac-is-btn" style="flex:1; background:#64748b;"><?php _e('عميل سريع (غير مسجل)', 'ac-inventory-system'); ?></button>
        </div>
    </div>
</div>

<!-- Step 4: Confirmation -->
<div id="step-4" class="ac-is-sale-step" style="display:none;">
    <div class="ac-is-header-flex" style="margin-bottom:20px;">
        <h2><?php _e('الخطوة 4: تأكيد الفاتورة', 'ac-inventory-system'); ?></h2>
    </div>
    <div class="ac-is-grid">
        <div class="ac-is-card">
            <h3><?php _e('ملخص المنتجات', 'ac-inventory-system'); ?></h3>
            <table class="ac-is-table" id="review-cart-table"></table>
            <div style="text-align: left; padding: 15px; font-size: 1.4rem; font-weight: 800;">
                <small><?php _e('الإجمالي المستحق:', 'ac-inventory-system'); ?></small> <span id="review-total"></span> EGP
            </div>
        </div>
        <div class="ac-is-card">
            <h3><?php _e('ملخص العميل', 'ac-inventory-system'); ?></h3>
            <div id="review-customer-info" style="margin-top:15px; line-height:2;"></div>
            <div class="ac-is-form-group" style="margin-top:20px;">
                <label><?php _e('الفرع البائع', 'ac-inventory-system'); ?></label>
                <select id="ac-is-sale-branch">
                    <?php foreach($branches as $branch): ?><option value="<?php echo $branch->id; ?>"><?php echo esc_html($branch->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <button type="button" id="ac-is-finalize-sale" class="ac-is-btn" style="width:100%; height:60px; margin-top:30px; background:#059669; font-size:1.2rem;">
                <?php _e('تأكيد وإصدار الفاتورة', 'ac-inventory-system'); ?>
            </button>
        </div>
    </div>
</div>

<style>
.step-item { background: #fff; border: 2px solid #e2e8f0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; font-weight: 700; color: #94a3b8; }
.step-item.active { border-color: var(--ac-primary); color: var(--ac-primary); }
.step-item.complete { background: var(--ac-primary); border-color: var(--ac-primary); color: #fff; }
.step-item small { position: absolute; top: 45px; white-space: nowrap; color: #64748b; }
</style>
