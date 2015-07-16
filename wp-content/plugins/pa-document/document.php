<?php
/**
 * Plugin Name: PA Document (shared on wplocker.com)
 * Plugin URI: http://pressapps.co/plugins/
 * Description: Standalone Documentation Posts Plugin
 * Author: PressApps Team
 * Version: 1.3.0
 */

/*-----------------------------------------------------------------------------------*/
/* Return option page data */
/*-----------------------------------------------------------------------------------*/

$pado_options = get_option( 'pado_options' );

define( 'PADO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PADO_PLUGIN_URL', plugins_url("", __FILE__) );

define( 'PADO_PLUGIN_INCLUDES_DIR', PADO_PLUGIN_DIR . "/includes/" );
define( 'PADO_PLUGIN_INCLUDES_URL', PADO_PLUGIN_URL . "/includes/" );

define( 'PADO_PLUGIN_ASSETS_DIR', PADO_PLUGIN_DIR . "/assets/" );
define( 'PADO_PLUGIN_ASSETS_URL', PADO_PLUGIN_URL . "/assets/" );

define( 'PADO_PLUGIN_TEMPLATES_DIR', PADO_PLUGIN_DIR . "/templates/" );
define( 'PADO_PLUGIN_TEMPLATES_URL', PADO_PLUGIN_URL . "/templates/" );

class PADO_DOCUMENT{
    
    /**
     * Setup the Environment for the Plugin
     */
    function __construct() {

        global $pado_options;

        include_once PADO_PLUGIN_INCLUDES_DIR . 'functions.php';
        include_once PADO_PLUGIN_INCLUDES_DIR . 'actions.php';
        include_once PADO_PLUGIN_INCLUDES_DIR . 'filters.php';
        include_once PADO_PLUGIN_INCLUDES_DIR . 'help.php';
        if ( $pado_options['reorder'] == 1 ) {
            include_once PADO_PLUGIN_INCLUDES_DIR . 'admin/reorder.php';
        }
        if ( is_admin() ) {
            include_once PADO_PLUGIN_INCLUDES_DIR . 'admin/page.php';
        }

        load_plugin_textdomain( 'pressapps', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
        
        add_action('init'              ,array($this,'init'));

        add_action( 'wp_head', 'pado_print_document_css' );

    }
    
    function init(){
        
        register_post_type( 'document',array(
            'description'           => __('Document Posts','pressapps'),
            'labels'                => array(
                'name'                  => __('Documents'                     ,'pressapps'),
                'singular_name'         => __('Document'                      ,'pressapps'),
                'add_new'               => __('Add New'                    ,'pressapps'),  
                'add_new_item'          => __('Add New Document'              ,'pressapps'),  
                'edit_item'             => __('Edit Document'                 ,'pressapps'),  
                'new_item'              => __('New Document'                  ,'pressapps'),  
                'view_item'             => __('View Document'                 ,'pressapps'),  
                'search_items'          => __('Search Documents'              ,'pressapps'),  
                'not_found'             => __('No Documents found'            ,'pressapps'),  
                'not_found_in_trash'    => __('No Documents found in Trash'   ,'pressapps'),
                'all_items'             => __('All Documents'                 ,'pressapps'),
            ),
            'public'                => true,
            'menu_position'         => 5,
            'rewrite'               => array('slug' => 'document'),
            'supports'              => array('title','editor', 'thumbnail'),
            'public'                => true,
            'show_ui'               => true,
            'publicly_queryable'    => true,
            'exclude_from_search'   => false
        ));
        
        register_taxonomy( 'document_category',array( 'document' ),array( 
            'hierarchical'  => false,
            'labels'        => array(
                'name'              => __( 'Categories'             ,'pressapps'),
                'singular_name'     => __( 'Category'               ,'pressapps'),
                'search_items'      => __( 'Search Categories'      ,'pressapps'),
                'all_items'         => __( 'All Categories'         ,'pressapps'),
                'parent_item'       => __( 'Parent Category'        ,'pressapps'),
                'parent_item_colon' => __( 'Parent Category:'       ,'pressapps'),
                'edit_item'         => __( 'Edit Category'          ,'pressapps'),
                'update_item'       => __( 'Update Category'        ,'pressapps'),
                'add_new_item'      => __( 'Add New Category'       ,'pressapps'),
                'new_item_name'     => __( 'New Category Name'      ,'pressapps'),
                'popular_items'     => NULL,
                'menu_name'         => __( 'Categories'             ,'pressapps') 
            ),
            'show_ui'       => true,
            'public'        => true,
            'query_var'     => true,
            'hierarchical'  => true,
            'rewrite'       => array( 'slug' => 'document_category' )
        ));
        
        wp_register_style( 'pado_default' , PADO_PLUGIN_ASSETS_URL . '/css/default.css' );
        wp_register_style( 'palo_perfect-scrollbar' , PADO_PLUGIN_ASSETS_URL . '/css/perfect-scrollbar.min.css' );
        wp_register_style( 'pado_admin' , PADO_PLUGIN_ASSETS_URL . '/css/admin.css' );
        wp_register_script( 'pado_tooltip' , PADO_PLUGIN_ASSETS_URL . '/js/tooltip.min.js' , array('jquery') );
        wp_register_script( 'palo_perfect-scrollbar' , PADO_PLUGIN_ASSETS_URL . '/js/perfect-scrollbar.min.js', array( 'jquery' ) );
        wp_register_script( 'pado_custom' , PADO_PLUGIN_ASSETS_URL . '/js/custom.js' , array( 'jquery', 'palo_perfect-scrollbar' ) );
    }
}
$pado = new PADO_DOCUMENT();