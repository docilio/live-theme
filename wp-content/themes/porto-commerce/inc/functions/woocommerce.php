<?php

// get woocommerce version number
function porto_get_woo_version_number() {
    // If get_plugins() isn't available, require it
    if ( ! function_exists( 'get_plugins' ) )
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    // Create the plugins folder and file variables
    $plugin_folder = get_plugins( '/' . 'woocommerce' );
    $plugin_file = 'woocommerce.php';

    // If the plugin version number is set, return it
    if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
        return $plugin_folder[$plugin_file]['Version'];
    } else {
        // Otherwise return null
        return NULL;
    }
}

// remove actions
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

// add actions
add_action('woocommerce_before_shop_loop', 'porto_grid_list_toggle', 40);
add_action('woocommerce_before_shop_loop', 'woocommerce_pagination', 50);
add_action('woocommerce_before_shop_loop_item_title', 'porto_loop_product_thumbnail', 10);
add_action('woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt', 9);
add_action('woocommerce_archive_description', 'porto_woocommerce_category_image', 2);

// add filters
add_filter('woocommerce_show_page_title', 'porto_woocommerce_show_page_title');
add_filter('woocommerce_layered_nav_link', 'porto_layered_nav_link');
add_filter('loop_shop_per_page', 'porto_loop_shop_per_page');

add_filter('woocommerce_available_variation', 'porto_woocommerce_available_variation', 10, 3);

add_filter('woocommerce_related_products_args', 'porto_remove_related_products', 10);
add_filter('add_to_cart_fragments', 'porto_woocommerce_header_add_to_cart_fragment');

add_filter('woocommerce_cart_item_class', 'porto_woocommerce_cart_item_class', 10, 3);

// change action position
add_action('woocommerce_share', 'porto_woocommerce_share');

function porto_woocommerce_share() {
    global $porto_settings;

    if (!$porto_settings['product-share']) return;

    get_template_part('share');
}

// hide woocommer page title
function porto_woocommerce_show_page_title($args) {
    return false;
}

// show grid/list toggle buttons
function porto_grid_list_toggle() {
?>
    <div class="gridlist-toggle">
        <a href="#" id="grid" title="<?php echo __('Grid View', 'porto') ?>"></a><a href="#" id="list" title="<?php echo __('List View', 'porto') ?>"></a>
    </div>
<?php
}

// get product count per page
function porto_loop_shop_per_page() {
    global $porto_settings;

    parse_str($_SERVER['QUERY_STRING'], $params);

    // replace it with theme option
    if ($porto_settings['category-item']) {
        $per_page = explode(',', $porto_settings['category-item']);
    } else {
        $per_page = explode(',', '12,24,36');
    }

    $item_count = !empty($params['count']) ? $params['count'] : $per_page[0];

    return $item_count;
}

// add thumbnail image parameter
function porto_woocommerce_available_variation($variations, $product, $variation) {

    if ( has_post_thumbnail( $variation->get_variation_id() ) ) {
        $attachment_id = get_post_thumbnail_id( $variation->get_variation_id() );
        $image_thumb_link = wp_get_attachment_image_src($attachment_id, 'shop_thumbnail');

        $variations = array_merge( $variations, array( 'image_thumb' =>
            $image_thumb_link[0]
        ) );
    } else if ( has_post_thumbnail() ) {
        $attachment_id = get_post_thumbnail_id();
        $image_thumb_link = wp_get_attachment_image_src($attachment_id, 'shop_thumbnail');

        $variations = array_merge( $variations, array( 'image_thumb' =>
            $image_thumb_link[0]
        ) );
    }
    return $variations;
}

// add sort order parameter
function porto_layered_nav_link($link) {

    parse_str($_SERVER['QUERY_STRING'], $params);

    if (!empty($params['orderby']))
        $link = add_query_arg( 'orderby', $params['orderby'], $link );

    if (!empty($params['count']))
        $link = add_query_arg( 'count', $params['count'], $link );

    return $link;
}

// change product thumbnail in products list page
function porto_loop_product_thumbnail() {
    global $porto_settings;

    $id = get_the_ID();
    $size = 'shop_catalog';

    $gallery = get_post_meta($id, '_product_image_gallery', true);
    $attachment_image = '';
    if (!empty($gallery) && $porto_settings['category-image-hover']) {
        $gallery = explode(',', $gallery);
        $first_image_id = $gallery[0];
        $attachment_image = wp_get_attachment_image($first_image_id , $size, false, array('class' => 'hover-image'));
    }

    $thumb_image = get_the_post_thumbnail($id , $size, array('class' => ''));
    if (!$thumb_image) {
        if ( wc_placeholder_img_src() ) {
            $thumb_image = wc_placeholder_img( $size );
        }
    }

    echo '<div class="inner'.(($attachment_image)?' img-effect':'').'">';

    // show images
    echo $thumb_image;
    echo $attachment_image;

    echo '</div>';
}

// change product thumbnail in products widget
function porto_widget_product_thumbnail() {
    global $porto_settings;

    $id = get_the_ID();
    $size = 'shop_thumbnail';

    $gallery = get_post_meta($id, '_product_image_gallery', true);
    $attachment_image = '';
    if (!empty($gallery) && $porto_settings['category-image-hover']) {
        $gallery = explode(',', $gallery);
        $first_image_id = $gallery[0];
        $attachment_image = wp_get_attachment_image($first_image_id , $size, false, array('class' => 'hover-image '));
    }

    $thumb_image = get_the_post_thumbnail($id , $size, array('class' => ''));
    if (!$thumb_image) {
        if ( wc_placeholder_img_src() ) {
            $thumb_image = wc_placeholder_img( $size );
        }
    }

    echo '<div class="inner'.(($attachment_image)?' img-effect':'').'">';

    // show images
    echo $thumb_image;
    echo $attachment_image;

    echo '</div>';
}

// remove related products
function porto_remove_related_products( $args ) {
    global $porto_settings;

    if (isset($porto_settings['product-related']) && !$porto_settings['product-related'])
        return array();
    return $args;
}

// add ajax cart fragment
function porto_woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $porto_settings;

    $_cartQty = WC()->cart->cart_contents_count;

    $header_type = $porto_settings['header-type'];

    $fragments['#mini-cart .cart-items'] = '<span class="cart-items">'. (($porto_settings['minicart-type'] == 'minicart-inline' || $header_type == 'side')
            ? '<span class="mobile-hide">' . sprintf( _n( '%d item', '%d items', $_cartQty, 'woocommerce' ), $_cartQty ) . '</span><span class="mobile-show">' . $_cartQty . '</span>'
            : (($_cartQty > 0) ? $_cartQty : '0')) .'</span>';

    return $fragments;
}

// ajax remove cart item
add_action( 'wp_ajax_porto_cart_item_remove', 'porto_cart_item_remove' );
add_action( 'wp_ajax_nopriv_porto_cart_item_remove', 'porto_cart_item_remove' );
function porto_cart_item_remove() {

    $cart = WC()->instance()->cart;
    $cart_id = $_POST['cart_id'];
    $cart_item_id = $cart->find_product_in_cart($cart_id);

    if ($cart_item_id) {
        $cart->set_quantity($cart_item_id, 0);
    }

    $cart_ajax = new WC_AJAX();
    $cart_ajax->get_refreshed_fragments();

    exit();
}

// refresh cart fragment
add_action( 'wp_ajax_porto_refresh_cart_fragment', 'porto_refresh_cart_fragment' );
add_action( 'wp_ajax_nopriv_porto_refresh_cart_fragment', 'porto_refresh_cart_fragment' );
function porto_refresh_cart_fragment() {

    $cart_ajax = new WC_AJAX();
    $cart_ajax->get_refreshed_fragments();

    exit();
}

function porto_get_products_by_ids($product_ids) {
    $product_ids = explode(',', $product_ids);
    $product_ids = array_map('trim', $product_ids);

    $args = array(
        'post_type'    => 'product',
        'post_status' => 'publish',
        'ignore_sticky_posts'    => 1,
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key'         => '_visibility',
                'value'     => array('catalog', 'visible'),
                'compare'     => 'IN'
            )
        ),
        'post__in' => $product_ids
    );

    $query = new WP_Query($args);

    return $query;
}

function porto_get_rating_html( $product, $rating = null ) {

    if ( ! is_numeric( $rating ) ) {
        $rating = $product->get_average_rating();
    }

    $rating_html  = '<div class="star-rating" title="' . $rating . '">';

    $rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . __( 'out of 5', 'woocommerce' ) . '</span>';

    $rating_html .= '</div>';

    return $rating_html;
}

// Quick View Html
add_action('wp_ajax_porto_product_quickview', 'porto_product_quickview');
add_action('wp_ajax_nopriv_porto_product_quickview', 'porto_product_quickview');

function porto_product_quickview() {

    global $post, $product;
    $post = get_post($_GET['pid']);
    $product = wc_get_product( $post->ID );

    if ( post_password_required() ) {
        echo get_the_password_form();
        die();
        return;
    }

    $displaytypenumber = 0;
    if (function_exists('wcva_get_woo_version_number')) {
        require_once wcva_plugin_path() . '/includes/wcva_common_functions.php';
    }

    if (function_exists('wcva_return_displaytype_number'))
        $displaytypenumber = wcva_return_displaytype_number($product,$post);

    $goahead = 1;

    if(isset($_SERVER['HTTP_USER_AGENT'])){
        $agent = $_SERVER['HTTP_USER_AGENT'];
    }

    if (preg_match('/(?i)msie [5-8]/', $agent)) {
        $goahead = 0;
    }

    ?>

    <div class="quickview-wrap quickview-wrap-<?php echo $post->ID ?> single-product">
        <div class="product product-summary-wrap">

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 summary-before">
                    <?php
                    do_action( 'woocommerce_before_single_product_summary' );
                    ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 summary entry-summary">
                    <?php
                    do_action( 'woocommerce_single_product_summary' );
                    ?>
                    <script type="text/javascript">
                        /* <![CDATA[ */
                        <?php
                        $suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                        $assets_path          = esc_url(str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() )) . '/assets/';
                        $frontend_script_path = $assets_path . 'js/frontend/';
                        ?>
                        var wc_add_to_cart_params = <?php echo array2json(apply_filters( 'wc_add_to_cart_params', array(
                            'ajax_url'                => WC()->ajax_url(),
                            'ajax_loader_url'         => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
                            'i18n_view_cart'          => esc_attr__( 'View Cart', 'woocommerce' ),
                            'cart_url'                => get_permalink( wc_get_page_id( 'cart' ) ),
                            'is_cart'                 => is_cart(),
                            'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
                        ) )) ?>;
                        var wc_single_product_params = <?php echo array2json(apply_filters( 'wc_single_product_params', array(
                            'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'woocommerce' ),
                            'review_rating_required'    => get_option( 'woocommerce_review_rating_required' ),
                        ) )) ?>;
                        var woocommerce_params = <?php echo array2json(apply_filters( 'woocommerce_params', array(
                            'ajax_url'        => WC()->ajax_url(),
                            'ajax_loader_url' => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
                        ) )) ?>;
                        var wc_cart_fragments_params = <?php echo array2json(apply_filters( 'wc_cart_fragments_params', array(
                            'ajax_url'      => WC()->ajax_url(),
                            'fragment_name' => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments' )
                        ) )) ?>;
                        var wc_add_to_cart_variation_params = <?php echo array2json(apply_filters( 'wc_add_to_cart_variation_params', array(
                            'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
                            'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
                        ) )) ?>;
                        jQuery(document).ready(function($) {
                            $( document ).off( 'click', '.plus, .minus');
                            $( document ).off( 'click', '.add_to_cart_button');
                            $.getScript('<?php echo $frontend_script_path . 'add-to-cart' . $suffix . '.js' ?>');
                            $.getScript('<?php echo $frontend_script_path . 'single-product' . $suffix . '.js' ?>');
                            $.getScript('<?php echo $frontend_script_path . 'woocommerce' . $suffix . '.js' ?>');
                            <?php if (($goahead == 1) && ($displaytypenumber > 0)) : ?>
                            $.getScript('<?php echo porto_js . '/manage-variation-selection.js' ?>');
                            <?php else : ?>
                            $.getScript('<?php echo $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js' ?>');
                            <?php endif; ?>
                        });
                        /* ]]> */
                    </script>
                </div><!-- .summary -->
            </div>
        </div>
    </div>

    <?php

    die();
}

function porto_woocommerce_category_image() {
    if ( is_product_category() ){
        $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        if ($term) {
            $image = esc_url(get_metadata($term->taxonomy, $term->term_id, 'category_image', true));
            if ( $image ) {
                echo '<img src="' . $image . '" class="category-image" alt="' . esc_attr($term->name) . '" />';
            }
        }
    }
}

function porto_woocommerce_cart_item_class($class, $cart_item, $cart_item_key) {
    return $class . ' porto_cart_item_' . $cart_item_key;
}

function porto_woocommerce_single_excerpt() {
    global $post;

    if ( ! $post->post_excerpt ) {
        return;
    }
    ?>
    <div class="description">
        <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
    </div>
    <?php
}

function porto_woocommerce_product_nav() {
    global $porto_settings;

    if (!$porto_settings['product-nav'])
        return;

    if ( is_singular('product') ) {
        global $post;

        // get categories
        $terms = wp_get_post_terms( $post->ID, 'product_cat' );
        foreach ( $terms as $term ) $cats_array[] = $term->term_id;

        // get all posts in current categories
        $query_args = array('posts_per_page' => -1, 'post_status' => 'publish', 'post_type' => 'product', 'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $cats_array
            )));
        $r = new WP_Query($query_args);

        // show next and prev only if we have 3 or more
        if ($r->post_count > 2) {

            $prev_product_id = -1;
            $next_product_id = -1;

            $found_product = false;
            $i = 0;

            $current_product_index = $i;
            $current_product_id = get_the_ID();

            $first_product_index = 0;

            if ($r->have_posts()) {
                while ($r->have_posts()) {
                    $r->the_post();
                    $current_id = get_the_ID();

                    if ($current_id == $current_product_id) {
                        $found_product = true;
                        $current_product_index = $i;
                    }

                    $is_first = ($current_product_index == $first_product_index);

                    if ($is_first) {
                        $prev_product_id = get_the_ID(); // if product is first then 'prev' = last product
                    } else {
                        if (!$found_product && $current_id != $current_product_id) {
                            $prev_product_id = get_the_ID();
                        }
                    }

                    if ($i == 0) { // if product is last then 'next' = first product
                        $next_product_id = get_the_ID();
                    }

                    if ($found_product && $i == $current_product_index + 1) {
                        $next_product_id = get_the_ID();
                    }

                    $i++;
                }

                echo '<div class="product-nav">';

                if ($prev_product_id != -1) {
                    porto_show_link_product($prev_product_id, $cats_array, "next");
                }
                if ($next_product_id != -1) {
                    porto_show_link_product($next_product_id, $cats_array, "prev");
                }

                echo '</div>';
            }

            wp_reset_postdata();
        }
    }
}

function porto_show_link_product($post_id, $categories_as_array, $label) {

    global $woocommerce;

    // get post according post id
    $query_args = array( 'post__in' => array($post_id), 'posts_per_page' => 1, 'post_status' => 'publish', 'post_type' => 'product', 'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $categories_as_array
        )));
    $r_single = new WP_Query($query_args);
    if ($r_single->have_posts()) {
        $r_single->the_post();
        global $product;
        ?>
        <div class="product-<?php echo $label ?>">
            <a href="<?php the_permalink() ?>" title="">
                <span class="product-link"></span>
                <span class="product-popup">
                    <span class="featured-box">
                        <span class="box-content">
                            <span class="product-image">
                                <span class="inner">
                                    <?php if (has_post_thumbnail()) the_post_thumbnail('shop_thumbnail'); else echo '<img src="'. wc_placeholder_img_src() .'" alt="Placeholder" width="'.$woocommerce->get_image_size('shop_thumbnail_image_width').'" height="'.$woocommerce->get_image_size('shop_thumbnail_image_height').'" />'; ?>
                                </span>
                            </span>
                            <span class="product-details">
                                <span class="product-title"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></span>
                                <?php echo $product->get_price_html(); ?>
                            </span>
                        </span>
                    </span>
                </span>
            </a>
        </div>
        <?php
        wp_reset_postdata();
    }
}

