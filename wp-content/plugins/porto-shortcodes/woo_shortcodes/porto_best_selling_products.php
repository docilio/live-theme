<?php

// Porto Best Selling Products
add_shortcode('porto_best_selling_products', 'porto_shortcode_best_selling_products');
add_action('vc_build_admin_page', 'porto_load_best_selling_products_shortcode');
add_action('vc_after_init', 'porto_load_best_selling_products_shortcode');

function porto_shortcode_best_selling_products($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_woo_template('porto_best_selling_products'))
        include $template;
    return ob_get_clean();
}

function porto_load_best_selling_products_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    // woocommerce best selling products
    vc_map(
        array(
            'name' => "Porto " . __( 'Best Selling Products', 'js_composer' ),
            'base' => 'porto_best_selling_products',
            'icon' => 'porto_vc_woocommerce',
            'category' => __( 'WooCommerce', 'js_composer' ),
            'description' => __( 'Show best selling products on sale', 'porto' ),
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

    if (!class_exists('WPBakeryShortCode_Porto_Best_Selling_Products')) {
        class WPBakeryShortCode_Porto_Best_Selling_Products extends WPBakeryShortCode {
        }
    }
}