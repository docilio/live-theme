<?php

function pado_help_tab(){
    
    $post_id    = (isset($_GET['post']))?$_GET['post']:-1;
    $post_type  = (isset($_GET['post_type']))?$_GET['post_type']:NULL;
    
    if($post_id!=-1){
        $post       = get_post($post_id);
        $post_type  = $post->post_type;
        
    }
        
    if($post_type=='document'){

        $screen = get_current_screen();
    
        $screen->add_help_tab( array(
            'id'	=> 'pressapps_document_shortcode',
            'title'	=> __( 'Document Shortcode', 'pressapps' ),
            'content'	=>
            
                '<p>' . __('<h2>Document Shortcode</h2>','pressapps') . '</p>' .
            
                '<p>' . __( 'You can use <code>[document]</code> shortcode to include the Document on any page, post or custom post type.', 'pressapps' ) . '</p>' .

                '<p>' . __( 'The shortcode accepts one optional attribute:', 'pressapps' ) . '</p>' . 
                '<p>' . __( '<b>category</b> = <i>-1</i> <b>|</b> <i>{any document category id}</i>', 'pressapps' ) . '</p>' . 
                '<p>' . __( '<b>Example</b>', 'pressapps' ) . '</p>' . 
                '<p>' . sprintf(__( '<code>[document category={category_id}]</code> {category_id} you will find it <a href="%s">here</a> under shortcode column', 'pressapps' ),admin_url('edit-tags.php?taxonomy=document_category&post_type=document') ). '</p>'
                
        ));
    }
}

add_action( 'admin_print_styles'     ,'pado_help_tab');