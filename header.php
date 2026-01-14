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
                        // WordPressのメニュー設定を無視して、常にコードで定義したメニューを表示する
                        if (function_exists('fx_blog_default_menu')) {
                            fx_blog_default_menu();
                        } else {
                            // 万が一関数がない場合のフォールバック（直書き）
                            echo '<ul class="nav-menu">';
                            echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/fx-overseas-comparison-ranking/')) . '">比較</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/fx-overseas-comparison-ranking/')) . '">ランキング</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/#faq')) . '">FAQ</a></li>';
                            echo '</ul>';
                        }
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