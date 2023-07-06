<?php

spl_autoload_register( function ( $class ) {

	// project-specific namespace prefix
	$prefix = 'Gloo\RepeaterField';
	
	// If the specified $class does not include our namespace, duck out.
	if ( false === strpos( $class, 'Gloo\RepeaterField' ) ) {
		return; 
	}
	// base directory for the namespace prefix
	$base_dir = gloo()->modules_path( 'repeater-field/inc/classes/');

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
