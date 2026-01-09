<?php
/**
 * アーカイブページのテンプレート（サムネイル付き）
 */

get_header();
?>

<main class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">
                <header class="archive-header">
                    <?php
                    the_archive_title('<h1 class="archive-title">', '</h1>');
                    the_archive_description('<div class="archive-description">', '</div>');
                    ?>
                </header>

                <?php if (have_posts()) : ?>
                    <div class="archive-posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('archive-post-card'); ?>>
                            <a href="<?php the_permalink(); ?>" class="archive-post-link">
                                <div class="archive-post-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <div class="no-thumbnail">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="archive-post-content">
                                    <h2 class="archive-post-title"><?php the_title(); ?></h2>
                                    <div class="archive-post-meta">
                                        <span class="archive-post-date">📅 <?php echo get_the_date(); ?></span>
                                    </div>
                                    <div class="archive-post-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 40, '...'); ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                    </div>

                    <?php
                    // ページネーション
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← 前へ',
                        'next_text' => '次へ →',
                    ));
                    ?>
                <?php else : ?>
                    <p>記事が見つかりませんでした。</p>
                <?php endif; ?>
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<style>
/* Archive Posts Grid - サムネイル付きカードレイアウト */
.archive-posts-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.archive-post-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}

.archive-post-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.archive-post-link {
    display: flex;
    text-decoration: none;
    color: inherit;
}

.archive-post-thumbnail {
    flex-shrink: 0;
    width: 180px;
    height: 120px;
    overflow: hidden;
    background: #f0f0f0;
}

.archive-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.archive-post-thumbnail .no-thumbnail {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 12px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: #666;
}

.archive-post-content {
    flex: 1;
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.archive-post-title {
    font-size: 17px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 8px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.archive-post-card:hover .archive-post-title {
    color: #22c55e;
}

.archive-post-meta {
    font-size: 12px;
    color: #888;
    margin-bottom: 8px;
}

.archive-post-excerpt {
    font-size: 13px;
    color: #666;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile Responsive */
@media (max-width: 600px) {
    .archive-post-link {
        flex-direction: column;
    }
    
    .archive-post-thumbnail {
        width: 100%;
        height: 180px;
    }
    
    .archive-post-content {
        padding: 16px;
    }
    
    .archive-post-title {
        font-size: 16px;
    }
}
</style>

<?php
get_footer();
