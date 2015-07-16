<?php
global $pado_options; 
global $pressapps_document_data,$post;

if(count($pressapps_document_data['document'])==0){
    _e('No Document Found','pressapps');
    return ;
}
?>
<div id="pado-main">
    <div id="pado-sidebar">

        <?php

        if(isset($pressapps_document_data['terms'])){

            $c = 0;
            $p = 0;

            foreach($pressapps_document_data['terms'] as $terms){
                if(count($pressapps_document_data['document'][$terms->term_id])>0){  
                    $c++;
                ?>
                <ul>
                <li class="sidebar_cat_title"><i class="pado-icon-right-dir"></i><a><?php echo $terms->name; ?></a></li>

                  <?php
                    foreach($pressapps_document_data['document'][$terms->term_id] as $post) {
                        setup_postdata($post);
                        $p++;
                  ?>

                    <li class="sidebar_doc_title"><a href="#document-<?php echo $p; ?>"><i class="<?php echo pado_document_icon(); ?>"></i> <?php the_title(); ?></a></li>


                <?php 
                    } ?>
                </ul>

                <?php 
                }
            }

        } else {

              ?>
            <ul>
              <?php
            $p = 0;
            foreach($pressapps_document_data['document'] as $post){
                setup_postdata($post);
                $p++;
              ?>

                <li class="sidebar_doc_title"><a href="#document-<?php echo $p; ?>"><i class="<?php echo pado_document_icon(); ?>"></i> <?php the_title(); ?></a></li>

              <?php
            } ?>
            </ul>
            <?php
        } ?>

    </div>

    <div id="pado-content">
        <?php
        if(isset($pressapps_document_data['terms'])){
            ?>
            <?php
            $c = 0;
            $p = 0;
            foreach($pressapps_document_data['terms'] as $terms){
                if(count($pressapps_document_data['document'][$terms->term_id])>0){  
                    $c++;
                    ?>
                    <div id="cat-<?php echo $c; ?>">
                        <h3 class="pado-section-heading">
                            <a href="#cat-<?php echo $c; ?>" class="pado-sharing-link" title="<?php _e( 'Link to this page section', 'pressapps' ); ?>"><i class="icon pado-icon-link"></i></a><a name="#cat-<?php echo $c; ?>"></a><?php echo $terms->name; ?>
                        </h3>
                        <?php
                        foreach($pressapps_document_data['document'][$terms->term_id] as $post){
                            setup_postdata($post);
                            $p++;
                            ?>
                            <article id="document-<?php echo $p; ?>" class="document type-pressapps_document status-publish clearfix">
                                <h3 class="pado-post-heading"><a href="#document-<?php echo $p; ?>" class="pado-sharing-link" title="<?php _e( 'Link to this page section', 'pressapps' ); ?>"><i class="icon pado-icon-link"></i></a><a name="<?php the_ID(); ?>"></a><?php the_title(); ?></h3>
                                <?php if ( has_post_thumbnail() ) { ?>
                                    <div class="document-featured">
                                        <?php the_post_thumbnail(); ?>
                                    </div>
                                <?php } ?>                                
                                <div class="document-content">
                                    <?php the_content(); ?>
                                </div>
                                <?php 
                                if ( $pado_options['voting'] != 0) {
                                    pado_docs_votes();
                                }
                                ?>
                                <p class="pado-back-top"><a href="#top"><i class="pado-icon-angle-up"></i> <?php _e( 'Back To Top', 'pressapps' ); ?></a></p>
                            </article>
                        <?php
                    } ?>
                    </div>
            <?php } 
            }
            ?>
            <?php
            
        }else{
            $p = 0;
            foreach($pressapps_document_data['document'] as $post){
                setup_postdata($post);
                $p++;
                ?>
                <article id="document-<?php echo $p; ?>" class="document type-pressapps_document status-publish clearfix">
                    <h3 class="pado-post-heading"><a href="#document-<?php echo $p; ?>" class="pado-sharing-link" title="<?php _e( 'Link to this page section', 'pressapps' ); ?>"><i class="icon pado-icon-link"></i></a><a name="<?php the_ID(); ?>"></a><?php the_title(); ?></h3>
                    <?php if ( has_post_thumbnail() ) { ?>
                        <div class="document-featured">
                            <?php the_post_thumbnail(); ?>
                        </div>
                    <?php } ?>                                
                    <div class="document-content">
                        <?php the_content(); ?>
                    </div>
                    <?php 
                    if ( $pado_options['voting'] != 0) {
                        pado_docs_votes();
                    }
                    ?>
                    <p class="pado-back-top"><a href="#top"><i class="pado-icon-angle-up"></i> <?php _e( 'Back To Top', 'pressapps' ); ?></a></p>
                </article>
                <?php
            }
        }
        ?>
    </div>
    
</div>
