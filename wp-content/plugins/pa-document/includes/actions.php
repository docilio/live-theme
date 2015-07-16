<?php
/**
 * @package name
 */

/**
 * 
 * Add the Additional column Values for the document_category Taxonomy
 * 
 * @param string $out
 * @param string $column
 * @param int $term_id
 * @return string
 */
function pado_manage_document_category_custom_column($out,$column,$term_id){
    switch($column){
        case 'shortcode':
            $temp = '[document category=' . $term_id . ']';
            return $temp;
            break;
    }
}

add_action( 'manage_document_category_custom_column' , 'pado_manage_document_category_custom_column',10,3); 

/**
 * 
 * Add the Additional column Values for the document Post Type
 * 
 * @global type $post
 * @param string $column
 */

function pado_manage_document_custom_column($column){
    global $post;
    switch($column){
        case 'category':
            $terms = wp_get_object_terms($post->ID  ,'document_category');
            foreach($terms as $term){
                $temp  = " <a href=\"" . admin_url('edit-tags.php?action=edit&taxonomy=document_category&tag_ID=' . $term->term_id . '&post_type=document') . "\" ";
                $temp .= " class=\"row-title\">{$term->name}</a><br/>";
                echo $temp;
            }
            break;
        case 'document_slug':
        $post_slug = get_post($post->ID)->post_name;
        echo $post_slug;     
            break;
    }
}

add_action( 'manage_document_posts_custom_column' , 'pado_manage_document_custom_column'); 

/**
 * Category Based Filtering options
 * 
 * @global string $typenow
 */

function pado_document_restrict_manage_posts(){
    global $typenow;
    
    if($typenow=='document'){
        ?>
        <select name="document_category">
            <option value="0"><?php _e('Select Category','pressapps'); ?></option>
        <?php
        $categories = get_terms('document_category');
        if(count($categories)>0){
            foreach($categories as $cat){
                if($_GET['document_category']==$cat->slug){
                    echo "<option value={$cat->slug} selected=\"selected\">{$cat->name}</option>";
                }else{
                    echo "<option value={$cat->slug} >{$cat->name}</option>";
                }
            }
        }
        ?>
        </select>
        <?php
    }
    
}

add_action('restrict_manage_posts','pado_document_restrict_manage_posts');


/**
 * Shortcode field for the Edit Taxonomy Page
 * 
 * @param string $taxonomy
 */

function pado_document_category_edit_form_fields($taxonomy){
    $tag_id = $_GET['tag_ID'];
    ?>
    <tr>
        <th scope="row" valign="top"><label for="shortcode"><?php _e('Shortcode','pressapps');?></label></th>
        <td>[document category=<?php echo $tag_id; ?>]</td>
    </tr>
    <?php
}

add_action('document_category_edit_form_fields','pado_document_category_edit_form_fields');

