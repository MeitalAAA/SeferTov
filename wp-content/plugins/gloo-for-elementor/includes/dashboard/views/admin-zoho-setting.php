<?php
$all_modules    = gloo()->modules->get_all_modules();
$active_modules = gloo()->modules->get_active_modules();

include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>
<div class="gloo-item-container">
    <img src="<?php echo $images_url . 'GlooHero.png' ?>" class="gloo-building"/>
    <div class="gloo-items">
        <!-- zoho -->
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title">
                <img src="<?php echo $images_url . 'forms-extensions.jpeg' ?>"/></div>
                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_repeater_field" <?php echo in_array( 'gloo_repeater_field', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Repeater Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_for_terms" <?php echo in_array( 'gloo_form_fields_for_terms', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'Terms field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_for_cpt" <?php echo in_array( 'gloo_form_fields_for_cpt', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'Custom Post Type Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_for_users" <?php echo in_array( 'gloo_form_fields_for_users', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'User Type Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_elementor_select2_fields" <?php echo in_array( 'gloo_elementor_select2_fields', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Select2 Fields' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_color_picker" <?php echo in_array( 'gloo_form_fields_color_picker', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Color Picker Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_wysiwyg_field" <?php echo in_array( 'gloo_wysiwyg_field', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Wysiwyg Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_autocomplete_address_fields" <?php echo in_array( 'gloo_autocomplete_address_fields', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Autocomplete Address Fields' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=gloo_autocomplete_address' ); ?>"><?php _e( 'settings' ); ?></a></small>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( 'Add the class {autocomplete_address} to any form input and make it compatible with Google Autocomplete Places API.' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_field_validation" <?php echo in_array( 'gloo_form_field_validation', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Form Field Validation', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_for_datepicker" <?php echo in_array( 'gloo_form_fields_for_datepicker', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Datepicker Field', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_checkbox_radio_field_control" <?php echo in_array( 'gloo_checkbox_radio_field_control', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Checkbox & Radio Field Control', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="composer_field" <?php echo in_array( 'composer_field', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Calculation Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_for_range" <?php echo in_array( 'gloo_form_fields_for_range', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Range Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_image_upload_ui" <?php echo in_array( 'gloo_form_image_upload_ui', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Image Upload UI', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_filepond_upload" <?php echo in_array( 'gloo_form_filepond_upload', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Better Gallery Form Field' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="image_crop" <?php echo in_array( 'image_crop', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Image Cropping UI', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="ajax_reload_prevention" <?php echo in_array( 'ajax_reload_prevention', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Ajax Reload Prevention', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_country_dial_code_field" <?php echo in_array( 'gloo_form_country_dial_code_field', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Country Dial Code Field', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_form_fields_for_time_span" <?php echo in_array( 'gloo_form_fields_for_time_span', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Time Span Field', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="signature_field" <?php echo in_array( 'signature_field', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Signature Field', 'gloo' ); ?>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title">
                <img src="<?php echo $images_url . 'forms-actions.jpeg' ?>"/></div>
                <ul>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_frontend_post_submission" <?php echo in_array( 'gloo_frontend_post_submission', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'Frontend Post Submission' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=gloo_frontend_post_creation' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_frontend_post_editing" <?php echo in_array( 'gloo_frontend_post_editing', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'Frontend Post Editing' ); ?>
                            <small class="module-setting"><a
                                        href="<?php echo admin_url( 'admin.php?page=gloo_frontend_post_editing' ); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_frontend_user_submission" <?php echo in_array( 'gloo_frontend_user_submission', $active_modules ) ? 'checked="checked"' : ''; ?>/>
			                <?php _e( 'Frontend User Submission' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_frontend_user_editing" <?php echo in_array( 'gloo_frontend_user_editing', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Frontend User Editing' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_frontend_comment_submission" <?php echo in_array( 'gloo_frontend_comment_submission', $active_modules ) ? 'checked="checked"' : ''; ?>/>
							<?php _e( 'Submit Comment Form Action' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="form_otp" <?php echo in_array( 'form_otp', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'OTP form action' ); ?>                            
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1" name="zoho_crm_dynamic_form_action" <?php echo in_array( 'zoho_crm_dynamic_form_action', $active_modules ) ? 'checked="checked"' : ''; ?>/>
                            <?php _e( 'Zoho CRM Form Submit Action' ); ?>
                            <small class="module-setting"><a href="<?php echo admin_url( 'admin.php?page=gloo_zoho_crm_form_submit_action'); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="Salesforce_Crm_Dynamic_Form_Action" <?php echo in_array( 'Salesforce_Crm_Dynamic_Form_Action', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Salesforce CRM Form Submit Action', 'gloo' ); ?>
                            <small class="module-setting"><a href="<?php echo admin_url( 'admin.php?page=gloo_salesforce_crm_form_submit_action'); ?>"><?php _e( 'settings' ); ?></a></small>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="activetrail_form_submit_action" <?php echo in_array( 'activetrail_form_submit_action', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'ActiveTrail Form Submit Action', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="powerlink_form_action" <?php echo in_array( 'powerlink_form_action', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Powerlink Form Submit Action', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="cookies_form_action" <?php echo in_array( 'cookies_form_action', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Save Cookies Form Action', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="form_actions_pro" <?php echo in_array( 'form_actions_pro', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Form Actions Pro', 'gloo' ); ?>
                            <span><?php _e('Alpha');?></span>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="login_form_action" <?php echo in_array( 'login_form_action', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'Login Form Action', 'gloo' ); ?>
                        </lable>
                    </li>
                    <li>
                        <lable>
                            <input type="checkbox" class="flipswitch" value="1"
                                   name="gloo_pdf_generator" <?php echo in_array( 'gloo_pdf_generator', $active_modules ) ? 'checked="checked"' : ''; ?> />
							<?php _e( 'PDF Generator Form Action', 'gloo' ); ?>
                            <small class="module-setting"><a href="https://gloo.ooo/plugins/gloo-pdf-generator.zip" target="_blank"><?php _e( 'Download' ); ?></a></small>
                            <div class="gloo-tool">
                                <a href="#" class="tool-tip"></a>
                                <div class="tool-tip-content">
                                    <p><?php _e( 'The PDF Generator requires a standalone PHP library that needs to be downloaded and installed as a separate plugin. Click on download, install, and activate the plugin before using this feature.' ); ?></p>
                                </div>
                            </div>
                        </lable>
                    </li>
                </ul>
            </div>
        </div>
        <div class="gloo-feature">
            <div class="gloo-box">
                <div class="gloo-feature-title"><img
                            src="<?php echo $images_url . 'glooniversitybanner.jpg' ?>"/></div>
				<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-features.php' ); ?>
            </div>
        </div>
    </div>
</div>
<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); ?>
