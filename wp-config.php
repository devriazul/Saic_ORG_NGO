<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'saicorg' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'bEW4])(,i;}}R,&;L%l`~0M ETn}WpUlSSWk3KK8-u+WU~)x8Kru$~*7YXw;EL=H' );
define( 'SECURE_AUTH_KEY',  'q&*w(}xuo=6R4@jJ$;WMwcbOi4<&{oS)[`UH~,kU~3zi6J9~$!6<x]>cAN5H&,X9' );
define( 'LOGGED_IN_KEY',    '[pT)zRu .FH[VcyjdvOIprbUwrW-fsI/^:U^5&S7-sUK9Hb@2*o`:wMC[sMCg_>|' );
define( 'NONCE_KEY',        'lrvKflqg` M|^XFP@2P5TZm,jJkHrf,Xhc6Gp0_{t9oufc8#BoiG0*]cK4LKa5Rt' );
define( 'AUTH_SALT',        'WCDK8zE!>Zz54{{O!H#w;V:NGLQPG[R:aQMDJIzN]-Yz%t_S9u_s tsbCD7Z_DgO' );
define( 'SECURE_AUTH_SALT', ']a[(C~%nEQN)G_CJJUee)9>y/p6?s#&/7P(R#qtx|ZW=[ u=~>jBWhVq|3g%~r%f' );
define( 'LOGGED_IN_SALT',   'P+Fu-cpyN4?l7#T;{LPPU<_z>3oRy>QW~).ZvSiz`aSe*UV#L[+fFV^iiky/6UCA' );
define( 'NONCE_SALT',       'Ox8+.WdM,7U++yVAVd?I)2h8kPL r RYE}FEmM(%2g73(mMKp]fNc{qDTWXtKUH.' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
