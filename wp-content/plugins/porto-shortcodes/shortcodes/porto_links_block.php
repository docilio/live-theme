<?php

// Porto Links Block
add_shortcode('porto_links_block', 'porto_shortcode_links_block');
add_action('vc_build_admin_page', 'porto_load_links_block_shortcode');
add_action('vc_after_init', 'porto_load_links_block_shortcode');

function porto_shortcode_links_block($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_links_block'))
        include $template;
    return ob_get_clean();
}

function porto_load_links_block_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Links Block", 'porto'),
        "base" => "porto_links_block",
        "category" => __("Porto", 'porto'),
        "icon" => "porto_vc_links_block",
        'is_container' => true,
        'weight' => - 50,
        'js_view' => 'VcColumnView',
        "as_parent" => array('only' => 'porto_links_item'),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Title", "porto"),
                "param_name" => "title",
                "value" => "Navigation",
                "admin_label" => true
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Font Awesome Icon or Icon Class', 'porto'),
                'param_name' => 'icon',
                'description' => __('Input font awesome icon or icon class. You can see <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome Icons in here</a>.', 'porto')
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Links_Block')) {
        class WPBakeryShortCode_Porto_Links_Block extends WPBakeryShortCodesContainer {
        }
    }
}