<?php
/**
 * The main template file
 */

get_header();
?>

<main class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">
                <?php if (have_posts()): ?>
                        <?php
                        $post_count = 0;
                        while (have_posts()):
                            the_post();
                            $post_count++;
                            if ($post_count > 1)
                                break; // Display only the latest post
                            ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                            <header class="post-header">
                                <h1 class="post-title"><a href="<?php the_permalink(); ?>"
                                        style="color: inherit; text-decoration: none;"><?php the_title(); ?></a></h1>
                                <div class="post-meta">
                                    <span><?php echo get_the_date(); ?></span>
                                </div>
                            </header>

                            <div class="post-content">
                                <?php the_content(); ?>
                            </div>
                        </article>
                    <?php endwhile; ?>

                    <?php
                    // 関連記事セクション（同じカテゴリの記事を表示）
                    $categories = get_the_category();
                    if ($categories) {
                        $category_ids = array();
                        foreach ($categories as $category) {
                            $category_ids[] = $category->term_id;
                        }

                        $related_args = array(
                            'category__in' => $category_ids,
                            'post__not_in' => array(get_the_ID()),
                            'posts_per_page' => 8,
                            'orderby' => 'rand',
                            'post_status' => 'publish',
                        );

                        $related_query = new WP_Query($related_args);

                        if ($related_query->have_posts()):
                        ?>
                        <section class="related-posts-section">
                            <h2 class="related-posts-title">📚 関連記事</h2>
                            <div class="related-posts-grid">
                                <?php while ($related_query->have_posts()):
                                    $related_query->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>" class="related-post-card">
                                        <div class="related-post-thumbnail">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('medium'); ?>
                                            <?php else: ?>
                                                <div class="no-thumbnail">No Image</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-post-title"><?php the_title(); ?></div>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </section>
                        <?php
                        endif;
                        wp_reset_postdata();
                    }
                    ?>

                    <!-- Pagination for older posts -->
                    <div class="posts-navigation" style="margin-top: 40px; text-align: center;">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '&laquo; 新しい記事',
                            'next_text' => '過去の記事 &raquo;',
                            'screen_reader_text' => ' ',
                        ));
                        ?>
                        </div>
                <?php else: ?>
                        <p>記事が見つかりませんでした。</p>
                <?php endif; ?>
            </div>

            <aside class="sidebar">
                <div id="table-of-contents" class="toc-widget">
                    <h3 class="toc-title">目次</h3>
                    <nav id="toc-nav" class="toc-nav"></nav>
                </div>

                <!-- Moved Widgets Inside Sidebar -->
                <div class="sidebar-widgets-container">
                    <?php fx_blog_render_below_widgets(); ?>
                </div>
            </aside>
        </div>
    </div>
</main>

<style>
    /* 最新記事セクション - PC横並び4つ */
    .related-posts-section {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 2px solid #eee;
    }

    .related-posts-title {
        font-size: 22px;
        font-weight: 800;
        color: #1a1a2e;
        margin-bottom: 24px;
        padding-left: 12px;
        border-left: 5px solid #FFD700;
    }

    .related-posts-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .related-post-card {
        display: block;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .related-post-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .related-post-thumbnail {
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    }

    .related-post-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .related-post-thumbnail .no-thumbnail {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 11px;
    }

    .related-post-title {
        padding: 14px 12px;
        font-size: 13px;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1.5;
        text-align: center;
    }

    .related-post-card:hover .related-post-title {
        color: #22c55e;
    }

    /* Tablet (2 columns) */
    @media (max-width: 900px) {
        .related-posts-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Mobile (2 columns, smaller) */
    @media (max-width: 600px) {
        .related-posts-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .related-post-title {
            font-size: 12px;
            padding: 10px;
        }

        .related-posts-title {
            font-size: 18px;
        }
    }
</style>

<?php
get_footer();

