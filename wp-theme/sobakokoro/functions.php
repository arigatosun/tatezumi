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
 * 公開前は検索に載せない。
 *
 * wp_head に直接書き出すと WordPress 側の robots タグと二重になるので、
 * 既存のタグに noindex を足す形にしている。
 * 公開するときは、この関数と add_filter の2行を消す。
 */
function sobakokoro_noindex(array $robots): array
{
    $robots['noindex'] = true;
    $robots['nofollow'] = true;

    return $robots;
}
add_filter('wp_robots', 'sobakokoro_noindex');

/**
 * 検索結果に出る説明文。
 *
 * WordPress は description を自動では出さないので、ページごとに用意する。
 * 店名は「店舗情報」の設定から取るので、店名を変えれば説明文も追従する。
 */
function sobakokoro_meta_description(): void
{
    $name = sobakokoro_shop('shop_name');
    $desc = '';

    if (is_front_page()) {
        $desc = sprintf(
            '%s。島根県三瓶在来種のそば粉を使った、朝打ち自家製麺のお店です。%s。営業時間・お品書き・アクセスはこちら。',
            $name,
            sobakokoro_shop('place')
        );
    } elseif (is_page('menu')) {
        $desc = sprintf(
            '%sのお品書き。温かいお蕎麦・冷たいお蕎麦・ミニ丼・ちょっと一品・お飲み物を、写真と価格つきでご案内します（表示はすべて税込）。',
            $name
        );
    } elseif (is_page('access')) {
        $desc = sprintf(
            '%sへのアクセスと営業時間。%s（%s）、定休 %s。%s。地図と経路案内はこちら。',
            $name,
            sobakokoro_shop('hours'),
            sobakokoro_shop('lo'),
            sobakokoro_shop('closed'),
            sobakokoro_shop('place')
        );
    }

    if ($desc !== '') {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
}
add_action('wp_head', 'sobakokoro_meta_description', 2);
