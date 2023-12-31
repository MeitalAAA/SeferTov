<?php
define("WP_DEBUG", false);
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'sefe1yec_up1');

/** Database username */
define('DB_USER', 'sefe1yec_up1');

/** Database password */
define('DB_PASSWORD', '%7Hq7ftxfEC1@8os');
define('WP_MEMORY_LIMIT', '512');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'QhhqMOTr-^9Z<2&ldZyA^^_c!3E%HIQ/<*&6Wm92RXB>{qp7_(<?FQRBicDLW,3u' );
define( 'SECURE_AUTH_KEY',   'Z=]8A <knC+NktN4?xm6B]{Ow<s.%&Aa~lN==rq,d:jGcm[N/J`SFZR8+.T><!:7' );
define( 'LOGGED_IN_KEY',     '8|p*=_}h5[e/R7re%S$3UE(hg1=:=o}JnKafa(V/IAx2*dcyFq3lz%gPHP*Gtoqm' );
define( 'NONCE_KEY',         'sn + jR6l=MmCsz6UU6Rgnbf&9gHYwtI3D.FE%;?~uy0&U:u$IVIg!QewOG|QvQ)' );
define( 'AUTH_SALT',         ':ujI?#C(3Nz5QfTk]FoG)a2OyKQ] 8W;;*W2$QP;1[f|o,=)m!Uj5HGsd`q6-l`#' );
define( 'SECURE_AUTH_SALT',  'a(#*VwMs(b^|$9+KAlx_7hNL(2I4]T4No&_gY1XVd+!|d.rq+s=6ie3C}RsIi$(9' );
define( 'LOGGED_IN_SALT',    ':qxdxA0{]nlPq5}><cu!Q~+ptW^zDkC>t;q*xD2>l0:,Up?;-g2,Bj.bvE<[Cqr{' );
define( 'NONCE_SALT',        'XM#ZmFRn^_fDh&D6IUl8z,1Rn`{:R$l^3BBw](jy2 BX>URt&dR&v-YO~;9%n`9Q' );

/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'fyu_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */

/* Add any custom values between this line and the "stop editing" line. */
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
