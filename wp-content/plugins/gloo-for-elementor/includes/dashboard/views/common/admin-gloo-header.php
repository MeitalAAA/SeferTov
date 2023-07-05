<?php 
$gloo_license_info = get_option('gloo_license_info');
$images_url = gloo()->plugin_url( 'assets/images/admin/' ); 
$interactor_images = gloo()->get_interactor_images(8);
$gloo_data = get_plugin_data(dirname(dirname(dirname(dirname(plugin_dir_path(__FILE__))))).'/gloo-for-elementor.php');
?>
<div class="wrap">
    <div class="gloo-wrapper">
        <!-- gloo bar -->
        <div class="gloo-bar">
            <div class="gloo-logo">
                <img src="<?php echo $images_url . 'GLOO - LOGO WHITE.png' ?>"/>
            </div>
            <div class="gloo-login">
                    <?php if ( isset( $gloo_license_info['key'] ) && ! empty( $gloo_license_info['key'] ) ): ?>
                    <p><?php _e( 'LICENSE : ' );  echo substr( $gloo_license_info['key'], 0, 10 ) . '********'; ?></p>
                    <?php else : ?>
                        <p><?php _e( 'Please Activate' ); ?></p>
                    <?php endif; ?>
                <?php 
                if(isset( $_GET['page'] ) && $_GET['page'] === gloo()->admin_page): ?>
                    <a class="gloo-login-link" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=gloo_for_elementor_settings');?>"><?php _e( 'License' ); ?></a>
                <?php else: ?>
                    <a class="gloo-login-link" href="javascript:history.back();"><?php _e( 'Dashboard' ); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <!-- gloo version -->
        <div class="gloo-version">
            <?php if(isset($gloo_data['Version']) && !empty($gloo_data['Version'])): ?>
                <p><?php _e( 'Ver : ' );  echo $gloo_data['Version']; ?></p>
            <?php endif; 

            if(isset( $_GET['page'] ) && $_GET['page'] === gloo()->admin_page): 
                if(isset($gloo_license_info['status']) && !empty($gloo_license_info['status']) && $gloo_license_info['status'] == 'activate'): ?>
                    <p><?php _e( 'Status :' ); ?><span class="gloo-active"><?php _e( ' Active' ); ?></span></p>
                <?php else :?>
                    <p><?php _e( 'Status :' ); ?><span class="gloo-inactive"><?php _e( ' Inactive' ); ?></span></p>
                <?php endif;
            endif; ?>
        </div>