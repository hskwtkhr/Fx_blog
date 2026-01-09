<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-column footer-about">
                <h3 class="footer-title"><?php bloginfo('name'); ?></h3>
                <p class="footer-description"><?php bloginfo('description'); ?></p>
                <p class="footer-tagline">常識を逆転する。人生を逆転する。FXで逆転する。</p>
            </div>
            
            <div class="footer-column footer-links">
                <h3 class="footer-title">主要記事</h3>
                <ul class="footer-menu">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a></li>
                    <?php
                    // おすすめ記事を取得（最近の投稿）
                    $recent_posts = wp_get_recent_posts(array(
                        'numberposts' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    foreach ($recent_posts as $post_item) {
                        echo '<li><a href="' . esc_url(get_permalink($post_item['ID'])) . '">' . esc_html($post_item['post_title']) . '</a></li>';
                    }
                    wp_reset_query();
                    ?>
                </ul>
            </div>
            
            <div class="footer-column footer-nav">
                <h3 class="footer-title">ナビゲーション</h3>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'footer-menu',
                    'fallback_cb' => false,
                ));
                ?>
                <?php if (!has_nav_menu('primary')) : ?>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog')); ?>">ブログ</a></li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p class="copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
