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
define( 'DB_NAME', 'recipeweb_wp' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '^bfcml8t[fCr:7*q]oE(6^Kh^g3/>8[+~vJ:+S g[pM)n*VN}**6R}3mf{B%fV^b' );
define( 'SECURE_AUTH_KEY',  'l@}vkUg[Z1+%J^o]i<Yb1x7B1%@!Kh2uhCn5j,#jA6Tgo&~fSX(JIYuV9eTNqMNf' );
define( 'LOGGED_IN_KEY',    '+tn<>o*RW3s6p8$LTwIG#o1n$JnWwi|g*td!Y19K?/LGqUuZ[}|8Ws3^![/Y?d^q' );
define( 'NONCE_KEY',        'GFYdT/KqDylWN9TStp6)S%YBJIGj/Q{CI%Ly]Yc4;a$C*e%lpTR]wD>Aye=g (0$' );
define( 'AUTH_SALT',        't4:qf$]!3XSXpGc^eLNpAxu:+B2h.0a`5T$}p8%54],=.fnm3>+1MhQ>>;kNvZLQ' );
define( 'SECURE_AUTH_SALT', 'PYAA{s?$PBf/qPZ Jfy?^KFga5JLB^IVeiY:R%t)FyaV6c4MsXn=~r_#oy.O*-/]' );
define( 'LOGGED_IN_SALT',   'J[j|n+oxOz}HIGHjoKNyt)T1Ehv<(>cM40@[ f4J4V}$Hcsh3s^.n##d|B|wq3`6' );
define( 'NONCE_SALT',       '_~@k?NZ4A@Ln2{A!qRD|xCbvp#=F5z2$jF9B 5.~5xkCYj<rYxbe>`Bf$:nqYcm/' );

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
