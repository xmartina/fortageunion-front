<?php
namespace UiCore;
defined('ABSPATH') || exit();

/**
 * Frontend Sidebar
 *
 * @author Andrei Voica <andrei@uicore.co
 * @since 1.0.0
 */
class Sidebar
{
    /**
     * Add Frontend sidebar
     *
     * @author Andrei Voica <andrei@uicore.co
     * @since 1.0.0
     */
    function __construct()
    {
        add_action('uicore_sidebar', [$this, 'sidebar_display']);
    }

    /**
     * Chec if Sidebar is active and display it
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 1.0.0
     */
    public function sidebar_display()
    {
        //Blog Archieve
        $blog_sidebar = Helper::get_option('blog_sidebar_id', 'none');
        $blog_sticky = Helper::get_option('blog_sidebars', 'false');

        if (\class_exists('\UiCore\Blog\Frontend') && (Blog\Frontend::is_blog() && !is_singular()) && $blog_sidebar !== 'none') {
            $this->get_sidebar($blog_sidebar, $blog_sticky);
        }

        //Blog Single
        $blog_single_sidebar = apply_filters('uicore_blogs_sidebar', Helper::get_option('blogs_sidebar_id'), get_post());
        $blog_single_sticky = Helper::get_option('blogs_sidebars');

        if (is_singular('post') && $blog_single_sidebar !== 'none') {
            $this->get_sidebar($blog_single_sidebar, $blog_single_sticky);
        }

        //Portfolio Archieve
        $portfolio_sidebar = Helper::get_option('portfolio_sidebar_id');
        $portfolio_sticky = Helper::get_option('portfolio_sidebars');

        if (is_post_type_archive('portfolio') && $portfolio_sidebar !== 'none') {
            $this->get_sidebar($portfolio_sidebar, $portfolio_sticky);
        }

        //Portfolio Single
        $portfolio_single_sidebar = Helper::get_option('portfolios_sidebar_id');
        $portfolio_single_sticky = Helper::get_option('portfolios_sidebars');

        if (is_singular('portfolio') && $portfolio_single_sidebar !== 'none') {
            $this->get_sidebar($portfolio_single_sidebar, $portfolio_single_sticky);
        }

        if (class_exists('WooCommerce')) {
            //WooCommerce Shop
            $woocommerce_sidebar = Helper::get_option('woocommerce_sidebar_id');
            $woocommerce_sticky = Helper::get_option('woocommerce_sidebars');

            if ((is_shop() || is_product_taxonomy()) && $woocommerce_sidebar !== 'none') {
                $this->get_sidebar($woocommerce_sidebar, $woocommerce_sticky);
            }

            //WooCommerce Product
            $woocommerce_single_sidebar = Helper::get_option('woocommerces_sidebar_id');
            $woocommerce_single_sticky = Helper::get_option('woocommerces_sidebars');

            if (is_product() && $woocommerce_single_sidebar !== 'none') {
                $this->get_sidebar($woocommerce_single_sidebar, $woocommerce_single_sticky);
            }
        }
    }

    /**
     * Render Sidebar HTML
     *
     * @param string $sidebar
     * @param string $sticky
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 1.0.0
     */
    public function get_sidebar($sidebar, $sticky)
    {
        echo '<aside id="secondary" class="uicore-sidebar uicore-animate">';
        echo '<div class="uicore-sidebar-content ';
        if ($sticky == 'true') {
            echo 'uicore-sticky ' ;
        }
        echo '">';
        dynamic_sidebar($sidebar);
        echo '</div>';
        echo '</aside>';
    }
}
