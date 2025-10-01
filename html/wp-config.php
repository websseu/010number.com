<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '010number' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'qwer1234!@#$' );

/** Database hostname */
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
define( 'AUTH_KEY',         'E]Ma3F:]b:[8ec*1-h_tPcdf/t~pyX-4w|7Y?]?A0{KxbrgnURCq,z0gG`?)|Ki&' );
define( 'SECURE_AUTH_KEY',  ')@Z4[5/;M!:#@BJb4{>m,~7invI#icY9l%cvHv41NfNEW`>c !(^O`xOVIYS:i20' );
define( 'LOGGED_IN_KEY',    'ES0^#5[7siVs4Ojn*=yYH?bzlOXLG-NaiV+AkBw.~rse*2GK=YXI]qhPck+jhp=e' );
define( 'NONCE_KEY',        'ezNoEK668Z%eu8/M%o9+.D%0pZppip#N&sC^_^u1JuUG+ ~S/KTG(bOvIqadPzJ[' );
define( 'AUTH_SALT',        'J(>;h]Wnsfa.&;9mqrK<=R$@f,I2im)Q)b@^CK$A-xJ-s)FE`PdDCRF+LJg,74C}' );
define( 'SECURE_AUTH_SALT', ',gA8qw,yfRVnUAMtijnQ@D`zdA,fDo:Cf$88YOry4Ya&(h@jONrcII*EJOi]M7Wo' );
define( 'LOGGED_IN_SALT',   'UD%t=UgCzL:Bd$`8LRTfGVzk+FsFGqyxi[X#Gqp>]|0.%/|RH@g9$fGm+*0=9>@F' );
define( 'NONCE_SALT',       'hls+rH_ZGCVnL7%:eTGhKbNQxnF(p?0GJ,coiH5?9.z7LPs6dR_Zh;Xom.OY+$7J' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
