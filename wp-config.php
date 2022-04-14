<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_hcfqx' );

/** MySQL database username */
define( 'DB_USER', 'wp_y0eyy' );

/** MySQL database password */
define( 'DB_PASSWORD', '_WsLN%BrbO8N55iz' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '85~Mr;wJ/81Ak:igr;720vvZ]1uryQU6P@kJ#yc1;:Ck#6waSF6GWYao!!;Jo289');
define('SECURE_AUTH_KEY', 'oi~W_5iOy)4268#W2q#EP71)]3O03&XSJ7@4MlJ9Vk(m8:O38&#Tj2]C#1V1l%J!');
define('LOGGED_IN_KEY', '-2m*z60Ycs]|56UM!02f-)gw1;@h53k3h(+K&HL)y|7Wy2&W)1dQ4xTu)h&G*Mb%');
define('NONCE_KEY', '1jg~L@V2/2B8(t0g9%*Y8419auYeX*Q+kZl3x@T#5:U[cx#CH%ms9;*6U:PM5b[(');
define('AUTH_SALT', 'F1nd08T#4g143I2W&_0_Ol&0cG+29Xj52Q5I_6_A6#|Xb1-24An3g|+Y|Mg4|-0/');
define('SECURE_AUTH_SALT', 'E]2SjGYQ~Tcg:71@eEuo~9K8x[4bCI[[A0#mAff84K:n;/22_k;1vVMt66%][uf8');
define('LOGGED_IN_SALT', 'zAP1t3~~jiS-W;6y:(:mMw0dd~_3]R*oRC]n]2:1wgmo0oe+/!Z:!6Tv/Nx8+t(:');
define('NONCE_SALT', 'O2345&ylR!yBWx9c4:N59P/aK(5U4pRdOGD;ylCKGx9_A0|m81m-V_P-Q2k/0w;@');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'RSuyRSvPk_';


define('WP_ALLOW_MULTISITE', true);
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
