<?php

// include the generic functions file.
include_once gloo()->modules_path( 'woocommerce-price-widget/inc/functions.php' );


spl_autoload_register( function ( $class ) {

	// project-specific namespace prefix
	$prefix = 'OTW\WoocommercePriceWidget';
	
	// If the specified $class does not include our namespace, duck out.
	if ( false === strpos( $class, 'OTW\WoocommercePriceWidget' ) ) {
		return; 
	}
	// base directory for the namespace prefix
	//$base_dir = plugin_dir_path(OTW_WOOCOMMERCE_PRICE_WIDGET_FILE) .'inc/classes/';
	$base_dir = gloo()->modules_path( 'woocommerce-price-widget/inc/classes/');

	// does the class use the namespace prefix?
	$len = strlen( $prefix );

	// get the relative class name
	$relative_class = substr( $class, $len+1 );
	
	$file = $base_dir .  $relative_class  . '.php';

	// if the file exists, require it
	if ( file_exists( $file ) ) {
		include_once( $file );
	}
} );
