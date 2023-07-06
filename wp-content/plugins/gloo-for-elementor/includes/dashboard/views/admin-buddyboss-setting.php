<?php
$all_modules    = gloo()->modules->get_all_modules();
$active_modules = gloo()->modules->get_active_modules();

include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>
<div class="gloo-item-container">
    <img src="<?php echo $images_url . 'GlooHero.png' ?>" class="gloo-building"/>
    <div class="gloo-items">
        
        <!-- Buddyboss press -->
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title"><img
                            src="<?php echo $images_url . 'BUDDYPRESS  KIT header.jpg' ?>"/></div>
                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                    name="bb_dynamic_tags" <?php echo in_array( 'bb_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'BuddyBoss Dynamic Tag Kit' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="bp_community_dynamic_tags" <?php echo in_array( 'bp_community_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Community Dynamic Tag' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Get Friends' ); ?></p>
                                    <p><?php _e( '- Get Online Users' ); ?></p>
                                    <p><?php _e( '- Get Newest Memebers' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="bp_activities_dynamic_tags" <?php echo in_array( 'bp_activities_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Activities Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                    name="bb_group_dynamic_tags" <?php echo in_array( 'bb_group_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Buddyboss Groups Dynamic Tag' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- BuddyBoss Group Name' ); ?></p>
                                    <p><?php _e( '- BuddyBoss Group Current User Role' ); ?></p>
                                    <p><?php _e( '- BuddyBoss Group Fields' ); ?></p>
                                    <p><?php _e( '- BuddyBoss Group Settings' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="jsf_buddyboss" <?php echo in_array( 'jsf_buddyboss', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'JetSmartFilters BuddyBoss Addon' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="jedv_bp" <?php echo in_array( 'jedv_bp', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Dynamic Visibility: BuddyPress Addon' ); ?>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Buddyboss press -->
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title">
                    <img src="<?php echo $images_url . 'MEMEBERSHIPS+ HEADER.jpg' ?>"/>
                </div>
                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="product_related_courses" <?php echo in_array( 'product_related_courses', $active_modules ) ? 'checked="checked"' : ''; ?> />
                            <?php _e( 'Woocommerce Related Courses Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="dynamic_visibility_wishlist" <?php echo in_array( 'dynamic_visibility_wishlist', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Wishlist Dynamic Tag Kit' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="user_agents_extension" <?php echo in_array( 'user_agents_extension', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <small class="module-setting"><a href="<?php echo admin_url( 'admin.php?page=gloo_user_agents_extension' ); ?>"><?php _e( 'settings' ); ?></a></small>
                            <?php _e( 'User Agents Extension' ); ?>
                            <span><?php _e('Beta');?></span>
                            
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( '- User Agent Registration Capture System', 'gloo' ); ?></p>
                                    <p><?php _e( '- Current Stored User Agent Dynamic Tag', 'gloo' ); ?></p>
                                    <p><?php _e( '- Current Session User Agent Dynamic Tag', 'gloo' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_affiliate_dynamic_tags" <?php echo in_array( 'gloo_affiliate_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'AffiliateWP Dynamic Tag' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="jedv_ld" <?php echo in_array( 'jedv_ld', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Dynamic Visibility: LearnDash Addon' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="learndash_dynamic_tags" <?php echo in_array( 'learndash_dynamic_tags', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Learndash Dynamic Tag Kit' ); ?>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>   
                                <div class="tool-tip-content">
                                    <p><?php _e( '- Lesson Availability Dynamic Tag', 'gloo' ); ?></p>
                                    <p><?php _e( '- Lessons Id\'s Dynamic Tag', 'gloo' ); ?></p>
                                    <p><?php _e( '- Is User Enrolled Course', 'gloo' ); ?></p>
                                 </div>
                            </div>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Buddyboss press -->
        <div class="gloo-feature no-border">
            <?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-features.php' ); ?>
        </div>
    </div>
</div>
<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); ?>
