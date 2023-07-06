<?php
$gloo_license_info = get_option( 'gloo_license_info' );
$all_modules       = gloo()->modules->get_all_modules();
$active_modules    = gloo()->modules->get_active_modules();

include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>
<div class="gloo-item-container">
    <img src="<?php echo $images_url . 'GlooHero.png' ?>" class="gloo-building"/>
    <div class="gloo-items">
        <!-- little engine -->
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title">
                    <img src="<?php echo $images_url . 'POWER GLOO header.jpg' ?>"/>
                </div>

                <ul>
					<?php /*<li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_content_trimmer" <?php echo in_array(    'gloo_content_trimmer', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Content Trimmer' ); ?>
                        </lable>
                    </li><?php */ ?>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_typography_plus" <?php echo in_array( 'gloo_typography_plus', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Typography+' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_clickable" <?php echo in_array( 'gloo_clickable', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Clickable+' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_keyframes" <?php echo in_array( 'gloo_keyframes', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Keyframe Animator' ); ?>
                        </lable>
                    </li>

                    
                    <li>
        <lable>
            <input type="checkbox" class="flipswitch" value="1"
                   name="grain_Control" <?php echo in_array( 'grain_Control', $active_modules ) ? 'checked="checked"' : ''; ?>/>
            <?php _e( 'Grain Control' ); ?>
           
        </lable>
    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_draggable" <?php echo in_array( 'gloo_draggable', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Draggable+' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="device_widget" <?php echo in_array( 'device_widget', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Device Widget' ); ?>
                        </lable>
                    </li>
                    <!-- <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="php_responsive" <?php echo in_array( 'php_responsive', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'PHP Responsivness' ); ?>
                            <span><?php _e('Beta');?></span>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( 'Using this feature requires managing seperate cache for every breakpoint in use. this feature is quite powerful but has the potential 
                                    to create issues if not used correctly so please use with care.
    ' ); ?></p>
                            </div>
                        </lable>
                    </li> -->
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_google_adsense" <?php echo in_array( 'gloo_google_adsense', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Google Adsense' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=google-adsense' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_listing_grid_shortcode_maker" <?php echo in_array( 'gloo_listing_grid_shortcode_maker', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <small class="module-setting"><a href="<?php echo admin_url( 'admin.php?page=listing_shortcode'); ?>"><?php _e( 'settings' ); ?></a></small>
                            <?php _e( 'Listing Grid Shortcode Maker' ); ?>
                            <span><?php _e('Beta');?></span>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '* requires jet engine' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="jsf_pagination_widget" <?php echo in_array( 'jsf_pagination_widget', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'JSF Pagination Widget' ); ?>
                            <span><?php _e('Beta');?></span>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '* requires JetSmartFilters' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="gloo_column_responsive_order" <?php echo in_array( 'gloo_column_responsive_order', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Column Responsive Order' ); ?>
                        </lable>
                    </li>
                </ul>
            </div>

            <div class="gloo-box">
                <div class="gloo-feature-title">
                    <img src="<?php echo $images_url . 'SUPERGLOO ADDONS header.jpg' ?>"/>
                </div>
                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="buddyboss_gloo_kit" <?php echo in_array( 'buddyboss_gloo_kit', $active_modules ) ? 'checked="checked"' : ''; ?> />

							<?php _e( 'BuddyBoss Gloo Kit' ); ?>

                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=gloo-buddyboss' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_learndash" <?php echo in_array( 'gloo_learndash', $active_modules ) ? 'checked="checked"' : ''; ?> />

							<?php _e( 'Memberships Extensions' ); ?>

                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=gloo-buddyboss' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="woo_gloo_modules" <?php echo in_array( 'woo_gloo_modules', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Woo Gloo Modules' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=woo-gloo-dashboard' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_zoho" <?php echo in_array( 'gloo_zoho', $active_modules ) ? 'checked="checked"' : ''; ?> />

							<?php _e( 'Forms Extensions', 'gloo' ); ?>
                            <small class="module-setting">
                                <a href="<?php echo admin_url( 'admin.php?page=gloo-zoho-setting' ); ?>"><?php _e( 'settings' ); ?></a>
                            </small>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Zoho CRM form action' ); ?></p>
                                    <p><?php _e( '- SalesForce CRM form action' ); ?></p>
                                    <p><?php _e( '- ActiveTrail Form Submit Action' );?></p>
                                    <p><?php _e( '- Autocomplete Address Fields' );?></p>
                                    <p><?php _e( '- Select2 Fields' );?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Fluid dynamics -->
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title"><img
                            src="<?php echo $images_url . 'FLUID DYNAMIC title only.jpg' ?>"/></div>
                <ul>
                    <li>
                        <lable>
                        <input type="checkbox" class="flipswitch" value="1"
                                   name="repeater_dynamic_tag" <?php echo in_array( 'repeater_dynamic_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Repeater Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                        <input type="checkbox" class="flipswitch" value="1"
                                   name="dynamify_repeaters" <?php echo in_array( 'dynamify_repeaters', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Dynamify Repeaters' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="fluid_visibility" <?php echo in_array( 'fluid_visibility', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'Fluid Logic' ); ?>
                            <span><?php _e('Beta');?></span>
                        </lable>
                    </li>
                    <li>
                        <?php 
                        $gloo_google_access_token = get_option('gloo_google_access_token');
                        $gloo_google_key = get_option('gloo_google_key');
                        $setting_text = 'activate';
                        $url = admin_url( 'admin.php?page=gloo_google_client' );
                        $is_setting_source = false; 

                        if(!empty($gloo_google_access_token) && !empty($gloo_google_key)) { 
                            $setting_text = 'sources';
                            $url = admin_url( 'admin.php?page=data-source' );
                            $is_setting_source = true;
                        } ?>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_dynamic_tag_maker" <?php echo in_array( 'gloo_dynamic_tag_maker', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Google Spreadsheet Tag' ); ?>
                            <small class="module-setting">
                                <a href="<?php echo $url; ?>"><?php echo $setting_text; ?></a>
                                <?php if($is_setting_source): ?>
                                <a href="<?php echo admin_url( 'admin.php?page=gloo_google_client' ); ?>"><?php _e('settings'); ?></a>
                                <?php endif; ?> 
                            </small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="dynamic_tags_everywhere" <?php echo in_array( 'dynamic_tags_everywhere', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Dynamic Tag Everywhere' ); ?>
                        </lable>
                    </li>
                     <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="dynamic_nav" <?php echo in_array( 'dynamic_nav', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Dynamic Nav Menu' ); ?>
                            
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=gloo_dnm_elementor_addon' ); ?>"><?php _e( 'settings' ); ?></a></small>
                            <span><?php _e('Beta');?></span>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="taxonomy_terms_dynamic_tags" <?php echo in_array( 'taxonomy_terms_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Taxonomy Terms Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_dynamic_composer" <?php echo in_array( 'gloo_dynamic_composer', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Dynamic Composer Kit' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Dynamic composer Widget' ); ?></p>
                                    <p><?php _e( '- Dynamic Css Composer Extension' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="elementor_global_tag" <?php echo in_array( 'elementor_global_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Globals Dynamic Tag' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Global Color Dynamic Tag' ); ?></p>
                                    <p><?php _e( '- Global Fonts Dynamic Tag' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <div class="gloo-check">
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="dynamic_attributes" <?php echo in_array( 'dynamic_attributes', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                        </div>
						<?php _e( 'Dynamic HTML Attributes Repeater' ); ?>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_time_span_dynamic_tag" <?php echo in_array( 'gloo_time_span_dynamic_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( ' Time Span Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="acf_dynamic_tag" <?php echo in_array( 'acf_dynamic_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'ACF Relationship Field Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="jet_relation_dynamic_tag" <?php echo in_array( 'jet_relation_dynamic_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Jet Relationship Field Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <!-- <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_gmb_review" <?php echo in_array( 'gloo_gmb_review', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'GMB Reviews Kit' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Google My Buisness kit', 'gloo' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_facebook_review" <?php echo in_array( 'gloo_facebook_review', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Facebook Reviews Kit' ); ?>
                        </lable>
                    </li> -->
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="native_dynamic_tags_kit" <?php echo in_array( 'native_dynamic_tags_kit', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Native Dynamic Tag Kit' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e('- Plugins Dynamic Tags');?></p>
                                    <p><?php _e('- Context Dynamic Tag');?></p>
                                    <p><?php _e('- User Tags');?></p>
                                    <p><?php _e('- Current URL Tags');?></p>
                                    <p><?php _e('- WP Nonce Tag');?></p>
                                    <p><?php _e('- User Role Tag');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="schema_control" <?php echo in_array( 'schema_control', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Schema Control' ); ?>
                            <span><?php _e('Beta');?></span>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="query_control" <?php echo in_array( 'query_control', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Query Control' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e('- Elementor posts widget control');?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="random_string_dynamic_tag" <?php echo in_array( 'random_string_dynamic_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Random String Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="cookies_dynamic_tag" <?php echo in_array( 'cookies_dynamic_tag', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Cookies Dynamic Tag' ); ?>
                        </lable>
                    </li>
                </ul>
            </div>
            <div class="gloo-support">
                <div class="gloo-fly">
                    <a href="https://desk.zoho.eu/portal/gloodesk/" target="_blank"
                       class="gloo-btn-black"><?php _e( 'Support' ); ?></a>
                </div>
            </div>
        </div>
        <!-- Interactor -->
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title"><img
                            src="<?php echo $images_url . 'INTERACTOR TITLE ONLY.jpg' ?>"/></div>
                <ul>
                    <li>
                        <div>
                            <input type="checkbox" class="flipswitch" name="interactor"
                                   value="1" <?php echo in_array( 'interactor', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Interactor Engine' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=interactor-settings' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </div>
                    </li>
                    <li style="display: none;">
                        <div class="gloo-check">
                            <input type="checkbox" class="flipswitch" value="1" disabled/>
                        </div>
						<?php _e( 'Animator Engine' ); ?>
                    </li>
                    <li>
                        <div>
                            <input type="checkbox" class="flipswitch" name="datalayer_connector"
                                   value="1" <?php echo in_array( 'datalayer_connector', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'GTM DataLayer Connector' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=datalayer_connector' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </div>
                    </li>
                    <li style="display: none;">
                        <div class="gloo-check">
                            <input type="checkbox" class="flipswitch" value="1" disabled/>
                        </div>
						<?php _e( 'Ajax Module For Interactor' ); ?>
                    </li>
                    <li>
                        <div>
                            <input type="checkbox" class="flipswitch" name="zapier_connector"
                                   value="1" <?php echo in_array( 'zapier_connector', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Zapier Connector' ); ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input type="checkbox" class="flipswitch" name="custom_webhook_connector"
                                   value="1" <?php echo in_array( 'custom_webhook_connector', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Custom Webhook Connector' ); ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input type="checkbox" class="flipswitch" name="interactor_cookies"
                                   value="1" <?php echo in_array( 'interactor_cookies', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Cookies Connector' ); ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input type="checkbox" class="flipswitch" name="interactor_gsap"
                                   value="1" <?php echo in_array( 'interactor_gsap', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'GSAP ScrollTrigger Connector' ); ?>
                        </div>
                    </li>
                </ul>

                <div class="gloo-support">
                    <!-- <div class="gloo-key">
                        <p><strong><?php _e( 'LICENSE:' ); ?></strong></p>

						<?php if ( isset( $gloo_license_info['key'] ) && ! empty( $gloo_license_info['key'] ) ): ?>
                            <p><?php echo substr( $gloo_license_info['key'], 0, 10 ) . '********'; ?></p>
						<?php else : ?>
                            <p><?php _e( 'Please Activate' ); ?></p>
						<?php endif; ?>
                    </div> -->
                </div>
				<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-features.php' ); ?>
            </div>
        </div>
    </div>
</div>
<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); ?>
