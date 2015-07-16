<?php

/**
 * Add the Additional Columns For the document_category Taxonomy
 * 
 * @param array $columns
 * @return array
 */       
function pado_manage_edit_document_category_columns($columns){
    
    $new_columns['cb']          = $columns['cb'];
    $new_columns['name']        = $columns['name'];
    $new_columns['shortcode']   = __("Shortcode",'pressapps');
    $new_columns['slug']        = $columns['slug'];
    $new_columns['posts']       = $columns['posts'];
    
    return $new_columns;
}

add_filter('manage_edit-document_category_columns','pado_manage_edit_document_category_columns');


/**
 * 
 * Rename the Columns for the document post type and adding new Columns
 * 
 * @param array $columns
 * @return array
 */

function pado_manage_edit_document_columns($columns){
    
    $new_columns['cb']          = $columns['cb'];
    $new_columns['title']       = __('Title','pressapps');
    $new_columns['category']    = __('Categories','pressapps');
    $new_columns['document_slug']        = __("Slug",'pressapps');
    $new_columns['date']        = $columns['date'];

    return $new_columns;
}

add_filter('manage_edit-document_columns','pado_manage_edit_document_columns');