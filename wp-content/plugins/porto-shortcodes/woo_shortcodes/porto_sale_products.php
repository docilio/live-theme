<?php

// Porto Sale Products
add_shortcode('porto_sale_products', 'porto_shortcode_sale_products');
add_action('vc_build_admin_page', 'porto_load_sale_products_shortcode');
add_action('vc_after_init', 'porto_load_sale_products_shortcode');

function porto_shortcode_sale_products($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_woo_template('porto_sale_products'))
        include $template;
    return ob_get_clean();
}

function porto_load_sale_products_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();
    $order_by_values = porto_vc_woo_order_by();
    $order_way_values = porto_vc_woo_order_way();

    // woocommerce sale products
    vc_map(
        array(
            'name' => "Porto " . __( 'Sale products', 'js_composer' ),
            'base' => 'porto_sale_products',
            'icon' => 'porto_vc_woocommerce',
            'category' => __( 'WooCommerce', 'js_composer' ),
            'description' => __( 'Show all products on sale', 'js_composer' ),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Title', 'woocommerce' ),
                    'param_name' => 'title',
                    'admin_label' => true
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'View mode', 'porto' ),
                    'param_name' => 'view',
                    'value' => porto_vc_commons('products_view_mode'),
                    'admin_label' => true
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Per page', 'js_composer' ),
                    'value' => 12,
                    'param_name' => 'per_page',
                    'description' => __( 'The "per_page" shortcode determines how many products to show on the page', 'js_composer' ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Columns', 'porto' ),
                    'param_name' => 'columns',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider', 'grid' )),
                    'std' => '4',
                    'value' => porto_vc_commons('products_columns')
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Column Width', 'porto' ),
                    'param_name' => 'column_width',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider', 'grid' )),
                    'value' => porto_vc_commons('products_column_width')
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Order by', 'js_composer' ),
                    'param_name' => 'orderby',
                    'value' => $order_by_values,
                    'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Order way', 'js_composer' ),
                    'param_name' => 'order',
                    'value' => $order_way_values,
                    'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Add Links Position', 'porto' ),
                    'desc' => 'Select position of add to cart, add to wishlist, quickview.',
                    'param_name' => 'addlinks_pos',
                    'value' => porto_vc_commons('products_addlinks_pos')
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Show Navigation', 'porto' ),
                    'param_name' => 'navigation',
                    'std' => 'yes',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider' )),
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Show Pagination', 'porto' ),
                    'param_name' => 'pagination',
                    'std' => 'no',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider' )),
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
                ),
                $animation_type,
                $animation_duration,
                $animation_delay,
                $custom_class
            )
        )
    );

    if (!class_exists('WPBakeryShortCode_Porto_Sale_Products')) {
        class WPBakeryShortCode_Porto_Sale_Products extends WPBakeryShortCode {
        }
    }
}