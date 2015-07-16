<?php

// add post-formats support
add_post_type_support( 'document', 'post-formats' );

function pado_get_display_document($args = array()){
    global $pressapps_document_data;
    global $pado_options;

    $default = array(
        'category'      => -1,
    );

    $args = shortcode_atts($default,$args);
    
    if ( $pado_options['reorder'] == 1 ) {
        $qry_args = array(
            'post_type'     => 'document',
            'numberposts'   => -1,
            'orderby' => 'menu_order', 
            'order' => 'ASC', 
        );
    } else {
        $qry_args = array(
            'post_type'     => 'document',
            'numberposts'   => -1,
        );
    }
    
    if(isset($args['category']) && $args['category']!=-1){
        $qry_args['tax_query']   = array(array(
                'taxonomy'  => 'document_category',
                'field'     => 'id',
                'terms'     => $args['category'],
            ),
        );
        if ( $pado_options['reorder'] == 1 ) {
            $pressapps_terms       = get_terms('document_category',array(
                'child_of'  => $args['category'],
                'orderby'   => 'term_group',
                'order' => 'ASC'
            ));
        } else {
            $pressapps_terms       = get_terms('document_category',array(
                'child_of'  => $args['category']
            ));
        }
    }else{
        if ( $pado_options['reorder'] == 1 ) {
            $pressapps_terms = get_terms('document_category' ,array(
                'orderby'   => 'term_group',
                'order' => 'ASC'
            ) );
        } else {
            $pressapps_terms = get_terms('document_category');
        }
    }
    
    if(count($pressapps_terms)>0){
        foreach($pressapps_terms as $term){
            $pressapps_terms_documents[$term->term_id] = get_posts(array_merge($qry_args,
                array('tax_query'     => array(
                    array(
                        'taxonomy'  => 'document_category',
			'field'     => 'id',
			'terms'     => $term->term_id,
                    )
                )
            )));
            
        }
        
        $pressapps_document_data = array(
            'dispaly_terms' => TRUE,
            'terms'         => $pressapps_terms,
            'document'        => $pressapps_terms_documents,
        );

    }else{
        
        $pressapps_documents = get_posts($qry_args);
        
        $pressapps_document_data = array(
            'dispaly_terms' => FALSE,
            'document'        => $pressapps_documents,
        );
    }
    
    $filename = PADO_PLUGIN_TEMPLATES_DIR . "document-default.php";  
    
    ob_start();
    include_once $filename;

    wp_enqueue_style('pado_default');
    if(is_admin()){
        wp_enqueue_style('pado_admin');
    } 
    wp_enqueue_script('pado_tooltip');
    wp_enqueue_script('pado_custom');

    wp_enqueue_style('palo_perfect-scrollbar');
    wp_enqueue_script('palo_perfect-scrollbar');

    wp_reset_query();
    return ob_get_clean();
    
}

/**
 * 
 * Disply the document HTML generated based on the template file of document.
 * 
 * @param array $args
 */
function pado_the_display_document($args   = array()){
    echo pado_get_display_document($args);
}

/**
 * Add the Shortcode for the document part with the following options 
 * 
 * @param array $atts
 * @return string
 */
function pado_shortcode_document($atts = array()){
        
    return pado_get_display_document($atts);
}

add_shortcode('document', 'pado_shortcode_document');


function pado_print_document_css() {

    global $pado_options;

    echo '<style text="text/css"id="document-style-css">' . "\n";

    if ( $pado_options['color'] ) {
        echo '.sidebar_doc_title a, .sidebar_doc_title a:visited, .pado-section-heading, .pado-back-top a:hover, .pado-sharing-link:hover i { color: ' . sanitize_text_field( $pado_options['color'] ) . "}\n" ;
        echo '.sidebar_doc_title:hover, .pado-section-heading:before { background-color: ' . sanitize_text_field( $pado_options['color'] ) . "}\n" ;
        if ($pado_options['custom-css']) {
            echo sanitize_text_field( $pado_options['custom-css'] ) . "\n";
        }
    }

    echo "</style>\n";
}

function pado_print_document_custom_css() {

    global $pado_options;

    echo '<style text="text/css" id="document-custom-css">' . "\n" . sanitize_text_field( $pado_options['custom-css'] ) . "\n</style>\n";
}

/*-----------------------------------------------------------------------------------*/
/* Post format icons */
/*-----------------------------------------------------------------------------------*/

function pado_document_icon() {
    if (get_post_format() == 'video') {
        return 'pado-icon-video';
    } elseif (get_post_format() == 'image') {
        return 'pado-icon-picture';
    } elseif (get_post_format() == 'link') {
        return 'pado-icon-link';
    } elseif (get_post_format() == 'audio') {
        return 'pado-icon-music';
    } elseif (get_post_format() == 'quote') {
        return 'pado-icon-quote-right';
    } elseif (get_post_format() == 'chat') {
        return 'pado-icon-chat-empty';
    } elseif (get_post_format() == 'gallery') {
        return 'pado-icon-th-large';
    } elseif (get_post_format() == 'status') {
        return 'pado-icon-comment-empty';

    } else {
        return 'pado-icon-doc-text';
    }
}


/*-----------------------------------------------------------------------------------*/
/* Voting */
/*-----------------------------------------------------------------------------------*/

function pado_docs_votes($is_ajax = FALSE) {

        global $pado_options;        
        global $post;
        $votes_like = (int) get_post_meta($post->ID, '_votes_likes', true);
        $votes_dislike = (int) get_post_meta($post->ID, '_votes_dislikes', true);
        $voted_like             = sprintf(_n('%s person found this helpful', '%s people found this helpful', $votes_like, 'pressapps'), $votes_like);
        $voted_dislike  = sprintf(_n('%s person did not find this helpful', '%s people did not find this helpful', $votes_dislike, 'pressapps'), $votes_dislike);
        $vote_like_link                 = __("I found this helpful", 'pressapps');
        $vote_dislike_link      = __("I did not find this helpful", 'pressapps');
        $cookie_vote_count      = '';

        if ($pado_options['icon'] == 'thumbs') {
            $like_icon = '<i class="pado-icon-thumbs-up"></i> ';
            $dislike_icon = '<i class="pado-icon-thumbs-down"></i> ';
        } else {
            $like_icon = '';
            $dislike_icon = '';
        }

        if(isset($_COOKIE['vote_count'])){
            $cookie_vote_count = @unserialize(base64_decode($_COOKIE['vote_count']));
        }
        
        if(!is_array($cookie_vote_count) && isset($cookie_vote_count)){
            $cookie_vote_count = array();
        }
       
        echo (($is_ajax)?'':'<div class="pado-votes">');
                                
        if (is_user_logged_in() || $pado_options['voting'] == 1) :
            
                if(is_user_logged_in())
                    $vote_count = (array) get_user_meta(get_current_user_id(), 'vote_count', true);
                else
                    $vote_count = $cookie_vote_count;
                
                if (!in_array( $post->ID, $vote_count )) :

                        echo '<p data-toggle="tooltip" title="' . $vote_like_link . '" class="pado-likes"><a class="pado-like-btn" href="javascript:" post_id="'  . $post->ID . '">' . $like_icon .'<span class="count">' . $votes_like . '</span></a></p>';
                        echo '<p data-toggle="tooltip" title="' . $vote_dislike_link . '" class="pado-dislikes"><a class="pado-dislike-btn" href="javascript:" post_id="' . $post->ID . '">' . $dislike_icon . '<span class="count">' . $votes_dislike . '</span></a></p>';

                else :
                        // already voted
                        echo '<p data-toggle="tooltip" title="' . $voted_like . '" class="pado-likes">' . $like_icon .'<span class="count">' . $votes_like . '</span></p> ';
                        echo '<p data-toggle="tooltip" title="' . $voted_dislike . '" class="pado-dislikes">' . $dislike_icon . '<span class="count">' . $votes_dislike . '</span></p> ';
                endif;
        
        else :
                // not logged in
                echo '<p data-toggle="tooltip" title="' . $voted_like . '" class="pado-likes">' . $like_icon .'<span class="count">' . $votes_like . '</span></p> ';
                echo '<p data-toggle="tooltip" title="' . $voted_dislike . '" class="pado-dislikes">' . $dislike_icon . '<span class="count">' . $votes_dislike . '</span></p> ';
        endif;
        
        echo (($is_ajax)?'':'</div>');

}

function pado_docs_vote() {
    global $post;
    global $pado_options;    

    if (is_user_logged_in()) {
        
        $vote_count = (array) get_user_meta(get_current_user_id(), 'vote_count', true);
        
        if (isset( $_GET['vote_like'] ) && $_GET['vote_like']>0) :
                
                $post_id = (int) $_GET['vote_like'];
                $the_post = get_post($post_id);
                
                if ($the_post && !in_array( $post_id, $vote_count )) :
                        $vote_count[] = $post_id;
                        update_user_meta(get_current_user_id(), 'vote_count', $vote_count);
                        $post_votes = (int) get_post_meta($post_id, '_votes_likes', true);
                        $post_votes++;
                        update_post_meta($post_id, '_votes_likes', $post_votes);
                        $post = get_post($post_id);
                        pado_docs_votes(true);
                        die('');
                endif;
                
        elseif (isset( $_GET['vote_dislike'] ) && $_GET['vote_dislike']>0) :
                
                $post_id = (int) $_GET['vote_dislike'];
                $the_post = get_post($post_id);
                
                if ($the_post && !in_array( $post_id, $vote_count )) :
                        $vote_count[] = $post_id;
                        update_user_meta(get_current_user_id(), 'vote_count', $vote_count);
                        $post_votes = (int) get_post_meta($post_id, '_votes_dislikes', true);
                        $post_votes++;
                        update_post_meta($post_id, '_votes_dislikes', $post_votes);
                        $post = get_post($post_id);
                        pado_docs_votes(true);
                        die('');
                        
                endif;
                
        endif;

    } elseif (!is_user_logged_in() && $pado_options['voting'] == 1) {

        // ADD VOTING FOR NON LOGGED IN USERS USING COOKIE TO STOP REPEAT VOTING ON AN ARTICLE
        $vote_count = '';
        
        if(isset($_COOKIE['vote_count'])){
            $vote_count = @unserialize(base64_decode($_COOKIE['vote_count']));
        }
        
        if(!is_array($vote_count) && isset($vote_count)){
            $vote_count = array();
        }
        
        if (isset( $_GET['vote_like'] ) && $_GET['vote_like']>0) :
                
                $post_id = (int) $_GET['vote_like'];
                $the_post = get_post($post_id);
                
                if ($the_post && !in_array( $post_id, $vote_count )) :
                        $vote_count[] = $post_id;
                        $_COOKIE['vote_count']  = base64_encode(serialize($vote_count));
                        setcookie('vote_count', $_COOKIE['vote_count'] , time()+(10*365*24*60*60),'/');
                        $post_votes = (int) get_post_meta($post_id, '_votes_likes', true);
                        $post_votes++;
                        update_post_meta($post_id, '_votes_likes', $post_votes);
                        $post = get_post($post_id);
                        pado_docs_votes(true);
                        die('');
                endif;
                
        elseif (isset( $_GET['vote_dislike'] ) && $_GET['vote_dislike']>0) :
                
                $post_id = (int) $_GET['vote_dislike'];
                $the_post = get_post($post_id);
                
                if ($the_post && !in_array( $post_id, $vote_count )) :
                        $vote_count[] = $post_id;
                        $_COOKIE['vote_count']  = base64_encode(serialize($vote_count));
                        setcookie('vote_count', $_COOKIE['vote_count'] , time()+(10*365*24*60*60),'/');
                        $post_votes = (int) get_post_meta($post_id, '_votes_dislikes', true);
                        $post_votes++;
                        update_post_meta($post_id, '_votes_dislikes', $post_votes);
                        $post = get_post($post_id);
                        pado_docs_votes(true);
                        die('');
                        
                endif;
                
        endif;

    } elseif (!is_user_logged_in() && $pado_options['voting'] == 2) {

        return;
        
    }
        
}

add_action('init', 'pado_docs_vote');

add_action('wp_head','pado_common_js');

function pado_common_js(){
    $pado = array(
        'base_url'  => esc_url(home_url()),
    );
    ?>
<script type="text/javascript">
    PADO = <?php echo json_encode($pado); ?>;
</script>
    <?php
}



