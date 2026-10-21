<?php
/**
 * 登録済みのお品書きを、店内のメニューブック（2026-10-21 受領）に合わせる
 *
 * seed.php は登録済みの品を飛ばすため、価格の変更はこちらで反映する。
 * 実行方法:
 *   wp eval-file wp-content/themes/sobakokoro/seed/update-menu-2026-10.php
 *
 * 何度実行しても同じ結果になる。消す品はゴミ箱に移すだけなので、管理画面から戻せる。
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "WordPress の外からは実行できません。wp eval-file を使ってください。\n");
    exit(1);
}

function sobakokoro_update_find_item(string $title): ?WP_Post
{
    $found = get_posts([
        'post_type'      => 'menu_item',
        'title'          => $title,
        'posts_per_page' => 1,
        'post_status'    => ['publish', 'draft', 'pending', 'private'],
    ]);

    return $found[0] ?? null;
}

function sobakokoro_update_meta(int $post_id, string $key, string $value): void
{
    if ($value === '') {
        delete_post_meta($post_id, $key);
    } else {
        update_post_meta($post_id, $key, $value);
    }
}

// ---- 1. 区分名「ミニ丼」→「ご飯もの」 -------------------------------------

$term = get_term_by('slug', 'donburi', 'menu_cat');
if ($term instanceof WP_Term && $term->name !== 'ご飯もの') {
    wp_update_term($term->term_id, 'menu_cat', ['name' => 'ご飯もの']);
    WP_CLI::log('区分名を「ご飯もの」に変えました');
}

// ---- 2. 品名の変更 ----------------------------------------------------------

$renames = [
    '鴨南蛮'     => 'かも南蛮',
    '天丼'       => 'ミニ天丼',
    '牛丼'       => 'ミニ牛丼',
    'ネギトロ丼' => 'ミニネギトロ丼',
    '揚出し豆腐' => '揚げ出し豆腐',
];

foreach ($renames as $old => $new) {
    $post = sobakokoro_update_find_item($old);
    if ($post === null || sobakokoro_update_find_item($new) !== null) {
        continue;
    }
    wp_update_post(['ID' => $post->ID, 'post_title' => $new]);
    WP_CLI::log("品名を変えました: {$old} → {$new}");
}

// ---- 3. メニューブックにない品をゴミ箱へ ------------------------------------

foreach (['海老天ぶっかけ', '梅おろしそば（冷）'] as $title) {
    $post = sobakokoro_update_find_item($title);
    if ($post !== null) {
        wp_trash_post($post->ID);
        WP_CLI::log("ゴミ箱に移しました: {$title}");
    }
}

// ---- 4. 価格・注記・タグ・並び順をデータに合わせる ---------------------------

$items   = require __DIR__ . '/menu-data.php';
$created = 0;
$updated = 0;

foreach ($items as $i => $row) {
    $post = sobakokoro_update_find_item($row['title']);

    if ($post === null) {
        $post_id = wp_insert_post([
            'post_type'   => 'menu_item',
            'post_title'  => $row['title'],
            'post_status' => 'publish',
            'menu_order'  => ($i + 1) * 10,
        ]);
        if (is_wp_error($post_id) || $post_id === 0) {
            WP_CLI::warning("登録できません: {$row['title']}");
            continue;
        }
        $created++;
        WP_CLI::log("追加しました: {$row['title']}");
    } else {
        $post_id = $post->ID;
        wp_update_post(['ID' => $post_id, 'menu_order' => ($i + 1) * 10]);
        $updated++;
    }

    wp_set_object_terms($post_id, $row['cat'], 'menu_cat');
    sobakokoro_update_meta($post_id, '_sobakokoro_price', $row['price']);
    sobakokoro_update_meta($post_id, '_sobakokoro_note', $row['note'] ?? '');
    sobakokoro_update_meta($post_id, '_sobakokoro_badge', $row['badge'] ?? '');

    if (!empty($row['photo']) && !has_post_thumbnail($post_id)) {
        WP_CLI::warning("写真が未登録です（管理画面でアイキャッチを設定してください）: {$row['title']}");
    }
}

// ---- 5. お品書きページの注意書きにセット割引を足す ----------------------------

$menu_page = get_page_by_path('menu');
$set_note  = '<li>各種そばと丼ぶりのセットで50円引きになります。</li>';
if ($menu_page instanceof WP_Post && !str_contains($menu_page->post_content, '50円引き')) {
    $content = str_contains($menu_page->post_content, '</ul>')
        ? preg_replace('#</ul>(?!.*</ul>)#s', $set_note . "\n</ul>", $menu_page->post_content, 1)
        : $menu_page->post_content . "\n<ul>\n{$set_note}\n</ul>";
    wp_update_post(['ID' => $menu_page->ID, 'post_content' => $content]);
    WP_CLI::log('お品書きページの注意書きにセット割引を足しました');
}

WP_CLI::success("お品書き: {$created}件を追加、{$updated}件を更新しました");
