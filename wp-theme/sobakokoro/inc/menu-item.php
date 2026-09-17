<?php
/**
 * お品書き（menu_item）の投稿タイプと区分（menu_cat）
 *
 * 1品につき1投稿。写真はアイキャッチで登録する。
 * 「写真のある品はカード、ない品は価格表」という出し分けは、
 * アイキャッチの有無をテンプレート側で見て自動で行う。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * お品書きの区分と、その表示順。
 *
 * ここの並び順がそのままページの並び順になる。
 * en   … 見出しの右に出る英字ラベル
 * rest … 写真付きカードの下に置く価格表の小見出し
 * nav  … ページ上部のジャンプリンクに出す短い名前
 */
function sobakokoro_menu_sections(): array
{
    return [
        'hot'      => ['name' => '温かいお蕎麦',       'en' => 'HOT',      'nav' => '温かいお蕎麦',   'rest' => 'ほかの温かいお蕎麦'],
        'cold'     => ['name' => '冷たいお蕎麦',       'en' => 'COLD',     'nav' => '冷たいお蕎麦',   'rest' => 'ほかの冷たいお蕎麦'],
        'topping'  => ['name' => 'トッピング・大盛り', 'en' => 'TOPPING',  'nav' => 'トッピング',     'rest' => 'ほかのトッピング'],
        'donburi'  => ['name' => 'ミニ丼',             'en' => 'DONBURI',  'nav' => 'ミニ丼',         'rest' => 'ほかのミニ丼'],
        'side'     => ['name' => 'ちょっと一品',       'en' => 'SIDE',     'nav' => 'ちょっと一品',   'rest' => 'ほかの一品'],
        'drink'    => ['name' => 'お飲み物',           'en' => 'DRINK',    'nav' => 'お飲み物',       'rest' => 'ほかのお飲み物'],
        'seasonal' => ['name' => '季節のおすすめ',     'en' => 'SEASONAL', 'nav' => '季節のおすすめ', 'rest' => 'ほかの季節のおすすめ'],
    ];
}

/**
 * 投稿タイプと区分タクソノミーの登録。
 */
function sobakokoro_register_menu_item(): void
{
    register_post_type('menu_item', [
        'label'         => 'お品書き',
        'labels'        => [
            'name'               => 'お品書き',
            'singular_name'      => 'お品',
            'add_new'            => '新しいお品を追加',
            'add_new_item'       => 'お品を追加',
            'edit_item'          => 'お品を編集',
            'new_item'           => '新しいお品',
            'view_item'          => 'お品を表示',
            'search_items'       => 'お品を検索',
            'not_found'          => 'お品が見つかりません',
            'not_found_in_trash' => 'ゴミ箱にお品はありません',
            'all_items'          => 'お品書き一覧',
            'menu_name'          => 'お品書き',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'show_in_rest'  => false,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-food',
        'supports'      => ['title', 'thumbnail', 'page-attributes'],
        'has_archive'   => false,
        'rewrite'       => false,
        'hierarchical'  => false,
    ]);

    register_taxonomy('menu_cat', ['menu_item'], [
        'label'             => '区分',
        'labels'            => [
            'name'          => '区分',
            'singular_name' => '区分',
            'add_new_item'  => '区分を追加',
            'edit_item'     => '区分を編集',
            'all_items'     => '区分一覧',
            'menu_name'     => '区分',
        ],
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
        'hierarchical'      => true,
        'rewrite'           => false,
    ]);
}
add_action('init', 'sobakokoro_register_menu_item');

/**
 * テーマを有効化したときに、区分の初期データを入れておく。
 * 店主に区分を手で作らせないための処理。
 */
function sobakokoro_seed_menu_cats(): void
{
    foreach (sobakokoro_menu_sections() as $slug => $section) {
        if (!term_exists($slug, 'menu_cat')) {
            wp_insert_term($section['name'], 'menu_cat', ['slug' => $slug]);
        }
    }
}
add_action('after_switch_theme', 'sobakokoro_seed_menu_cats');

/**
 * 指定した区分のお品を、並び順どおりに取得する。
 *
 * @param string $slug      区分のスラッグ（hot / cold など）
 * @param bool   $with_photo true: 写真あり / false: 写真なし / null: すべて
 * @return WP_Post[]
 */
function sobakokoro_get_menu_items(string $slug, ?bool $with_photo = null): array
{
    $posts = get_posts([
        'post_type'      => 'menu_item',
        'posts_per_page' => -1,
        'orderby'        => ['menu_order' => 'ASC', 'date' => 'ASC'],
        'tax_query'      => [[
            'taxonomy' => 'menu_cat',
            'field'    => 'slug',
            'terms'    => $slug,
        ]],
    ]);

    if ($with_photo === null) {
        return $posts;
    }

    return array_values(array_filter($posts, static function (WP_Post $post) use ($with_photo): bool {
        return has_post_thumbnail($post) === $with_photo;
    }));
}

/**
 * 価格の表示文字列。「550円」「+300円」「無料」など、入力された値をそのまま出す。
 */
function sobakokoro_price(int $post_id): string
{
    return (string) get_post_meta($post_id, '_sobakokoro_price', true);
}

/**
 * 品名の下に出る小さな注記。
 */
function sobakokoro_note(int $post_id): string
{
    return (string) get_post_meta($post_id, '_sobakokoro_note', true);
}

/**
 * 「数量限定」「お持ち帰り可」などのタグ。
 */
function sobakokoro_badge(int $post_id): string
{
    return (string) get_post_meta($post_id, '_sobakokoro_badge', true);
}
