<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
/** @noinspection DuplicatedCode */
opcache_invalidate(__FILE__, true);
header('Cache-Control: no-store');

/**
 * uPress auto login script
 *
 * @package    uPress Auto Login
 * @author     uPress <support@upress.co.il>
 * @link       https://www.upress.co.il
 */
// phpcs:ignoreFile

define( 'WP_USE_THEMES', false );
define( 'WP_ADMIN', true );

if( ! file_exists( __DIR__ . '/wp-load.php' ) ) {
    require_once( dirname( __DIR__ ) . '/wp-load.php' );
} else {
    require_once( __DIR__ . '/wp-load.php' );
}
global $wpdb, $wp_version;

class UpressWpAutologin {
    const MIN_WP_VERSION = '3.7';

    public static function check_version() {
        global $wp_version;

        if ( version_compare( $wp_version, self::MIN_WP_VERSION, '<' ) ) {
            wp_die( "WordPress version is too old ({$wp_version} < {self::MIN_WP_VERSION}).", 400 );
            exit;
        }
    }

    public static function check_auth() {
        // No authorization parameter? get out...
        if ( empty( $_GET ) ) {
            wp_die( 'Authorization failed: Link expired or invalid, try loggin in again through the link in the dashboard.', 401 );
            exit;
        }
    }

    public static function get_home_url() {
        $current_url = "http" . ( is_ssl() ? 's' : '' ) . "://{$_SERVER['HTTP_HOST']}" . $_SERVER['REQUEST_URI'];

        return strtolower( trim( substr( $current_url, 0, stripos( $current_url, basename( __FILE__ ) ) - 1 ) ) );
    }

    public static function redirect_to_home_url() {
        $site_url = strtolower( trim( get_option( 'siteurl' ) ) );

        if ( self::get_home_url() != $site_url ) {
            wp_redirect( $site_url . "/" . basename( __FILE__ ) . "?" . array_keys($_GET)[0] );
            exit;
        }
    }

    /**
     * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
     *
     * @param string|array $ips List of IPs or subnets (can be a string if only a single one)
     *
     * @return bool
     */
    public static function checkIp($requestIp, $ips)
    {
        if (!is_array($ips)) {
            $ips = [$ips];
        }

        $method = substr_count($requestIp, ':') > 1 ? 'checkIp6' : 'checkIp4';

        foreach ($ips as $ip) {
            if (self::$method($requestIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compares two IPv4 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @param string $ip IPv4 address or subnet in CIDR notation
     *
     * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet
     */
    public static function checkIp4($requestIp, $ip)
    {
        if (!filter_var($requestIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
            return false;
        }

        if (stripos($ip, '/') !== false) {
            $ip = explode('/', $ip, 2);
            $address = $ip[0];
            $netmask = $ip[1];

            if ('0' === $netmask) {
                return filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
            }

            if ($netmask < 0 || $netmask > 32) {
                return false;
            }
        } else {
            $address = $ip;
            $netmask = 32;
        }

        if (false === ip2long($address)) {
            return false;
        }

        return 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
    }

    /**
     * Compares two IPv6 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @author David Soria Parra <dsp at php dot net>
     *
     * @see https://github.com/dsp/v6tools
     *
     * @param string $ip IPv6 address or subnet in CIDR notation
     *
     * @return bool
     */
    public static function checkIp6($requestIp, $ip)
    {
        if (!((\extension_loaded('sockets') && \defined('AF_INET6')) || @inet_pton('::1'))) {
            return false;
        }

        if (stripos($ip, '/') !== false) {
            $ip = explode('/', $ip, 2);
            $address = $ip[0];
            $netmask = $ip[1];

            if ('0' === $netmask) {
                return (bool) unpack('n*', @inet_pton($address));
            }

            if ($netmask < 1 || $netmask > 128) {
                return false;
            }
        } else {
            $address = $ip;
            $netmask = 128;
        }

        $bytesAddr = unpack('n*', @inet_pton($address));
        $bytesTest = unpack('n*', @inet_pton($requestIp));

        if (!$bytesAddr || !$bytesTest) {
            return false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xFFFF >> $left) & 0xFFFF;
            if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
                return false;
            }
        }

        return true;
    }
    public static function get_server_ip() {
        $server_ip = $_SERVER['SERVER_ADDR'];

        if ( self::checkIp( $server_ip, ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'] ) ) {
            $server_ip = gethostbyname( gethostname() );
        }

        return $server_ip;
    }

    public static function get_client_ip() {
        $client_ip = $_SERVER['REMOTE_ADDR'];

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $client_ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
            $client_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return $client_ip;
    }

    public static function verify_google() {
        $google_ips = get_site_transient( '_upress_autologin_google_ips' );
        if ( empty( $google_ips ) ) {
            $google_ips = file_get_contents( 'https://www.gstatic.com/ipranges/goog.txt' );
            $google_ips = preg_split( '/\r\n|\r|\n/', $google_ips );
            set_site_transient( '_upress_autologin_google_ips', $google_ips, DAY_IN_SECONDS );
        }

        if( self::checkIp( self::get_client_ip(), $google_ips ) || stripos( $_SERVER['HTTP_USER_AGENT'], 'googlebot' ) !== false ) {
            http_response_code(404);
            header('HTTP/1.0 404 Not Found', true, 404);
            exit;
        }
    }
}

UpressWpAutologin::verify_google();
UpressWpAutologin::redirect_to_home_url();
UpressWpAutologin::check_version();
UpressWpAutologin::check_auth();

$current_url = UpressWpAutologin::get_home_url();

$users          = [];
$sites          = [];
$network_admins = [];

$auth_key          = trim( array_keys($_GET)[0] );
$verification_hash = '';
$server_ip         = UpressWpAutologin::get_server_ip();
$client_ip         = UpressWpAutologin::get_client_ip();

if ( function_exists( 'wp_roles' ) ) {
    $roles = wp_roles()->role_objects;
} else {
    global $wp_roles;
    $roles = $wp_roles->role_objects;
}

uasort( $roles, function ( $a, $b ) {
    if ( 'administrator' == $a->name ) {
        return - 1;
    }
    if ( 'administrator' == $b->name ) {
        return 1;
    }

    return strnatcmp( $a->name, $b->name );
} );

// Load list of users available to login to
if ( is_multisite() ) {
    // Get regular users from all blogs
    // get_sites() not available on wp < 4.6
    if( function_exists( 'get_sites' ) ) {
        $sites = get_sites();
    } else {
        $sites = wp_get_sites();
    }

    foreach ( $sites as $site ) {
        $blog_id    = is_object( $site ) ? $site->blog_id : $site['blog_id'];
        $site_users = get_users( [ 'blog_id' => $blog_id ] );
        $users      = array_merge( $users, $site_users );
    }

    // Get multisite super admins
    $wp_network_admins        = $wpdb->get_results( 'SELECT ID, user_login FROM ' . $wpdb->users );
    $network_admins_usernames = unserialize( $wpdb->get_var( 'SELECT * FROM ' . $wpdb->sitemeta . ' WHERE meta_key = \'site_admins\'', 3 ) );
    $wp_network_admins        = array_filter( $wp_network_admins, function ( $user ) use ( $network_admins_usernames ) {
        return in_array( $user->user_login, $network_admins_usernames );
    } );
    $wp_network_admins        = array_map( function ( $user ) {
        return get_user_by( 'ID', $user->ID );
    }, $wp_network_admins );
    $users                    = array_merge( $users, $wp_network_admins );
} else {
    // This is a normal wordpress install, get all regular users
    $users = get_users( [ 'role__in' => [ 'administrator', 'editor' ], 'number' => 100 ] );
}

// Filter out duplicate users
$mapped_users = [];
$users        = array_filter( $users, function ( $user ) use ( &$mapped_users ) {
    if ( in_array( $user->ID, $mapped_users ) ) {
        return false;
    }
    $mapped_users[] = $user->ID;

    return true;
} );
sort( $users );


if ( count( $_POST ) ) {
    if(isset($_POST['unload'])) {
        wp_die( 'Login cancelled.', 200 );
        exit;
    }

    // Check the verification hash
    $upress_auth     = ! empty( $_POST['token'] ) ? trim( $_POST['token'] ) : '';
    $calculated_hash = hash_hmac( 'sha256', $client_ip . $server_ip . $auth_key, 'EoE8mNAT7Ym975yJdNzEob8qS3ijfrONAT7x' );

    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wp-autologin' ) || ! hash_equals( $calculated_hash, $upress_auth ) ) {
        wp_die( 'Authorization failed: You are not allowed to login at this time.', 403 );
        exit;
    }

    if ( count( $users ) > 1 ) {
        $user_id    = (int) $_POST['uid'];
        $user       = get_user_by( 'id', $user_id );
        $user_login = $user->user_login;
    } else {
        $user_id    = $users[0]->ID;
        $user_login = $users[0]->user_login;
    }

    $user = wp_set_current_user( $user_id, $user_login );
    wp_set_auth_cookie( $user_id, true );
    do_action( 'wp_login', $user_login, $user );

    wp_redirect( get_admin_url() );
    exit;
}

// Get auth data for current website
$verify = wp_remote_post( 'https://my4.upress.io/api/autologin/authorize/v2', array(
    'user-agent' => 'uPressAutologin/' . $server_ip,
    'sslverify'  => true,
    'blocking'   => true,
    'timeout'    => 30,
    'body'       => array(
        'v'         => defined( 'AUTOLOGIN_DEV' ) ? AUTOLOGIN_DEV : $auth_key,
        'ip'        => $client_ip,
        'server_ip' => $server_ip,
        'host'      => get_site_url(),
        'dev'       => defined( 'AUTOLOGIN_DEV' ) ? AUTOLOGIN_DEV : ''
    ),
) );
$verify = json_decode( wp_remote_retrieve_body( $verify ), true );
if ( is_wp_error( $verify ) || ! isset( $verify['hash'] ) ) {
    wp_die( 'Authorization failed: Request expired.', 401 );
    exit;
}
$verification_hash = $verify['hash'];

?><!doctype html>
<html>
<head>
    <title><?php echo esc_html( get_bloginfo( 'name' ) ); ?> One Click Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="none, noarchive, nositelinkssearchbox, nosnippet, notranslate, noimageindex">
    <style>
        [hidden] {
            display: none !important;
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f0f0f1;
            min-width: 0;
            color: #3c434a;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
            font-size: 13px;
            line-height: 1.4;
        }

        #login {
            max-width:  600px;
            width: 100%;
            margin: 0 auto;
            padding: 5% 0 0;
        }

        .login form {
            margin-top: 20px;
            margin-left: 16px;
            margin-right: 16px;
            padding: 26px 24px;
            font-weight: 400;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.13);
        }
        label {
            font-weight: 400;
            font-size: 14px;
            line-height: 1.5;
            display: inline-block;
            margin-bottom: 3px;
        }
        select {
            display: block;
            outline: none;
            height: 38px;
            box-shadow: 0 0 0 transparent;
            border-radius: 4px;
            border: 0.0625rem solid #8c8f94;
            background-color: #fff;
            color: #2c3338;

            font-size: 24px;
            line-height: 1.33333333;
            width: 100%;
            padding: 0.1875rem 0.3125rem;
            margin: 10px 0;
            min-height: 40px;
            max-height: none;
        }
        select.readonly, select[readonly] {
            background: rgba(255,255,255,.5);
            border-color: rgba(222,222,222,.75);
            -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.04);
            box-shadow: inset 0 1px 2px rgba(0,0,0,.04);
            color: rgba(51,51,51,.5);
            pointer-events: none;
        }

        .login button {
            display: inline-block;
            font-size: 13px;
            margin: 0;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            border-radius: 3px;
            white-space: nowrap;
            box-sizing: border-box;

            background: #2271b1;
            border-color: #2271b1;
            color: #fff;
            text-decoration: none;
            text-shadow: none;

            min-height: 32px;
            line-height: 2.30769231;
            padding: 0 12px;

            float: right;
        }
        button > svg {
            vertical-align: text-bottom;
            height: 1em;
            width: 1em;
            animation: spin 0.5s infinite linear;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @-webkit-keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .login h1 {
            text-align: center;
        }
        .login h1 a {
            background-image: none, url('data:image/png;base64,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');
            width: 160px;
            background-size: 160px;
            background-position: center top;
            background-repeat: no-repeat;
            height: 84px;
            font-size: 20px;
            font-weight: 400;
            line-height: 1.3;
            margin: 0 auto 25px;
            padding: 0;
            text-decoration: none;
            text-indent: -9999px;
            outline: 0;
            overflow: hidden;
            display: block;
        }
    </style>
</head>
<body class="login login-action-login wp-core-ui  locale-en-us">
<div id="login">
    <h1>
        <a href="https://my.upress.co.il/"
           aria-hidden="true"
           tabindex="-1"
           rel="noopener nofollow"
           target="_blank">
        </a>
    </h1>

    <form method="post">
        <?php wp_nonce_field( 'wp-autologin' ); ?>
        <input type="hidden" name="token" value="<?php echo esc_attr( $verification_hash ); ?>">

        <div>
            <label for="uid">Login to "<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" as</label><br/>
            <select id="uid" name="uid" class="select" <?php echo count( $users ) <= 1 ? 'disabled' : ''; ?> style="width: 100%; max-width: 100%;">
                <?php if ( is_multisite() ) : ?>
                    <optgroup label="Super Administrators">
                        <?php foreach ( $users as $user ) : if ( ! in_array( $user->user_login, $network_admins_usernames ) ) {
                            continue;
                        } ?>
                            <option value="<?php echo esc_attr( $user->ID ); ?>">
                                <?php echo esc_html( $user->user_login ); ?>
                                <?php echo esc_html( $user->user_login !== $user->display_name ? ' (' . $user->display_name . ')' : '' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php foreach ( $roles as $key => $role ) : ?>
                    <?php
                    $role_users = array_filter( $users, function ( $user ) use ( $key ) {
                        return $user->has_cap( $key );
                    } );
                    if ( count( $role_users ) <= 0 ) {
                        continue;
                    }
                    $users = array_udiff($users, $role_users, function($a, $b) { return $a->ID == $b->ID; });
                    ?>
                    <optgroup label="<?php echo esc_attr( ucwords( str_replace( '_', ' ', $role->name ) ) ); ?>">
                        <?php foreach ( $role_users as $user ) : ?>
                            <option value="<?php echo esc_attr( $user->ID ); ?>">
                                <?php echo esc_html( $user->user_login ); ?>
                                <?php echo esc_html( $user->user_login !== $user->display_name ? ' (' . $user->display_name . ')' : '' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>

        <p class="submit">
            <button name="wp-submit" id="wp-submit" class="button button-primary button-large" onclick="setTimeout((function() { this.disabled = true; this.querySelector('svg').removeAttribute('hidden')}).bind(this), 100)">
                <svg width="24" height="24" viewBox="0 0 24 24" hidden><path fill="currentColor" d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" /></svg>
                Login
            </button>
        </p>
    </form>
</div>

<div class="clear"></div>
<script>
    window.addEventListener("beforeunload", function (e) {
        var formdata = new FormData();
        formdata.append('unload', 'true');

        var request = new XMLHttpRequest();
        request.open('POST', window.location, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.send(formdata);

        return;
    });
</script>
</body>
</html>