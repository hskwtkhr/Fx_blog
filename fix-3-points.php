<?php
require_once("/var/www/html/wp-load.php");

$post_id = 15;
$post = get_post($post_id);

if (!$post) {
    die("記事が見つかりませんでした\n");
}

echo "記事ID: " . $post_id . "\n";

$content = $post->post_content;

// 「初心者が業者選びで重視すべき3つのポイント」のセクションを探す
// 3つのh3タグ（①、②、③で始まる）を含む範囲を特定

// まず、該当セクションの開始位置を探す
$start_marker = "初心者が業者選び";
$start_pos = strpos($content, $start_marker);

if ($start_pos === false) {
    die("該当セクションが見つかりませんでした\n");
}

echo "セクションの開始位置: " . $start_pos . "\n";

// セクションの前後200文字を表示して確認
echo "\nセクション内容（一部）:\n";
echo substr($content, max(0, $start_pos - 100), 500) . "...\n";
