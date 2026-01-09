<?php
/**
 * WordPress投稿をインポートするためのスクリプト
 * 
 * 使用方法：
 * 1. WordPressにログインした状態で、以下にアクセス：
 *    http://localhost:9000/wp-content/themes/fx-blog/import-post.php
 * 2. 記事が自動的にインポートされます
 * 3. インポート後、このファイルを削除してください（セキュリティのため）
 */

// エラー表示を有効化（デバッグ用）
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    die('エラー: wp-load.php が見つかりませんでした。WordPressが正しくインストールされているか確認してください。');
}

// CLI実行の許可
if (defined('WP_CLI') && WP_CLI) {
    // WP-CLIからの実行時は認証チェックをスキップ
} elseif (!function_exists('current_user_can') || !current_user_can('administrator')) {
    die('エラー: このスクリプトは管理者のみ実行可能です。WordPressにログインしてください。');
}

// HTMLファイルのパス
// CLI実行時は絶対パスまたは相対パスを柔軟に扱う
$html_file = '/var/www/html/wp-content/国内業者VS海外業者編.html'; // Docker内での配置場所を想定

if (!file_exists($html_file)) {
    // 開発環境用のパスも試行
    $html_file = __DIR__ . '/../../../国内業者VS海外業者編.html';
}

if (!file_exists($html_file)) {
    die('エラー: HTMLファイルが見つかりません: ' . htmlspecialchars($html_file));
}

$html_content = file_get_contents($html_file);

if ($html_content === false) {
    die('エラー: HTMLファイルを読み込めませんでした。');
}

// コメント行を削除
$html_content = preg_replace('/<!--.*?-->/s', '', $html_content);

// タイトルを抽出（最初のh1タグ）
preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $html_content, $title_matches);
$post_title = !empty($title_matches[1]) ? strip_tags($title_matches[1]) : '海外FXのおすすめ比較ランキング！国内FXとの違いを徹底解説';

// h1タグを画像に置き換え（無効化：特定の記事のみ手動で設定）
// $image_tag = '<img src="/wp-content/uploads/2025/11/header-title.png" alt="' . esc_attr($post_title) . '" style="width: 100%; height: auto; display: block; margin-bottom: 20px;">';
// $html_content = preg_replace('/<h1[^>]*>.*?<\/h1>/i', $image_tag, $html_content);


// 既存の投稿を検索（最新の投稿を取得）
$existing_post = null;
$all_posts = get_posts(array(
    'post_type' => 'post',
    'posts_per_page' => 10,
    'post_status' => 'any',
    'orderby' => 'date',
    'order' => 'DESC',
));

// タイトルが一致する投稿を探す
foreach ($all_posts as $post) {
    if (trim($post->post_title) === trim($post_title)) {
        $existing_post = $post;
        break;
    }
}

// 見つからない場合は、最新の1件を取得
if (!$existing_post && !empty($all_posts)) {
    $existing_post = $all_posts[0];
}

// 投稿データ
$post_data = array(
    'post_title'    => $post_title,
    'post_content'  => $html_content,
    'post_status'   => 'publish',
    'post_type'     => 'post',
    'post_author'   => 1,
);

if ($existing_post) {
    // 既存の投稿を更新
    $post_data['ID'] = $existing_post->ID;
    $post_id = wp_update_post($post_data, true);
    
    if (is_wp_error($post_id)) {
        die('エラー: ' . $post_id->get_error_message());
    }
    $message = '投稿を更新しました（ID: ' . $post_id . '、タイトル: ' . esc_html($post_title) . '）。';
    $edit_url = admin_url('post.php?action=edit&post=' . $post_id);
} else {
    // 新規投稿を作成
    $post_id = wp_insert_post($post_data, true);
    
    if (is_wp_error($post_id)) {
        die('エラー: ' . $post_id->get_error_message());
    }
    $message = '投稿を作成しました（ID: ' . $post_id . '、タイトル: ' . esc_html($post_title) . '）。';
    $edit_url = admin_url('post.php?action=edit&post=' . $post_id);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>投稿インポート完了</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f5f5f5; }
        .success { background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 3px; }
        .button:hover { background: #005177; }
        .error { color: #d63638; }
    </style>
</head>
<body>
    <div class="success">
        <h1>✓ 投稿インポート完了</h1>
        <p><?php echo esc_html($message); ?></p>
        <p><a href="<?php echo esc_url($edit_url); ?>" class="button">投稿を編集する</a></p>
        <p><a href="<?php echo esc_url(admin_url('edit.php')); ?>" class="button">投稿一覧に戻る</a></p>
        <p><a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="button" target="_blank">記事を表示する</a></p>
        <hr style="margin: 30px 0;">
        <p style="color: #666; font-size: 14px;">※ セキュリティのため、このファイル（import-post.php）を削除してください。</p>
    </div>
</body>
</html>
