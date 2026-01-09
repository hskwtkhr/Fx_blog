<?php
/**
 * 固定ページのテンプレート
 */

get_header();
?>

<main class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                        <header class="post-header">
                            <h1 class="post-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="post-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <aside class="sidebar">
                <div id="table-of-contents" class="toc-widget">
                    <h3 class="toc-title">目次</h3>
                    <nav id="toc-nav" class="toc-nav"></nav>
                </div>
                <?php get_sidebar(); ?>
            </aside>
        </div>
    </div>
</main>

<?php
get_footer();

