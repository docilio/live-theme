<?php

/**
 * Define variables
 */

define('porto_lib',                   get_template_directory() . '/inc');                     // library directory
define('porto_admin',                 porto_lib . '/admin');                    // admin directory
define('porto_plugins',               porto_lib . '/plugins');                  // plugins directory
define('porto_content_types',         porto_lib . '/content_types');            // content_types directory
define('porto_menu',                  porto_lib . '/menu');                     // menu directory
define('porto_functions',             porto_lib . '/functions');                // functions directory
define('porto_options_dir',           porto_admin . '/porto');                // options directory

define('porto_dir',                   get_template_directory());                  // template directory
define('porto_uri',                   get_template_directory_uri());              // template directory uri
define('porto_css',                   porto_uri . '/css');                      // css uri

define('porto_js',                    porto_uri . '/js');                       // javascript uri
define('porto_plugins_uri',           porto_uri . '/inc/plugins');              // plugins uri
define('porto_options_uri',           porto_uri . '/inc/admin/porto');        // plugins uri

$theme = wp_get_theme();
define('porto_version',               $theme->get('Version'));                    // get current version

/**
 * Wordpress theme check
 */
// set content width
if ( ! isset( $content_width ) ) $content_width = 900;

/**
 * Porto functions
 */
require_once(porto_functions . '/functions.php');

/**
 * Porto theme options
 */
require_once(porto_admin . '/theme_options.php');

/**
 * Menu
 */
require_once(porto_menu . '/menu.php');

/**
 * Content Types
 */
require_once(porto_content_types . '/content_types.php');

/**
 * Install Plugins
 */
require_once(porto_plugins . '/plugins.php');

/**
 * Theme support & Theme setup
 */
// theme setup
if ( ! function_exists( 'porto_setup' ) ) :
    function porto_setup() {

        add_theme_support( "title-tag" );
        //add_theme_support( "custom-header", array() );
        //add_theme_support( 'custom-background', array() );
        add_editor_style( array( 'style.css', 'style_rtl.css' ) );

        if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
            if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
                add_filter( 'woocommerce_enqueue_styles', '__return_false' );
            } else {
                define( 'WOOCOMMERCE_USE_CSS', false );
            }
        }

        // translation
        load_theme_textdomain('porto', porto_dir.'/languages');
        load_child_theme_textdomain('porto', get_stylesheet_directory().'/languages');

        // default rss feed links
        add_theme_support('automatic-feed-links');

        // add support for post thumbnails
        add_theme_support( 'post-thumbnails' );

        // add image sizes
        add_image_size( 'blog-large', 1140, 445, true );
        add_image_size( 'blog-medium', 463, 348, true );
        add_image_size( 'related-post', 450, 231, true );

        add_image_size( 'portfolio-large', 560, 560, true );
        add_image_size( 'portfolio-medium', 367, 367, true );
        add_image_size( 'related-portfolio', 450, 450, true );

        add_image_size( 'member', 367, 367, true );

        add_image_size( 'widget-thumb-medium', 85, 85, true );
        add_image_size( 'widget-thumb', 50, 50, true );

        // woocommerce support
        add_theme_support('woocommerce');

        // allow shortcodes in widget text
        add_filter('widget_text', 'do_shortcode');

        // register menus
        register_nav_menus( array(
            'main_menu' => 'Main Menu',
            'top_nav' => 'Top Navigation',
            'view_switcher' => 'View Switcher',
            'currency_switcher' => 'Currency Switcher'
        ));

        // add post formats
        add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio', 'chat'));
    }
endif;
add_action( 'after_setup_theme', 'porto_setup' );

/**
 * Enqueue css, js files
 */
add_action('wp_enqueue_scripts',    'porto_css', 100);
add_action('wp_enqueue_scripts',    'porto_scripts', 100);
add_action('admin_enqueue_scripts', 'porto_admin_css', 100);
add_action('admin_enqueue_scripts', 'porto_admin_scripts', 100);
add_action( 'wp_footer',            'porto_footer_hook', 1 );

function porto_css() {

    global $porto_settings;

    // plugin styles
    wp_deregister_style( 'porto-plugins' );
    if (is_rtl()) {
        $css_file = porto_dir.'/css/plugins_rtl_'.get_current_blog_id().'.css';
        if (file_exists($css_file)) {
            wp_register_style( 'porto-plugins', porto_uri.'/css/plugins_rtl_'.get_current_blog_id().'.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-plugins', porto_uri.'/css/plugins_rtl.css?ver=' . porto_version );
        }
    } else {
        $css_file = porto_dir.'/css/plugins_'.get_current_blog_id().'.css';
        if (file_exists($css_file)) {
            wp_register_style( 'porto-plugins', porto_uri.'/css/plugins_'.get_current_blog_id().'.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-plugins', porto_uri.'/css/plugins.css?ver=' . porto_version );
        }
    }
    wp_enqueue_style( 'porto-plugins' );

    // porto styles
    wp_deregister_style( 'porto-theme' );
    if (is_rtl()) {
        $css_file = porto_dir.'/css/theme_rtl_'.get_current_blog_id().'.css';
        if (file_exists($css_file)) {
            wp_register_style( 'porto-theme', porto_uri.'/css/theme_rtl_'.get_current_blog_id().'.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-theme', porto_uri.'/css/theme_rtl.css?ver=' . porto_version );
        }
    } else {
        $css_file = porto_dir.'/css/theme_'.get_current_blog_id().'.css';
        if (file_exists($css_file)) {
            wp_register_style( 'porto-theme', porto_uri.'/css/theme_'.get_current_blog_id().'.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-theme', porto_uri.'/css/theme.css?ver=' . porto_version );
        }
    }
    wp_enqueue_style( 'porto-theme' );

    ?>

    <!--[if IE]>
    <link rel="stylesheet" href="<?php echo porto_css; ?>/ie8.css?ver=<?php echo porto_version ?>" />
    <![endif]-->
    <?php

    // load master slider styles
    if ( !class_exists('Master_Slider') ) {
        wp_deregister_style( 'masterslider-main' );
        wp_register_style( 'masterslider-main', porto_css . '/masterslider.main.min.css?ver=' . porto_version );
    }
    wp_enqueue_style ( 'masterslider-main');

    // deregister woocommerce variation swatch css
    if (class_exists('wcva_shop_page_swatches')) {
        wp_deregister_style('wcva-shop-frontend');
        wp_dequeue_style('wcva-shop-frontend');
    }
    if (class_exists('wcva_register_style_scripts')) {
        wp_deregister_style('wcva-frontend');
        wp_dequeue_style('wcva-frontend');
    }

    // skin styles
    wp_deregister_style( 'porto-skin' );
    if (is_rtl()) {
        $css_file = porto_dir.'/css/skin_rtl_'.get_current_blog_id().'.css';
        if (file_exists($css_file)) {
            wp_register_style( 'porto-skin', porto_uri.'/css/skin_rtl_'.get_current_blog_id().'.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-skin', porto_uri.'/css/skin_rtl.css?ver=' . porto_version );
        }
    } else {
        $css_file = porto_dir.'/css/skin_'.get_current_blog_id().'.css';
        if (file_exists($css_file)) {
            wp_register_style( 'porto-skin', porto_uri.'/css/skin_'.get_current_blog_id().'.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-skin', porto_uri.'/css/skin.css?ver=' . porto_version );
        }
    }
    wp_enqueue_style( 'porto-skin' );

    // custom styles
    wp_deregister_style( 'porto-style' );
    wp_register_style( 'porto-style', porto_uri . '/style.css' );
    wp_enqueue_style( 'porto-style' );

    if (is_rtl()) {
        wp_deregister_style( 'porto-style-rtl' );
        wp_register_style( 'porto-style-rtl', porto_uri . '/style_rtl.css' );
        wp_enqueue_style( 'porto-style-rtl' );
    }

    // Load Google Fonts
    $gfont = array();
    if (isset($porto_settings['body-font']['google']) && $porto_settings['body-font']['google']) {
        $font = urlencode($porto_settings['body-font']['font-family']);
        if (!in_array($font, $gfont))
            $gfont[] = $font;
    }
    if (isset($porto_settings['alt-font']['google']) && $porto_settings['alt-font']['google']) {
        $font = urlencode($porto_settings['alt-font']['font-family']);
        if (!in_array($font, $gfont))
            $gfont[] = $font;
    }

    $font_family = '';
    foreach ($gfont as $font)
        $font_family .= $font . ':300,300italic,400,400italic,600,600italic,700,700italic,800,800italic%7C';

    if ($font_family) {
        wp_register_style( 'porto-google-fonts', "//fonts.googleapis.com/css?family=" . $font_family . "&amp;subset=latin,greek-ext,cyrillic,latin-ext,greek,cyrillic-ext,vietnamese" );
        wp_enqueue_style( 'porto-google-fonts' );
    }
}

function porto_scripts() {
    global $porto_settings;

    if (!is_admin() && !in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) )) {
        wp_reset_postdata();

        // deregister scripts
        wp_deregister_script( 'jquery-cookie' );

        // comment reply
        if ( is_singular() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }

        // porto scripts
        wp_deregister_script( 'porto-plugins' );
        if (is_rtl())
            wp_register_script( 'porto-plugins', porto_js .'/plugins_rtl'.(WP_DEBUG?'':'.min').'.js', array('jquery', 'jquery-migrate'), porto_version );
        else
            wp_register_script( 'porto-plugins', porto_js .'/plugins'.(WP_DEBUG?'':'.min').'.js', array('jquery', 'jquery-migrate'), porto_version );
        wp_enqueue_script( 'porto-plugins' );

        // blueimap gallery
        wp_deregister_script( 'jquery-blueimp-gallery' );
        wp_register_script( 'jquery-blueimp-gallery', porto_js.'/blueimp/jquery.blueimp-gallery.min.js', array(), porto_version );
        wp_enqueue_script( 'jquery-blueimp-gallery' );

        // load master slider plugin
        if ( !class_exists('Master_Slider') ) {
            wp_deregister_script( 'masterslider-core' );
            wp_register_script( 'masterslider-core', porto_js . '/masterslider.min.js', porto_version );
        }
        wp_enqueue_script( 'masterslider-core', false, array(), false, false );

        // load woocommerce variation select js

        if (class_exists('wcva_register_style_scripts')) {
            $goahead = 1;

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $agent = $_SERVER['HTTP_USER_AGENT'];
            }

            if (preg_match('/(?i)msie [5-8]/', $agent)) {
                $goahead = 0;
            }

            if ($goahead == 1) {
                wp_deregister_script('wc-add-to-cart-variation');
                wp_dequeue_script ('wc-add-to-cart-variation');
                wp_register_script( 'wc-add-to-cart-variation', porto_js . '/manage-variation-selection.js' ,array( 'jquery'), porto_version );
            } else {
                wp_enqueue_script('wc-add-to-cart-variation');
            }
        }

        if (class_exists('wcva_shop_page_swatches')) {
            wp_deregister_script('wcva-shop-frontend');
            wp_dequeue_script('wcva-shop-frontend');
        }

        // load porto theme js file

        wp_deregister_script( 'porto-theme' );
        wp_register_script( 'porto-theme', porto_js .'/theme'.(WP_DEBUG?'':'.min').'.js', array('jquery'), porto_version, true );
        wp_enqueue_script( 'porto-theme' );

        wp_localize_script( 'porto-theme', 'js_porto_vars', array(
            'rtl' => esc_js(is_rtl() ? true : false),
            'ajax_url' => esc_js(admin_url( 'admin-ajax.php' )),
            'post_zoom' => esc_js($porto_settings['post-zoom']),
            'portfolio_zoom' => esc_js($porto_settings['portfolio-zoom']),
            'member_zoom' => esc_js($porto_settings['member-zoom']),
            'page_zoom' => esc_js($porto_settings['page-zoom']),
            'container_width' => esc_js($porto_settings['container-width']),
            'show_sticky_header' => esc_js($porto_settings['enable-sticky-header']),
            'show_sticky_header_tablet' => esc_js($porto_settings['enable-sticky-header-tablet']),
            'show_sticky_header_mobile' => esc_js($porto_settings['enable-sticky-header-mobile']),
            'request_error' => esc_js(__('The requested content cannot be loaded.<br/>Please try again later.', 'porto')),
            'ajax_loader_url' => esc_js(str_replace(array('http:', 'https'), array('', ''), porto_uri . '/images/ajax-loader@2x.gif'))
        ) );
    }
}

function porto_admin_css() {
    wp_enqueue_style('porto_admin_css', porto_css . '/admin.css', false, porto_version, 'all');
}

function porto_admin_scripts() {
    if (function_exists('add_thickbox'))
        add_thickbox();

    wp_enqueue_media();

    wp_register_script('porto-admin', porto_js.'/admin.js', array('common', 'jquery', 'media-upload', 'thickbox'), porto_version, true);
    wp_enqueue_script('porto-admin');
}

// Disable the WordPress Admin Bar for all but admins
if (! current_user_can('edit_posts')):
    show_admin_bar(false);
endif;

function porto_footer_hook() {
    add_filter('style_loader_tag', 'porto_style_loader_tag');
}

function porto_style_loader_tag($tag) {
    return str_replace("rel='stylesheet'", "rel='stylesheet' property='stylesheet'", $tag);
}