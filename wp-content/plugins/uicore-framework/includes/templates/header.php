<?php
namespace UiCore;
defined('ABSPATH') || exit();

use UiCore\Utils;

/**
 * Here we generate the header
 */
class Header
{
    private $is_header;

    private $is_topbar;

    private $header_layout;

    private $is_wide;

    private $menu;

    private $extra_class;

    /**
     * __construct
     *
     * @return void
     */
    function __construct()
    {
        //Hook this to init to get is_user_logged_in() -> for maintenance mode
        add_action('wp_loaded', function () {
            add_action('uicore_before_body_content', [$this, 'init']);
        });

        //clear nav menu item id (we use the same menu twice so will end up with duplicate id's)
        add_filter('nav_menu_item_id', function(){return null;} , 100, 1);
    }

    /**
     * header_display
     *
     * @return void
     */
    function init()
    {
        //continue only if is not in maintenance mode or 404 page
        $is_maintenance = Helper::get_option('gen_maintenance') === 'false';
        if (!is_404() && !$is_maintenance && !is_user_logged_in()) {
            return;
        }

        global $post;

        //check if post id is setted if not return 0
        $post_id = $post->ID ?? 0;

        //Top Bar
        $this->is_topbar = Helper::po('topbar', 'header_top', 'false', $post_id) === 'true';
        if ($this->is_topbar) {
            add_action('uicore_page', [$this, 'top_bar'],1);
        }


        //Elementor PRO Theme Builder First!!!
        if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {

            $this->is_header = Helper::po('header', 'header', 'true', $post_id) == 'true';
            $this->is_header = apply_filters('uicore_is_header', $this->is_header);
            //continue only if we have header
            if ($this->is_header) {
                $this->header_layout = Helper::get_option('header_layout');
                $this->is_wide = Helper::get_option('header_wide') === 'true';

                $this->set_the_menu();

                //Main Header
                add_action('uicore_page', [$this, 'header'],10);

                //Mobile Header
                if (Helper::get_option('mmenu_animation') == 'fade') {
                    add_action('uicore_body_end', [$this, 'mobile_menu_display']);
                } else {
                    echo '<div class="uicore-mobile-menu-overflow">';
                    $this->mobile_menu_display();
                    add_action('uicore_after_body_content', function () {
                        echo '</div>';
                    });
                }
            }
        }
    }

    public function set_the_menu()
    {
        global $wp_version;

        do_action('uicore_before_set_menu');

        //wp 6.1. submenu bug temp fix
        $args = [
            'theme_location' => 'primary',
            'container_class' => 'uicore-menu-container uicore-nav',
            'menu_class' => 'uicore-menu',
            'fallback_cb' => '',
            'depth' => 6,
            'echo' => false,
            'link_before'       => '<span class="ui-menu-item-wrapper">',
            'link_after'        => '</span>',
            'items_wrap' => '<ul class="%2$s">%3$s</ul>'
        ];
        if($wp_version === '6.1'){
            unset($args['depth']);
        }
        $menu = wp_nav_menu($args);
        
        do_action('uicore_after_set_menu');

        $this->menu = $menu;    
    }

    /**
     * Settings fields
     *
     * @return void
     */
    public function top_bar()
    {
        // prettier-ignore
        ?>
        <div class="uicore uicore-top-bar elementor-section <?php echo $this->is_wide
            ? null
            : 'elementor-section-boxed ';
            ?>"
            <?php if (Helper::get_option('header_top_dismissable') === 'true'){ ?>
            style="display:none;"
            <?php } ?>
            >
            <div class="uicore elementor-container">

                <div class="ui-tb-col-1 uicore-animate">
                <?php if (Helper::get_option('header_topone') === 'custom') {
                    echo do_shortcode(wp_kses_post(Helper::get_option('header_topone_content', '')));
                } elseif (Helper::get_option('header_topone') === 'menu') {
                    wp_nav_menu([
                        'theme_location' => 'uicore-menu-one',
                        'container_class' => 'uicore-menu-one',
                        'fallback_cb' => '',
                        'depth' => 1,
                    ]);
                } else {
                    echo '<div class="uicore uicore-socials">';
                    echo Utils::get_social_icons();
                    echo '</div>';
                } ?>
                </div>

                <?php
                //prettier-ignore
                //Col two

                if (Helper::get_option('header_toplayout') === 'two columns') { ?>
                    <div class="ui-tb-col-2 uicore-animate">
                    <?php if (Helper::get_option('header_toptwo') === 'custom') {
                                echo do_shortcode(wp_kses_post(Helper::get_option('header_toptwo_content', '')));
                        } elseif (Helper::get_option('header_toptwo') === 'menu') {
                                wp_nav_menu([
                                    'theme_location' => 'uicore-menu-two',
                                    'container_class' => 'uicore-menu-two',
                                    'fallback_cb' => '',
                                    'depth' => 1,
                                ]);
                        } else {
                                echo '<div class="uicore uicore-socials">';
                                echo Utils::get_social_icons();
                                echo '</div>';
                        } ?>

                    </div>
                <?php } ?>
            </div>
            <?php
            if (Helper::get_option('header_top_dismissable') === 'true'){
                echo '<a id="ui-banner-dismiss" class="uicore-animate uicore-i-close"></a>';
            }
            ?>
        </div>
        <?php if (Helper::get_option('header_top_dismissable') === 'true'){ ?>
        <script>
            if(!localStorage.getItem('uicore_tb') || localStorage.getItem('uicore_tb') != '<?php echo Helper::get_option('header_top_token'); ?>'){
                document.querySelector('.uicore-top-bar').style.display = 'block';
            }
        </script>
        <?php
        }
    }

    /**
     * Settings fields
     *
     * @return void
     */
    public function header()
    {
        //prettier-ignore
        global $post;
        //check if post id is setted if not return 0
        $post_id = $post->ID ?? 0;
        $post_type = $post->post_type ?? 'page';

        if (Helper::get_option('blogs_progress') === 'true' && is_single() && 'post' == $post_type) {
            echo '<div class="uicore-progress-bar"></div>';
        } ?>

        <div id="wrapper-navbar" itemscope itemtype="http://schema.org/WebSite" class="uicore uicore-navbar elementor-section<?php //Classic menu
            echo ' ';
            //Classic menu
            if ($this->header_layout === 'classic' || $this->header_layout === 'classic_center' || $this->header_layout === 'center_creative' || strpos($this->header_layout, 'ham') !== false) {
            if (Helper::get_option('header_wide') == 'false') {
                echo 'elementor-section-boxed ';
            }
            if ($this->header_layout === 'classic' || $this->header_layout == 'classic_center' || $this->header_layout === 'center_creative' ) {
                echo 'uicore-h-classic ';
            }


            if (Helper::get_option('header_sticky') === 'true') {
                echo 'uicore-sticky ';

                if (Helper::get_option( 'header_sticky_smart') === 'true') {
                    echo 'ui-smart-sticky ';
                }
                if (Helper::po('shrink', 'header_shrink', 'false', $post_id) === 'true') {
                    echo 'uicore-shrink ';
                }
            }
            if (!is_404() && !Helper::is_full()) {
                $is_transparent = Helper::po('transparent', 'header_transparent', 'true', $post_id) === 'true';
                if (
                    ($is_transparent && !is_singular('post')) ||
                    ($is_transparent && Helper::get_option('blogs_title') != 'simple page title' && is_singular('post'))
                ) {
                    echo 'uicore-transparent ';
                }
            }
            //Other Menu Type
        } else {
            echo 'uicore-left-menu ';
            if ($this->header_layout == 'mini') {
                echo ' uicore-mini-menu';
            } else {
                echo ' uicore-fixed';
            }
        } ?>"><div class="uicore-header-wrapper">
            <nav class="uicore elementor-container">
            <?php $this->get_markup(); ?>
            </nav>

            </div>
            <?php do_action('uicore_after_header'); ?>
        </div><!-- #wrapper-navbar end -->
        <?php

        //sticky top banner  and menu fix
        if (Helper::get_option('header_top_sticky') === 'true' && $this->is_topbar){ ?>
            <script>
                if (document.querySelector('.uicore-navbar.uicore-sticky')) {
                    document.querySelector('.uicore-navbar.uicore-sticky').style.top = document.querySelector('.uicore-top-bar').offsetHeight+'px';
                }
            </script>
            <?php
        }
    }

    /**
     * Settings fields
     *
     * @return void
     */
    public function get_logo()
    {

        global $post;

        //check if post id is setted if not return 0
        $post_id = $post->ID ?? 0;


        $main_logo = Helper::po('logo', 'logo', '', $post_id); //Helper::get_option('logo');
        $second_logo = Helper::po('logoS', 'logoS', '', $post_id);
        if (strlen($second_logo) < 3) {
            $second_logo = $main_logo;
        }

        $mobile_logo = Helper::po('logoMobile', 'logoMobile', '', $post_id);
        if (strlen($mobile_logo) < 3) {
            $mobile_logo = $main_logo;
        }
        $second_mobile_logo = Helper::po('logoSMobile', 'logoSMobile', '', $post_id);
        if (strlen($second_mobile_logo) < 3) {
            if (strlen(Helper::po('logoMobile', 'logoMobile', '', $post_id)) > 3) {
                $second_mobile_logo = $mobile_logo;
            } else {
                $second_mobile_logo = $second_logo;
            }
        }
        //prettier-ignore
        if (strlen($main_logo) > 2) {
            $logo_link = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url('/');
            $logo_link = apply_filters( 'uicore_logo_link', $logo_link );
            ?>

			<a href="<?php echo esc_url($logo_link); ?>" rel="home">
                <img class="uicore uicore-logo uicore-main" src="<?php echo esc_url($main_logo); ?>" alt="<?php echo esc_attr(get_option( 'blogname' )); ?>"/>
				<img class="uicore uicore-logo uicore-second" src="<?php echo esc_url($second_logo); ?>" alt="<?php echo esc_attr(get_option( 'blogname' )); ?>" />
				<img class="uicore uicore-logo uicore-mobile-main" src="<?php echo esc_url( $mobile_logo); ?>" alt="<?php echo esc_attr(get_option( 'blogname' )); ?>" />
				<img class="uicore uicore-logo uicore-mobile-second" src="<?php echo esc_url( $second_mobile_logo); ?>" alt="<?php echo esc_attr(get_option( 'blogname' )); ?>" />
			</a>

		<?php }
    }

    /**
     * Get Nav Extras
     *
     * @param  string $type - For mobile or for desktop
     * @return void
     */
    public function get_nav_extra(string $type = 'desktop')
    {
        $search = Helper::get_option('header_search');
        $social = Helper::get_option('header_icons');
        $cta = Helper::get_option('header_cta');
        $custom_desktop = Helper::get_option('header_custom_desktop');
        $custom_mobile = Helper::get_option('header_custom_mobile');
        $woo = 'false';
        $drawer = Helper::get_option('header_side_drawer');
        if (class_exists('WooCommerce')) {
            $woo = Helper::get_option('woo');
        }

        if (
            $search === 'true' ||
            $social === 'true' ||
            $cta === 'true' ||
            $woo === 'true' ||
            $custom_mobile === 'true' ||
            $custom_desktop === 'true' ||
            $drawer === 'true'
        ) {
            echo '<div class="uicore uicore-extra">';
            //Custom Area
            $this->get_custom($type);

            //Search
           $this->get_search(false);


            //WooCommerce Cart
            if ($woo == 'true' && $type === 'desktop') { ?>
					<?php $this->get_cart(); ?>
			<?php }

            //Social Icons
            $this->get_socials();

            //CTA
            $this->get_cta();

            //Sliding Drawer
            $this->get_drawer();

            echo '</div>';
        }
    }

    function get_cart($css_class ='')
    {

        if (class_exists('WooCommerce') && Helper::get_option('woo') === 'true') { ?>
        <div class="uicore uicore-cart-icon uicore-link <?php echo $css_class; ?>" title="<?php echo wp_kses_data(WC()->cart->get_cart_subtotal()); ?>">
            <span id="uicore-site-header-cart">
                    <?php $this->cart_link(); ?>
            </span>
        </div>
        <?php }
    }

    /**
     * Cart Link.
     *
     * Displayed a link to the cart including the number of items present and the cart total.
     *
     * @return void
     */
    function cart_link()
    {
        ?>
		<span class="uicore-icon-holder"></span>
		<span class="uicore-item-count" id="uicore-count-update">
			<?php echo \WC()->cart->get_cart_contents_count(); ?>
		</span>
		<?php
    }

    /**
     *  Markup for mobile and Ham Menu
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.0
     */
    function mobile_menu_display()
    {
        global $post;

        //check if post id is setted if not return 0
        $post_id = $post->ID ?? 0;

        $main_logo = Helper::po('logo', 'logo', '', $post_id);
        $second_logo = Helper::po('logoS', 'logoS', '', $post_id);
        if (strlen($second_logo) < 3) {
            $second_logo = $main_logo;
        }

        $mobile_logo = Helper::po('logoMobile', 'logoMobile', '', $post_id);
        if (strlen($mobile_logo) < 3) {
            $mobile_logo = $main_logo;
        }
        $second_mobile_logo = Helper::po('logoSMobile', 'logoSMobile', '', $post_id);
        if (strlen($second_mobile_logo) < 3) {
            $second_mobile_logo = $second_logo;
        }
        if (Helper::get_option('mmenu_logo') == 'false') {
            $logo = $mobile_logo;
        } else {
            $logo = $second_mobile_logo;
        }
        $mobile_menu = apply_filters('uicore_mobile_menu','default');
        if('default' != $mobile_menu){
            global $wp_version;
             //wp 6.1. submenu bug temp fix
                $args = [
                    'menu' => $mobile_menu,
                    'container_class' => 'uicore-menu-container uicore-nav',
                    'menu_class' => 'uicore-menu',
                    'fallback_cb' => '',
                    'depth' => 6,
                    'echo' => false,
                    'link_before'       => '<span class="ui-menu-item-wrapper">',
                    'link_after'        => '</span>',
                    'items_wrap' => '<ul class="%2$s">%3$s</ul>'
                ];
                if($wp_version === '6.1'){
                    unset($args['depth']);
                }

            $mobile_menu = wp_nav_menu($args);
        }else{
            $mobile_menu = $this->menu;
        }

        //uicore logo link filter
        $logo_link = apply_filters('uicore_logo_link', home_url('/'));
        ?>
        <div class="uicore-navigation-wrapper uicore-navbar elementor-section elementor-section-boxed uicore-mobile-menu-wrapper
        <?php
        if(strpos($this->header_layout, 'ham') !== false){
            echo 'uicore-ham-' . trim( str_replace('ham','',$this->header_layout) ) .' ';
        }
        ?>
        ">
			<nav class="uicore elementor-container">
				<div class="uicore-branding uicore-mobile">
                <?php
                ob_start();
                ?>
					<a href="<?php echo esc_url($logo_link); ?>" rel="home">
						<img class="uicore uicore-logo"  src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_option( 'blogname' )); ?>"/>
					</a>
                <?php
                $output = ob_get_clean();
                $output = apply_filters( 'uicore-mobile-menu-logo', $output);
                echo $output;
                ?>
				</div>


                <div class="uicore-branding uicore-desktop">
                <?php if(Helper::get_option('menu_logo') != 'none'){
                        if(Helper::get_option('menu_logo') === 'primary'){
                            $logo = $main_logo;
                        }else{
                            $logo = $second_logo;
                        }
                    ?>
                    <?php
                    ob_start();
                    ?>
					<a href="<?php echo esc_url($logo_link); ?>" rel="home">
						<img class="uicore uicore-logo"  src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_option( 'blogname' )); ?>"/>
					</a>
                    <?php
                    $output = ob_get_clean();
                    $output = apply_filters( 'uicore-desktop-menu-logo', $output);
                    echo $output;
                    ?>
                <?php } ?>
				</div>


				<button type="button" class="uicore-toggle uicore-ham">
					<span class="bars">
						<span class="bar"></span>
						<span class="bar"></span>
						<span class="bar"></span>
					</span>
				</button>
			</nav>
			<div class="uicore-navigation-content">
                <?php
                $custom_content = apply_filters('uicore-mobile-menu-content', false);
                if($custom_content){
                    echo $custom_content;
                }else{
                    echo $mobile_menu;
                    $this->get_nav_extra('mobile');
                }
                    
                ?>
            </div>
		</div>
		<?php
    }



    function get_branding()
    {
        ?>
		 <div class="uicore-branding">
                <?php
                ob_start();
                $this->get_logo();
                $output = ob_get_clean();
                $output = apply_filters( 'uicore-logo', $output);
                echo $output;
                ?>
        </div>
		<?php
    }

    function get_nav()
    {
        ?>
        <div class='uicore-nav-menu'>
            <?php

            //no need for menu in ham mode
            if (strpos($this->header_layout, 'ham') === false) {
                echo $this->menu;
            }

            if ($this->header_layout === 'classic') {
                $this->get_nav_extra();
            }
            ?>
        </div>
		<?php
    }

    function get_extra()
    {

        if ($this->header_layout != 'classic' && strpos($this->header_layout, 'ham') === false) {
            $this->get_nav_extra();
        }
    //    $this->get_cart();

    }

    function get_mobile()
    {
        $mobile_menu = $this->menu;
        $custom_mobile = Helper::get_option('header_custom_mobile');

        $this->get_cart('uicore_hide_desktop');

        if ($mobile_menu) { ?>
            <button type="button" class="uicore-toggle uicore-ham">
                <span class="bars">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </span>
            </button>
            <?php
            } else {
                if ($custom_mobile == 'true') {
                    echo '<div class="uicore-custom-area-mobile">';
                    dynamic_sidebar('uicore-hca');
                    echo '</div>';
                }
            }

    }

    function get_cta()
    {
        $cta = Helper::get_option('header_cta');
        if ($cta == 'true') { ?>
            <div class="uicore-cta-wrapper">
				<a href="<?php echo esc_url( Helper::get_option('header_ctalink','#')); ?>"
					target="<?php echo esc_attr( Helper::get_option('header_ctatarget') ); ?>"
					class="uicore-btn <?php echo Helper::get_option('header_cta_inverted') === 'true' ? 'uicore-inverted' : ''; echo esc_attr( apply_filters('uicore_cta_class', '')) ?>">
                    <span class="elementor-button-text">
						<?php
                        echo do_shortcode(Helper::get_option('header_ctatext'));
                        ?>
                    </span>
				</a>
            </div>
        <?php }
    }

    function get_socials()
    {
        $social = Helper::get_option('header_icons');
        if ($social == 'true') { ?>
            <div class="uicore uicore-socials">
                <?php echo Utils::get_social_icons(); ?>
            </div>
        <?php }
    }

    function get_search($force = true)
    {
        $search = Helper::get_option('header_search');
        if ($search == 'true' && (Helper::get_option('header_layout') === 'classic' || $force)) { ?>
            <div class="uicore uicore-search-btn uicore-i-search uicore-link uicore_hide_mobile"></div>
        <?php }
    }

    function get_custom($type='desktop')
    {
        $custom_mobile = Helper::get_option('header_custom_mobile');
        $custom_desktop = Helper::get_option('header_custom_desktop');
        if (
            ($custom_mobile == 'true' && $type == 'mobile') ||
            ($custom_desktop == 'true' && $type == 'desktop') ||
            ($custom_desktop == 'true' && $type == 'mobile' && strpos($this->header_layout, 'ham') !== false)
        ) {
            $class = ($type == 'mobile' && $custom_desktop === 'true' && $custom_mobile === 'false') ? 'uicore-only-desktop' : '';
            $class = ($type == 'mobile' && $custom_desktop === 'false' && $custom_mobile === 'true') ? 'uicore-only-mobile' : $class;
            ?>
            <div class="uicore-custom-area <?php echo $class; ?>">
                <?php dynamic_sidebar('uicore-hca'); ?>
            </div>
        <?php }
    }

    function get_drawer()
    {
        $drawer = Helper::get_option('header_side_drawer');
        if ($drawer == 'true' && in_array(Helper::get_option('header_layout'), ['classic','center_creative','classic_center']) ) {
            add_action('uicore_after_body_content', [$this,'get_drawer_content']);
            ?>
            <button type="button" class="uicore-link uicore_hide_mobile uicore-drawer-toggle uicore-ham">
                <span class="bars">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </span>
                <?php if(Helper::get_option('header_sd_text')){
                    echo'<span class="ui-text">'.Helper::get_option('header_sd_text').'</span>';
                }
                ?>
            </button>
        <?php }
    }

    function get_drawer_content()
    {
        echo '<!-- DRAWER CONTENT -->';
        echo '<div class="ui-drawer">';
            echo '<div class="ui-sd-backdrop"></div>';
            echo '<div class="ui-drawer-wrapp">';
                echo '<div class="ui-drawer-content">';
                    dynamic_sidebar('uicore-drawer');
                echo '</div>';
                if(Helper::get_option('header_sd_toggle') === 'click'){
                    echo '<button class="uicore-drawer-toggle ui-close">×</button>';
                }
            echo '</div>';
        echo '</div>';
    }

    function get_empty()
    {
    }



    function get_markup()
    {
        if($this->header_layout === 'center_creative'){
            $modules = [
                'empty',
                'branding',
                'nav',
                'mobile',
                'socials',
                'cta',
                'cart',
                'search',
                'search',
                'drawer'
            ];

            $markup = [
                'row1' => [
                    'left'      => ['socials'],
                    'center'    => ['branding'],
                    'right'     => ['custom','cta']
                ],
                'row2' => [
                    'left'      => ['drawer'],
                    'center'    => ['nav'],
                    'right'     => ['cart','search'],
                ]
            ];
            $markup = \apply_filters('uicore_header_items_placement', $markup);

            foreach ($markup as $row => $content) {
               echo '<div class="ui-header-'.$row.'">';
                foreach ($content as $position => $types) {
                    echo '<div class="ui-header-'.$position.'">';
                    foreach ($types as $type) {
                        echo $this->{'get_'.$type}();
                    }
                    echo '</div>'; //end position
                }
               echo '</div>'; //end row

            }
            echo $this->get_mobile();

        }else{
            $this->get_branding();

            $this->get_nav();

            $this->get_extra();

            $this->get_mobile();
        }
    }

}
