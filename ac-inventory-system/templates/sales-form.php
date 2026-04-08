<?php
$products = AC_IS_Inventory::get_products();
$branches = AC_IS_Inventory::get_branches();
?>
<div class="ac-is-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-weight:800; font-size:1.5rem; margin:0; color:var(--ac-sidebar-bg);"><?php _e('إنشاء فاتورة مبيعات', 'ac-inventory-system'); ?></h2>
</div>

<div class="ac-is-grid">
    <!-- Left Column: Product Selection / Scanner -->
    <div class="ac-is-selection-area">
        <div class="ac-is-sales-modes" style="display:flex; gap:10px; margin-bottom:20px;">
            <button type="button" class="ac-is-mode-btn ac-is-btn active" data-mode="manual" style="flex:1; background:#fff; color:var(--ac-sidebar-bg) !important; border: 2px solid var(--ac-sidebar-bg); padding:8px;">
                <span class="dashicons dashicons-edit"></span> <?php _e('إدخال يدوي', 'ac-inventory-system'); ?>
            </button>
            <button type="button" class="ac-is-mode-btn ac-is-btn" data-mode="scan" id="ac-is-toggle-scanner" style="flex:1; background:#fff; color:var(--ac-primary) !important; border: 2px solid var(--ac-primary); padding:8px;">
                <span class="dashicons dashicons-camera"></span> <?php _e('مسح بالباركود', 'ac-inventory-system'); ?>
            </button>
        </div>

        <div id="ac-is-reader-container" style="display:none; margin-bottom:20px;">
            <div id="ac-is-reader"></div>
            <div class="ac-is-scan-overlay">
                <div class="ac-is-scan-frame">
                    <div class="ac-is-scan-frame-corner-tr"></div>
                    <div class="ac-is-scan-frame-corner-bl"></div>
                    <div class="ac-is-scan-frame-corner-br"></div>
                    <div class="ac-is-scan-corners"></div>
                </div>
            </div>
            <div class="ac-is-scan-status"><?php _e('جاهز للمسح الضوئي', 'ac-inventory-system'); ?></div>
        </div>

        <div id="ac-is-manual-search" class="ac-is-card" style="margin-bottom:20px; padding:15px;">
            <input type="text" id="ac-is-sale-product-search" placeholder="<?php _e('بحث سريع...', 'ac-inventory-system'); ?>" style="width:100%;">
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
                            <?php echo esc_html($product->name); ?> (<?php echo $product->stock_quantity; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ac-is-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                <div class="ac-is-form-group">
                    <label><?php _e('السيريال', 'ac-inventory-system'); ?></label>
                    <input type="text" id="ac-is-sale-serial">
                </div>
                <div class="ac-is-form-group">
                    <label><?php _e('الكمية', 'ac-inventory-system'); ?></label>
                    <input type="number" id="ac-is-sale-qty" min="1" value="1">
                </div>
            </div>
            <button type="button" id="ac-is-add-to-list" class="ac-is-btn" style="width:100%; background:#1e293b;">
                <span class="dashicons dashicons-plus-alt" style="margin-left:8px;"></span><?php _e('إضافة للفاتورة', 'ac-inventory-system'); ?>
            </button>
        </div>
    </div>

    <!-- Right Column: Cart & Customer -->
    <div class="ac-is-cart-area">
        <form id="ac-is-multi-sale-form">
            <div class="ac-is-card" style="padding:20px; margin-bottom:20px; border-top: 4px solid var(--ac-primary);">
                <h3><?php _e('المنتجات المختارة', 'ac-inventory-system'); ?></h3>
                <table class="ac-is-table" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php _e('المنتج', 'ac-inventory-system'); ?></th>
                            <th><?php _e('الكمية', 'ac-inventory-system'); ?></th>
                            <th><?php _e('السعر', 'ac-inventory-system'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="ac-is-cart-body">
                        <tr class="empty-cart"><td colspan="4" style="text-align:center;"><?php _e('لم يتم اختيار منتجات', 'ac-inventory-system'); ?></td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2"><?php _e('الإجمالي النهائي', 'ac-inventory-system'); ?></th>
                            <th colspan="2"><span id="ac-is-cart-total">0.00</span> EGP</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="ac-is-card" style="padding:20px;">
                <h3><?php _e('بيانات العميل', 'ac-inventory-system'); ?></h3>
                <div class="ac-is-form-group" style="margin-top:15px;">
                    <label><?php _e('رقم الهاتف', 'ac-inventory-system'); ?></label>
                    <input type="text" name="customer_phone" id="ac-is-customer-phone" placeholder="01xxxxxxxxx" required>
                </div>
                <div id="customer-details-fields">
                    <div class="ac-is-form-group">
                        <label><?php _e('اسم العميل', 'ac-inventory-system'); ?></label>
                        <input type="text" name="customer_name" id="ac-is-customer-name">
                    </div>
                    <div class="ac-is-form-group">
                        <label><?php _e('العنوان', 'ac-inventory-system'); ?></label>
                        <input type="text" name="customer_address" id="ac-is-customer-address">
                    </div>
                    <div class="ac-is-form-group">
                        <label><?php _e('البريد الإلكتروني', 'ac-inventory-system'); ?></label>
                        <input type="email" name="customer_email" id="ac-is-customer-email">
                    </div>
                </div>

                <div class="ac-is-form-group">
                    <label><?php _e('الفرع', 'ac-inventory-system'); ?></label>
                    <select name="branch_id" required>
                        <?php foreach($branches as $branch): ?>
                            <option value="<?php echo $branch->id; ?>"><?php echo esc_html($branch->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-top:20px;">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input type="checkbox" name="send_email" value="1" checked> <?php _e('إرسال الفاتورة للبريد الإلكتروني تلقائياً', 'ac-inventory-system'); ?>
                    </label>
                </div>

                <button type="submit" class="ac-is-btn" style="width:100%; margin-top:25px; height:50px; background:#059669; font-size:1.1rem;">
                    <?php _e('تأكيد البيع وإصدار الفاتورة', 'ac-inventory-system'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
