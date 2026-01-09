<?php
/**
 * タイトルを画像に置き換えるスクリプト
 */

// WordPressの読み込み
$wp_load_paths = array(
    __DIR__ . '/../../../../wp-load.php',
    __DIR__ . '/../../../../../wp-load.php',
    dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php',
);

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('エラー: wp-load.php が見つかりませんでした。');
}

// 投稿ID 1を取得
$post_id = 1;
$post = get_post($post_id);

if (!$post) {
    die('エラー: 投稿が見つかりません。');
}

// 現在のコンテンツを取得
$content = $post->post_content;

// h1タグを画像タグに置き換え
$old_title = '<h2>海外FXのおすすめ比較ランキング！国内FXとの違いを徹底解説</h2>';
$new_image = '<img src="/wp-content/uploads/2025/11/header-title.png" alt="海外FXのおすすめ比較ランキング！国内FXとの違いを徹底解説" style="width: 100%; height: auto; display: block; margin-bottom: 20px;">';

$new_content = str_replace($old_title, $new_image, $content);

// 更新
$result = wp_update_post(array(
    'ID' => $post_id,
    'post_content' => $new_content,
));

if (is_wp_error($result)) {
    die('エラー: ' . $result->get_error_message());
}

echo "成功: 投稿ID {$post_id} のタイトルを画像に置き換えました。\n";
echo "URL: " . get_permalink($post_id) . "\n";
