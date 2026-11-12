<?php
/**
 * ログインに使うユーザー名を外から見えなくする
 *
 * WordPress は著者ページ・サイトマップ・REST API でユーザー名（スラッグ）を公開する。
 * 店のサイトに著者ページは要らないので、まとめて閉じる。
 * カテゴリーもお知らせ1種類しかないので、サイトマップから外す。
 */

if (!defined('ABSPATH')) {
    exit;
}

function sobakokoro_sitemap_providers($provider, string $name)
{
    return in_array($name, ['users', 'taxonomies'], true) ? false : $provider;
}
add_filter('wp_sitemaps_add_provider', 'sobakokoro_sitemap_providers', 10, 2);

function sobakokoro_redirect_author_page(): void
{
    if (is_author()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'sobakokoro_redirect_author_page');

/**
 * ユーザー一覧の API は、ログイン中だけ使えるようにする。
 * 投稿の編集画面がこの API を使うので、丸ごと止めると編集できなくなる。
 */
function sobakokoro_hide_users_endpoint(array $endpoints): array
{
    if (is_user_logged_in()) {
        return $endpoints;
    }

    foreach (array_keys($endpoints) as $route) {
        if (str_starts_with($route, '/wp/v2/users')) {
            unset($endpoints[$route]);
        }
    }

    return $endpoints;
}
add_filter('rest_endpoints', 'sobakokoro_hide_users_endpoint');
