<?php
define( 'WP_CACHE', true );


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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fortageunion' );

/** Database username */
define( 'DB_USER', 'fortageunion' );

/** Database password */
define( 'DB_PASSWORD', '123456' );

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
define( 'AUTH_KEY',         'j7ixr0l3xk6lztwztuaasb85hbdwirs0j3wzysecpp8bxxl67fcqh7yw0rnzwbxq' );
define( 'SECURE_AUTH_KEY',  'caow4vrdw1qk8iuzvshu9ryi3gicox0gzff3d4mdmbod5xs0yz2q64totjzf1cav' );
define( 'LOGGED_IN_KEY',    'txhc2v0gm5oneduibo1pwqu5toltide4bspgvsoa9oymziipxttpwtkmdrbwaez7' );
define( 'NONCE_KEY',        'xzhwmbdaixeagdc6v5uvmaifye5dui3ddek8a6bxmijh4jkcraff6gpbif2rn64k' );
define( 'AUTH_SALT',        'yscpnj38xgr0g7rzexoy4vszkg9s42jo3qy5hzpvghtaqccfs02u8fvpndql2cv0' );
define( 'SECURE_AUTH_SALT', 'ecfwp8ppthwi2pjpqeaeallfmo2fbbnubrzujlbvdheona9qisdyxswmpneqprxo' );
define( 'LOGGED_IN_SALT',   'sqcxfo8jsud067inynqorzxzs5wilnmxtbjlsz5xtlyfotiwsmpyszp8aatfunll' );
define( 'NONCE_SALT',       'zx6io2a81w8n7hysm4qk1thws8o8j1b2ymmwqydcv2wibzjgg0bbiguvxoc31p6f' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpdk_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
