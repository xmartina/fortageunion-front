<?php
/*
Plugin Name: UiCore Framework
Plugin URI: https://uicore.co
Description: Core plugin for UiCore themes.
Version: 4.1.3
Author: UiCore
Author URI: https://uicore.co
License: GPL3
Text Domain: uicore-framework
Domain Path: /languages
 * Elementor requires at least: 3.5.0
 * Elementor tested up to: 3.10.2
*/
namespace UiCore;

defined('ABSPATH') || exit();

/**
 * Core class
 *
 * @class Core The class that holds the entire UiCore plugin
 */
final class Core
{
    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '4.1.3';

    /**
     * Plugin Name
     *
     * @var string
     */
    public $themename;

    /**
     * Plugin Name
     *
     * @var string
     */
    public $apipath;

    /**
     *  Migrated to v2 on Framework V.3.2.4
     *
     * @var string
     */
    public $library = 'https://library.uicore.co/wp-json/uicore/v2/';


    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];


    private $is_theme = true;

    /**
     * Constructor for the UiCore class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {

        $themes = ['brisk','affirm','landio','level-wp','convertio','rise','framer-wp','vault'];
        $my_theme = wp_get_theme();
        $this->themename = str_replace('-child', '', $my_theme->get('TextDomain'));
        if (!in_array($this->themename, $themes)) {
            $this->themename = 'UiCore';
            $this->is_theme = false;
        }
        $this->apipath = 'https://api.uicore.co/v1/'. $this->themename;
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        add_action('plugins_loaded', [$this, 'init_plugin']);

    }

    /**
     * Initializes the UiCore() class
     *
     * Checks for an existing UiCore() instance
     * and if it doesn't find one, creates it.
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new Core();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('UICORE_VERSION', $this->version);
        define('UICORE_FILE', __FILE__);
        define('UICORE_PATH', dirname(UICORE_FILE));
        define('UICORE_INCLUDES', UICORE_PATH . '/includes');
        define('UICORE_URL', plugins_url('', UICORE_FILE));
        define('UICORE_ASSETS', UICORE_URL . '/assets');
        define('UICORE_NAME', str_replace('-wp','',ucfirst($this->themename) ));
        define('UICORE_API', $this->apipath);
        define('UICORE_LIBRARY', $this->library);
        define('UICORE_SETTINGS', 'uicore_theme_options');
        define('UICORE_BADGE', '<span class="ui-e-badge"></span>');
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Run on plugin activation. Set the time of installation and get the demos list.
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.0
     */
    public function activate()
    {
        $installed = get_option('uicore_installed');
        if (!$installed) {
            $installed_data = [
                "time"=>time(),
                "version" => $this->version
            ];
            update_option('uicore_installed', $installed_data);
        }

        //if theme name is UiCore => theme isn't seted so we can't continue
        //since we don't know for witch theme we awnt the demo list
        if( $this->themename != 'UiCore'){
            $api_response = wp_remote_get(UICORE_API . '/demos');
            $demos = wp_remote_retrieve_body($api_response);
            set_transient('uicore_demos', $demos, 20 * DAY_IN_SECONDS);
        }

        update_option('uicore_v3_migrated', true);


    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate()
    {
        Helper::delete_frontend_transients();
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        require UICORE_INCLUDES . '/core/settings_helper.php'; //Settings Helper Class

        if(\get_option('uicore_beta_debug')){
            require UICORE_INCLUDES . '/extra/class-debug.php'; //Debug Helper Class
        }

        require UICORE_INCLUDES . '/extra/helper.php'; //General Helper functions
        require UICORE_INCLUDES . '/extra/class-data.php'; //Generate data

        require UICORE_INCLUDES . '/extra/class-theme-options.php'; //Theme Options Settings
        require UICORE_INCLUDES . '/extra/class-settings.php'; //Theme Options Settings Manager + Legacy
        require UICORE_INCLUDES . '/extra/class-block-editor-style.php'; //Generate Styles from settings for Block Editor

        require_once UICORE_INCLUDES . '/extra/class-css.php'; //Frontend Theme Skin
        require_once UICORE_INCLUDES . '/extra/class-js.php'; //Frontend Theme Skin

        require UICORE_INCLUDES . '/extra/class-rest-api.php'; //Rest API Functions

        require UICORE_INCLUDES . '/class-common.php'; //General functions for both frontend and admin
        require UICORE_INCLUDES . '/class-assets.php'; //Define assets for frontend and admin

        if ($this->is_request('admin')) {
            require_once UICORE_INCLUDES . '/class-admin.php'; //Admin related functions
            require_once UICORE_INCLUDES . '/extra/class-admin-customizer.php'; //Admin Customizer functions
        }

        if ($this->is_request('frontend') && $this->is_theme) {
            require_once UICORE_INCLUDES . '/class-frontend.php'; //Frontend related functions

            if (Helper::get_option('disable_blog') === 'false' ){
                require_once UICORE_INCLUDES . '/blog/class-frontend.php'; //Frontend related functions
                require_once UICORE_INCLUDES . '/blog/class-template.php'; //Blog -> Used in Grid Element and frontend
            }

            if(function_exists('tutor_lms')){
                require_once UICORE_INCLUDES . '/extra/class-tutor.php'; //adds Tutor LMS Support
            }
        }

        if (class_exists('WooCommerce')) {
            require_once UICORE_INCLUDES . '/woocommerce/class-frontend.php'; //WooCommerce related functions
        }

        if (Helper::get_option('disable_portfolio') === 'false' ){
            require_once UICORE_INCLUDES . '/portfolio/class-common.php'; //Frontend related functions
            require_once UICORE_INCLUDES . '/portfolio/class-frontend.php'; //Frontend related functions
            require_once UICORE_INCLUDES . '/portfolio/class-template.php'; //Portfolio -> Used in Grid Element and frontend
        }

        //ThemeBuilder works only with Elementor
        if (class_exists('\Elementor\Plugin') && $this->is_theme && (Helper::get_option('disable_tb') === 'false') ) {
            require UICORE_INCLUDES . '/elementor/class-core.php'; //Elementor Incubator
            require UICORE_INCLUDES . '/elementor/theme-builder/class-common.php'; //Theme Builder generic functions
            require UICORE_INCLUDES . '/elementor/theme-builder/class-rule.php'; //Theme Builder generic functions
            require UICORE_INCLUDES . '/elementor/theme-builder/class-rest-api.php'; //Theme Builder generic functions
            require UICORE_INCLUDES . '/elementor/theme-builder/documents/class-base.php';//Elementor Documnet Type
            require UICORE_INCLUDES . '/elementor/theme-builder/documents/class-single.php';//Elementor Documnet Type
            if ($this->is_request('admin')) {
                require_once UICORE_INCLUDES . '/elementor/theme-builder/class-admin.php';
            }
            if ($this->is_request('frontend')) {
                require_once UICORE_INCLUDES . '/elementor/theme-builder/class-frontend.php'; //Frontend related functions
            }
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {
        add_action('init', [$this, 'init_classes']);

        add_action('init', [$this, 'migration']);

        // Localize our plugin
        add_action('init', [$this, 'localization_setup']);
    }

    /**
     * Check if is after update and run the migrations needed
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.4
     */
    public function migration()
    {
        $installed_data = [
            "time"=>time(),
            "version" => $this->version
        ];

        $installed = get_option('uicore_installed');

        //Migrate
        if ($installed && isset($installed['version'])) {
            if($installed['version'] != $this->version ){

                Settings::clear_cache(true);
                update_option('uicore_installed', $installed_data);
                Helper::activate_ep();

            }
        //First Time
        }else{
            Helper::activate_ep();
            Settings::clear_cache(true);
            update_option('uicore_installed', $installed_data);

        }

    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        $this->container['assets'] = new Assets();
        if ($this->is_request('admin')) {
            $this->container['admin'] = new Admin();
        }

        if ($this->is_request('frontend') && $this->is_theme) {
            $this->container['frontend'] = new Frontend();
        }
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('uicore-framework', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return (is_admin() || self::is_wplogin());

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() && !defined('DOING_CRON'));
        }
    }

    /**
     * Check if is the login screen
     *
     * @return bool
     */
    static function is_wplogin(){
        $ABSPATH_MY = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, ABSPATH);
        return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files()) ) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') || $_SERVER['PHP_SELF']== '/wp-login.php');
    }
}

Core::init();
// delete_option('uicore_connect');