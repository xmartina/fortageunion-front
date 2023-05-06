<?php
namespace UiCore\Elementor\ThemeBuilder;

use UiCore\Settings as Settings;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit();

/**
 * Theme Builder generic functions
 *
 * @author Andrei Voica <andrei@uicore.co
 * @since 2.0.0
 */
class Common
{

    /**
     * Construct Theme Builder generic functions
     *
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.0
     */
    public function __construct()
    {
		add_filter( 'pll_copy_taxonomies', [$this,'pll_copy_tax'], 10, 2 );
        add_action('init', [$this, 'register_ctp'], 0);
        add_filter('single_template', [$this, 'custom_templates']);
        add_action( 'elementor/documents/register', [ $this, 'register_tb_types' ] );
        add_filter( 'theme_uicore-tb_templates', [$this, 'custom_templates_list'] );
        add_shortcode('uicore-block', [$this, 'blocks_shortcode']);

        add_action( 'elementor/element/section/section_background/before_section_end', [$this, 'feature_img_controls'] );
        add_action( 'elementor/frontend/section/before_render', [$this, 'feature_img_render'],11, 1 );
    }

    function feature_img_render($section){
        $active = $section->get_settings('section_feature_img');
		if ('yes' === $active && !is_home()) {
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(\get_queried_object_id()), 'full');
            if (isset($img[0]) && $img[0] != null) {
                $url = esc_url($img[0]);
                $section->add_render_attribute('_wrapper', 'style', 'background-image:url('.$url.')');
            }
		}
    }
    public function feature_img_controls($section){
        $section->start_injection(
			[
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'background_color',
			] );

		$section->add_control(
			'section_feature_img',
			[
				'label'        => UICORE_BADGE . esc_html__( 'Feature Image', 'uicore-framework' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Please also set an image as a fallback.', 'uicore-framework' ),
				'render_type'  => 'template',
				'frontend_available' => false,
                'condition' => [
                    'background_background' => [ 'classic' ],
                ]
			]
		);
        $section->end_injection();
    }

    /**
     * Register uicore Theme Builder Elementor Document Type
     *
     * @param [type] $documents_manager
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
	public function register_tb_types( $documents_manager )
    {
        $docs_types = [
            'header' => Documents\Base::get_class_full_name(),
            'footer' => Documents\Base::get_class_full_name(),
            'popup' => Documents\Base::get_class_full_name(),
            'megamenu' => Documents\Base::get_class_full_name(),
            'popup' => Documents\Base::get_class_full_name(),
            'pagetitle' => Documents\Single::get_class_full_name(),
            'single' => Documents\Single::get_class_full_name(),
            'archieve' => Documents\Base::get_class_full_name(),
        ];

        foreach ( $docs_types as $type => $class_name ) {
			$documents_manager->register_document_type( $type, $class_name );
		}
	}

    /**
     * Register Custom Post for Theme Builder
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function register_ctp()
    {
        $name =  __('Theme Builder','uicore-framework');
        $slug = 'uicore-tb';

        register_taxonomy(
            'tb_type',
            [],
            [
                'hierarchical' => false,
                'public' => false,
                'label' => _x( 'Type', 'Theme Builder', 'uicore-framework' ),
                'show_ui' => false,
                'show_admin_column' => false,
                'query_var' => true,
                'show_in_rest' => false,
                'rewrite' => false,
            ]
        );
        register_taxonomy(
            'tb_rule',
            [],
            [
                'hierarchical' => false,
                'public' => false,
                'show_ui' => false,
                'show_admin_column' => false,
                'query_var' => true,
                'show_in_rest' => false,
                'rewrite' => false,
            ]
        );

        register_post_type($slug, [
            'labels' => [
                'name' => $name,
                'singular_name' => $name,
            ],
            'has_archive' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => true,
            'taxonomies' => ['tb_type','tb_rule'],
            'menu_icon' => 'dashicons-format-gallery',
            'public' => true,
            'rewrite' => false,
            'show_in_rest' => false,
			'exclude_from_search' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => [ 'title', 'thumbnail', 'author', 'elementor' ],
        ]);
    }

    /**
     * Force specific template for theme builder
     *
     * @param [type] $single
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function custom_templates($single)
    {
        global $post;

        /* Checks for single template by post type */
        if ( $post->post_type == 'uicore-tb' ) {

            //we need more controll on popup
            if(self::get_the_type($post->ID) === 'popup'){
                return UICORE_INCLUDES . '/elementor/theme-builder/templates/popup.php';
            }
            //default
            return UICORE_INCLUDES . '/elementor/theme-builder/templates/canvas.php';
        }
        return $single;
    }

    /**
     * Add Theme Builder Canvas to Templates
     *
     * @param [type] $post_templates
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function custom_templates_list( $post_templates )
    {
        $post_templates[UICORE_INCLUDES . '/elementor/theme-builder/templates/canvas.php'] = "ThemeBuilder Canvas";
        $post_templates[UICORE_INCLUDES . '/elementor/theme-builder/templates/popup.php'] = "ThemeBuilder Popup";
        return $post_templates;
    }

    /**
     * Get Elementor content for display
     *
     * @param [type] $content_id
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    static function get_elementor_content( $content_id , $with_style = true )
    {
        $content = '';
        $content_id = apply_filters( 'wpml_object_id', $content_id, 'post', true );
        if(\class_exists('\Elementor\Plugin')){
            $elementor_instance = \Elementor\Plugin::instance();
            $content = $elementor_instance->frontend->get_builder_content_for_display( $content_id );

            if($with_style){
                $css_file = new \Elementor\Core\Files\CSS\Post( $content_id );
                $css_file->enqueue();
            }


        }
        return $content;
    }

    /**
     * Get ThemeBuilder element Type
     *
     * @param int $post_id
     * @return string
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    static function get_the_type($post_id)
    {
        $type = wp_get_post_terms($post_id, 'tb_type', ['fields' => 'names']);
        $type = $type[0] ? str_replace('_type_', '', $type[0] ) : '';
        $type = ($type === 'mm') ? 'mega menu' : $type;
        return $type;
    }

    static function get_cpt_list()
    {
        $args = array(
			'public'   => true,
			'_builtin' => true,
		);
		$post_types = get_post_types( $args, 'objects' );

        $args['_builtin'] = false;
        $custom_post_type = get_post_types( $args, 'objects' );

        $post_types = array_merge( $post_types, $custom_post_type );
        unset( $post_types['attachment'] );
        unset( $post_types['post'] );
        unset( $post_types['page'] );
        unset( $post_types['elementor_library'] );
        unset( $post_types['uicore-tb'] );
        unset( $post_types['e-landing-page'] );
        return $post_types;
    }

    /**
     * Get Rule locations list
     *
     * @return array
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
	public static function get_location_selections() {

        $post_types = self::get_cpt_list();
        
		$special_pages = array(
			array(
				'name'    => __( '404 Page', 'uicore-framework' ),
				'value'    => 'special-404'
			),
			array(
				'name'    => __( 'Search Page', 'uicore-framework' ),
				'value'    => 'special-search'
			),
			array(
				'name'    => __( 'Blog / Posts Page', 'uicore-framework' ),
				'value'    => 'special-blog'
            ),
			array(
				'name'    => __( 'Front Page', 'uicore-framework' ),
				'value'    => 'special-front'
			),
			array(
				'name'    => __( 'Date Archive', 'uicore-framework' ),
				'value'    => 'special-date'
			),
			array(
				'name'    => __( 'Author Archive', 'uicore-framework' ),
				'value'    => 'special-author'
			)
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$special_pages[] = array(
				'name'    => __( 'WooCommerce Shop Page', 'uicore-framework' ),
				'value'    => 'special-woo-shop'
			);
		}
        foreach($post_types as $post_type){
            $special_pages[] = array(
				'name'    => $post_type->label,
				'value'    => 'cp-'.$post_type->name
			);
            $special_pages[] = array(
				'name'    => $post_type->label. ' Archive',
				'value'    => 'cp-archive-'.$post_type->name
			);

        }
        // print_r($special_pages);
		$selection_options = array(
			'basic'         => array(
				'label' => __( 'Basic', 'uicore-framework' ),
				'value' => array(
					array(
						'name'    => __( 'Entire Website', 'uicore-framework' ),
						'value'    => 'basic-global'
					),
					array(
						'name'    => __( 'All Pages', 'uicore-framework' ),
						'value'    => 'basic-page'
					),
					array(
						'name'    => __( 'All Blog Posts', 'uicore-framework' ),
						'value'    => 'basic-single'
					),
					array(
						'name'    => __( 'All Archives', 'uicore-framework' ),
						'value'    => 'basic-archives'
					),

				),
			),

			'special-pages' => array(
				'label' => __( 'Special Pages', 'uicore-framework' ),
				'value' => $special_pages,
			),
		);


		$selection_options['specific-target'] = array(
			'label' => __( 'Specific Target', 'uicore-framework' ),
			'value' => array(
				array(
					'name'    => __( 'Specific Pages / Posts / Taxonomies, etc.', 'uicore-framework' ),
					'value'    => 'specifics'
				),
			),
		);

		return $selection_options;
	}

    /**
     * Shortcode function for blocks
     *
     * @param [type] $atts
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function blocks_shortcode($atts)
    {
        $atr = shortcode_atts(
            [
                'id' => false,
            ],
            $atts
        );
        if($atr['id']){
            return Common::get_elementor_content($atr['id']);
        }
    }

    static function popup_markup($content, $id)
    {
        //check first if is in edit mode but on a page where is embeded and hide it if so
        if( isset($_GET['elementor-preview']) && $_GET['elementor-preview'] != $id ){
            return;
        }


        $is_prev = isset($_GET['ui-popup-preview']);
        $is_editor = isset($_GET['elementor-preview']);
        $css_class = $is_editor ? $id . ' ui-popup-active' : $id;

        //we don't need it if is prev iframe
        if($is_prev){
            ?>
            <style>
            #wpadminbar { display:none !important;}
            </style>
            <?php
            return null;
        }

        $trigger = false;
		$settings = get_post_meta($id, 'tb_settings', true);
        if(is_array($settings)){
            $trigger = $settings['trigger'];
        }


        self::get_generic_style();

        self::get_specific_style($id,$settings);
        ?>

        <div class="ui-popup-wrapper ui-popup-<?php echo $css_class; ?>">

            <?php
            if($settings['overlay'] === 'true'){
            ?>
                <div class="ui-popup-overlay"></div>
            <?php }
            ?>

            <div class="ui-popup">
            <?php
                if($settings['close'] === 'true'){
            ?>
                <div class="ui-popup-close">
                    <i class="eicon-close"></i>
                </div>
            <?php }

                echo $content;
            ?>
            </div>
        </div>
        <?php
        if($trigger && !$is_editor){
           self::get_js($id,$trigger, $settings);
        }
    }

    static function css_position_filter($value)
    {
        if($value === 'bottom' || $value === 'right'){
            return 'flex-end';
        }elseif($value === 'top' || $value === 'left'){
            return 'flex-start';
        }else{
            return $value;
        }
    }

    static function get_js($id,$trigger,$settings)
    {
        $js = null;
        $extra = null;

        //run the triggers js only if we need to show the popup again
        if( $trigger['maxShow']['enable'] === 'true' ){
            $js .= "
            if(!localStorage.getItem('uicore_popup_".$id."') || (localStorage.getItem('uicore_popup_".$id."') < ".$trigger['maxShow']['amount']." ) ){
                ";

            $extra .= "
            localStorage.setItem('uicore_popup_".$id."', Number(localStorage.getItem('uicore_popup_".$id."')) + 1); ";
        }


        $condition = 'true';


        //responsive
        if($settings['responsive']['desktop'] === 'true'){
            $condition .= " && !window.matchMedia( '(min-width: 1025px)' ).matches";
        }
        if($settings['responsive']['tablet'] === 'true'){
            $condition .= " && !window.matchMedia( '(min-width: 768px) and ( max-width: 1025px)' ).matches";
        }
        if($settings['responsive']['mobile'] === 'true'){
            $condition .= " && !window.matchMedia( '(max-width: 767px)' ).matches";
        }

        $js .= "if(".$condition."){
            ";


        if( $settings['pageScroll']  === 'true'){
            $extra .= ' document.body.setAttribute("style","overflow:hidden;"); ';
        }

        //Triggers
        $js .=  'var uipopupTrigger'.$id.' = function() {
                    jQuery(".ui-popup-'.$id.'").addClass("ui-popup-active");'
                    .$extra.'
                    };';

        if( $trigger['pageLoad']['enable'] === 'true' ){
            $js .= "
            jQuery( document ).ready(function() {
                setTimeout(function(){
                        uipopupTrigger".$id."();
                }, ".($trigger['pageLoad']['delay'] * 1000)." );
            });
            ";
        }
        if( $trigger['pageScroll']['enable'] === 'true' ){
            $direction = ($trigger['pageScroll']['direction'] === 'down')
            ? '> previousScroll && (currentScroll/(docheight-winheight)) > scrolltrigger'
            : '< previousScroll';
            $js .= "
            jQuery( document ).ready(function() {
                var previousScroll = 0;
                var scrolltrigger = 0.".$trigger['pageScroll']['amount'].";

                window.addEventListener('scroll', pageScrollTrigger".$id.");
                function pageScrollTrigger".$id."() {
                    var currentScroll = jQuery(this).scrollTop();
                    var docheight = jQuery(document).height();
                    var winheight = jQuery(window).height();
                    if (currentScroll ". $direction ."){
                        uipopupTrigger".$id."();
                        window.removeEventListener('scroll', pageScrollTrigger".$id.");
                    }
                    previousScroll = currentScroll;
                };
            });
            ";
        }
        if( $trigger['scrollToElement']['enable'] === 'true' ){
            $element = $trigger['scrollToElement']['selector'];
            $js .= "
            jQuery( document ).ready(function() {
                window.addEventListener('scroll', scrollElementTrigger".$id.");
                function scrollElementTrigger".$id."() {
                    var top= jQuery('".$element."').offset().top;
                    var bottom = jQuery('".$element."').offset().top + jQuery('".$element."').outerHeight();
                    var toBottom= jQuery(window).scrollTop() + jQuery(window).innerHeight();
                    var toTop = jQuery(window).scrollTop();

                    if ((toBottom > top) && (toTop < bottom)){
                        uipopupTrigger".$id."();
                        window.removeEventListener('scroll', scrollElementTrigger".$id.");
                    }
                }
            });
            ";
        }
        if( $trigger['click']['enable'] === 'true' ){
            $no = $trigger['click']['clicks'];
            $js .= "
            jQuery( document ).ready(function() {
                var clicks = 0;
                var maxClicks = ".$no.";
                window.addEventListener('click', clickTrigger".$id.");
                function clickTrigger".$id."() {
                    clicks++;
                    if (clicks > maxClicks){
                        uipopupTrigger".$id."();
                        window.removeEventListener('click', clickTrigger".$id.");
                    }
                }
            });
            ";
        }
        if( $trigger['clickOnElement']['enable'] === 'true' ){
            $element = $trigger['clickOnElement']['selector'];
            $js .= "
            jQuery( document ).ready(function() {
                jQuery('".$element."').bind('click', uipopupTrigger".$id.");
            });
            ";
        }
        if( $trigger['onExit']['enable'] === 'true' ){
            $js .= "
            jQuery( document ).ready(function() {
                document.addEventListener('mouseout', onExitTrigger".$id.");
                function onExitTrigger".$id."(event) {
                    if (!event.toElement && !event.relatedTarget) {
                        uipopupTrigger".$id."();
                        document.removeEventListener('mouseout', onExitTrigger".$id.");
                    }
                }
            });
            ";
        }

        //run the triggers js only if we need to show the popup again (close the js if)
        if( $trigger['maxShow']['enable'] === 'true' ){
            $js .= "
                }
                ";
        }

        //responsive
        $js .= "
        }
        ";

        //close on overlay
        if($settings['overlay'] && $settings['closeOnOverlay'] === 'true'){
            $extra_class_for_close = ", .ui-popup-overlay";
        }else{
            $extra_class_for_close = "";
        }
        ?>
        <script>
        <?php echo $js; ?>
        jQuery( document ).ready(function() {
            jQuery('.ui-popup-close, #ui-close-popup<?php echo $extra_class_for_close; ?>').on('click', function(){
                jQuery(this).closest('.ui-popup-active').removeClass('ui-popup-active');
                document.body.setAttribute("style","overflow:auto;");
            })
        })
        </script>
        <?php
    }

    static function get_specific_style($id, $settings)
    {

        $css = null;
        $css_wrapp = null;
        $css_close = null;


        if($settings['width']['mode'] === 'custom'){
            $css .= 'width:' . $settings['width']['size'] . 'px;';
        }elseif($settings['width']['mode'] === 'full'){
            $css .= 'min-width:100vw;';
        }

        if($settings['height']['mode'] === 'custom'){
            $css .= 'max-height:' . $settings['height']['size'] . 'px;';
        }elseif($settings['height']['mode'] === 'full'){
            $css .= 'min-height:100vh;';
        }

        $position = explode(" ", $settings['position']);
        $css_wrapp .= 'align-items:'. self::css_position_filter($position[0]) .';';
        $css_wrapp .= 'justify-content:'. self::css_position_filter($position[1]) .';';

        if($settings['close'] === 'true'){
            $css_close = '.ui-popup-'. $id . ' .ui-popup-close {';
            $css_close .= 'color:' . Settings::color_filter($settings['closeColor']['default']);
            $css_close .= '}';
            $css_close .= '.ui-popup-'. $id . ' .ui-popup-close:hover {';
            $css_close .= 'color:' . Settings::color_filter($settings['closeColor']['hover']);
            $css_close .= '}';
        }

        $animation = str_replace(' ', '', ucwords($settings['animation']) );
        $css .= 'animation-name:uicore'.$animation;

        ?>
        <style id="ui-popup-style-<?php echo $id; ?>">
        .ui-popup-<?php echo $id; ?>{
            <?php echo $css_wrapp; ?>
        }
        .ui-popup-<?php echo $id; ?> .ui-popup{
            <?php echo $css; ?>
        }
        <?php echo $css_close; ?>

        </style>
        <?php

    }

    static function get_generic_style()
    {
        static $is_embeded = false;
        if( !$is_embeded ){
            $is_embeded = true;
            ?>
            <style id="ui-popup-style">
            .ui-popup-background, .ui-popup-wrapper, .ui-popup-overlay{
                position: fixed;
                width: 100vw;
                height:100vh;
                top: 0;
                left: 0;
            }
            .ui-popup-overlay{
                background-color: rgba(0, 0 ,0, 70%);
            }
            .ui-popup-wrapper{
                display: none;
                z-index: 9999;
                animation-name: uicoreFadeIn;
	            animation-timing-function: ease-in-out;
                animation-duration: .4s;
            }
            .ui-popup-active{
                display: flex;
            }
            .ui-popup{
                display: none;
                position: relative;
                width: 100%;
                max-width: 100vw;
                max-height: 95vh;
                animation-duration: .6s;
				overflow: hidden;
            }
            .ui-popup-active .ui-popup{
                display: flex;
            }
            .ui-popup-close{
                position: absolute;
                right: 6px;
                top: 6px;
                padding: 10px;
                font-size: 20px;
                z-index: 1;
                line-height: 1;
                cursor: pointer;
                transition: all .3s ease-in-out;
            }
            .ui-popup-wrapper [data-elementor-type=uicore-tb] {
                overflow: hidden auto;
                width: 100%;
            }
            .ui-popup-wrapper [data-elementor-type=uicore-tb] .elementor-section-wrap:not(:empty)+#elementor-add-new-section {
                display: none;
            }
			.elementor-add-section:not(.elementor-dragging-on-child) .elementor-add-section-inner {
				background-color: #fff;
			}
            </style>

        <?php

        }
    }

	function pll_copy_tax( $taxonomies, $sync ) {
		$taxonomies[] = 'tb_type';
		return $taxonomies;
	}
}
new Common();
