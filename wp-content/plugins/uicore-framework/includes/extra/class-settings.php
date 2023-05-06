<?php

namespace UiCore;

defined('ABSPATH') || exit();

// Here we store and define all the needed settings
class Settings
{

	/**
	 * Get current settings
	 *
	 * @return array
	 */
	public static function current_settings()
	{
		return ThemeOptions::get_all();
	}

	/**
	 * Set default settings
	 *
	 * @return WP_REST_Response
	 */
	public static function update_style($settings)
	{

		$path = UICORE_INCLUDES . '/extra/assets-manager';
		//Required for CSS Class and Js Class
    if(!\class_exists('MatthiasMullie\Minify\Minify')){
      require_once $path . '/Minify.php';
      require_once $path . '/CSS.php';
      require_once $path . '/JS.php';
      require_once $path . '/Exception.php';
      require_once $path . '/Exceptions/BasicException.php';
      require_once $path . '/Exceptions/FileImportException.php';
      require_once $path . '/Exceptions/IOException.php';
      require_once $path . '/ConverterInterface.php';
      require_once $path . '/Converter.php';
    }
	require_once $path . '/customCSS.php';

	if(!class_exists('MatthiasMullie\PathConverter\NoConverter')) {
		require_once $path . '/NoConverter.php';
	}

		//generate style and fonts for block editor
		new BlockEditorStyle($settings);

		//frontend assets
		new CSS($settings);
		new JS($settings);

		//Clear frontend transients
		Helper::delete_frontend_transients();
	}

	/**
	 * Update Theme Options Settings
	 *
	 * @param array $settings
	 * @return void
	 * @author Andrei Voica <andrei@uicore.co
	 * @since 1.0.0
	 */
	public static function update_settings($settings)
	{
		//Update the db
		$new_settings = ThemeOptions::update_all($settings);

		//update elementor options and glbals
		Settings::elementor_update($settings);

		//Update all styles and scripts
		Settings::update_style($new_settings);

		return ['status' => 'success'];
	}

	/**
	 * Disable elementor color and typo and update globals
	 *
	 * @param array $json
	 * @return void
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 1.0.0
	 */
	public static function elementor_update(array $json)
	{
		update_option('elementor_disable_color_schemes', 'yes');
		update_option('elementor_disable_typography_schemes', 'yes');

		Settings::update_globals_from_uicore($json);
	}

	/**
	 * Clear Style transients (style-json, style) and regenerate them (default)
	 *
	 * @param boolean $regenerate
	 * @return void
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 1.0.0
	 */
	public static function clear_cache($regenerate = true)
	{
		Helper::delete_frontend_transients();
		if ($regenerate) {
			$new = Settings::current_settings();
			Settings::update_settings($new);
		}
	}

	/**
	 * Chec if the new value is different from the default one
	 *
	 * @param mixed $default
	 * @param mixed $current
	 * @return boolean
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 3.0.0
	 */
	public static function is_not_default($default,$current){
		if(is_array($default)){
			return strcmp(json_encode($default), json_encode($current));
		}else{
			return ($default != $current);
		}
	}

    /**
	 * Get Page Options default settings
	 *
	 * @return array
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 4.1.0
	 */
    public static function po_get_default_settings(){
        return
        [
            'pagetitle' => 'theme default',
            'pagetitle_bg' => [
              'type' => 'theme default',
              'solid' => '#E8E8E8',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
              'image' => [
                'url' => '',
                'attachment' => 'scroll',
                'position' => [
                  'd' => 'top center',
                  't' => 'center center',
                  'm' => 'center center',
                ],
                'repeat' => 'no-repeat',
                'size' => [
                  'd' => 'cover',
                  't' => 'cover',
                  'm' => 'cover',
                ],
              ],
            ],
            'pagetitle_overlay' => [
              'type' => 'theme default',
              'solid' => 'rgba(219, 84, 97, 0.1)',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
            ],
            'pagetitle_color' => [
              'type' => 'theme default',
              'solid' => 'rgba(219, 84, 97, 0.1)',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
            ],
            'breadcrumbs' => 'theme default',
            'layout' => 'theme default',
            'bodybg' => [
              'type' => 'theme default',
              'solid' => '#E8E8E8',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
              'image' => [
                'url' => '',
                'attachment' => 'scroll',
                'position' => [
                  'd' => 'top center',
                  't' => 'center center',
                  'm' => 'center center',
                ],
                'repeat' => 'no-repeat',
                'size' => [
                  'd' => 'cover',
                  't' => 'cover',
                  'm' => 'cover',
                ],
              ],
            ],
            'boxbg' => [
              'type' => 'theme default',
              'solid' => 'rgba(219, 84, 97, 0.1)',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
            ],
            'header' => 'theme default',
            'transparent' => 'theme default',
            'shrink' => 'theme default',
            'topbar' => 'theme default',
            'footer' => 'theme default',
            'copyright' => 'theme default',
            'customcss' => '',
            'customjs' => '',
            'performance_preload' => [
              0 => [
                'url' => '',
                'as' => '',
              ],
            ],
            'logo' => '',
            'logoS' => '',
            'logoMobile' => '',
            'logoSMobile' => '',
            'header_transparent_color_m' => [
              'type' => 'theme default',
              'solid' => 'rgba(219, 84, 97, 0.1)',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
            ],
            'header_transparent_color_h' => [
              'type' => 'theme default',
              'solid' => 'rgba(219, 84, 97, 0.1)',
              'gradient' => [
                'angle' => '0',
                'color1' => '#19187C',
                'color2' => '#532df5',
              ],
            ],
          ];
    }

    /**
	 * Get Page Options as array or json (both default and non default values)
	 *
	 * @param string $post_id
	 * @param bol $json
	 * @return string|array
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 4.1.0
	 */
    public static function po_get_page_settings($post_id,$json=false)
    {
        $default_settings = self::po_get_default_settings();
        $meta = get_post_meta($post_id, 'page_options', true);
        //not empty
        if(Helper::isJson($meta)){
            $meta = json_decode($meta, true);
        }else{
            $meta = [];
        }
        $meta = \wp_parse_args($meta, $default_settings);
        if($json){
            return \json_encode($meta);
        }
        return $meta;
    }

    /**
	 * Filter settings and keep only non default values
	 *
	 * @param string $settings
	 * @return string|false
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 4.1.0
	 */
    public static function po_get_options_for_save($settings)
    {
      $settings = stripslashes($settings);

        if(Helper::isJson($settings)){
            $settings = json_decode($settings, true);
        }else{
            $settings = [];
        }
        $db_settings = [];
        foreach(self::po_get_default_settings() as $key => $value){

            if(isset($settings[$key]) && self::is_not_default($value, $settings[$key])){
                $db_settings[$key] = $settings[$key];
            }else{
                if(is_array($db_settings) && isset($db_settings[$key])){
                    unset($db_settings[$key]);
                }
            }
        }
        $db_settings = addslashes( json_encode($db_settings) );
        return $db_settings;
    }

	/**
	 * Update elementor globals from uicore settings array and clear elementor css cache
	 *
	 * @param array $json
	 * @return void
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 1.0.0
	 */
	public static function update_globals_from_uicore(array $json = null)
	{

    // \add_filter('ui_is_theme_options_save','\__return_true');
		if ($json === null) {
			$json = Settings::current_settings();
		}

		$kit_id = get_option('elementor_active_kit');
		if (!$kit_id) {
			$kit_id = Settings::create_default_kit();
		}
		if (is_wp_error($kit_id)) {
			return;
		}
		if (isset($json['pColor'])) {
			//Helper Function => wil return meta array with needed changes

			$meta_old = get_post_meta($kit_id, '_elementor_page_settings', true);

			if (!is_wp_error($meta_old) && is_array($meta_old)) {
				$meta_new = $meta_old;
			} else {
				$meta_new = [
					'system_colors' => [],
					'system_typography' => [],
					'__globals__' => [],
				];
			}

			$meta_new['system_colors'][0] = [
				'_id' => 'uicore_primary',
				'title' => 'Primary',
				'color' => $json['pColor'],
			];
			$meta_new['system_colors'][1] = [
				'_id' => 'uicore_secondary',
				'title' => 'Secondary',
				'color' => $json['sColor'],
			];
			$meta_new['system_colors'][2] = [
				'_id' => 'uicore_accent',
				'title' => 'Accent',
				'color' => $json['aColor'],
			];
			$meta_new['system_colors'][3] = [
				'_id' => 'uicore_headline',
				'title' => 'Headline',
				'color' => $json['hColor'],
			];
			$meta_new['system_colors'][4] = [
				'_id' => 'uicore_body',
				'title' => 'Body',
				'color' => $json['bColor'],
			];
			$meta_new['system_colors'][5] = [
				'_id' => 'uicore_dark',
				'title' => 'Dark Neutral',
				'color' => $json['dColor'],
			];
			$meta_new['system_colors'][6] = [
				'_id' => 'uicore_light',
				'title' => 'Light Neutral',
				'color' => $json['lColor'],
			];
			$meta_new['system_colors'][7] = [
				'_id' => 'uicore_white',
				'title' => 'White',
				'color' => $json['wColor'],
			];

			$meta_new['system_typography'][0] = [
				'_id' => 'uicore_primary',
				'title' => 'Primary',
				'typography_font_family' => $json['pFont']['f'],
				'typography_font_weight' => ($json['pFont']['st'] === 'regular') ? 'normal' : $json['pFont']['st'],
				'typography_typography' => 'custom',
			];
			$meta_new['system_typography'][1] = [
				'_id' => 'uicore_secondary',
				'title' => 'Secondary',
				'typography_font_family' => $json['sFont']['f'],
				'typography_font_weight' => ($json['sFont']['st'] === 'regular') ? 'normal' : $json['sFont']['st'],
				'typography_typography' => 'custom',
			];
			$meta_new['system_typography'][2] = [
				'_id' => 'uicore_text',
				'title' => 'Text',
				'typography_font_family' => $json['tFont']['f'],
				'typography_font_weight' => ($json['tFont']['st'] === 'regular') ? 'normal' : $json['tFont']['st'],
				'typography_typography' => 'custom',
			];
			$meta_new['system_typography'][3] = [
				'_id' => 'uicore_accent',
				'title' => 'Accent',
				'typography_font_family' => $json['aFont']['f'],
				'typography_font_weight' => ($json['aFont']['st'] === 'regular') ? 'normal' : $json['aFont']['st'],
				'typography_typography' => 'custom',
			];

			//Allways custom so it can overwrite the other pops
			unset($meta_new['__globals__']['button_typography_typography']);
			$meta_new['button_typography_typography'] = 'custom';

			$meta_new['button_typography_font_family'] = self::font_filter($json['button_typography_typography']['f'], $json);
			$meta_new['button_typography_font_weight'] = $json['button_typography_typography']['st'];
			$meta_new['button_typography_text_transform'] = $json['button_typography_typography']['t'];
			$meta_new['button_typography_letter_spacing'] = array(
				"unit" => "em",
				"size" => $json['button_typography_typography']['ls']
			);
			$meta_new['button_typography_line_height'] = array(
				"unit" => "em",
				"size" => 1
			);

			if (
				strlen($json['button_typography_typography']['s']['d']) == 0
			) {
				unset($meta_new['button_typography_font_size']);
			} else {
				$meta_new['button_typography_font_size'] = [
					'unit' => 'px',
					'size' => $json['button_typography_typography']['s']['d'],
				];
			}
			if (strlen($json['button_typography_typography']['s']['t']) == 0) {
				unset($meta_new['button_typography_font_size_tablet']);
			} else {
				$meta_new['button_typography_font_size_tablet'] = [
					'unit' => 'px',
					'size' => $json['button_typography_typography']['s']['t'],
				];
			}

			if (strlen($json['button_typography_typography']['s']['m']) == 0) {
				unset($meta_new['button_typography_font_size_mobile']);
			} else {
				$meta_new['button_typography_font_size_mobile'] = [
					'unit' => 'px',
					'size' => $json['button_typography_typography']['s']['m'],
				];
			}


			$meta_new = self::process_color($meta_new, 'button_text_color', $json['button_typography_typography']['c']);
			$meta_new = self::process_color(
				$meta_new,
				'button_hover_text_color',
				$json['button_typography_typography']['ch']
			);
			$meta_new = self::process_color($meta_new, 'button_background_color', $json['button_background_color']['m']);
			$meta_new = self::process_color(
				$meta_new,
				'button_hover_background_color',
				$json['button_background_color']['h']
			);

			$meta_new['button_border_border'] = $json['button_border_border'];

			$meta_new['button_border_radius'] = $meta_new['button__hover_border_radius'] = [
				'unit' => 'px',
				'top' => $json['button_border_radius'],
				'right' => $json['button_border_radius'],
				'bottom' => $json['button_border_radius'],
				'left' => $json['button_border_radius'],
				'isLinked' => true,
			];

			if ($json['button_border_border'] != 'none') {
				$meta_new['button_border_width'] = $meta_new['button_hover_border_width'] = [
					'unit' => 'px',
					'top' => $json['button_border_width'],
					'right' => $json['button_border_width'],
					'bottom' => $json['button_border_width'],
					'left' => $json['button_border_width'],
					'isLinked' => true,
				];

				$meta_new = self::process_color($meta_new, 'button_border_color', $json['button_border_color']['m']);
				$meta_new = self::process_color($meta_new, 'button_hover_border_color', $json['button_border_color']['h']);
			} else {
				unset($meta_new['button_border_width']);
				unset($meta_new['button_border_color']);
				unset($meta_new['button_hover_border_color']);
			}

			$meta_new['button_padding'] = [
				'unit' => 'px',
				'top' => $json['button_padding']['d']['top'] ?? 0,
				'right' => $json['button_padding']['d']['right'] ?? 0,
				'bottom' => $json['button_padding']['d']['bottom'] ?? 0,
				'left' => $json['button_padding']['d']['left'] ?? 0,
				'isLinked' => false,
			];

			if (strlen($json['button_padding']['t']['top']) == 0) {
				unset($meta_new['button_padding_tablet']);
			} else {
				$meta_new['button_padding_tablet'] = [
					'unit' => 'px',
					'top' => $json['button_padding']['t']['top'],
					'right' => $json['button_padding']['t']['right'],
					'bottom' => $json['button_padding']['t']['bottom'],
					'left' => $json['button_padding']['t']['left'],
					'isLinked' => false,
				];
			}
			if (strlen($json['button_padding']['m']['top']) == 0) {
				unset($meta_new['button_padding_mobile']);
			} else {
				$meta_new['button_padding_mobile'] = [
					'unit' => 'px',
					'top' => $json['button_padding']['m']['top'],
					'right' => $json['button_padding']['m']['right'],
					'bottom' => $json['button_padding']['m']['bottom'],
					'left' => $json['button_padding']['m']['left'],
					'isLinked' => false,
				];
			}


      $meta_new['container_width'] = [
				'unit' => 'px',
				'size' => $json['gen_full_w'] ?? '1140',
				'sizes' => []
			];

			remove_filter('add_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);
			remove_filter('update_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);

			update_post_meta($kit_id, '_elementor_page_settings', $meta_new);

			//update site kit css (will generate the new css with the new globals)
			if (class_exists('\Elementor\Plugin')) {
				//Force Clear cache
				// \Elementor\Plugin::$instance->files_manager->clear_cache();
				$post_css_file = new \Elementor\Core\Files\CSS\Post($kit_id);
				$post_css_file->update();
			}

			add_filter('add_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);
			add_filter('update_post_metadata', ['\UiCore\Elementor\Core', 'update_globals_from_elementor'], 20, 5);

		}
	}

	/**
	 * Create default elementor kit
	 *
	 * @return int|WP_Error — The post ID on success. The value 0 or WP_Error on failure.
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 1.0.0
	 */
	public static function create_default_kit()
	{
		$postarr = [
			'post_type' => 'elementor_library',
			'post_title' => __('Default Kit', 'elementor'),
			'post_status' => 'publish',
      'meta_input' => [
				'_elementor_edit_mode' => 'builder',
				'_elementor_template_type' => 'kit',
			],
		];

		$id = wp_insert_post($postarr);
    update_option( 'elementor_active_kit', $id );
    return $id;
	}

	public static function font_filter($fam, $settings)
	{
		switch ($fam) {
			case "Primary":
				$font = $settings['pFont']['f'];
				break;
			case "Secondary":
				$font = $settings['sFont']['f'];
				break;
			case "Text":
				$font = $settings['tFont']['f'];
				break;
			case "Accent":
				$font = $settings['aFont']['f'];
				break;
			default:
				$font = $fam;
		}
		return $font;
	}
	/**
	 * Check if is global and return css value for it
	 *
	 * @param $color
	 * @return string Css Value for color( color code or css variable if is global)
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 1.0.0
	 */
	public static function color_filter($color)
	{
		if ($color == 'Primary') {
			$color = 'var(--e-global-color-uicore_primary)';
		} elseif ($color == 'Secondary') {
			$color = 'var(--e-global-color-uicore_secondary)';
		} elseif ($color == 'Accent') {
			$color = 'var(--e-global-color-uicore_accent)';
		} elseif ($color == 'Headline') {
			$color = 'var(--e-global-color-uicore_headline)';
		} elseif ($color == 'Body') {
			$color = 'var(--e-global-color-uicore_body)';
		} elseif ($color == 'Dark Neutral') {
			$color = 'var(--e-global-color-uicore_dark)';
		} elseif ($color == 'Light Neutral') {
			$color = 'var(--e-global-color-uicore_light)';
		} elseif ($color == 'White') {
			$color = 'var(--e-global-color-uicore_white)';
		}
		return $color;
	}

	static function process_color($meta, $key, $value)
	{
		$colors_array = ['Primary', 'Secondary', 'Accent', 'Headline', 'Body', 'Dark Neutral', 'Light Neutral', 'White'];
		if (in_array($value, $colors_array)) {
			$preset = $value;
			if ($preset == 'Dark Neutral') {
				$preset = 'dark';
			}
			if ($preset == 'Light Neutral') {
				$preset = 'light';
			}
			unset($meta[$key]);
			$meta['__globals__'][$key] = 'globals/colors?id=uicore_' . strtolower($preset);
		} else {
			unset($meta['__globals__'][$key]);
			$meta[$key] = $value;
		}
		return $meta;
	}

   static function get_settings_data()
   {
      return array (
         0 =>
         array (
           'id' => 0,
           'adv' => true,
           'name' => _x('Layout', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the website layout.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('site layout', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         1 =>
         array (
           'id' => 1,
           'adv' => true,
           'name' => _x('Boxed Container Width', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the boxed outer container width.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('site boxed width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 0,
           'visible' =>
           array (
             'gen_layout' => 'boxed',
           ),
         ),
         2 =>
         array (
           'id' => 2,
           'adv' => true,
           'name' => _x('Boxed Background Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the boxed inner container background color.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('site background color boxed', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 0,
           'visible' =>
           array (
             'gen_layout' => 'boxed',
           ),
         ),
         3 =>
         array (
           'id' => 3,
           'adv' => true,
           'name' => _x('Body Background', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the <body> background.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('body background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         4 =>
         array (
           'id' => 4,
           'adv' => true,
           'name' => _x('Container Width', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the container maximum width.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('container width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         5 =>
         array (
           'id' => 5,
           'name' => _x('RTL', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable right to left writing for arabic languages.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('rtl', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         6 =>
         array (
           'id' => 6,
           'adv' => true,
           'name' => _x('Back to Top', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a back to top button on bottom right corner.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('back top scroll', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         7 =>
         array (
           'id' => 7,
           'adv' => true,
           'name' => _x('Back to Top: Show on Mobile', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show the back to top button on mobile devices.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('back top scroll mobile', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 6,
           'visible' =>
           array (
             'gen_btop' => 'true',
           ),
         ),
         8 =>
         array (
           'id' => 8,
           'name' => _x('404 Page', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select a custom 404 page to overwrite the default one.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('404 page error', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         9 =>
         array (
           'id' => 9,
           'name' => _x('Maintenance Mode', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable maintenance mode sitewide.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('maintenance mode', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         10 =>
         array (
           'id' => 10,
           'name' => _x('Maintenance Page', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select a custom maintenance page.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('maintenance mode page', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 9,
           'visible' =>
           array (
             'gen_maintenance' => 'true',
           ),
         ),
         11 =>
         array (
           'id' => 11,
           'adv' => true,
           'name' => _x('Browser Theme Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the browser toolbar color. Available on Chrome 39+ for Android.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('browser theme color toolbar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         12 =>
         array (
           'id' => 12,
           'adv' => true,
           'name' => _x('Browser Theme Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the toolbar color.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('browser theme color toolbar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 11,
           'visible' =>
           array (
             'gen_themecolor' => 'true',
           ),
         ),
         13 =>
         array (
           'id' => 13,
           'adv' => true,
           'name' => _x('Site Border (Passepartout)', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set a colored border around the website.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('site border', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         14 =>
         array (
           'id' => 14,
           'adv' => true,
           'name' => _x('Site Border Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the site border color.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('site border color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 13,
           'visible' =>
           array (
             'gen_siteborder' => 'true',
           ),
         ),
         15 =>
         array (
           'id' => 15,
           'adv' => true,
           'name' => _x('Site Border Width', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the site border width.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('site border width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 13,
           'visible' =>
           array (
             'gen_siteborder' => 'true',
           ),
         ),
         16 =>
         array (
           'id' => 16,
           'name' => _x('Primary Logo', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Branding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the default logo.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('primary logo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         17 =>
         array (
           'id' => 17,
           'name' => _x('Secondary Logo', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Branding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set logo for transparent headers.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('secondary logo transparent', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         18 =>
         array (
           'id' => 18,
           'adv' => true,
           'name' => _x('Mobile Logo', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Branding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set logo for mobile devices. If left blank, Primary Logo will be used.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile logo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         19 =>
         array (
           'id' => 19,
           'adv' => true,
           'name' => _x('Secondary Mobile Logo', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Branding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set logo for mobile devices on transparent headers. If left blank, Secondary Logo will be used.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('secondary mobile logo transparent', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         20 =>
         array (
           'id' => 20,
           'name' => _x('Favicon', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Branding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the icon for browser tab and home screen. Recommended size: 196px x 196 px.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('favicon', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         21 =>
         array (
           'id' => 21,
           'name' => 'Primary',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Your main brand color. Used by most elements throughout the website.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('main primary color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         22 =>
         array (
           'id' => 22,
           'name' => 'Secondary',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Your secondary brand color. Used mainly as hover color or by secondary elements.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('hover secondary color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         23 =>
         array (
           'id' => 23,
           'adv' => true,
           'name' => _x('H1', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set Heading 1 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h1 H1', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         24 =>
         array (
           'id' => 24,
           'adv' => true,
           'name' => _x('H2', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set Heading 2 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h2 H2', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         25 =>
         array (
           'id' => 25,
           'adv' => true,
           'name' => _x('H3', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set Heading 3 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h3 H3', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         26 =>
         array (
           'id' => 26,
           'adv' => true,
           'name' => _x('H4', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set Heading 4 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h4 H4', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         27 =>
         array (
           'id' => 27,
           'adv' => true,
           'name' => _x('H5', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set Heading 5 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h5 H5', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         28 =>
         array (
           'id' => 28,
           'adv' => true,
           'name' => _x('H6', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set Heading 6 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h6 H6', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         29 =>
         array (
           'id' => 29,
           'adv' => true,
           'name' => _x('Body', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set <body> and <p> options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('body paragraph', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         30 =>
         array (
           'id' => 30,
           'name' => _x('Enable Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable / disable the header sitewide.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('enable header', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         31 =>
         array (
           'id' => 31,
           'adv' => true,
           'name' => _x('Header: Layout', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the base layout for header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header layout', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         32 =>
         array (
           'id' => 32,
           'adv' => true,
           'name' => _x('Header Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the header background.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         33 =>
         array (
           'id' => 33,
           'adv' => true,
           'name' => _x('Header Border', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set a 1px border bottom to header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header border', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         34 =>
         array (
           'id' => 34,
           'adv' => true,
           'name' => _x('Header Border Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the header border color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header border color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 33,
           'visible' =>
           array (
             'header_border' => 'true',
           ),
         ),
         35 =>
         array (
           'id' => 35,
           'adv' => true,
           'name' => _x('Header Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set top/bottom spacing for header bar.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' =>
           array (
             'header_layout' => 'classic',
           ),
         ),
         36 =>
         array (
           'id' => 36,
           'adv' => true,
           'name' => _x('Logo Height', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the header logo height.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('logo height', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         37 =>
         array (
           'id' => 37,
           'adv' => true,
           'name' => _x('Content Alignment', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the inner content alignment for Left Header', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header align', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' =>
           array (
             'header_layout' => 'left',
           ),
         ),
         38 =>
         array (
           'id' => 38,
           'name' => _x('Enable Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable a top bar above the header. Hides on scroll automatically.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         39 =>
         array (
           'id' => 39,
           'adv' => true,
           'name' => _x('Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the background for top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         40 =>
         array (
           'id' => 40,
           'adv' => true,
           'name' => _x('Text Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text color for top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner text color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         41 =>
         array (
           'id' => 41,
           'adv' => true,
           'name' => _x('Text Size', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text size for top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner text size', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         42 =>
         array (
           'id' => 42,
           'adv' => true,
           'name' => _x('Link Colors', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the link colors for top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner link color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         43 =>
         array (
           'id' => 43,
           'adv' => true,
           'name' => _x('Top Bar Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the top/bottom spacing for top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         44 =>
         array (
           'id' => 44,
           'name' => _x('Layout', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the top banner column layout.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner layout', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         45 =>
         array (
           'id' => 45,
           'name' => _x('First Column Content Type', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Choose the content type for the first column.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner first column content', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         46 =>
         array (
           'id' => 46,
           'adv' => true,
           'name' => _x('First Column Alignment', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the alignment for the first column.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner first column alignment', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         47 =>
         array (
           'id' => 47,
           'name' => _x('First Column Custom Content', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner first column content', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 38,
           'visible' =>
           array (
             'header_top' => 'true',
           ),
         ),
         48 =>
         array (
           'id' => 48,
           'name' => _x('Second Column Content Type', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Choose the content type for the second column.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top bar second column content', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 44,
           'visible' =>
           array (
             'header_toplayout' => 'two columns',
           ),
         ),
         49 =>
         array (
           'id' => 49,
           'adv' => true,
           'name' => _x('Second Column Alignment', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the alignment for the second column.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top bar second column alignment', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 44,
           'visible' =>
           array (
             'header_toplayout' => 'two columns',
           ),
         ),
         50 =>
         array (
           'id' => 50,
           'adv' => true,
           'name' => _x('Transparent Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set header to transparent background before scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('transparent header', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         51 =>
         array (
           'id' => 51,
           'adv' => true,
           'name' => _x('Transparent Header: Menu Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the menu color for transparent header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('transparent header menu color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 50,
           'visible' =>
           array (
             'header_transparent' => 'true',
           ),
         ),
         52 =>
         array (
           'id' => 52,
           'adv' => true,
           'name' => _x('Transparent Header: Border', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a 1px border bottom to transparent header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('transparent header border', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 50,
           'visible' =>
           array (
             'header_transparent' => 'true',
           ),
         ),
         53 =>
         array (
           'id' => 53,
           'adv' => true,
           'name' => _x('Transparent Header: Border Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the border color for transparent header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('transparent header border color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 50,
           'visible' =>
           array (
             'header_transparent' => 'true',
           ),
         ),
         54 =>
         array (
           'id' => 54,
           'adv' => true,
           'name' => _x('Wide Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Stretches the header container to full screen width.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('wide header', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         55 =>
         array (
           'id' => 55,
           'adv' => true,
           'name' => _x('Sticky Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the header to fixed on top after scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sticky header', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         56 =>
         array (
           'id' => 56,
           'adv' => true,
           'name' => _x('Change Height On Scroll', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change header padding after scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('change height', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 55,
           'visible' =>
           array (
             'header_sticky' => 'true',
           ),
         ),
         57 =>
         array (
           'id' => 57,
           'adv' => true,
           'name' => _x('Padding Before Scroll', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the top/bottom padding of the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('padding before scroll', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 56,
           'visible' =>
           array (
             'header_shrink' => 'true',
           ),
         ),
         58 =>
         array (
           'id' => 58,
           'name' => _x('Call to Action Button', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a call to action button on the right side of the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('call to action button', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         59 =>
         array (
           'id' => 59,
           'adv' => true,
           'name' => _x('Invert Colors on transparent header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the call to action button style.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('call to action button style', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 58,
           'visible' =>
           array (
             'header_transparent' => 'true',
           ),
         ),
         60 =>
         array (
           'id' => 60,
           'adv' => true,
           'name' => _x('Border Radius', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the call to action button style.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('call to action button border radius', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 58,
           'visible' =>
           array (
             'header_cta' => 'true',
           ),
         ),
         61 =>
         array (
           'id' => 61,
           'name' => _x('Button Text', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the call to action button text.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('call to action button text', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 58,
           'visible' =>
           array (
             'header_cta' => 'true',
           ),
         ),
         62 =>
         array (
           'id' => 62,
           'name' => _x('Button Link', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the call to action button link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('call to action button link', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 58,
           'visible' =>
           array (
             'header_cta' => 'true',
           ),
         ),
         63 =>
         array (
           'id' => 63,
           'adv' => true,
           'name' => _x('Link Target', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the call to action button link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('call to action button link target', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 58,
           'visible' =>
           array (
             'header_cta' => 'true',
           ),
         ),
         64 =>
         array (
           'id' => 64,
           'name' => _x('Search', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a search icon on the right side of the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('search', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' =>
           array (
             'header_layout' => 'classic',
           ),
         ),
         65 =>
         array (
           'id' => 65,
           'name' => _x('Social Icons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add social icons on the right side of the header (classic header) or bottom (left header).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('social icons', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         66 =>
         array (
           'id' => 66,
           'name' => _x('Enable Widget Area (Desktop Screens Only)', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a custom widget area on the right side of the menu.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('widget area desktop', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' => true,
         ),
         67 =>
         array (
           'id' => 67,
           'adv' => true,
           'name' => _x('Menu Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set menu text options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('menu typography', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         68 =>
         array (
           'id' => 68,
           'adv' => true,
           'name' => _x('Menu Item Spacing', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the left/right padding for menu items.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('menu item spacing', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         69 =>
         array (
           'id' => 69,
           'adv' => true,
           'name' => _x('Menu Alignment', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the menu alignment on header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('menu alignment', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' =>
           array (
             'header_layout' => 'classic',
           ),
         ),
         70 =>
         array (
           'id' => 70,
           'adv' => true,
           'name' => _x('Dropdown Background Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the dropdown menu background color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('dropdown background color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         71 =>
         array (
           'id' => 71,
           'adv' => true,
           'name' => _x('Dropdown Menu Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the dropdown menu text options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('dropdown menu typography', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         72 =>
         array (
           'id' => 72,
           'adv' => true,
           'name' => _x('Mobile Logo Height', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the logo height on mobile header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile logo height', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         73 =>
         array (
           'id' => 73,
           'adv' => true,
           'name' => _x('Mobile Menu Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the mobile menu background.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile menu background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         74 =>
         array (
           'id' => 74,
           'adv' => true,
           'name' => _x('Use Secondary Logo', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Use secondary mobile logo on menu dropdown. If Secondary Mobile Logo is not set, Secondary Logo will be used instead.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile secondary logo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         75 =>
         array (
           'id' => 75,
           'adv' => true,
           'name' => _x('Mobile Menu Animation', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the entrance animation for mobile popover menu.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile menu animation', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         76 =>
         array (
           'id' => 76,
           'adv' => true,
           'name' => _x('Mobile Menu Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set mobile menu text options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile menu typography', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         77 =>
         array (
           'id' => 77,
           'adv' => true,
           'name' => _x('Mobile Menu Alignment', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set mobile menu text alignment.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile menu alignment', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         78 =>
         array (
           'id' => 78,
           'name' => _x('Enable Widget Area (Mobile Screens Only)', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a custom widget area on the bottom on mobile menu popover.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('mobile widget area', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         79 =>
         array (
           'id' => 79,
           'adv' => true,
           'name' => _x('Footer: Layout', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the footer column layout.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('layout', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         80 =>
         array (
           'id' => 80,
           'adv' => true,
           'name' => _x('Footer Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the footer background.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('footer background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         81 =>
         array (
           'id' => 81,
           'adv' => true,
           'name' => _x('Footer Vertical Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the footer top/bottom spacing.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('footer vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         82 =>
         array (
           'id' => 82,
           'adv' => true,
           'name' => _x('Footer Title Style', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text options for all titles in footer.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('footer title', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         83 =>
         array (
           'id' => 83,
           'adv' => true,
           'name' => _x('Footer Text Style', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text options for all footer text.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('footer text', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         84 =>
         array (
           'id' => 84,
           'adv' => true,
           'name' => _x('Footer Link Colors', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the main and hover colors for footer links.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('footer link color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         85 =>
         array (
           'id' => 85,
           'adv' => true,
           'name' => _x('Wide Footer', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Stretch the footer to full screen width.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('wide footer', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         86 =>
         array (
           'id' => 86,
           'name' => _x('Enable Copyright', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable copyright bar below the footer.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('enable copyright', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         87 =>
         array (
           'id' => 87,
           'adv' => true,
           'name' => _x('Copyright Background Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the background color for copyright bar.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright background color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         88 =>
         array (
           'id' => 88,
           'adv' => true,
           'name' => _x('Border', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set a border between footer and copyright bar.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright border', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         89 =>
         array (
           'id' => 89,
           'adv' => true,
           'name' => _x('Border Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the copyright bar border color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright border color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 88,
           'visible' =>
           array (
             'copyrights_border' => 'true',
           ),
         ),
         90 =>
         array (
           'id' => 90,
           'adv' => true,
           'name' => _x('Wide Border', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Stretch the copyright border to full screen width.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright border wide', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 88,
           'visible' =>
           array (
             'copyrights_border' => 'true',
           ),
         ),
         91 =>
         array (
           'id' => 91,
           'adv' => true,
           'name' => _x('Copyright Vertical Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set top/bottom spacing for copyright bar.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         92 =>
         array (
           'id' => 92,
           'adv' => true,
           'name' => _x('Copyright Text Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the copyright text color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright text color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         93 =>
         array (
           'id' => 93,
           'adv' => true,
           'name' => _x('Copyright Font Size', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the copyright font size.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright font size', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         94 =>
         array (
           'id' => 94,
           'adv' => true,
           'name' => _x('Copyright Link Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the main and hover colors for copyright bar links.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright link color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         95 =>
         array (
           'id' => 95,
           'name' => _x('Copyright Social Icons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add social icons on the copyright bar.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copyright social icons', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 86,
           'visible' =>
           array (
             'copyrights' => 'true',
           ),
         ),
         96 =>
         array (
           'id' => 96,
           'name' => _x('Enable Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable / disable page title sitewide.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('enable page title', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         97 =>
         array (
           'id' => 97,
           'adv' => true,
           'name' => _x('Page Title Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the page title background.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         98 =>
         array (
           'id' => 98,
           'adv' => true,
           'name' => _x('Page Title Background Overlay', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the overlay layer for page title background.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title background overlay', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         99 =>
         array (
           'id' => 99,
           'adv' => true,
           'name' => _x('Featured Image as Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Use page / post featured image as background. If no featured image is set, the above background will be used instead.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('featured image', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         100 =>
         array (
           'id' => 100,
           'adv' => true,
           'name' => _x('Vertical Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the top/bottom spacing for page title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         101 =>
         array (
           'id' => 101,
           'adv' => true,
           'name' => _x('Title Tag', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select the heading tag for page title. Recommended tags: H1 or H2.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title tag', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         102 =>
         array (
           'id' => 102,
           'adv' => true,
           'name' => _x('Title Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select the color for page title. Recommended tags: H1 or H2.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         103 =>
         array (
           'id' => 103,
           'adv' => true,
           'name' => _x('Title Text Transform', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text transform option for page title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title text transform', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         104 =>
         array (
           'id' => 104,
           'adv' => true,
           'name' => _x('Title Text Align', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text alignment for page title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title text align', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         105 =>
         array (
           'id' => 105,
           'adv' => true,
           'name' => _x('Container Max Width', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the page title container maximum width for desktop screens. Mobile screens will use full container width.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('container max width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         106 =>
         array (
           'id' => 106,
           'adv' => true,
           'name' => _x('Breadcrumbs', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add breadcrumb links on the right side of page title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('breadcrumbs', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 96,
           'visible' =>
           array (
             'pagetitle' => 'true',
           ),
         ),
         107 =>
         array (
           'id' => 107,
           'adv' => true,
           'name' => _x('Layout', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the blog page and archive layout.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog layout', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         108 =>
         array (
           'id' => 108,
           'adv' => true,
           'name' => _x('Grid Columns', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the number of columns for blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid columns', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         109 =>
         array (
           'id' => 109,
           'adv' => true,
           'name' => _x('Grid Items Spacing', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item spacing for blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid item spacing', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         110 =>
         array (
           'id' => 110,
           'adv' => true,
           'name' => _x('Item Hover Effect', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item hover effect for blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('item hover effect', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         111 =>
         array (
           'id' => 111,
           'adv' => true,
           'name' => _x('Image Ratio', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item image ratio for blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('item image ratio', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 107,
           'visible' =>
           array (
             'blog_layout' => 'classic',
           ),
         ),
         112 =>
         array (
           'id' => 112,
           'adv' => true,
           'name' => _x('Image Border Radius', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item image border radius for blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('item image border radius', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         113 =>
         array (
           'id' => 113,
           'adv' => true,
           'name' => _x('Post Title Style', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the text options for post title in blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('post title', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         114 =>
         array (
           'id' => 114,
           'adv' => true,
           'name' => _x('Show Excerpt on Blog Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable / disable excerpt in blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('post excerpt', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         115 =>
         array (
           'id' => 115,
           'adv' => true,
           'name' => _x('Excerpt Length', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the excerpt length (number of words) in blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('post excerpt length', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         116 =>
         array (
           'id' => 116,
           'adv' => true,
           'name' => _x('Post Excerpt Style', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the post excerpt options for blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('post excerpt style', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         117 =>
         array (
           'id' => 117,
           'adv' => true,
           'name' => _x('Show Author on Blog Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide author name in blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('author', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         118 =>
         array (
           'id' => 118,
           'adv' => true,
           'name' => _x('Show Date on Blog Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide post date in blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('date', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         119 =>
         array (
           'id' => 119,
           'adv' => true,
           'name' => _x('Show Category on Blog Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide post category in blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('category', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         120 =>
         array (
           'id' => 120,
           'adv' => true,
           'name' => _x('Vertical Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the top/bottom spacing on blog page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         121 =>
         array (
           'id' => 121,
           'adv' => true,
           'name' => _x('Blog Items on Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the number of posts displayed on a page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog items', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         122 =>
         array (
           'id' => 122,
           'name' => _x('Blog Page Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar for blog page and archive.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         123 =>
         array (
           'id' => 123,
           'name' => _x('Blog Page Sidebar Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar position for blog page and archive.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sidebar position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 122,
           'visible' => true,
         ),
         124 =>
         array (
           'id' => 124,
           'adv' => true,
           'name' => _x('Blog Page Sticky Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar to sticky on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sticky sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 122,
           'visible' => true,
         ),
         125 =>
         array (
           'id' => 125,
           'adv' => true,
           'name' => _x('Blog Post Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the blog post title layout. Default page title is set in Theme Options - Page Title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('page title', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         126 =>
         array (
           'id' => 126,
           'adv' => true,
           'name' => _x('Blog Post Featured Image', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide the post featured image below the post title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('featured image', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 125,
           'visible' =>
           array (
             'blogs_title' => 'simple page title',
           ),
         ),
         127 =>
         array (
           'id' => 127,
           'adv' => true,
           'name' => _x('Container Max Width', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the page title container maximum width for desktop screens. Mobile screens will use full container width.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('container max width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 125,
           'visible' =>
           array (
             'blogs_title' => 'default page title',
           ),
         ),
         128 =>
         array (
           'id' => 128,
           'adv' => true,
           'name' => _x('Show Author on Blog Post', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide author name on blog post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('author', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         129 =>
         array (
           'id' => 129,
           'adv' => true,
           'name' => _x('Show Date on Blog Post', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide date on blog post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('date', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         130 =>
         array (
           'id' => 130,
           'adv' => true,
           'name' => _x('Show Category on Blog Post', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide category on blog post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('category', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         131 =>
         array (
           'id' => 131,
           'adv' => true,
           'name' => _x('Show Tags on Blog Post', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide tags on blog post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('tags', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         132 =>
         array (
           'id' => 132,
           'adv' => true,
           'name' => _x('Narrow Post Width', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the post container width to 65% of default container width. Only applied on desktop screens.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('narrow width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         133 =>
         array (
           'id' => 133,
           'adv' => true,
           'name' => _x('Reading Progress', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show a reading progress bar below the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('reading progress', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         134 =>
         array (
           'id' => 134,
           'name' => _x('Blog Post Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar for blog post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         135 =>
         array (
           'id' => 135,
           'name' => _x('Blog Post Sidebar Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar position for blog post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sidebar position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 134,
           'visible' => true,
         ),
         136 =>
         array (
           'id' => 136,
           'adv' => true,
           'name' => _x('Blog Post Sticky Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar to sticky on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('sticky sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 134,
           'visible' => true,
         ),
         137 =>
         array (
           'id' => 137,
           'adv' => true,
           'name' => _x('H1', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post Heading 1 options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h1 H1', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         138 =>
         array (
           'id' => 138,
           'adv' => true,
           'name' => _x('H2', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post Heading 2 options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h2 H2', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         139 =>
         array (
           'id' => 139,
           'adv' => true,
           'name' => _x('H3', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post Heading 3 options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h3 H3', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         140 =>
         array (
           'id' => 140,
           'adv' => true,
           'name' => _x('H4', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post Heading 4 options.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h4 H4', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         141 =>
         array (
           'id' => 141,
           'adv' => true,
           'name' => _x('H5', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post Heading 5 options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h5 H5', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         142 =>
         array (
           'id' => 142,
           'adv' => true,
           'name' => _x('H6', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post Heading 6 options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('h6 H6', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         143 =>
         array (
           'id' => 143,
           'adv' => true,
           'name' => _x('Body', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set blog post <body> and <p> options.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('body paragraph', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         144 =>
         array (
           'id' => 144,
           'adv' => true,
           'name' => _x('Layout', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the portfolio page and archive layout.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio layout', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         145 =>
         array (
           'id' => 145,
           'adv' => true,
           'name' => _x('Grid Columns', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the number of columns for portfolio grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio grid columns', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 144,
           'visible' => true,
         ),
         146 =>
         array (
           'id' => 146,
           'adv' => true,
           'name' => _x('Item Size', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item size for justified tiles grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid item size', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 144,
           'visible' =>
           array (
             'portfolio_layout' => 'justified',
           ),
         ),
         147 =>
         array (
           'id' => 147,
           'adv' => true,
           'name' => _x('Item Spacing', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item spacing for portfolio grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid item spacing', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         148 =>
         array (
           'id' => 148,
           'adv' => true,
           'name' => _x('Item Hover Effect', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item hover effect for portfolio grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid item hover effect', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         149 =>
         array (
           'id' => 149,
           'adv' => true,
           'name' => _x('Image Ratio', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item image ratio for portfolio grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid item image ratio', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 144,
           'visible' => true,
         ),
         150 =>
         array (
           'id' => 150,
           'adv' => true,
           'name' => _x('Image Border Radius', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the item image border radius for portfolio grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid item image border radius', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         151 =>
         array (
           'id' => 151,
           'adv' => true,
           'name' => _x('Vertical Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set top/bottom spacing for portfolio page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         152 =>
         array (
           'id' => 152,
           'adv' => true,
           'name' => _x('Portfolio Items on Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the number of portfolio items displayed on a page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         153 =>
         array (
           'id' => 153,
           'adv' => true,
           'name' => _x('Full Width', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Stretch portfolio grid to full page width.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio full width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         154 =>
         array (
           'id' => 154,
           'name' => _x('Portfolio Page Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar for portfolio page and archive.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio page sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         155 =>
         array (
           'id' => 155,
           'name' => _x('Portfolio Page Sidebar Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar position for portfolio page and archive.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio page sidebar position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 154,
           'visible' => true,
         ),
         156 =>
         array (
           'id' => 156,
           'adv' => true,
           'name' => _x('Portfolio Page Sticky Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar to sticky on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio page sidebar sticky', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 154,
           'visible' => true,
         ),
         157 =>
         array (
           'id' => 157,
           'name' => _x('Portfolio Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select a custom portfolio page. If left blank, portfolio will be available at yoursite.com/portfolio.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio custom page', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         158 =>
         array (
           'id' => 158,
           'name' => _x('Portfolio Post Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar for portfolio post.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio post sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         159 =>
         array (
           'id' => 159,
           'name' => _x('Portfolio Post Sidebar Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar position for portfolio posts.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio post sidebar position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 158,
           'visible' => true,
         ),
         160 =>
         array (
           'id' => 160,
           'adv' => true,
           'name' => _x('Portfolio Post Sticky Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar to sticky on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio post sidebar sticky', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 158,
           'visible' => true,
         ),
         161 =>
         array (
           'id' => 161,
           'adv' => true,
           'name' => _x('Header Cart Icon', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show / hide a shopping cart icon on the right side of the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('shop page', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         162 =>
         array (
           'id' => 162,
           'adv' => true,
           'name' => _x('Grid Columns', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the number of columns for shop page grid.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('grid columns', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         163 =>
         array (
           'id' => 163,
           'adv' => true,
           'name' => _x('Shop Page Vertical Padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the top/bottom spacing for shop page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('vertical padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         164 =>
         array (
           'id' => 164,
           'adv' => true,
           'name' => _x('Products on Page', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set number of products displayed on a page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('products number', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         165 =>
         array (
           'id' => 165,
           'name' => _x('Shop Page Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar for shop page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('shop page sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         166 =>
         array (
           'id' => 166,
           'name' => _x('Shop Page Sidebar Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar position for shop page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('shop sidebar position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 165,
           'visible' => true,
         ),
         167 =>
         array (
           'id' => 167,
           'adv' => true,
           'name' => _x('Shop Page Sticky Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar to sticky on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('shop sidebar sticky', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 165,
           'visible' => true,
         ),
         168 =>
         array (
           'id' => 168,
           'name' => _x('Product Page Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar for product page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('product page sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         169 =>
         array (
           'id' => 169,
           'name' => _x('Product Page Sidebar Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar position for product page.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('product sidebar position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 168,
           'visible' => true,
         ),
         170 =>
         array (
           'id' => 170,
           'adv' => true,
           'name' => _x('Product Page Sticky Sidebar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the sidebar to sticky on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('WooCommerce', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('product sidebar sticky', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 168,
           'visible' => true,
         ),
         171 =>
         array (
           'id' => 171,
           'name' => _x('Facebook', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Facebook page link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('facebook', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         172 =>
         array (
           'id' => 172,
           'name' => _x('Twitter', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Twitter account link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('twitter', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         173 =>
         array (
           'id' => 173,
           'name' => _x('YouTube', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Youtube channel link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('youtube', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         174 =>
         array (
           'id' => 174,
           'name' => _x('Instagram', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Instagram account link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('instagram', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         175 =>
         array (
           'id' => 175,
           'name' => _x('LinkedIn', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('LinkedIn profile link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('linkedin', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         176 =>
         array (
           'id' => 176,
           'name' => _x('Pinterest', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Pinterest profile link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('pinterest', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         177 =>
         array (
           'id' => 177,
           'name' => _x('Twitch', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Twitch account link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('pinterest', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         178 =>
         array (
           'id' => 178,
           'name' => _x('Custom CSS', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set your custom CSS code. Loaded before &lt;/head&gt; tag.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Custom', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom css CSS', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         179 =>
         array (
           'id' => 179,
           'name' => _x('Custom JS', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set your custom JavaScript code. Loaded before &lt;/body&gt; tag. &lt;script&gt; tags are automatically added.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Custom', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom js JS javascript', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         180 =>
         array (
           'id' => 180,
           'adv' => true,
           'name' => _x('Header Shadow', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a box shadow on the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header shadow', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' =>
           array (
             'header_layout' => 'classic',
           ),
         ),
         181 =>
         array (
           'id' => 181,
           'name' => _x('Snapchat', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Snapchat profile link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('facebook', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         182 =>
         array (
           'id' => 182,
           'name' => _x('Reddit', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Reddit profile link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('reddit', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         183 =>
         array (
           'id' => 183,
           'name' => _x('TikTok', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('TikTok profile link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('tiktok', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         184 =>
         array (
           'id' => 184,
           'name' => _x('Whatsapp', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Whatsapp shortlink.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('whatsapp', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         185 =>
         array (
           'id' => 185,
           'name' => _x('Vimeo', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Vimeo channel link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('vimeo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         186 =>
         array (
           'id' => 186,
           'name' => _x('WeChat', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('WeChat profile link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('wechat', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         187 =>
         array (
           'id' => 187,
           'name' => _x('Messenger', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Messenger username link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('messenger', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         188 =>
         array (
           'id' => 188,
           'name' => 'Accent',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Usually a contrasting color used to draw attention to key pieces of your website.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('accent color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         189 =>
         array (
           'id' => 189,
           'name' => 'Headline',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('A dark, contrasting color, used by all headlines in your website.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('headline color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         190 =>
         array (
           'id' => 190,
           'name' => 'Body',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('A neutral grey, easy to read color, used by all text elements.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('body color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         191 =>
         array (
           'id' => 191,
           'name' => 'Dark Neutral',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Generally used as background color for footer, copyright and dark sections.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('dark neutral color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         192 =>
         array (
           'id' => 192,
           'name' => 'Light Neutral',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Generally used as background color for light, alternating sections.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('light neutral color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         193 =>
         array (
           'id' => 193,
           'name' => 'Primary',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('The main font of your website. Used by most headlines.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('primary font family', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         194 =>
         array (
           'id' => 194,
           'name' => 'Secondary',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('The secondary font of your website. Used by secondary headlines and smaller elements.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('primary font family', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         195 =>
         array (
           'id' => 195,
           'name' => 'Text',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('The most readable font, used by all text elements.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('text font family', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         196 =>
         array (
           'id' => 196,
           'name' => 'Accent',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('The odd one. Usually found in accent headlines.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('accent font family', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         197 =>
         array (
           'id' => 197,
           'adv' => true,
           'name' => _x('Date Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Choose the date display type.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('date type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 129,
           'visible' =>
           array (
             'blogs_date' => 'true',
           ),
         ),
         198 =>
         array (
           'id' => 198,
           'adv' => true,
           'name' => _x('Typography', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set global typography options for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons typography', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         199 =>
         array (
           'id' => 199,
           'adv' => true,
           'name' => _x('Background', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global background colors for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons background color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         200 =>
         array (
           'id' => 200,
           'adv' => true,
           'name' => _x('Border Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global border type for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons border type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         201 =>
         array (
           'id' => 201,
           'adv' => true,
           'name' => _x('Border Width', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global border width for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons border width', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         202 =>
         array (
           'id' => 202,
           'adv' => true,
           'name' => _x('Border Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global border color for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons border color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         203 =>
         array (
           'id' => 203,
           'adv' => true,
           'name' => _x('Border Radius', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global border radius for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons border radius', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         204 =>
         array (
           'id' => 204,
           'adv' => true,
           'name' => _x('Padding', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global padding for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('button buttons padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         205 =>
         array (
           'id' => 205,
           'adv' => true,
           'name' => _x('Sticky on Top', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Top Banner stays fixed on top on page scroll.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner sticky fixed', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         206 =>
         array (
           'id' => 206,
           'adv' => true,
           'name' => _x('Dismissable', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Allow users to dismiss the banner using a close button.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner dismissable', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         207 =>
         array (
           'id' => 207,
           'adv' => true,
           'name' => _x('Token', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Generate a new token to show the top bar again after dismissing it.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner dismissable token', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         208 =>
         array (
           'id' => 208,
           'adv' => true,
           'name' => _x('Enable Animations', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable animations engine. Turning this feature off will increase your website performance in browser (client side).', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animations enable', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         209 =>
         array (
           'id' => 209,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         210 =>
         array (
           'id' => 210,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         211 =>
         array (
           'id' => 211,
           'adv' => true,
           'name' => _x('Reveal Background Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the background color for reveal animation.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation reveal background color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         212 =>
         array (
           'id' => 212,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         213 =>
         array (
           'id' => 213,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         214 =>
         array (
           'id' => 214,
           'adv' => true,
           'name' => _x('Animation Delay', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the time before animation starts loading.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         215 =>
         array (
           'id' => 215,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         216 =>
         array (
           'id' => 216,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         217 =>
         array (
           'id' => 217,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         218 =>
         array (
           'id' => 218,
           'adv' => true,
           'name' => _x('Animation Delay', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the time before animation starts loading.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         219 =>
         array (
           'id' => 219,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside the header.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         220 =>
         array (
           'id' => 220,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         221 =>
         array (
           'id' => 221,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         222 =>
         array (
           'id' => 222,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         223 =>
         array (
           'id' => 223,
           'adv' => true,
           'name' => _x('Animation Delay', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the time before animation starts loading.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         224 =>
         array (
           'id' => 224,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside page title.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         225 =>
         array (
           'id' => 225,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         226 =>
         array (
           'id' => 226,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         227 =>
         array (
           'id' => 227,
           'adv' => true,
           'name' => _x('Animation Delay', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the time before animation starts loading.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         228 =>
         array (
           'id' => 228,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside the footer.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         229 =>
         array (
           'id' => 229,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         230 =>
         array (
           'id' => 230,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         231 =>
         array (
           'id' => 231,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside the blog grid.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         232 =>
         array (
           'id' => 232,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         233 =>
         array (
           'id' => 233,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         234 =>
         array (
           'id' => 234,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside the portfolio grid.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         235 =>
         array (
           'id' => 235,
           'adv' => true,
           'name' => _x('Animation Type', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation type', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         236 =>
         array (
           'id' => 236,
           'adv' => true,
           'name' => _x('Animation Duration', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation speed.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation duration', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         237 =>
         array (
           'id' => 237,
           'adv' => true,
           'name' => _x('Delay Between Elements', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the animation delay between elements inside the shop grid.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animation delay elements', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         238 =>
         array (
           'id' => 238,
           'adv' => true,
           'name' => _x('Project ID', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Global Fonts', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set your Adobe TypeKit project ID.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('typekit adobe font', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         239 =>
         array (
           'id' => 239,
           'adv' => true,
           'name' => _x('Blurred Background (Glassmorphism)', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Blur background behind header. Active only if a transparent color is set as background.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header background blur', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         240 =>
         array (
           'id' => 240,
           'adv' => true,
           'name' => _x('Hamburger Icon Color', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the normal and hover colors for menu hamburger icon.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header hamburger icon color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         241 =>
         array (
           'id' => 241,
           'adv' => true,
           'name' => _x('Full Screen Menu Logo', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select the logo you want to show inside the full screen menu (Hamburger Header).', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header hamburger logo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         242 =>
         array (
           'id' => 242,
           'adv' => true,
           'name' => _x('Focus Hover Item', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Keeps the hover item in focus by decreasing opacity on the other items.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header item focus', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         243 =>
         array (
           'id' => 243,
           'adv' => true,
           'name' => _x('Full Screen Menu Background', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the background for full screen menu (Hamburger Header).', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header menu background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         244 =>
         array (
           'id' => 244,
           'adv' => true,
           'name' => _x('Highlight Current Page', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Uses menu hover color to highlight current page menu item.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header current page highlight', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         245 =>
         array (
           'id' => 245,
           'adv' => true,
           'name' => _x('Item Style', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the grid item style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog grid item style', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         246 =>
         array (
           'id' => 246,
           'adv' => true,
           'name' => _x('Post Navigation', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable next / previous navigation at the end of the post.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog post navigation', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         247 =>
         array (
           'id' => 247,
           'name' => _x('Discord', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Discord channel link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('discord', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         248 =>
         array (
           'id' => 248,
           'name' => _x('Telegram', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Telegram link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('telegram', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         249 =>
         array (
           'id' => 249,
           'name' => _x('Emoji Script', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable the Emoji script (wp-emoji-release.min.js).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('emoji', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         250 =>
         array (
           'id' => 250,
           'name' => _x('Elementor Icons (eicon)', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable the default Elementor icon pack.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('eicon icon elementor', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         251 =>
         array (
           'id' => 251,
           'name' => _x('Font Awesome (fa)', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable Font Awesome icon pack.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('font awesome fa', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         252 =>
         array (
           'id' => 252,
           'name' => _x('Default WP Block Styles', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable the default styles from Gutenberg block library.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('block styles', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         253 =>
         array (
           'id' => 253,
           'name' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable top banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('top banner', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         254 =>
         array (
           'id' => 254,
           'name' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable header globally.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         255 =>
         array (
           'id' => 255,
           'name' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable footer globally.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('footer', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         256 =>
         array (
           'id' => 256,
           'name' => _x('Copyright Bar', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable copyright bar globally.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('copright', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         257 =>
         array (
           'id' => 257,
           'name' => _x('Animations', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable all animations.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('animations', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         258 =>
         array (
           'id' => 258,
           'name' => _x('Smart Preload', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn on to enable smart preloading of inner pages.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('smart preload', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         259 =>
         array (
           'id' => 259,
           'name' => _x('WP Embed', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable WP Embed Script (wp-embed.min.js).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('wp embed', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         260 =>
         array (
           'id' => 260,
           'name' => _x('Widgets', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to disable sidebar and widgets styles.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('widgets sidebar', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         261 =>
         array (
           'id' => 261,
           'name' => _x('All Font Styles', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to load only used font styles. If left on, all font styles will be loaded instead.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('font styles', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         262 =>
         array (
           'id' => 262,
           'name' => _x('Preload Featured Images', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn on to preload page title featured image.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Performance', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('preload featured image', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         263 =>
         array (
           'id' => 263,
           'adv' => true,
           'name' => _x('Top Banner', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for Top Banner.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin top banner', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         264 =>
         array (
           'id' => 264,
           'adv' => true,
           'name' => _x('Menu', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for main menu.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin menu', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         265 =>
         array (
           'id' => 265,
           'adv' => true,
           'name' => _x('Mobile Menu', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for mobile menu.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin mobile menu', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         266 =>
         array (
           'id' => 266,
           'adv' => true,
           'name' => _x('Page Title', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for page title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin page title', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         267 =>
         array (
           'id' => 267,
           'adv' => true,
           'name' => _x('Sidebars', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for sidebars.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin sidebars', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         268 =>
         array (
           'id' => 268,
           'adv' => true,
           'name' => _x('Footer', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for footer.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin footer', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         269 =>
         array (
           'id' => 269,
           'adv' => true,
           'name' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for blog.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin blog', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         270 =>
         array (
           'id' => 270,
           'adv' => true,
           'name' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for portfolio.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin portfolio', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         271 =>
         array (
           'id' => 271,
           'adv' => true,
           'name' => _x('Hamburger Icon', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for menu hamburger icon.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin hamburger icon', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         272 =>
         array (
           'id' => 272,
           'adv' => true,
           'name' => _x('Back to Top', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for back to top button.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin back to top', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         273 =>
         array (
           'id' => 273,
           'adv' => true,
           'name' => _x('Links', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for links.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin links', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         274 =>
         array (
           'id' => 274,
           'adv' => true,
           'name' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Select theme skin for buttons.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Theme Skin', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('skin buttons', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         275 =>
         array (
           'id' => 275,
           'adv' => true,
           'name' => _x('Enable Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Use this feature to customize WP admin and theme options panel.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer enable', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         276 =>
         array (
           'id' => 276,
           'adv' => true,
           'name' => _x('Theme Name', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change theme name in WP dashboard.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer theme name', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         277 =>
         array (
           'id' => 277,
           'adv' => true,
           'name' => _x('Theme Icon', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change theme icon in WP dashboard.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer theme icon', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         278 =>
         array (
           'id' => 278,
           'adv' => true,
           'name' => _x('Theme Logo', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change logo in theme options (top left).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer theme logo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         279 =>
         array (
           'id' => 279,
           'adv' => true,
           'name' => _x('Panel Main Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change main color in theme options panel.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer theme color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         280 =>
         array (
           'id' => 280,
           'adv' => true,
           'name' => _x('Dashboard Content', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change content in dashboard tab. HTML is allowed.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer dashboard content', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         281 =>
         array (
           'id' => 281,
           'adv' => true,
           'name' => _x('Disable Documentation Icon', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the documentation icon on top right.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer documentation icon', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         282 =>
         array (
           'id' => 282,
           'adv' => true,
           'name' => _x('Disable Demo Import', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the demo import tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer demo import', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         283 =>
         array (
           'id' => 283,
           'adv' => true,
           'name' => _x('Disable Performance Manager', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the performance tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer performance', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         284 =>
         array (
           'id' => 284,
           'adv' => true,
           'name' => _x('Disable Plugins Manager', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the plugins tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer plugins', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         285 =>
         array (
           'id' => 285,
           'adv' => true,
           'name' => _x('Disable Updates', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the updates tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer updates', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         286 =>
         array (
           'id' => 286,
           'adv' => true,
           'name' => _x('Disable Settings Reset', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the Reset Settings button in System tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer updates', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         287 =>
         array (
           'id' => 287,
           'adv' => true,
           'name' => _x('Allow Advanced Settings', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Turn off to hide advanced settings (marked with a colored dot).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer advanced settings', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         288 =>
         array (
           'id' => 288,
           'adv' => true,
           'name' => _x('Login Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change the login screen background color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer login background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         289 =>
         array (
           'id' => 289,
           'adv' => true,
           'name' => _x('Login Form Background', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change the login form background color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer login form background', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         290 =>
         array (
           'id' => 290,
           'adv' => true,
           'name' => _x('Login Form Text Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change the login form text color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer login text color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         291 =>
         array (
           'id' => 291,
           'adv' => true,
           'name' => _x('Login Primary Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change the login screen primary color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer login primary color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         292 =>
         array (
           'id' => 292,
           'adv' => true,
           'name' => _x('Login Screen Logo', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change the login screen logo (replaces WordPress logo).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer login logo', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         293 =>
         array (
           'id' => 293,
           'adv' => true,
           'name' => _x('Login Screen Logo Height', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the custom login screen logo height.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer login logo height', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         294 =>
         array (
           'id' => 294,
           'adv' => true,
           'name' => _x('Login Screen Text Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Change the login screen text color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer text color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         295 =>
         array (
           'id' => 295,
           'adv' => true,
           'name' => _x('Smart Sticky', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Sticky header appears when scrolling up.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('smart sticky', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 55,
           'visible' =>
           array (
             'header_sticky' => 'true',
           ),
         ),
         296 =>
         array (
           'id' => 296,
           'adv' => true,
           'name' => _x('Header Bottom Area padding', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set top/bottom spacing for header bottom area bar.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header bottom padding', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 31,
           'visible' =>
           array (
             'header_layout' => 'center_creative',
           ),
         ),
         297 =>
         array (
           'id' => 297,
           'adv' => true,
           'name' => _x('Side Drawer', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a side drawer toggle in the header.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('side drawer', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         298 =>
         array (
           'id' => 298,
           'adv' => true,
           'name' => _x('Side Drawer Text', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the side drawer toggle text.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('side drawer text', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 297,
           'visible' =>
           array (
            'header_side_drawer' => 'true',
          ),
         ),
         299 =>
         array (
           'id' => 299,
           'adv' => true,
           'name' => _x('Side Drawer Toggle', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the side drawer open action.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('side drawer toggle', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 297,
           'visible' =>
           array (
            'header_side_drawer' => 'true',
          ),
         ),
         300 =>
         array (
           'id' => 300,
           'adv' => true,
           'name' => _x('Side Drawer Position', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the side drawer position.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('side drawer position', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 297,
           'visible' =>
           array (
            'header_side_drawer' => 'true',
          ),
         ),
         301 =>
         array (
           'id' => 301,
           'adv' => true,
           'name' => _x('OpenSea', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('OpenSea link.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Social', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('open sea, opensea', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         302 =>
         array (
           'id' => 302,
           'adv' => true,
           'name' => _x('Show Breadcrumbs', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Display the breadcrumbs just before the post title.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog page title breadcrumb', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 125,
           'visible' => array (
              'blogs_title' => 'simple page title',
            )
         ),
         303 =>
         array (
           'id' => 303,
           'adv' => true,
           'name' => _x('Author Box', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add the author info at the end of the post.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog post author box info', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         304 =>
         array (
           'id' => 304,
           'adv' => true,
           'name' => _x('Author Box Style', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the author box style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog post author box style', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 303,
           'visible' => array (
              'blogs_author_box' => 'true',
            )
         ),
         305 =>
         array (
           'id' => 305,
           'adv' => true,
           'name' => _x('Related Posts', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show related posts at the end of the post.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog post related', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 303,
           'visible' => true
         ),
         306 =>
         array (
           'id' => 306,
           'adv' => true,
           'name' => _x('Related Posts Filter', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Show related posts based on:', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog post related filter', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 303,
           'visible' => array (
              'blogs_related' => 'true',
            )
         ),
         307 =>
         array (
           'id' => 307,
           'adv' => true,
           'name' => _x('Related Posts Style', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the related posts display style.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog post related style', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => 303,
           'visible' => array (
              'blogs_related' => 'true',
            )
         ),
         308 =>
         array (
           'id' => 308,
           'name' => 'White',
           'category' => _x('Design System', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Generally used as background for white sections.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('white color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         309 =>
         array (
           'id' => 309,
           'adv' => true,
           'name' => _x('Disable Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Disable blog functionality.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer blog', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         310 =>
         array (
           'id' => 310,
           'adv' => true,
           'name' => _x('Disable Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Disable portfolio functionality.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer portfolio', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         311 =>
         array (
           'id' => 311,
           'adv' => true,
           'name' => _x('Disable WooComerce', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Disable WooCommerce functionality.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer woocommerce', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         312 =>
         array (
           'id' => 312,
           'adv' => true,
           'name' => _x('Disable Theme Builder', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Disable Theme Builder functionality.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer theme builder', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         313 =>
         array (
           'id' => 313,
           'adv' => true,
           'name' => _x('Disable Custom Code', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the Custom Code tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer custom code', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         314 =>
         array (
           'id' => 314,
           'adv' => true,
           'name' => _x('Disable Typography', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide the Typography tab.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer typography', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         315 =>
         array (
           'id' => 315,
           'adv' => true,
           'name' => _x('Disable Element Pack Pro Menu', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Hide Element Pack Pro menu from WordPress dashboard.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Admin Customizer', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('admin customizer element pack', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         316 =>
         array (
           'id' => 316,
           'adv' => true,
           'name' => _x('Custom Content in Header', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('The content will be added in the &lt;/head&gt; section (use this for analytics code).', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Custom', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom script markup head', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         317 =>
         array (
           'id' => 317,
           'adv' => true,
           'name' => _x('Custom Content in Footer', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('The content will be added before the closing &lt;/body&gt; tag.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Custom', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom script markup footer', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         318 =>
         array (
           'id' => 318,
           'adv' => true,
           'name' => _x('Hover Interaction', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the button hover effect.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Buttons', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('hover interaction', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         319 =>
         array (
           'id' => 319,
           'adv' => true,
           'name' => _x('Menu Hover Interaction', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the hover effect for menu items.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('menu hover interaction', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         320 =>
         array (
           'id' => 320,
           'adv' => true,
           'name' => _x('Grain Overlay Effect', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a noise texture over your website.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('noise texture grain', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 321,
           'adv' => true,
           'name' => _x('Custom Cursor', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Add a custom cursor to your website.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom cursor', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 322,
           'adv' => true,
           'name' => _x('Keep Default Cursor', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Keeps the default system cursor behind the custom one.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom cursor', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 323,
           'adv' => true,
           'name' => _x('Cursor Style', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the cursor style and effect.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom cursor style', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 324,
           'adv' => true,
           'name' => _x('Cursor Hover Effect', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the cursor animation on link elements.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom cursor hover effect', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 325,
           'adv' => true,
           'name' => _x('Cursor Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the cursor color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('custom cursor color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 326,
           'adv' => true,
           'name' => _x('Links Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the global links color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('General', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('link color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 327,
           'adv' => true,
           'name' => _x('Post Links Color', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the post links color.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('post blog link color', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 328,
           'adv' => true,
           'name' => _x('Mobile Navigation Breakpoint', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set the screen resolution where the mobile menu replaces the desktop menu.', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Header', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('header mobile breakpoint', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 329,
           'adv' => true,
           'name' => _x('Navigation', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Portfolio', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Enable next / previous navigation at the end of the page.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('portfolio post navigation', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
         array (
           'id' => 330,
           'adv' => true,
           'name' => _x('Wide Images Outer Offset', 'Admin - Theme Options', 'uicore-framework'),
           'category' => _x('Blog', 'Admin - Theme Options', 'uicore-framework'),
           'desc' => _x('Set overflow offset for wide align images: 0-10.', 'Admin - Theme Options', 'uicore-framework'),
           'tags' => _x('blog wide image offset', 'Admin - Theme Options Search', 'uicore-framework'),
           'dependecies' => NULL,
           'visible' => true,
         ),
      );
   }
}
