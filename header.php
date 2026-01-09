<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-title-wrapper">
                <h1 class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
                <p class="site-subtitle">常識を逆転する。人生を逆転する。FXで逆転する。</p>
            </div>
            
            <nav class="main-navigation">
                <div class="nav-links-wrapper">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'nav-menu',
                        'fallback_cb' => 'fx_blog_default_menu',
                    ));
                    ?>
                </div>
                
                <div class="header-search-container">
                    <button id="search-toggle-btn" class="search-toggle-btn" aria-label="検索">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="header-search-form-wrapper">
                        <?php get_search_form(); ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
