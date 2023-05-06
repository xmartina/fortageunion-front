<?php
namespace UiCore\Elementor;

use Elementor\Controls_Stack;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use UiCore\ThemeOptions;
use UiCore\Settings;
use UiCore\Helper;
use UiCore\Data;

/**
 * Elementor Related functions
 */
class Core
{

    /**
     * Elementor Font Type Name for Typekit
     */
    const TYPEKIT = 'uicore_typekit';

    const TYPEKIT_FONTS_LINK = 'https://use.typekit.net/%s.css';

    /**
     * Elementor Font Type Name for Typekit
     */
    const CUSTOM = 'uicore_custom';


    public function __construct()
    {

        $this->required_files();
        $this->custom_post_elementor_support();
        add_filter('elementor/icons_manager/additional_tabs', [$this, 'add_custom_icons']);
        add_filter('add_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);
        add_filter('update_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);

        //Add Suport For theme Builder Locations
        add_action( 'elementor/theme/register_locations', [$this, 'elementor_locations'] );

        //Elementor missing ggogle fonts
        add_filter( 'elementor/fonts/additional_fonts',[$this, 'new_google_fonts'],20,1 );

        add_filter( 'elementor/fonts/groups', [ $this, 'register_fonts_groups' ] );
        add_filter( 'elementor/fonts/additional_fonts', [ $this, 'register_fonts_in_control' ] );
        add_action( 'elementor/fonts/print_font_links/' . self::TYPEKIT, [ $this, 'print_typekit_font_link' ] );

        //Inline css
        add_filter( 'uicore_frontent_css', [ $this, 'print_custom_font_link' ] );

        //Theme Style Button Selectors fix
        add_action( 'elementor/element/kit/section_buttons/after_section_end', [$this, 'override_theme_style_button_control'], 20, 2);

        //Theme Style Container Width
        add_action( 'elementor/element/kit/section_settings-layout/after_section_end', [$this, 'override_theme_style_container_width_control'], 20, 2);

        //MEtform Reset Default btn style
        add_action( 'elementor/element/mf-button/mf_btn_section_style/after_section_end', [$this, 'override_mf_style_button_control'], 20, 2);
        add_action( 'elementor/element/mf-button/mf_btn_border_style_tabs/after_section_end', [$this, 'override_mf_style_button_control_border'], 20, 2);
        add_action( 'elementor/element/mf-file-upload/input_section/after_section_end', [$this, 'override_mf_style_upload_control'], 20, 2);

        //Temp Fix for Elementor 3.7.x
        \add_filter("elementpack/extend/visibility_controls", function($val){return false;});
        //Remove this bc is overwriting mini cart from woo
        \add_filter("elementpack/widget/wc_mini_cart", function($val){return false;});
        
        remove_filter( 'woocommerce_add_to_cart_fragments','\ElementPack\Modules\WcMiniCart\Module::element_pack_mini_cart_fragment' );
        remove_filter( 'woocommerce_locate_template','\ElementPack\Modules\WcMiniCart\Module::woocommerce_locate_template', 12, 3 );

        //EP Page Lines fix
		add_action('elementor/documents/register_controls', [$this, 'bdt_lines_fix'], 2, 1);

        //EP Scroll Nav Bg Fix
        add_action( 'elementor/element/bdt-scrollnav/section_style_nav/after_section_start', [$this, 'override_ep_bg_control'], 20, 2);
        
        //EP add Marquwe in Custom Carousel
        add_action( 'elementor/element/bdt-custom-carousel/section_additional_options/before_section_end', [$this, 'override_ep_custom_carousel_marquee'], 20, 2);
        add_action( 'elementor/element/bdt-custom-carousel/section_slides_style/before_section_end', [$this, 'override_ep_custom_carousel_marquee_style'], 20, 2);
        add_filter( 'elementor/widget/render_content', [$this, 'override_ep_custom_carousel_marquee_render'], 20, 2);

        // WPML String Translation plugin exist check
        if ( defined( 'WPML_ST_VERSION' ) ) {

            if ( class_exists( 'WPML_Elementor_Module_With_Items' ) ) {
                $this->load_wpml_modules();
            }

            add_filter( 'wpml_elementor_widgets_to_translate', [$this, 'add_translatable_nodes'] );
        }

    }

    function required_files()
    {
        //Extender
        require UICORE_INCLUDES . '/elementor/class-extender.php';
        //Widgets
        require UICORE_INCLUDES . '/elementor/class-widgets.php';
        //Assets Manager
        require UICORE_INCLUDES . '/elementor/class-assets-manager.php';
    }

    function load_wpml_modules()
    {
        require_once( UICORE_INCLUDES. '/elementor/compatibility/class-wpml-ui-highlighted-text.php');
    }

    function add_translatable_nodes( $nodes_to_translate )
    {
        $nodes_to_translate[ 'highlighted-text' ] = [
			'conditions' => [ 'widgetType' => 'highlighted-text' ],
			'fields'     => [],
			'integration-class' => '\UiCore\Elementor\WPML_UI_HighlightedText',
		];
        return $nodes_to_translate;
    }

    function bdt_lines_fix($section){
		$section->update_responsive_control(
			'ep_grid_line_output',
            [
				'selectors' => [
					'#uicore-page' => 'position: relative;',
					'#uicore-page::before' => '
									content: "";
									position: absolute;
									top: 0;
									right: 0;
									bottom: 0;
									left: 0;
									margin-right: auto;
									margin-left: auto;
									pointer-events: none;
									z-index: var(--ep-grid-line-z-index, 0);
									min-height: 100vh;

									width: calc(100% - (2 * 0px));
									max-width: var(--ep-grid-line-max-width, 100%);
									background-size: calc(100% + var(--ep-grid-line-width, 1px)) 100%;
									background-image: repeating-linear-gradient(var(--ep-grid-line-direction, 90deg), var(--ep-grid-line-column-color, transparent), var(--ep-grid-line-column-color, transparent) calc((100% / var(--ep-grid-line-columns, 12)) - var(--ep-grid-line-width, 1px)), var(--ep-grid-line-color, #eee) calc((100% / var(--ep-grid-line-columns, 12)) - var(--ep-grid-line-width, 1px)), var(--ep-grid-line-color, #eee) calc(100% / var(--ep-grid-line-columns, 12)));'

				],
            ]
        );
	}

    function override_mf_style_button_control( Controls_Stack $element, $section_id )
    {
        $element->update_responsive_control(
			'mf_btn_text_padding',
            [
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                    'unit' => 'px',
                ]
            ]
        );
        $element->update_responsive_control(
			'mf_btn_text_color',
            [
                'default' => ''
            ]
        );
        $element->update_responsive_control(
			'mf_btn_hover_color',
            [
                'default' => ''
            ]
        );
    }
    function override_mf_style_button_control_border( Controls_Stack $element, $section_id )
    {
        $element->update_responsive_control(
			'mf_btn_border_radius',
            [
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                    'unit' => 'px',
                ]
            ]
        );

    }
    function override_mf_style_upload_control( Controls_Stack $element, $section_id )
    {
        $element->update_control(
			'mf_input_color',
            [
                'default' => '',
            ]
        );
        $element->update_control(
			'mf_input_color_hover',
            [
                'default' => '',
            ]
        );
        $element->update_control(
			'mf_input_color_focus',
            [
                'default' => '',
            ]
        );
        $element->update_control(
			'mf_file_upload_file_name_color',
            [
                'default' => '',
            ]
        );
        $element->update_control(
			'mf_file_upload_file_name_hover_color',
            [
                'default' => '',
            ]
        );

    }
    public function override_ep_custom_carousel_marquee_render($content,Widget_Base $element){
        if($element->get_name() === 'bdt-custom-carousel'){
            $settings  = $element->get_settings_for_display();
            if(isset($settings['carousel_marquee']) && $settings['carousel_marquee'] === 'ui-is-marquee'){
                $auto = htmlspecialchars('"slidesPerView":"auto"');
               
                if($element->get_current_skin_id() === 'bdt-custom-content'){
                    $mobile = htmlspecialchars('"slidesPerView":'.(isset($settings["slides_per_view_mobile"]) ? (int)$settings["slides_per_view_mobile"] : 1) );
                    $tablet = htmlspecialchars('"slidesPerView":'.(isset($settings["slides_per_view_tablet"]) ? (int)$settings["slides_per_view_tablet"] : 2) );
                    $lg = htmlspecialchars('"slidesPerView":'.(isset($settings["slides_per_view"]) ? (int)$settings["slides_per_view"] : 3) );
    
                    $content = \str_replace([$mobile,$tablet,$lg],$auto,$content);
                }
               

                if(isset($settings['carousel_marquee_reverse']) && $settings['carousel_marquee_reverse'] === 'yes'){
                    $autoplay = htmlspecialchars('{"autoplay":{"delay":null}');
                    $autoplay_reverse = htmlspecialchars('{"autoplay":{"delay":null, "reverseDirection":true}');
                    $content = \str_replace($autoplay,$autoplay_reverse,$content);
                }
            }
        }
        return $content;
    }
    public function override_ep_custom_carousel_marquee_style(Controls_Stack $element, $section_id){
        $element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'custom_content_typography',
				'label' => __( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .swiper-wrapper',
				'condition'=> [
					'_skin' => 'bdt-custom-content'
				],
			]
		);

        $control_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), 'skin_template_slides' );
        if ( is_wp_error( $control_data ) ) {
            return;
        }

        // Then you can access and modify the repeater fields as an array directly
        $control_data['fields']['editor_content']['type'] = Controls_Manager::WYSIWYG;

        // And then just update the control in the stack/widget
        $element->update_control( 'skin_template_slides', $control_data );

    }
    public function override_ep_custom_carousel_marquee(Controls_Stack $element, $section_id){

        $element->start_injection(
			[
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'skin',
			] );
        $element->add_control(
			'carousel_marquee',
			[
				'label' => __('Enable Marquee', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'ui-is-marquee',
                'prefix_class'=> '',
                'render_type'  => 'template',
				'condition' => [
					'skin' => 'carousel'
				]
			]
		);
        $element->add_control(
			'carousel_marquee_reverse',
			[
				'label' => __('Reverse Marquee Direction', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
                'render_type'  => 'template',
				'condition' => [
					'skin' => 'carousel',
                    'carousel_marquee' => 'ui-is-marquee'
				]
			]
		);
        $element->end_injection();

        $element->update_control(
			'autoplay_speed',
            [
                'condition' => [
                    'autoplay' => 'yes',
					'carousel_marquee!' => 'ui-is-marquee'
				]
            ]
        );
        $element->update_responsive_control(
			'slides_per_view',
            [
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'carousel_marquee',
                            'operator' => '!=',
                            'value' => 'ui-is-marquee',
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'carousel_marquee',
                                    'operator' => '===',
                                    'value' => 'ui-is-marquee',
                                ],
                                [
                                    'name' => '_skin',
                                    'operator' => '!=',
                                    'value' => 'bdt-custom-content',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
    public function override_ep_bg_control( Controls_Stack $element, $section_id )
    {
        $element->add_control(
			'nav_bar_background_color',
			[
				'label'     => __( 'Nav Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul' => 'background-color: {{VALUE}};',
				],
			]
		);
        $element->add_control(
            'nav_bar_filter',
            [
                'label' => _x( 'Background Blur', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 25,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul' => 'backdrop-filter: blur({{SIZE}}px);'
				],
            ]
        );
        $element->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'        => 'nav_bar_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '0',
				'selector'    => '{{WRAPPER}} .bdt-scrollnav ul',
			]
		);

		$element->add_responsive_control(
			'nav_bar_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'nav_bar_shadow',
				'selector' => '{{WRAPPER}} .bdt-scrollnav ul',
			]
		);
        $element->update_control('nav_offset',
			array(
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav > div' => 'margin: {{SIZE}}{{UNIT}};',
				],
			)
		);
    }

     /**
     * Change Theme stylle Button selector classes
     *
     * @param \Elementor\Controls_Stack $element
     * @param string $section_id
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.2.3
     */
	public function override_theme_style_container_width_control( Controls_Stack $element, $section_id ) {
        $element->update_responsive_control(
			'container_width',
			array(
				'selectors' => [
					'.elementor-section.elementor-section-boxed nav.elementor-container,
                    .elementor-section.elementor-section-boxed > .elementor-container, .uicore-ham-creative .uicore-navigation-content,
                    .container-width .uicore-megamenu > .elementor,
                    #wrapper-navbar.elementor-section.elementor-section-boxed .elementor-container .uicore-megamenu .elementor-section.elementor-section-boxed .elementor-container,
                    #wrapper-navbar.elementor-section.elementor-section-full_width .elementor-container .uicore-megamenu .elementor-section.elementor-section-boxed .elementor-container
                    ' => 'max-width: {{SIZE}}{{UNIT}}',
					'.e-container' => '--container-max-width: {{SIZE}}{{UNIT}}',
				],
			)
		);

	}

    static function get_buttons_class($state='default',$style_type='full'){
        $not = array('.bdt-offcanvas-button');
        $all_style_selectors = array(
			'{{WRAPPER}} input[type="button"]',
			'{{WRAPPER}} input[type="submit"]',
			'{{WRAPPER}} .elementor-button.elementor-button',
            '{{WRAPPER}} .elementor-button:not('.implode('):not(',$not).')',  //maybe not
            '{{WRAPPER}} .bdt-button-primary',
            '{{WRAPPER}} .bdt-ep-button',
            'button.metform-btn',
            'button.metform-btn:not(.toggle)',
            '{{WRAPPER}} .bdt-callout a.bdt-callout-button',
            '{{WRAPPER}} .bdt-contact-form .elementor-field-type-submit .elementor-button',
            '{{WRAPPER}} [type="submit"]',
            '{{WRAPPER}} .tutor-button',
            '{{WRAPPER}} .tutor-login-form-wrap input[type="submit"]',
            '{{WRAPPER}} .wp-block-button__link',
            '.uicore-mobile-menu-wrapper .uicore-cta-wrapper a',
		);
        $no_padding_selectors = array(
			'.uicore-navbar a.uicore-btn'         
		);
		if(apply_filters( "uicore_woo_buttons_global", \function_exists('is_shop') )){
			 $all_style_selectors = array_merge( $all_style_selectors, [
				 '{{WRAPPER}}.woocommerce #respond input#submit',
				 '{{WRAPPER}}.uicore-woo-page a.button:not(.add_to_cart_button):not(.product_type_grouped):not(.product_type_external):not(.product_type_simple)',
				 '{{WRAPPER}}.uicore-woo-page a.checkout-button.button.alt',
			 ]);
			$no_padding_selectors = array_merge($no_padding_selectors,[
				'{{WRAPPER}} .widget.woocommerce a.button',
				'{{WRAPPER}} .woocommerce button.button',
				'{{WRAPPER}} .woocommerce div.product form.cart .button',
				'{{WRAPPER}} .woocommerce-cart-form .button',
				'{{WRAPPER}} .woocommerce #respond input#submit.alt',
				'{{WRAPPER}}.woocommerce a.button.alt',
				'.woocommerce button.button.alt',
				'{{WRAPPER}}.woocommerce button.button.alt.disabled',
				'{{WRAPPER}}.woocommerce input.button.alt'   
			]);
		}
        $only_hover = array(
            '.uicore-navbar a.uicore-btn',
            '.uicore-transparent:not(.uicore-scrolled) .uicore-btn.uicore-inverted',
            '{{WRAPPER}} .metform-btn'
        );
        if($style_type === 'full'){
            $selectors = \array_merge($all_style_selectors,$no_padding_selectors);
        }else{
            $selectors = $all_style_selectors;
        }

        if($state != 'default'){
            $selectors = \array_merge($selectors,$only_hover);
            foreach ($selectors as $selector){
                $new_selector[] = $selector.':hover';
                $new_selector[] = $selector.':focus';
            }
            $selectors = $new_selector;
        }

        return implode( ',', $selectors );

    }

    /**
     * Change Theme stylle Button selector classes
     *
     * @param \Elementor\Controls_Stack $element
     * @param string $section_id
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.2.3
     */
	public function override_theme_style_button_control( Controls_Stack $element, $section_id ) {

        $controls_manager = Plugin::$instance->controls_manager;
        $typographyGroup = $controls_manager->get_control_groups('typography');
        foreach ($typographyGroup->get_fields() as $field_key => $field) {
            $control_id = "button_typography_{$field_key}";
            $old_control_data = $controls_manager->get_control_from_stack($element->get_unique_name(), $control_id);
            if($control_id != 'button_typography_font_size'){
                $element->update_control($control_id, [
                    'selectors'  => [
                        $this->get_buttons_class() => isset($old_control_data['selector_value']) ? $old_control_data['selector_value'] : reset($old_control_data['selectors']),
                    ]
                ]);
            }else{
               $element->update_responsive_control(
                    'button_typography_font_size',
                    array(
                        'selectors' => array(
                            $this->get_buttons_class() => 'font-size: {{SIZE}}{{UNIT}};',
                        ),
                    )
                );
            }
        }

        $element->update_control(
			'button_text_color',
			array(
				'selectors' => array(
					$this->get_buttons_class() => 'color: {{VALUE}};',
				),
			)
		);
		$element->update_control(
			'button_background_color',
			array(
				'selectors' => array(
					$this->get_buttons_class() => 'background-color: {{VALUE}};',
				),
			)
		);
        $element->update_control(
            'button_box_shadow',
            array(
                'selector' => $this->get_buttons_class()
            )
        );
        $element->update_control(
            'button_border',
            array(
                'selector' => $this->get_buttons_class()
            )
        );
        $typographyGroup = $controls_manager->get_control_groups('border');

        foreach ($typographyGroup->get_fields() as $field_key => $field) {
            $control_id = "button_border_{$field_key}";
            $old_control_data = $controls_manager->get_control_from_stack($element->get_unique_name(), $control_id);
            $element->update_control($control_id, [
                'selectors'  => [
                    $this->get_buttons_class() => reset($old_control_data['selectors']),
                ]
            ]);
        }
		$border_radius_class = $this->get_buttons_class() . ', .quantity input, .coupon input';
        $element->update_control(
			'button_border_radius',
			array(
				'selectors' => array(
					$border_radius_class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
        $element->update_control(
			'button_hover_text_color',
			array(
				'selectors' => array(
					$this->get_buttons_class('hover') => 'color: {{VALUE}};',
				),
			)
		);
		$element->update_control(
			'button_hover_background_color',
			array(
				'selectors' => array(
					$this->get_buttons_class('hover') => 'background-color: {{VALUE}};',
				),
			)
		);
        $element->update_control(
            'button_hover_box_shadow',
            array(
                'selector' => $this->get_buttons_class('hover')
            )
        );
        $element->update_control(
            'button_hover_border',
            array(
                'selector' => $this->get_buttons_class('hover')
            )
        );
        $typographyGroup = $controls_manager->get_control_groups('border');

        foreach ($typographyGroup->get_fields() as $field_key => $field) {
            $control_id = "button_hover_border_{$field_key}";
            $old_control_data = $controls_manager->get_control_from_stack($element->get_unique_name(), $control_id);
            $element->update_control($control_id, [
                'selectors'  => [
                    $this->get_buttons_class('hover') => reset($old_control_data['selectors']),
                ]
            ]);
        }
        $element->update_control(
			'button_hover_border_radius',
			array(
				'selectors' => array(
					$this->get_buttons_class('hover') => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
        $element->update_responsive_control(
			'button_padding',
			array(
				'selectors' => array(
					$this->get_buttons_class('default','no_padding') => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

    public static function update_globals_from_elementor($check, $object_id, $meta_key, $value, $prev_value)
    {
        $kit_id = get_option('elementor_active_kit');
        if ($object_id == $kit_id && $meta_key == '_elementor_page_settings') {
            //settings prefix
            $current_settings = Settings::current_settings();

            $is_uicore = \apply_filters('ui_is_theme_options_save',false);
            $the_filter = current_filter();

            //Global colors
            $global_colors = [
                [
                    'option' => 'pColor',
                    'id' => 'uicore_primary',
                    'name' => 'Primary',
                ],
                [
                    'option' => 'sColor',
                    'id' => 'uicore_secondary',
                    'name' => 'Secondary',
                ],
                [
                    'option' => 'aColor',
                    'id' => 'uicore_accent',
                    'name' => 'Accent',
                ],
                [
                    'option' => 'hColor',
                    'id' => 'uicore_headline',
                    'name' => 'Headline',
                ],
                [
                    'option' => 'bColor',
                    'id' => 'uicore_body',
                    'name' => 'Body',
                ],
                [
                    'option' => 'dColor',
                    'id' => 'uicore_dark',
                    'name' => 'Dark Neutral',
                ],
                [
                    'option' => 'lColor',
                    'id' => 'uicore_light',
                    'name' => 'Light Neutral',
                ],
                [
                    'option' => 'wColor',
                    'id' => 'uicore_white',
                    'name' => 'White',
                ],
            ];
            error_log($is_uicore ? 'TRUE' : 'FALSE');
            foreach ($global_colors as $id => $color) {
                //let's first check if they are uicore_globals else ovewride them
                if (!$is_uicore) {
                    //is not uicore than we need to update uicore
                    $to_set = $value['system_colors'][$id]['color'];
                    $current_settings[$color['option']] = $to_set;
                } else {
                    //is uicore than we need to update Elementor
                    $value['system_colors'][$id]['color'] = $current_settings[$color['option']];
                    $value['system_colors'][$id]['_id'] = $color['id'];
                    $value['system_colors'][$id]['name'] = $color['name'];
                }
            }


            //Global Fonts
            $global_fonts = [
                [
                    'option' => 'pFont',
                    'id' => 'uicore_primary',
                    'name' => 'Primary',
                ],
                [
                    'option' => 'sFont',
                    'id' => 'uicore_secondary',
                    'name' => 'Secondary',
                ],
                [
                    'option' => 'tFont',
                    'id' => 'uicore_text',
                    'name' => 'Text',
                ],
                [
                    'option' => 'aFont',
                    'id' => 'uicore_accent',
                    'name' => 'Accent',
                ],
            ];
            foreach ($global_fonts as $id => $font) {
                //let's first check if they are uicore_globals else ovewride them
                if (!$is_uicore) {
                    $to_set = [
                        'f' => $value['system_typography'][$id]['typography_font_family'],
                        'st' => $value['system_typography'][$id]['typography_font_weight'],
                    ];
                    $current_settings[$font['option']] = $to_set;
                } else {
                    $value['system_typography'][$id] = [
                        '_id' => $font['id'],
                        'title' => $font['name'],
                        'typography_font_family' => $current_settings[$font['option']]['f'],
                        'typography_font_weight' => $current_settings[$font['option']]['st'],
                        'typography_typography' => 'custom',
                    ];
                }
            }

            //Buttons are not handled in both ways vbeacause we are forceing to use only UiCore Impl.
            // Settings::update_globals_from_uicore()

            if (!$is_uicore) {
                self::uicore_meta_trick($the_filter, $object_id, $meta_key, $value, $prev_value);
                $check = $value;

                //Update the db
                $new_settings = ThemeOptions::update_all($current_settings,0);

            }



        } elseif ($object_id == $kit_id && $meta_key == '_elementor_css') {
            $elementor_settings = get_post_meta($kit_id, '_elementor_page_settings', true);

            if (!$elementor_settings) {
                Settings::update_globals_from_uicore();
            }
        }

        return $check;
    }

    static function uicore_meta_trick(
        $filter,
        $object_id,
        $meta_key,
        $meta_value,
        $unique_or_prev_value,
        $old_value = null
    ) {

        // Remove the filters and save the new meta value. Make sure that
        // the priority and number of arguments are exactly the same as
        // when you added the filters.
        remove_filter('add_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);
        remove_filter('update_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);

        // Manually save the meta data.
        if ('add_post_metadata' === $filter) {
            add_metadata('post', $object_id, $meta_key, $meta_value, $unique_or_prev_value);
        } elseif ('update_post_metadata' === $filter) {
            update_metadata('post', $object_id, $meta_key, $meta_value, $unique_or_prev_value);
        }
        // // Finally, re-add the filters.
        // add_filter('add_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);
        // add_filter('update_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);

        //just to be sure
        // \Elementor\Plugin::$instance->files_manager->clear_cache();
        // Settings::clear_cache();

    }

    function custom_post_elementor_support()
    {
        //if exists, assign to $cpt_support var
        $cpt_support = get_option('elementor_cpt_support');

        //check if option DOESN'T exist in db
        if (!$cpt_support) {
            $cpt_support = ['page', 'post', 'portfolio']; //create array of our default supported post types
            update_option('elementor_cpt_support', $cpt_support); //write it to the database
        }

        //if it DOES exist, but portfolio is NOT defined
        elseif (!in_array('portfolio', $cpt_support)) {
            $cpt_support[] = 'portfolio'; //append to array
            update_option('elementor_cpt_support', $cpt_support); //update database
        }

        //otherwise do nothing, portfolio already exists in elementor_cpt_support option
    }


    /**
     * Add Support For custom location used in Theme Builder
     *
     * @param [type] $elementor_theme_manager
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.2.0
     */
    function elementor_locations($elementor_theme_manager)
    {
        $elementor_theme_manager->register_all_core_location();
    }

    /**
     * Add new google fonts to elementor
     *
     * @param [type] $old
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.2.0
     */
    function new_google_fonts($old)
    {
		$new = [
			"Anek Latin"        =>  'googlefonts',
			"Plus Jakarta Sans" =>  'googlefonts',
			"Space Grotesk"     =>  'googlefonts',
			"Jost"              =>  'googlefonts',
			"Albert Sans"       =>  'googlefonts',
			"Crimson Text"      =>  'googlefonts',
		];
		return array_merge($old, $new);
    }

    public function register_fonts_groups( $font_groups )
    {
		$new_groups = [
            self::CUSTOM =>__( 'UiCore Custom', 'uicore-framework' ),
            self::TYPEKIT =>__( 'UiCore Typekit', 'uicore-framework' ),
        ];
		return array_merge( $new_groups, $font_groups );
	}


    public function register_fonts_in_control( $font_groups )
    {
        $uicore_custom = Data::get_custom_fonts('simple',self::CUSTOM);
        $uicore_typekit = Data::get_typekit_fonts('simple',self::TYPEKIT);

        $new_groups = array_merge($uicore_custom, $uicore_typekit);

		return array_merge( $new_groups, $font_groups );
    }

    function print_custom_font_link( $font )
    {
        $fonts = Helper::get_option('customFonts');
        $css = '';
        if(\is_array($fonts)){
            foreach($fonts as $font){
                $css .= $this->get_font_face_css($font);
            }
        }
        return $css;
    }
    function get_font_face_css( $font )
    {
            $css = '';
            $font_display = get_option( 'elementor_font_display','auto');
			foreach ( $font['variants'] as $key => $variant ) {

                $links = $variant['src'];

                //Font Style
                if (strpos($variant['type'], 'italic') !== false) {
                    $font_style = 'italic';
                } else {
                    $font_style = 'normal';
                }
                //Font Weight
                if ((strpos($variant['type'], 'regular') !== false) ||(strpos($variant['type'], 'normal') !== false)) {
                    $font_weight = '400';
                } else {
                    if (strlen(str_replace('italic', '', $variant['type'])) < 2) {
                        $font_weight = 'normal';
                    } else {
                        $font_weight = str_replace('italic', '', $variant['type']);
                    }
                }

				$css  .= ' @font-face { font-family:"' . esc_attr( $font['family'] ) . '";';
				$css .= 'src:';
				$arr  = array();
				if ( $links['woff'] ) {
					$arr[] = 'url("' . esc_url( $links['woff'] ) . '") format(\'woff\')';
				}
				if ( $links['ttf'] ) {
					$arr[] = 'url("' . esc_url( $links['ttf'] ) . '") format(\'truetype\')';
				}
				if ( $links['eot'] ) {
					$arr[] = 'url(' . esc_url( $links['eot'] ) . ") format('opentype')";
				}
				if ( $links['svg'] ) {
					$arr[] = 'url(' . esc_url( $links['svg'] ) . '#' . esc_attr( strtolower( str_replace( ' ', '_', $font['family'] ) ) ) . ") format('svg')";
				}
				$css .= join( ', ', $arr );
				$css .= ';';
				$css .= 'font-display:'.$font_display.';font-style:'.$font_style.';font-weight:'.$font_weight.';';
				$css .= '}';
			}

			return $css;

    }

    function print_typekit_font_link( $font )
    {
        $kit_url = sprintf( self::TYPEKIT_FONTS_LINK, $this->get_typekit_kit_id() );
        echo '<link rel="stylesheet" type="text/css" href="' . $kit_url . '">';
    }

    function get_typekit_kit_id(){
        $typekit =  Helper::get_option('typekit', false );
        if(isset($typekit['id'])){
            return $typekit['id'];
        }else{
            return;
        }
    }

    function add_custom_icons($tabs = [])
    {
        include UICORE_INCLUDES . '/elementor/generic/icons.php';
        $tabs['uicore-icons'] = [
            'name' => 'uicore-icons',
            'label' => __('Themify Icons', 'uicore-framework'),
            'url' => UICORE_ASSETS . '/fonts/themify-icons.css',
            'enqueue' => [UICORE_ASSETS . '/fonts/themify-icons.css'],
            'prefix' => 'ti-',
            'displayPrefix' => 'ti',
            'labelIcon' => 'fas fa-folder-open',
            'ver' => '1.0.0',
            'icons' => $icons //$icons from icons.php
        ];

        return $tabs;
    }
}
new Core();
