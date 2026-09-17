<?php
/**
 * 初期データの投入（1回だけ実行する）
 *
 * 実行方法（Local のサイトフォルダで）:
 *   wp eval-file wp-content/themes/sobakokoro/seed/seed.php
 *
 * 何度実行しても同じ結果になるよう、既にあるものは飛ばす。
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "WordPress の外からは実行できません。wp eval-file を使ってください。\n");
    exit(1);
}

/**
 * テーマの画像をメディアライブラリに登録する。登録済みならそのIDを返す。
 */
function sobakokoro_seed_attachment(string $filename): int
{
    $slug = sanitize_title(pathinfo($filename, PATHINFO_FILENAME));

    $existing = get_posts([
        'post_type'      => 'attachment',
        'name'           => $slug,
        'posts_per_page' => 1,
        'post_status'    => 'inherit',
    ]);
    if ($existing !== []) {
        return (int) $existing[0]->ID;
    }

    $src = get_template_directory() . '/assets/img/photo/' . $filename;
    if (!file_exists($src)) {
        WP_CLI::warning("画像が見つかりません: {$filename}");
        return 0;
    }

    $upload = wp_upload_bits($filename, null, file_get_contents($src));
    if (!empty($upload['error'])) {
        WP_CLI::warning("画像を保存できません: {$filename} / {$upload['error']}");
        return 0;
    }

    $id = wp_insert_attachment([
        'post_mime_type' => (string) wp_check_filetype($filename)['type'],
        'post_title'     => pathinfo($filename, PATHINFO_FILENAME),
        'post_status'    => 'inherit',
    ], $upload['file']);

    if (is_wp_error($id) || $id === 0) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));

    return (int) $id;
}

// ---- 1. 固定ページ ----------------------------------------------------------

$sobakokoro_notice = <<<'HTML'
<ul>
<li>表示価格はすべて税込です。</li>
<li>そば粉は島根県三瓶在来種を使用し、毎朝この店で打っています。</li>
<li>丼もののお米は「いのちの壱」を使用しています。</li>
<li>ご予約は承っておりません。先着順でのご案内となります。</li>
</ul>
HTML;

$pages = [
    'menu'   => ['title' => 'お品書き',           'content' => $sobakokoro_notice],
    'access' => ['title' => 'アクセス・営業時間', 'content' => ''],
];

foreach ($pages as $slug => $page) {
    if (get_page_by_path($slug) instanceof WP_Post) {
        WP_CLI::log("固定ページ「{$page['title']}」は作成済みです");
        continue;
    }

    $id = wp_insert_post([
        'post_type'    => 'page',
        'post_name'    => $slug,
        'post_title'   => $page['title'],
        'post_content' => $page['content'],
        'post_status'  => 'publish',
    ]);

    if (is_wp_error($id)) {
        WP_CLI::warning("固定ページを作れません: {$page['title']}");
    } else {
        WP_CLI::success("固定ページ「{$page['title']}」を作りました");
    }
}

// ---- 2. 区分 ----------------------------------------------------------------

sobakokoro_seed_menu_cats();
WP_CLI::log('区分（温かいお蕎麦・冷たいお蕎麦 ほか）を用意しました');

// ---- 3. お品書き ------------------------------------------------------------

$items   = require __DIR__ . '/menu-data.php';
$created = 0;
$skipped = 0;

foreach ($items as $i => $row) {
    $existing = get_posts([
        'post_type'      => 'menu_item',
        'title'          => $row['title'],
        'posts_per_page' => 1,
        'post_status'    => 'any',
    ]);

    if ($existing !== []) {
        $skipped++;
        continue;
    }

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

    wp_set_object_terms($post_id, $row['cat'], 'menu_cat');
    update_post_meta($post_id, '_sobakokoro_price', $row['price']);

    if (!empty($row['note'])) {
        update_post_meta($post_id, '_sobakokoro_note', $row['note']);
    }
    if (!empty($row['badge'])) {
        update_post_meta($post_id, '_sobakokoro_badge', $row['badge']);
    }
    if (!empty($row['photo'])) {
        $attachment_id = sobakokoro_seed_attachment($row['photo']);
        if ($attachment_id > 0) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    $created++;
}

WP_CLI::success("お品書き: {$created}件を登録、{$skipped}件は登録済みのため飛ばしました");

// ---- 4. お知らせ ------------------------------------------------------------

if (get_posts(['posts_per_page' => 1]) === []) {
    wp_insert_post([
        'post_type'   => 'post',
        'post_title'  => '9月の休業日は店頭に掲示しています。',
        'post_status' => 'publish',
    ]);
    WP_CLI::success('お知らせを1件入れました（トップに出ます）');
}

WP_CLI::success('初期データの投入が終わりました。');
WP_CLI::log('このあと 設定 > パーマリンク を一度開いて保存してください（固定ページのURLを確定させるため）。');
