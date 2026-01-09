<?php
/**
 * FXブログテーマの機能
 */

// テーマのセットアップ
function fx_blog_setup()
{
    // タイトルタグのサポート
    add_theme_support('title-tag');

    // アイキャッチ画像のサポート
    add_theme_support('post-thumbnails');

    // HTML5マークアップのサポート
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // ナビゲーションメニューの登録
    register_nav_menus(array(
        'primary' => 'プライマリーメニュー',
    ));
}
add_action('after_setup_theme', 'fx_blog_setup');

// スタイルシートとスクリプトの読み込み
function fx_blog_scripts()
{
    wp_enqueue_style('fx-blog-style', get_stylesheet_uri(), array(), '1.0.30');

    // Chart.jsライブラリの読み込み
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);
    wp_enqueue_script('search-toggle', get_template_directory_uri() . '/js/search-toggle.js', array(), '1.0.0', true);

    // 投稿ページ、固定ページ、トップページで目次スクリプトを読み込み
    if (is_single() || is_page() || is_front_page() || is_home()) {
        wp_enqueue_script('fx-blog-toc', get_template_directory_uri() . '/js/toc.js', array(), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'fx_blog_scripts');

// ウィジェットエリアの登録
function fx_blog_widgets_init()
{
    register_sidebar(array(
        'name' => 'サイドバー',
        'id' => 'sidebar-1',
        'description' => 'メインサイドバーのウィジェットエリア',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'fx_blog_widgets_init');

// 投稿ページの抜粋文字数
function fx_blog_excerpt_length($length)
{
    return 120;
}
add_filter('excerpt_length', 'fx_blog_excerpt_length');

// 抜粋の最後の文字
function fx_blog_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'fx_blog_excerpt_more');

// デフォルトメニュー（メニューが設定されていない場合）
function fx_blog_default_menu()
{
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#comparison')) . '">比較</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#rankings')) . '">ランキング</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#faq')) . '">FAQ</a></li>';
    echo '</ul>';
}

// コメント機能を無効化
function fx_blog_disable_comments()
{
    // コメントを完全に無効化
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);

    // 既存のコメントを非表示
    add_filter('comments_array', '__return_empty_array', 10, 2);

    // コメントフォームを削除
    add_filter('comment_form_default_fields', '__return_empty_array');
    add_filter('comment_form_defaults', '__return_empty_array');
}
add_action('admin_init', 'fx_blog_disable_comments');

// 管理画面からコメント関連を非表示
function fx_blog_remove_comments_menu()
{
    remove_menu_page('edit-comments.php');
    remove_submenu_page('options-discussion.php', 'options-discussion.php');
}
add_action('admin_menu', 'fx_blog_remove_comments_menu');

// ダッシュボードからコメント関連ウィジェットを削除
function fx_blog_remove_comment_dashboard_widget()
{
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'fx_blog_remove_comment_dashboard_widget');

// サイドバーからコメント関連ウィジェットを削除
function fx_blog_remove_comment_widgets()
{
    unregister_widget('WP_Widget_Recent_Comments');
}
add_action('widgets_init', 'fx_blog_remove_comment_widgets');

// 検索ウィジェットをサイドバーから削除（ただし管理画面には表示される）
function fx_blog_remove_search_widget()
{
    unregister_widget('WP_Widget_Search');
}
add_action('widgets_init', 'fx_blog_remove_search_widget', 11);

// 「最近の投稿」ウィジェットのタイトルを「人気の投稿」に変更
function fx_blog_change_recent_posts_widget_title($title, $instance = array(), $id_base = '')
{
    // ウィジェットタイトルが「最近の投稿」の場合、「人気の投稿」に変更
    if ($title == '最近の投稿') {
        return '人気の投稿';
    }
    return $title;
}
add_filter('widget_title', 'fx_blog_change_recent_posts_widget_title', 10, 3);

// ウィジェットインスタンスのタイトルも変更
function fx_blog_change_widget_instance_title($instance, $widget)
{
    if ($widget instanceof WP_Widget_Recent_Posts) {
        if (isset($instance['title']) && $instance['title'] == '最近の投稿') {
            $instance['title'] = '人気の投稿';
        }
    }
    return $instance;
}
add_filter('widget_display_callback', 'fx_blog_change_widget_instance_title', 10, 2);

// 記事下部に表示するウィジェット（検索とコメントを除外）
function fx_blog_render_below_widgets()
{
    if (!is_active_sidebar('sidebar-1')) {
        return;
    }

    global $wp_registered_sidebars, $wp_registered_widgets;

    $sidebars_widgets = wp_get_sidebars_widgets();

    if (!isset($sidebars_widgets['sidebar-1'])) {
        return;
    }

    $widget_ids = $sidebars_widgets['sidebar-1'];

    foreach ($widget_ids as $widget_id) {
        // 検索ウィジェットとコメントウィジェットを除外
        if (strpos($widget_id, 'search') !== false || strpos($widget_id, 'recent-comments') !== false) {
            continue;
        }

        // ウィジェットを表示
        if (isset($wp_registered_widgets[$widget_id])) {
            $callback = $wp_registered_widgets[$widget_id]['callback'];
            $params = $wp_registered_widgets[$widget_id]['params'];

            // サイドバーの標準引数を注入
            if (isset($params[0]) && is_array($params[0])) {
                $params[0]['before_widget'] = '<div id="%1$s" class="widget %2$s">';
                $params[0]['after_widget'] = '</div>';
                $params[0]['before_title'] = '<h3 class="widget-title">';
                $params[0]['after_title'] = '</h3>';
            }

            if (is_callable($callback)) {
                call_user_func_array($callback, $params);
            }
        }
    }
}

// 画像付き最近の投稿ウィジェット
class FX_Blog_Recent_Posts_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'fx_blog_recent_posts',
            '画像付き最近の投稿',
            array('description' => 'アイキャッチ画像付きで最近の投稿を表示します。')
        );
    }

    public function widget($args, $instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '人気の投稿';
        // FORCE 5 posts as per user request (override widget settings)
        $number = 5;

        // 【修正】背景削除版
        echo '<div class="custom-popular-widget-wrapper" style="margin: 30px 0; width: 100%; box-sizing: border-box; display:block;">';

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        $r = new WP_Query(array(
            'posts_per_page' => $number,
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        ));

        if ($r->have_posts()) {
            echo '<div class="fx-recent-posts-grid">';
            while ($r->have_posts()) {
                $r->the_post();
                echo '<div class="fx-post-item">';
                echo '<a href="' . get_permalink() . '" class="fx-post-link">';
                echo '<div class="fx-post-thumbnail">';
                if (has_post_thumbnail()) {
                    the_post_thumbnail('large');
                } else {
                    echo '<div class="no-image" style="background:#f0f0f0; width:100%; height:150px; display:flex; align-items:center; justify-content:center; color:#999; font-size:12px;">No Image</div>';
                }
                echo '</div>';
                echo '<div class="fx-post-title">' . get_the_title() . '</div>';
                echo '</a>';
                echo '</div>';
            }
            echo '</div>';
            wp_reset_postdata();
        }

        echo $args['after_widget'];
        echo '</div>'; // End wrapper
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">タイトル:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">表示する投稿数:</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>"
                name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1"
                value="<?php echo $number; ?>" size="3" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : '';
        return $instance;
    }
}

// ウィジェットの登録
function fx_blog_register_widgets()
{
    register_widget('FX_Blog_Recent_Posts_Widget');
}
add_action('widgets_init', 'fx_blog_register_widgets');

// FAQショートコード
function fx_blog_faq_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(array(
        'question' => '',
    ), $atts);

    if (empty($atts['question'])) {
        return '';
    }

    $output = '<details class="faq-item">';
    $output .= '<summary class="faq-question">Q. ' . esc_html($atts['question']) . '</summary>';
    $output .= '<div class="faq-answer">' . wpautop(do_shortcode($content)) . '</div>';
    $output .= '</details>';

    return $output;
}
add_shortcode('faq', 'fx_blog_faq_shortcode');

// FAQセクション全体のラッパーショートコード
function fx_blog_faq_section_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(array(
        'title' => 'よくある質問',
    ), $atts);

    $output = '<section class="faq-section">';
    if (!empty($atts['title'])) {
        $output .= '<h2>' . esc_html($atts['title']) . '</h2>';
    }
    $output .= do_shortcode($content);
    $output .= '</section>';

    return $output;
}
add_shortcode('faq_section', 'fx_blog_faq_section_shortcode');

// ファビコンの設定
function fx_blog_favicon()
{
    $favicon_svg = get_template_directory_uri() . '/favicon.svg';
    $favicon_url = get_template_directory_uri() . '/favicon.ico';
    $favicon_png = get_template_directory_uri() . '/favicon.png';
    $apple_touch_icon = get_template_directory_uri() . '/apple-touch-icon.png';

    // ファビコンが存在するか確認
    $favicon_svg_path = get_template_directory() . '/favicon.svg';
    $favicon_path = get_template_directory() . '/favicon.ico';
    $favicon_png_path = get_template_directory() . '/favicon.png';
    $apple_touch_icon_path = get_template_directory() . '/apple-touch-icon.png';

    // SVG favicon (現代のブラウザ向け)
    if (file_exists($favicon_svg_path)) {
        echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($favicon_svg) . '">' . "\n";
    }

    // favicon.ico (古いブラウザ向け)
    if (file_exists($favicon_path)) {
        echo '<link rel="icon" type="image/x-icon" href="' . esc_url($favicon_url) . '">' . "\n";
    }

    // favicon.png (32x32)
    if (file_exists($favicon_png_path)) {
        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($favicon_png) . '">' . "\n";
    }

    // Apple Touch Icon (180x180)
    if (file_exists($apple_touch_icon_path)) {
        echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url($apple_touch_icon) . '">' . "\n";
    }

    // デフォルトのWordPressファビコンを削除
    remove_action('wp_head', 'wp_site_icon', 99);
}
add_action('wp_head', 'fx_blog_favicon', 1);

// 【緊急修正】レイアウト強制上書き用CSS注入
function fx_blog_inject_critical_css()
{
    echo '<style type="text/css">
        /* PC LAYOUT OVERRIDES (FORCE) */
        @media screen and (min-width: 1000px) {
            /* 0. FORCE PARENTS TO FULL WIDTH */
            html, body, .site, .site-content, .site-main {
                 width: 100% !important;
                 max-width: none !important;
            }

            /* 1. Global Container: Max 1500px */
            .site-main .container,
            .container {
                max-width: 1500px !important;
                width: 100% !important;
                margin: 0 auto !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                box-sizing: border-box !important;
                background: transparent !important;
                border: none !important;
            }
            
            /* Prevent horizontal overflow */
            html, body {
                overflow-x: hidden !important;
            }

            /* 2. Flex Wrapper: Full Width */
            .content-area {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important; /* Spread out */
                align-items: flex-start !important;
                gap: 50px !important;
                width: 100% !important;
                max-width: 1500px !important; /* CHANGED: Cap width at 1500px */
                margin: 0 auto !important;    /* CHANGED: Center it */
                background: transparent !important;
            }
            
            /* Sidebar Widget Adjustments for Widgets Moved Inside Sidebar */
            
            /* Popular Posts Wrapper (PHP Inline Styled) */
            .sidebar .custom-popular-widget-wrapper {
                 width: 100% !important;
                 box-sizing: border-box !important;
                 margin-top: 40px !important;
                 /* PHP inline style handles background/border */
            }

            /* FORCE 1 COLUMN IN SIDEBAR GRID */
            .sidebar .fx-recent-posts-grid {
                width: 100% !important;
                display: grid !important;
                grid-template-columns: 1fr !important; /* 1 column */
                gap: 15px !important;
            }
            
            /* General Widget Spacing in Sidebar */
            .sidebar .widget {
                margin-bottom: 30px !important;
                width: 100% !important;
            }
            
            .sidebar .sidebar-widgets-container {
                width: 100% !important;
                margin-top: 40px;
            }
            
            /* Categories Widget Styling - Premium Dark Buttons */
            .widget_categories {
                background: transparent !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 40px !important;
            }
            .widget_categories .widget-title {
                font-size: 20px !important;
                font-weight: 800 !important;
                color: #333 !important;
                margin-bottom: 25px !important;
                border-left: 5px solid #FFD700 !important;
                padding-left: 15px !important;
                line-height: 1.2 !important;
                border-bottom: none !important; /* Override previous */
            }
            .widget_categories ul {
                list-style: none !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }
            .widget_categories li {
                margin: 0 !important;
                border: none !important;
                padding: 0 !important;
            }
            .widget_categories a {
                display: block !important;
                background: #ffffff !important;
                color: #333 !important;
                padding: 16px 20px !important;
                border-radius: 8px !important;
                text-decoration: none !important;
                font-weight: 600 !important;
                transition: all 0.3s ease !important;
                border: 1px solid #e0e0e0 !important;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05) !important;
                position: relative !important;
                font-size: 15px !important;
            }
            .widget_categories a:hover {
                background: #fff !important;
                border-color: #FFD700 !important;
                color: #DAA520 !important;
                transform: translateX(5px) !important;
                box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2) !important;
            }
            /* Use simple character for arrow to avoid PHP escape issues */
            .widget_categories a::after {
                content: ">" !important;
                position: absolute !important;
                right: 20px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                color: #ccc !important;
                font-weight: bold !important;
                transition: all 0.3s ease !important;
                font-family: sans-serif !important;
            }
            .widget_categories a:hover::after {
                color: #FFD700 !important;
                right: 15px !important;
            }

            /* 3. Main Content: Expand to Fill */
            .main-content {
                flex: 1 1 auto !important;
                width: 100% !important; /* Force full width available */
                max-width: none !important;
                min-width: 0 !important;
                background: transparent !important;
            }

            /* 4. Sidebar: Normal Width */
            .sidebar {
                flex: 0 0 340px !important;
                width: 340px !important;
                max-width: 340px !important;
                min-width: 340px !important;
                margin: 0 !important;
                background: transparent !important;
                border: none !important;
                overflow: visible !important;
            }
            
            /* Widgets inside sidebar */
            .sidebar .sidebar-widgets-container {
                width: 100% !important;
                margin-top: 30px !important;
            }
            
            .sidebar .custom-popular-widget-wrapper {
                width: 100% !important;
                box-sizing: border-box !important;
                margin-bottom: 20px !important;
            }
            
            /* FORCE 1 COLUMN GRID IN SIDEBAR */
            .sidebar .fx-recent-posts-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 15px !important;
                width: 100% !important;
            }
            
            .sidebar .fx-post-item {
                width: 100% !important;
            }
            
            .sidebar .fx-post-thumbnail img {
                width: 100% !important;
                height: auto !important;
            }
        }
        
        /* MOBILE / STEP FIX */
         @media screen and (max-width: 768px) {
            .post-content h2#steps,
            .post-content h2.wp-block-heading#steps,
            .post-content h2[id^="step"] {
                border-left: 10px solid #FFA500 !important;
                padding: 15px 10px 15px 15px !important;
                font-size: 18px !important;
                width: 100% !important;
            }
            .post-content h2[id^="step"]::before, 
            .post-content h2[id^="step"]::after {
                content: none !important; 
                display: none !important;
            }
         }
         
         /* PC specfic fix for #steps summary heading (Prevent Huge Orange Box) */
         .post-content h2#steps,
         .post-content h2.wp-block-heading#steps {
            border-left: 8px solid #FFA500 !important;
            padding: 10px 20px !important;
            margin: 30px 0 !important;
            background: transparent !important;
            width: auto !important;
         }
         .post-content h2#steps::before {
            display: none !important;
         }
    </style>';
}
add_action('wp_head', 'fx_blog_inject_critical_css', 100);


