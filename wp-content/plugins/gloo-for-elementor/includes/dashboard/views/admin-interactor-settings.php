<?php
include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' );
?>
    <form action='' method='post'>
		<?php
		settings_fields( 'gloo_interactor_settings' );
		do_settings_sections( 'gloo_interactor_settings' );
		submit_button();
		?>
    </form>
<?php
include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' );
