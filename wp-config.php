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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'heroku_645e15d9ebab7c2' );

define('WP_HOME','https://malaivictorecommerce.herokuapp.com');
define('WP_SITEURL','https://malaivictorecommerce.herokuapp.com');

/** MySQL database username */
define( 'DB_USER', 'be56d4e50e42de' );

/** MySQL database password */
define( 'DB_PASSWORD', 'f8931ca9' );

/** MySQL hostname */
define( 'DB_HOST', 'eu-cdbr-west-02.cleardb.net' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '=S~Nv&- AJRO.Tq+<l,pbpdFcf*SXQzz;@[D]Q*L.DA8cJbOmOo193M9Cn-r<|H|' );
define( 'SECURE_AUTH_KEY',  'i DCPixs3lKE;/~pYjPHd:?$8TQ4lO=!Gd@H)HZVyrn;;;YC#CLz}T2I^%=jL1tB' );
define( 'LOGGED_IN_KEY',    'fjQxDXn+l`YWgoNB1aY_-YgtzcCb[Mg2`f-K:JNrMuBA>>Vr0yi|(vLpXR9HrjVL' );
define( 'NONCE_KEY',        'QH:K%[Xm#NoZmb;E0{]_abVymq?@[XY?}Kk`4D8(1-NF0Fz4iHWEQT6W9hAig+8O' );
define( 'AUTH_SALT',        'dd[)rI1X+5SgKM-a5]6{8KxdKwvXj5-m[VY0ss`9quY$#>:$4NBC3HsN%egJH6mA' );
define( 'SECURE_AUTH_SALT', 'BBqjc&lqol7UNWV%0vrfK-3?]+`l2Y1Xs{LfM`Bt)p4CJ|ySR.v)Zr5tq]M`3m!.' );
define( 'LOGGED_IN_SALT',   '5yNdr<}]A>/{-0mGy9`=|LRtX:]r|9L}#.<h61,z!%b4y~/ AY(,!}%%XT/CL%k&' );
define( 'NONCE_SALT',       'j#nCe|kc &}D~-KTr1,=s/phKe;OgSOK^<Z=r4EO0?B|W*$m(:iGI^!QdpA/0(vW' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
define('FS_METHOD', 'direct');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
