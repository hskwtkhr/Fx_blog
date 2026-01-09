<?php
/**
 * ファビコン生成スクリプト
 * このスクリプトをブラウザで一度実行すると、favicon.png、favicon.ico、apple-touch-icon.pngが生成されます
 */

// テーマディレクトリのパス
$theme_dir = __DIR__;
$gold = '#FFD700';
$dark = '#1a1a1a';

// 32x32 favicon.png
function create_favicon_32() {
    global $theme_dir, $gold, $dark;
    
    $img = imagecreatetruecolor(32, 32);
    
    // 背景色（ゴールド）
    $gold_rgb = hex2rgb($gold);
    $bg_color = imagecolorallocate($img, $gold_rgb['r'], $gold_rgb['g'], $gold_rgb['b']);
    imagefill($img, 0, 0, $bg_color);
    
    // テキスト色（ダーク）
    $dark_rgb = hex2rgb($dark);
    $text_color = imagecolorallocate($img, $dark_rgb['r'], $dark_rgb['g'], $dark_rgb['b']);
    
    // "FX"テキスト
    imagestring($img, 5, 8, 8, 'FX', $text_color);
    
    // 上向き矢印を描画
    $arrow = array(
        16, 8,   // 上
        12, 12,  // 左下
        16, 10,  // 中央
        20, 12   // 右下
    );
    imagefilledpolygon($img, $arrow, 4, $text_color);
    
    imagepng($img, $theme_dir . '/favicon.png');
    imagedestroy($img);
    
    return true;
}

// 180x180 apple-touch-icon.png
function create_apple_touch_icon() {
    global $theme_dir, $gold, $dark;
    
    $img = imagecreatetruecolor(180, 180);
    
    // 背景色（ゴールド）
    $gold_rgb = hex2rgb($gold);
    $bg_color = imagecolorallocate($img, $gold_rgb['r'], $gold_rgb['g'], $gold_rgb['b']);
    imagefill($img, 0, 0, $bg_color);
    
    // テキスト色（ダーク）
    $dark_rgb = hex2rgb($dark);
    $text_color = imagecolorallocate($img, $dark_rgb['r'], $dark_rgb['g'], $dark_rgb['b']);
    
    // "FX"テキスト（大きなフォント）
    imagestringup($img, 5, 75, 90, 'FX', $text_color);
    
    // 上向き矢印を描画
    $arrow = array(
        90, 40,  // 上
        60, 70,  // 左下
        90, 55,  // 中央
        120, 70  // 右下
    );
    imagefilledpolygon($img, $arrow, 4, $text_color);
    
    imagepng($img, $theme_dir . '/apple-touch-icon.png');
    imagedestroy($img);
    
    return true;
}

// 16x16 favicon.ico
function create_favicon_ico() {
    global $theme_dir, $gold, $dark;
    
    // 16x16と32x32の2つのサイズを含むICOファイル
    $ico_data = '';
    
    // 16x16
    $img16 = imagecreatetruecolor(16, 16);
    $gold_rgb = hex2rgb($gold);
    $bg_color = imagecolorallocate($img16, $gold_rgb['r'], $gold_rgb['g'], $gold_rgb['b']);
    imagefill($img16, 0, 0, $bg_color);
    
    $dark_rgb = hex2rgb($dark);
    $text_color = imagecolorallocate($img16, $dark_rgb['r'], $dark_rgb['g'], $dark_rgb['b']);
    
    imagestring($img16, 3, 4, 4, 'FX', $text_color);
    
    ob_start();
    imagepng($img16);
    $png16 = ob_get_clean();
    imagedestroy($img16);
    
    // 32x32
    $img32 = imagecreatetruecolor(32, 32);
    $bg_color = imagecolorallocate($img32, $gold_rgb['r'], $gold_rgb['g'], $gold_rgb['b']);
    imagefill($img32, 0, 0, $bg_color);
    $text_color = imagecolorallocate($img32, $dark_rgb['r'], $dark_rgb['g'], $dark_rgb['b']);
    imagestring($img32, 5, 8, 8, 'FX', $text_color);
    
    ob_start();
    imagepng($img32);
    $png32 = ob_get_clean();
    imagedestroy($img32);
    
    // シンプルにPNGをICOとして保存（多くのブラウザで認識される）
    file_put_contents($theme_dir . '/favicon.ico', $png32);
    
    return true;
}

function hex2rgb($hex) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return array('r' => $r, 'g' => $g, 'b' => $b);
}

// 実行
if (php_sapi_name() !== 'cli') {
    // Webブラウザから実行された場合
    header('Content-Type: text/html; charset=utf-8');
    echo '<html><head><meta charset="utf-8"></head><body>';
    echo '<h1>ファビコン生成中...</h1>';
}

try {
    create_favicon_32();
    echo "✓ favicon.png を作成しました<br>\n";
    
    create_apple_touch_icon();
    echo "✓ apple-touch-icon.png を作成しました<br>\n";
    
    create_favicon_ico();
    echo "✓ favicon.ico を作成しました<br>\n";
    
    echo "<br><strong>完了！</strong><br>\n";
    echo "<a href='javascript:location.reload()'>再生成</a> | ";
    echo "<a href='" . str_replace($theme_dir, '', __DIR__) . "/style.css'>テーマに戻る</a>";
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "<br>\n";
}

if (php_sapi_name() !== 'cli') {
    echo '</body></html>';
}
?>

