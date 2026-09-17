<?php
/**
 * 店舗情報（営業時間・電話・住所）の一元管理
 *
 * 全ページの上部バー・フッター・アクセスページがここの値を見る。
 * 1箇所直せば全部に反映されるので、更新漏れが起きない。
 */

if (!defined('ABSPATH')) {
    exit;
}

const SOBAKOKORO_SHOP_OPTION = 'sobakokoro_shop';

/**
 * 項目の定義と初期値。初期値は静的HTML版の内容をそのまま入れてある。
 */
function sobakokoro_shop_fields(): array
{
    return [
        'shop_name' => ['label' => '店名',           'default' => 'そばこころ 日生中央店'],
        'postal'    => ['label' => '郵便番号',       'default' => '〒666-0261'],
        'address'   => ['label' => '住所',           'default' => '兵庫県川辺郡猪名川町松尾台1-2-1　日生中央サピエ 1F'],
        'map_query' => [
            'label'   => '地図で使う住所',
            'default' => '兵庫県川辺郡猪名川町松尾台1丁目2-1',
            'hint'    => 'Googleマップが正しい場所を指す表記。建物名や階数を入れると位置がずれることがあるので、番地までにしています。',
        ],
        'place'     => ['label' => '場所の呼び方',   'default' => '日生中央サピエ 1階', 'hint' => '「日生中央サピエ 1階」のように、短く言い表す言い方'],
        'tel'       => ['label' => '電話番号',       'default' => '072-764-7366'],
        'hours'     => ['label' => '営業時間',       'default' => '11:00 – 21:00'],
        'lo'        => ['label' => 'ラストオーダー', 'default' => 'L.O. 20:00'],
        'closed'    => ['label' => '定休日',         'default' => '月曜（月2〜3回）'],
        'parking'   => ['label' => '駐車場',         'default' => '駐車場あり'],
    ];
}

/**
 * 店舗情報を1項目取り出す。
 *
 * @param string $key sobakokoro_shop_fields() のキー
 */
function sobakokoro_shop(string $key): string
{
    $saved  = get_option(SOBAKOKORO_SHOP_OPTION, []);
    $fields = sobakokoro_shop_fields();

    if (!isset($fields[$key])) {
        return '';
    }

    $value = is_array($saved) ? ($saved[$key] ?? '') : '';

    return $value !== '' ? (string) $value : (string) $fields[$key]['default'];
}

/**
 * tel: リンク用の番号（ハイフンなし）。
 */
function sobakokoro_tel_link(): string
{
    return preg_replace('/[^0-9+]/', '', sobakokoro_shop('tel')) ?? '';
}

/**
 * 管理画面のメニューに「店舗情報」を追加する。
 */
function sobakokoro_shop_menu(): void
{
    add_menu_page(
        '店舗情報',
        '店舗情報',
        'manage_options',
        'sobakokoro-shop',
        'sobakokoro_shop_page',
        'dashicons-store',
        6
    );
}
add_action('admin_menu', 'sobakokoro_shop_menu');

function sobakokoro_shop_register(): void
{
    register_setting('sobakokoro_shop_group', SOBAKOKORO_SHOP_OPTION, [
        'type'              => 'array',
        'sanitize_callback' => 'sobakokoro_shop_sanitize',
        'default'           => [],
    ]);
}
add_action('admin_init', 'sobakokoro_shop_register');

function sobakokoro_shop_sanitize($input): array
{
    $clean = [];
    foreach (sobakokoro_shop_fields() as $key => $field) {
        $raw = is_array($input) ? ($input[$key] ?? '') : '';
        $clean[$key] = is_string($raw) ? sanitize_text_field($raw) : '';
    }
    return $clean;
}

function sobakokoro_shop_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>店舗情報</h1>
        <p>ここを直すと、サイトの全ページ（上部の帯・フッター・アクセスのページ）にまとめて反映されます。</p>
        <form method="post" action="options.php">
            <?php settings_fields('sobakokoro_shop_group'); ?>
            <table class="form-table" role="presentation">
                <tbody>
                <?php foreach (sobakokoro_shop_fields() as $key => $field) : ?>
                    <tr>
                        <th scope="row">
                            <label for="sobakokoro_<?php echo esc_attr($key); ?>">
                                <?php echo esc_html($field['label']); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   id="sobakokoro_<?php echo esc_attr($key); ?>"
                                   name="<?php echo esc_attr(SOBAKOKORO_SHOP_OPTION); ?>[<?php echo esc_attr($key); ?>]"
                                   value="<?php echo esc_attr(sobakokoro_shop($key)); ?>"
                                   class="regular-text">
                            <?php if (!empty($field['hint'])) : ?>
                                <p class="description"><?php echo esc_html($field['hint']); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php submit_button('保存する'); ?>
        </form>
    </div>
    <?php
}
