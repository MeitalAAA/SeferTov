<?php
$all_modules    = gloo()->modules->get_all_modules();
$active_modules = gloo()->modules->get_active_modules();

include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>
<div class="gloo-item-container">
    <div class="gloo-items">
        
        <!-- Fluid dynamics -->
        <div class="gloo-feature" style=" width:100%; max-width:30%; flex:none;">
            <div class="gloo-box">
                <div class="gloo-feature-title">
                <img src="<?php echo $images_url . 'woo-gloo.jpg' ?>"/></div>

                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_dynamic_tags_kit" <?php echo in_array( 'woocommerce_dynamic_tags_kit', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Woocommerce Advanced Dynamic Tags' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Products by Backorder Status');?></p>
                                    <p><?php _e('Products by Catalog Visibility');?></p>
                                    <p><?php _e('Products by Virtual Status');?></p>
                                    <p><?php _e('Products by Downloadable Status');?></p>
                                    <p><?php _e('Product Attribute Value');?></p>
                                    <p><?php _e('Product Gallery');?></p>
                                    <p><?php _e('Thank You Page Order Details');?></p>
                                    <p><?php _e('Active Subscription Status');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_bundle_maker" <?php echo in_array( 'woocommerce_bundle_maker', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Bundle Maker Widget' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="checkout_anything" <?php echo in_array( 'checkout_anything', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Checkout Anything Form Action' ); ?>
                            <span><?php _e('Beta');?></span>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_woocommerce_price_widget" <?php echo in_array( 'gloo_woocommerce_price_widget', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Price + Widget' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Woocommerce price widget with special abilities');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_products" <?php echo in_array( 'woocommerce_products', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Related Products Dynamic Tag' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Display Related/Upsells/Cross Sells/Best Seller Products');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woo_product_discount" <?php echo in_array( 'woo_product_discount', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Woo Product Discount Widget' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woo_cart_values" <?php echo in_array( 'woo_cart_values', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Cart Values Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <?php 
                    $disabled = '';
                    
                    if ( !is_plugin_active( 'woo-variation-swatches/woo-variation-swatches.php' )) {
                        $disabled = 'disabled';
                    } ?>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_swatch" <?php echo in_array( 'woocommerce_swatch', $active_modules ) ? 'checked="checked"' : '';  echo $disabled;?>/>
                            <?php _e( 'Swatches Widget' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Require Woo Variation Swatches Plugin');?></p>
                                </div>
                            </div>
                            <span><?php _e('Beta');?></span>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_variation_table" <?php echo in_array( 'woocommerce_variation_table', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Woo Variation Table' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Require Product Variation Table Plugin');?></p>
                                </div>
                            </div>
                            <span><?php _e('Beta');?></span>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="dokan_dynamic_tags" <?php echo in_array( 'dokan_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Dokan Dynamic Tags' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Store Name' );?></p>
                                    <p><?php _e('- Store User Link ');?></p>
                                    <p><?php _e('- Store Vendor ID ');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="multi_currency_dynamic_tags" <?php echo in_array( 'multi_currency_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Multicurrency Dynamic Tag' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Require WooCommerce Multi Currency Premium');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Dokan -->
        <div class="gloo-feature" style="display: none;">
            <div class="gloo-box">
                <div class="gloo-feature-title">
                    <img src="<?php echo $images_url . 'DOKAN KIT header.jpg' ?>"/></div>

                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_dynamic_tags_kit" <?php echo in_array( 'woocommerce_dynamic_tags_kit', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Variations & Gallery Dynamic Tag Kit' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Woocommerce Variation Dynamic Tag');?></p>
                                    <p><?php _e('Woocommerce Gallery Images Dynamic Tag');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_woocommerce_price_widget" <?php echo in_array( 'gloo_woocommerce_price_widget', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Price + Widget' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Woocommerce price widget with special abilities');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="categories_title_to_div" <?php echo in_array( 'categories_title_to_div', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Categories Native Titles  H2 to Div' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Convert loop categories title from H2 to Div for SEO Purposes');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_products" <?php echo in_array( 'woocommerce_products', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Upsell & Cross-sell Dynamic Tag' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Display Related/Upsells/Cross Sells/Best Seller Products');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>

                    <?php 
                    $disabled = '';
                    
                    if ( !is_plugin_active( 'woo-variation-swatches/woo-variation-swatches.php' )) {
                        $disabled = 'disabled';
                    } ?>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woocommerce_swatch" <?php echo in_array( 'woocommerce_swatch', $active_modules ) ? 'checked="checked"' : '';  echo $disabled;?>/>
                            <?php _e( 'Swatches Widget' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a> 
                                <div class="tool-tip-content">
                                    <p><?php _e('Require Woo Variation Swatches Plugin');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="woo_product_discount" <?php echo in_array( 'woo_product_discount', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Woo Product Discount' ); ?>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>
        <div class="gloo-feature gloo-feature-2 no-border">
            <?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-features-2.php' ); ?>
        </div>
        <div class="gloo-feature gloo-feature-2 no-border">
            <?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-features.php' ); ?>
        </div>
    </div>
</div>
<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); ?>
