<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-9 <?php echo $porto_settings['search-size'] ?> sticky-menu-header">
    <?php if ($porto_settings['show-header-top']) : ?>
        <div class="header-top">
            <div class="container">
                <div class="header-left">
                    <?php
                    // show social links
                    echo porto_header_socials();

                    // show currency and view switcher
                    $currency_switcher = porto_currency_switcher();
                    $view_switcher = porto_view_switcher();

                    if ($currency_switcher || $view_switcher)
                        echo '<div class="switcher-wrap">';

                    echo $currency_switcher;

                    if ($currency_switcher && $view_switcher)
                        echo '<span class="gap switcher-gap">|</span>';

                    echo $view_switcher;

                    if ($currency_switcher || $view_switcher)
                        echo '</div>';
                    ?>
                </div>
                <div class="header-right">
                    <?php
                    // show welcome message and top navigation
                    $top_nav = porto_top_navigation();

                    if ($porto_settings['welcome-msg'])
                        echo '<span class="welcome-msg">' . force_balance_tags($porto_settings['welcome-msg']) . '</span>';

                    if ($porto_settings['welcome-msg'] && $top_nav)
                        echo '<span class="gap">|</span>';

                    echo $top_nav;
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="header-main">
        <div class="container">
            <div class="header-left">
                <?php // show logo ?>
                <h1 class="logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>" rel="home">
                        <?php if($porto_settings['logo'] && $porto_settings['logo']['url']) {
                            echo '<img class="img-responsive" src="' . esc_url(str_replace( array( 'http:', 'https:' ), '', $porto_settings['logo']['url'])) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';
                        } else {
                            bloginfo( 'name' );
                        } ?>
                    </a>
                </h1>
            </div>
            <div class="header-center">
                <?php
                // show search form
                echo porto_search_form();

                // show mobile toggle
                ?>
                <a class="mobile-toggle"><i class="fa fa-reorder"></i></a>
            </div>
            <div class="header-right">
                <div class="<?php if ($porto_settings['show-minicart'] && class_exists('WooCommerce')) echo 'header-minicart'.str_replace('minicart', '', $porto_settings['minicart-type']) ?>">
                    <?php
                    // show contact info and mini cart
                    $contact_info = $porto_settings['header-contact-info'];

                    if ($contact_info)
                        echo '<div class="header-contact">' . force_balance_tags($contact_info) . '</div>';

                    $minicart = porto_minicart();
                    echo $minicart;
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    $toggle_menu = porto_main_toggle_menu();
    if ($toggle_menu || $porto_settings['menu-block']) :
    ?>
    <div class="main-menu-wrap">
        <div id="main-menu" class="container">
            <div class="row">
                <div class="col-md-3 sidebar">
                    <?php
                    // show toggle menu
                    if ($toggle_menu) : ?>
                        <div id="main-toggle-menu" class="<?php echo (!$porto_settings['menu-toggle-onhome'] && is_front_page()) ? 'show-always' : 'closed' ?>">
                            <div class="menu-title closed">
                                <div class="toggle"></div>
                                <?php if ($porto_settings['menu-title']) : ?>
                                    <?php echo force_balance_tags($porto_settings['menu-title']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="toggle-menu-wrap">
                                <?php echo $toggle_menu ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($porto_settings['menu-block']) : ?>
                <div class="col-md-9">
                    <div class="menu-custom-block">
                        <?php echo force_balance_tags($porto_settings['menu-block']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</header>