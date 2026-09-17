<?php
/**
 * そばこころ テーマの共通設定
 *
 * 画面ごとのテンプレートは front-page.php / page-menu.php / page-access.php にある。
 * お品書きの投稿タイプと店舗情報の管理画面は inc/ 以下に分けてある。
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SOBAKOKORO_VERSION', '1.0.0');

require_once get_template_directory() . '/inc/menu-item.php';
require_once get_template_directory() . '/inc/menu-item-fields.php';
require_once get_template_directory() . '/inc/shop-info.php';

/**
 * テーマがサポートする機能。
 */
function sobakokoro_setup(): void
{
    load_theme_textdomain('sobakokoro', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // 暖簾の「そばこころ」の字が届いたら、外観 > カスタマイズ > サイト基本情報 から登録する。
    // 未登録のあいだはヘッダーに文字で店名を出す
    add_theme_support('custom-logo', [
        'height'      => 120,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // 料理写真は 4:3 で使う。原本の縦横比がずれていても自動で合わせる
    add_image_size('sobakokoro-dish', 1200, 900, true);
    // トップのヒーロー用（表示時にさらに上下が切られる前提の横長）
    add_image_size('sobakokoro-hero', 1800, 771, true);
}
add_action('after_setup_theme', 'sobakokoro_setup');

/**
 * 固定ページのURLをスラッグから引く。
 * ページがまだ無いときは空文字を返し、テンプレート側でリンクごと出さない。
 */
function sobakokoro_page_url(string $slug): string
{
    $page = get_page_by_path($slug);

    return $page instanceof WP_Post ? (string) get_permalink($page) : '';
}

/**
 * スタイルとフォントの読み込み。
 *
 * 静的HTML版と同じ構成（base.css → theme.css の順）を保つ。
 * ファイル更新日をバージョンに使うので、CSSを直せばキャッシュは自動で切り替わる。
 */
function sobakokoro_assets(): void
{
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style(
        'sobakokoro-fonts',
        'https://fonts.googleapis.com/css2?family=Shippori+Mincho+B1:wght@500;600;800&family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'sobakokoro-base',
        $uri . '/assets/css/base.css',
        ['sobakokoro-fonts'],
        (string) filemtime($dir . '/assets/css/base.css')
    );

    wp_enqueue_style(
        'sobakokoro-theme',
        $uri . '/assets/css/theme.css',
        ['sobakokoro-base'],
        (string) filemtime($dir . '/assets/css/theme.css')
    );

    // WordPress が既定で入れる style.css（テーマ情報のみ）は読ませない
    wp_deregister_style('sobakokoro-style');

    // トップのヒーローを切り替えるスクリプト（トップページだけで読む）
    if (is_front_page()) {
        wp_enqueue_script(
            'sobakokoro-hero',
            $uri . '/assets/js/hero-slider.js',
            [],
            (string) filemtime($dir . '/assets/js/hero-slider.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'sobakokoro_assets');

/**
 * Google Fonts への接続を先に張っておく（静的HTML版の preconnect 相当）。
 */
function sobakokoro_resource_hints(array $urls, string $relation): array
{
    if ($relation === 'preconnect') {
        $urls[] = ['href' => 'https://fonts.googleapis.com'];
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => ''];
    }
    return $urls;
}
add_filter('wp_resource_hints', 'sobakokoro_resource_hints', 10, 2);

/**
 * 公開前は検索に載せない。公開する時にこの関数ごと外す。
 */
function sobakokoro_noindex(): void
{
    echo '<meta name="robots" content="noindex,nofollow">' . "\n";
}
add_action('wp_head', 'sobakokoro_noindex', 1);
