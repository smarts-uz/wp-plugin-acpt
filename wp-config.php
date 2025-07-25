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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          '4=<pclQW%`y.Myo9A`rw|*Gm~D$aO_[I_H=<O( [?=.d=e3S,#dF}^aq[xa(Hfsj' );
define( 'SECURE_AUTH_KEY',   '..OcC;.-c,;Qx1C(yh,d- 5#]{I)+xML5tT#oA)3ryf3qFZ7S3]x,fn[o2OxoJ:n' );
define( 'LOGGED_IN_KEY',     'n=kig1jqk=e,otC9[38^AFKyZRGOgb/5yDNNQ2E6Cgd|I<0xr!,X[+4{*++AcpgX' );
define( 'NONCE_KEY',         'H>-r%:(zYjmBDLE;^8(Ss/3Nnp!towi(33bGu{4`W1%| .zOGFtgssaEf>z#CM%g' );
define( 'AUTH_SALT',         '8?V`ilz6VUP57QQv..a  Ysh.#hcR&OL(^/#<HY,[vDc{fD|2l.Od6_^J[>Ao?A&' );
define( 'SECURE_AUTH_SALT',  '!l9xrFm>G(^b_*Q1VDi%[=tmw)7Xyqt+`{c8k/MPK0Vp0z*[6Mn50s*ejc[!A]E5' );
define( 'LOGGED_IN_SALT',    ' bj>/F!c)=rqc/r{EO<^m8`xxY988*LF?Ft.;]|?j^013BTE`h`2Pt?dCP|QBLn!' );
define( 'NONCE_SALT',        'TeVb<7~tgF^:+na9!H<]i2sFV0^3JvFj=5)0EPqLLvY`Rv=^4|]803$M YgT$ug~' );
define( 'WP_CACHE_KEY_SALT', 'I|K17k|pPaNUx!Tq-+%`!>!qIfaOd]`}HkMJH$!qVh6[N3`1|7C5g,)*~-,Er>JA' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'DUPLICATOR_AUTH_KEY', '[Cz6$(C^[)$!DJPV}HE67/hXBzA/nL`$_.TJ<Bn]u=$bMi+zYl*@OIN:I`kFi%(&' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
