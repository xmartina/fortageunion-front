<?php

/**
 * uicore functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package uicore-theme
 */
defined('ABSPATH') || exit;

//Global Constants
define('UICORE_THEME_VERSION', '2.0.3');
define('UICORE_THEME_NAME', 'Rise');
define('UICORE_FRAMEWORK_VERSION', '4.1.3');


$uicore_includes = array(
	'/setup.php',
	'/default.php',
	'/template-tags.php',
	'/plugin-activation.php'
);

foreach ($uicore_includes as $file) {
	require_once get_template_directory() . '/inc' . $file;
}


//Required
if ( ! isset( $content_width ) ) {
	$content_width = 1000;
}
if ( is_singular() ) {
	wp_enqueue_script( "comment-reply" );
}

add_filter('uicore_settings_default_admin', 'uicore_default_admin_options');
function uicore_default_admin_options($default) {
	$settings = array(
		'pFont' => [
			'f' => 'Outfit',
			'st' => '600',
		],
		'sFont' => [
			'f' => 'Outfit',
			'st' => '500',
		],
		'tFont' => [
			'f' => 'Outfit',
			'st' => 'normal',
		],
		'aFont' => [
			'f' => 'Outfit',
			'st' => '500',
		],
	);
	return wp_parse_args($settings, $default);
}

add_filter('uicore_settings_default_front', 'uicore_default_front_options');
function uicore_default_front_options($default) {
	$settings = [
		'pColor'					=> '#3C6FF4',
		'sColor'					=> '#532DF5',
		'aColor'					=> '#20BFA9',
		'hColor'					=> '#1D263A',
		'bColor'					=> '#1D263A',
		'dColor'					=> '#1D263A',
		'lColor'					=> '#F9FAFB',
		'logo'						=> 'https://rise.uicore.co/business-consultant/wp-content/uploads/sites/3/2022/02/Rise-logo.webp',
		'fav'						=> 'https://rise.uicore.co/business-consultant/wp-content/uploads/sites/3/2022/02/Rise-Favicon.png',
		'pagetitle_bg' 				=> [
			'type' 			=> 'Light Neutral',
			'solid' 		=> 'Light Neutral',
			'gradient' 		=> [
				'angle' 		=> '180',
				'color1' 		=> '#ffeede',
				'color2' 		=> '#ffffff',
			],
			'image' 		=> [
				'url' 			=> '',
				'attachment' 	=> 'scroll',
				'position' 		=> [
					'd' => 'bottom center',
					't' => 'center center',
					'm' => 'center center',
				],
				'repeat' 		=> 'no-repeat',
				'size' 			=> [
					'd' => 'cover',
					't' => 'cover',
					'm' => 'contain',
				],
			],
		],
		'pagetitle_color'			=> 'Dark Neutral'
	];
	return wp_parse_args($settings, $default);
}

//disable element pack self update
function uicore_disable_plugin_updates( $value ) {

    $pluginsToDisable = [
        'bdthemes-element-pack/bdthemes-element-pack.php',
        'metform-pro/metform-pro.php'
    ];

    if ( isset($value) && is_object($value) ) {
        foreach ($pluginsToDisable as $plugin) {
            if ( isset( $value->response[$plugin] ) ) {
                unset( $value->response[$plugin] );
            }
        }
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'uicore_disable_plugin_updates' );