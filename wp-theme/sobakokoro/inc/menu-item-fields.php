<?php
/**
 * お品の編集画面に出す入力欄（価格・注記・タグ）
 *
 * 価格は「1,650円」「+300円」「無料」のように表記がまちまちなので、
 * 数値ではなく表示したい文字列をそのまま入れてもらう。
 */

if (!defined('ABSPATH')) {
    exit;
}

const SOBAKOKORO_BADGES = [
    ''        => '（なし）',
    'limited' => '数量限定',
    'takeout' => 'お持ち帰り可',
    'summer'  => '夏',
    'winter'  => '冬',
];

/**
 * タグのスラッグから、表示名とCSSクラスを引く。
 */
function sobakokoro_badge_label(string $slug): array
{
    return match ($slug) {
        'limited' => ['数量限定', 'tag'],
        'takeout' => ['お持ち帰り可', 'tag tag--navy'],
        'summer'  => ['夏', 'tag'],
        'winter'  => ['冬', 'tag'],
        default   => ['', ''],
    };
}

function sobakokoro_add_menu_item_metabox(): void
{
    add_meta_box(
        'sobakokoro_menu_item',
        'お品の内容',
        'sobakokoro_render_menu_item_metabox',
        'menu_item',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'sobakokoro_add_menu_item_metabox');

function sobakokoro_render_menu_item_metabox(WP_Post $post): void
{
    wp_nonce_field('sobakokoro_save_menu_item', 'sobakokoro_menu_item_nonce');

    $price = sobakokoro_price($post->ID);
    $note  = sobakokoro_note($post->ID);
    $badge = sobakokoro_badge($post->ID);
    ?>
    <p>
        <label for="sobakokoro_price"><strong>価格</strong></label><br>
        <input type="text" id="sobakokoro_price" name="sobakokoro_price"
               value="<?php echo esc_attr($price); ?>" class="regular-text"
               placeholder="例：1,650円　／　+300円　／　無料">
        <br><span class="description">表示したいとおりに入れてください。「円」まで含めて入力します。</span>
    </p>
    <p>
        <label for="sobakokoro_note"><strong>注記</strong></label><br>
        <input type="text" id="sobakokoro_note" name="sobakokoro_note"
               value="<?php echo esc_attr($note); ?>" class="large-text"
               placeholder="例：紀州鴨使用　／　大海老二尾・南瓜・ししとう・海苔">
        <br><span class="description">品名の下に小さく出ます。空のままでも構いません。</span>
    </p>
    <p>
        <label for="sobakokoro_badge"><strong>タグ</strong></label><br>
        <select id="sobakokoro_badge" name="sobakokoro_badge">
            <?php foreach (SOBAKOKORO_BADGES as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($badge, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><span class="description">品名の横に出る小さなラベルです。</span>
    </p>
    <p class="description">
        写真は右側の「アイキャッチ画像」から登録します。<br>
        <strong>写真を入れた品は大きなカードで、入れない品は価格表の行で表示されます。</strong>
    </p>
    <?php
}

function sobakokoro_save_menu_item(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $nonce = $_POST['sobakokoro_menu_item_nonce'] ?? '';
    if (!is_string($nonce) || !wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), 'sobakokoro_save_menu_item')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = [
        '_sobakokoro_price' => 'sobakokoro_price',
        '_sobakokoro_note'  => 'sobakokoro_note',
        '_sobakokoro_badge' => 'sobakokoro_badge',
    ];

    foreach ($fields as $meta_key => $input_name) {
        $raw = $_POST[$input_name] ?? '';
        $value = is_string($raw) ? sanitize_text_field(wp_unslash($raw)) : '';

        // タグは決められた値のみ受け付ける
        if ($meta_key === '_sobakokoro_badge' && !array_key_exists($value, SOBAKOKORO_BADGES)) {
            $value = '';
        }

        if ($value === '') {
            delete_post_meta($post_id, $meta_key);
        } else {
            update_post_meta($post_id, $meta_key, $value);
        }
    }
}
add_action('save_post_menu_item', 'sobakokoro_save_menu_item');

/**
 * お品書き一覧に価格と写真の有無を出して、抜けが分かるようにする。
 */
function sobakokoro_menu_item_columns(array $columns): array
{
    $new = [];
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['sobakokoro_price'] = '価格';
            $new['sobakokoro_photo'] = '写真';
        }
    }
    return $new;
}
add_filter('manage_menu_item_posts_columns', 'sobakokoro_menu_item_columns');

function sobakokoro_menu_item_column_content(string $column, int $post_id): void
{
    if ($column === 'sobakokoro_price') {
        $price = sobakokoro_price($post_id);
        echo $price !== '' ? esc_html($price) : '<span style="color:#b32d2e">未入力</span>';
    }

    if ($column === 'sobakokoro_photo') {
        echo has_post_thumbnail($post_id) ? 'あり（カード表示）' : '—';
    }
}
add_action('manage_menu_item_posts_custom_column', 'sobakokoro_menu_item_column_content', 10, 2);
