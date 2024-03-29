<?php get_header() ?>

<?php

global $porto_settings;

$faq_infinite = $porto_settings['faq-infinite'];

if ($faq_infinite) {
    global $wp_rewrite;

    $pagenum = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
    $pagelink = get_pagenum_link();

    if ( !$wp_rewrite->using_permalinks() || is_admin() || strpos($pagelink, '?') ) {
        if (strpos($pagelink, '?') !== false)
            $page_path = apply_filters( 'get_pagenum_link', $pagelink . '&amp;paged=');
        else
            $page_path = apply_filters( 'get_pagenum_link', $pagelink . '?paged=');
    } else {
        $page_path = apply_filters( 'get_pagenum_link', $pagelink . user_trailingslashit( $wp_rewrite->pagination_base . "/" ));
    }
}

?>

<div id="content" role="main">

    <?php if ($porto_settings['faq-title']) : ?>
        <h2><?php echo force_balance_tags($porto_settings['faq-title']) ?></h2>
    <?php endif; ?>

    <?php if (have_posts()) : ?>

        <div class="page-faqs <?php if ($faq_infinite) echo ' infinite-container' ?> clearfix">

            <?php
            $faq_taxs = array();

            $taxs = get_categories(array(
                'taxonomy' => 'faq_cat'
            ));

            foreach ($taxs as $tax) {
                $faq_taxs[urldecode($tax->slug)] = $tax->name;
            }

            if (is_array($faq_taxs)) {
                ksort($faq_taxs);
            }

            // Show Filters
            if (is_array($faq_taxs) && !empty($faq_taxs)) : ?>
                <ul class="faq-filter nav nav-pills sort-source">
                    <li class="active" data-filter="*"><a><?php echo __('Show All', 'porto'); ?></a></li>
                    <?php foreach ($faq_taxs as $faq_tax_slug => $faq_tax_name) : ?>
                        <li data-filter="<?php echo esc_attr($faq_tax_slug) ?>"><a><?php echo esc_html($faq_tax_name) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <hr>
            <?php endif; ?>

            <div class="faq-row <?php if ($faq_infinite) : ?> faqs-infinite<?php endif; ?>"<?php if ($faq_infinite) : ?> data-pagenum="<?php echo esc_attr($pagenum) ?>" data-path="<?php echo esc_url($page_path) ?>"<?php endif; ?>>
                <?php
                while (have_posts()) {
                    the_post();

                    get_template_part('content', 'archive-faq');
                }
                ?>
            </div>

            <?php porto_pagination(); ?>

        </div>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>

        <p><?php _e('Apologies, but no results were found for the requested archive.', 'porto'); ?></p>

    <?php endif; ?>

</div>

<?php get_footer() ?>