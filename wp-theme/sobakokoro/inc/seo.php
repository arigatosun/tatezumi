<?php
/**
 * SNS で共有されたときの表示（OGP）・検索エンジン向けの店舗情報・ファビコン
 *
 * 店名・住所・電話は「店舗情報」の設定から取るので、そこを直せばここにも反映される。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 共有されたときに出す画像。
 * ページにアイキャッチがあればそれを、なければトップのそば畑の写真を使う。
 *
 * @return array{url: string, width: int, height: int}
 */
function sobakokoro_ogp_image(): array
{
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        if ($img) {
            return ['url' => $img[0], 'width' => (int) $img[1], 'height' => (int) $img[2]];
        }
    }

    return [
        'url'    => get_template_directory_uri() . '/assets/img/ogp.jpg',
        'width'  => 1200,
        'height' => 630,
    ];
}

/**
 * 今表示しているページの URL。
 */
function sobakokoro_current_url(): string
{
    if (is_front_page()) {
        return home_url('/');
    }

    if (is_singular()) {
        return (string) get_permalink();
    }

    return home_url(add_query_arg([]));
}

function sobakokoro_ogp(): void
{
    $desc = sobakokoro_description();

    if ($desc === '' && is_singular()) {
        $desc = wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 60, '…');
    }

    $image = sobakokoro_ogp_image();

    $tags = [
        'og:locale'       => 'ja_JP',
        'og:type'         => is_front_page() ? 'website' : 'article',
        'og:site_name'    => sobakokoro_shop('shop_name'),
        'og:title'        => wp_get_document_title(),
        'og:description'  => $desc,
        'og:url'          => sobakokoro_current_url(),
        'og:image'        => $image['url'],
        'og:image:width'  => (string) $image['width'],
        'og:image:height' => (string) $image['height'],
    ];

    foreach ($tags as $property => $content) {
        if ($content !== '') {
            printf('<meta property="%s" content="%s">' . "\n", esc_attr($property), esc_attr($content));
        }
    }

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action('wp_head', 'sobakokoro_ogp', 3);

/**
 * Google に店舗の情報を伝える構造化データ（schema.org の Restaurant）。
 *
 * 営業時間は入れていない。定休日が「月曜（月2〜3回）」で、
 * 開いている月曜と閉まっている月曜を表せないため。
 * 営業時間は Google ビジネスプロフィールで管理する。
 */
function sobakokoro_structured_data(): void
{
    if (!is_front_page()) {
        return;
    }

    $data = [
        '@context'       => 'https://schema.org',
        '@type'          => 'Restaurant',
        'name'           => sobakokoro_shop('shop_name'),
        'url'            => home_url('/'),
        'image'          => get_template_directory_uri() . '/assets/img/ogp.jpg',
        'telephone'      => sobakokoro_shop('tel'),
        'servesCuisine'  => 'そば',
        'hasMenu'        => sobakokoro_page_url('menu'),
        'address'        => [
            '@type'           => 'PostalAddress',
            'postalCode'      => trim(str_replace('〒', '', sobakokoro_shop('postal'))),
            'streetAddress'   => sobakokoro_shop('address'),
            'addressCountry'  => 'JP',
        ],
    ];

    if ($data['hasMenu'] === '') {
        unset($data['hasMenu']);
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)
        . '</script>' . "\n";
}
add_action('wp_head', 'sobakokoro_structured_data', 4);

/**
 * 仮のファビコン（若草色の地に「そ」）。
 * 外観 > カスタマイズ > サイト基本情報 でサイトアイコンを登録すると、そちらが優先される。
 */
function sobakokoro_favicon(): void
{
    if (has_site_icon()) {
        return;
    }

    $dir = get_template_directory_uri() . '/assets/img/';

    printf('<link rel="icon" href="%s" sizes="32x32">' . "\n", esc_url($dir . 'favicon-32.png'));
    printf('<link rel="icon" href="%s" sizes="192x192">' . "\n", esc_url($dir . 'favicon-192.png'));
    printf('<link rel="apple-touch-icon" href="%s">' . "\n", esc_url($dir . 'favicon-180.png'));
}
add_action('wp_head', 'sobakokoro_favicon', 5);
add_action('admin_head', 'sobakokoro_favicon');
