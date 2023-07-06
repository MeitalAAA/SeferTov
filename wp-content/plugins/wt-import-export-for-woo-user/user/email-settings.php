<?php
if (!defined('WPINC')) {
	die;
}
?>
<div class="wt-iew-tab-content" data-id="wt-usermail">

	<div class="wt_iew_sub_tab_container">		
		<div class="wt_iew_sub_tab_content" data-id="usermail" style="display:block;">

			<h3><?php esc_html_e('Email Customization'); ?></h3>
			<span><?php	esc_html_e( 'Here you can customize the email send to newly imported users on your site.' );?></span>


			<form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>" id="wt_iew_email_settings_form" class="wt_iew_email_settings_form">			
				<?php
				// Set nonce:
				if (function_exists('wp_nonce_field')) {
					wp_nonce_field(WT_IEW_PLUGIN_ID);
				}
				?>
				<table class="form-table wt-iew-form-table">
					<tr>
						<th style="width: 25% !important;"><label><?php esc_html_e("Email subject", 'wt-import-export-for-woo'); ?></label></th>
						<td>
							<?php
							$user_email_subject_default = "Your {blogname} account has been created!";
							$user_email_subject = get_option( "wt_iew_user_email_subject", $user_email_subject_default );
							?>
							<input type="text" name="wt_iew_email_subject" value="<?php echo $user_email_subject; ?>">
						</td>
						<td></td>
					</tr>
					<tr>
						<th style="width: 25% !important;"><label><?php esc_html_e("Email body", 'wt-import-export-for-woo'); ?></label></th>
						<td style="width: 60%">
							<?php 
							$editor_settings = array('textarea_rows'=>40, 'editor_height'=> 400 );
							$user_email_body_default = "Hi {first_name},

Thanks for creating an account on {blogname}. Your username is {user_name}. You can access your account area to view orders, change your password.

Your password has been generated: {user_pass}

We look forward to seeing you soon.

{blogname}";
							$user_email_body = get_option( "wt_iew_user_email_body", $user_email_body_default );
							wp_editor( $user_email_body, 'wt_iew_user_email_body', $editor_settings); ?>						
						</td>
						<td></td>
					</tr>
					<tr><th style="width: 25% !important;"></th>
						<td>
							
							<span><?php	esc_html_e( 'You can use below placeholders in the template:' );?> <br/><br/>								
								<code>First name: <i>{first_name}</i></code><br/>
								<code>Last name: <i>{last_name}</i></code><br/>
								<code>User login: <i>{user_name}</i></code><br/>
								<code>Password <i>{user_pass}</i></code><br/>
								<code>Website: <i>{blogname}</i></code><br/>
								<code>Display name: <i>{display_name}</i></code><br/>
								<code>Nicename: <i>{user_nicename}</i></code>
							</span>
						</td></tr>
				</table>
				<?php
				$settings_button_title = __('Save settings', 'wt-import-export-for-woo');
				$settings_button_title = isset($settings_button_title) ? $settings_button_title : __('Update Settings', 'wt-import-export-for-woo');
				?>
				<div style="clear: both;"></div>
				<div class="wt-iew-plugin-toolbar bottom">
					<div class="left">
					</div>
					<div class="right">
						<input type="submit" name="wt_iew_update_admin_settings_form" value="<?php _e($settings_button_title); ?>" class="button button-primary" style="float:right;"/>
						<span class="spinner" style="margin-top:11px"></span>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>