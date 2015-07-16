<?php
/**
 * Admi post order
 */
global $pagenow;

if( $pagenow == 'edit.php') {
    if ( isset($_GET['post_type']) && 'document' == $_GET['post_type'] ) {
        add_action( 'init', 'pado_order_load_scripts' );
        add_filter( 'pre_get_posts', 'pado_order_reorder_list' );


    }
} elseif( $pagenow == 'edit-tags.php' ) {
    if ( isset($_GET['post_type']) && 'document' == $_GET['post_type'] ) {
        add_action( 'init', 'pado_order_load_scripts_taxonomies' );     
        add_filter( 'get_terms_orderby', 'pado_order_reorder_taxonomies_list', 10, 2 );


    }
} 


function pado_order_reorder_taxonomies_list($orderby, $args) {
    $orderby = "t.term_group";
    return $orderby;
}


function pado_order_reorder_list($query) {
    $query->set('orderby', 'menu_order');
    $query->set('order', 'ASC');
    return $query;
}


function pado_order_load_scripts() {
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('pado_order-update-order', PADO_PLUGIN_ASSETS_URL . 'js/order-posts.js');
    wp_enqueue_style('pado_order-admin-styles', PADO_PLUGIN_ASSETS_URL . 'css/admin.css');
}

function pado_order_load_scripts_taxonomies() {
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('pado_order-update-order', PADO_PLUGIN_ASSETS_URL . 'js/order-taxonomies.js');
    wp_enqueue_style('pado_order-admin-styles', PADO_PLUGIN_ASSETS_URL . 'css/admin.css');
}


function pado_order_save_order() {
    
    global $wpdb;
    
    $action             = $_POST['action']; 
    $posts_array        = $_POST['post'];
    $listing_counter    = 1;
    
    foreach ($posts_array as $post_id) {
        
        $wpdb->update( 
                    $wpdb->posts, 
                        array('menu_order'  => $listing_counter), 
                        array('ID'          => $post_id) 
                    );

        $listing_counter++;
    }
    
    die();
}

function pado_order_save_taxonomies_order() {
    global $wpdb;
    
    $action             = $_POST['action']; 
    $tags_array         = $_POST['tag'];
    $listing_counter    = 1;
    
    foreach ($tags_array as $tag_id) {
        
        $wpdb->update( 
                    $wpdb->terms, 
                        array('term_group'          => $listing_counter), 
                        array('term_id'     => $tag_id) 
                    );

        $listing_counter++;
    }
    
    die();
}

add_action('wp_ajax_pado_order_update_posts', 'pado_order_save_order');
add_action('wp_ajax_pado_order_update_taxonomies', 'pado_order_save_taxonomies_order');