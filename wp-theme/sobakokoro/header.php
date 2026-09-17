<?php
/**
 * 全ページ共通のヘッダー
 *
 * 上部の帯（住所・営業時間・電話）は「店舗情報」の設定から出している。
 */

if (!defined('ABSPATH')) {
    exit;
}

$sobakokoro_menu_url   = sobakokoro_page_url('menu');
$sobakokoro_access_url = sobakokoro_page_url('access');
$sobakokoro_logo_id    = (int) get_theme_mod('custom_logo');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="utilbar">
  <div class="utilbar__inner">
    <span class="utilbar__left">
      <?php echo esc_html(sobakokoro_shop('postal') . '　' . sobakokoro_shop('address')); ?>
      <?php if (sobakokoro_shop('parking') !== '') : ?>
        ／<?php echo esc_html(sobakokoro_shop('parking')); ?>
      <?php endif; ?>
    </span>
    <span class="utilbar__right">
      <span><?php echo esc_html(sobakokoro_shop('hours') . '　' . sobakokoro_shop('lo')); ?></span>
      <a class="utilbar__tel" href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>">
        <?php echo esc_html(sobakokoro_shop('tel')); ?>
      </a>
    </span>
  </div>
</div>

<header class="header">
  <div class="header__inner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
      <?php if ($sobakokoro_logo_id > 0) : ?>
        <?php
        // 暖簾の「そばこころ」の字。外観 > カスタマイズ > サイト基本情報 から差し替える
        echo wp_get_attachment_image($sobakokoro_logo_id, 'full', false, [
            'class' => 'brand__logo',
            'alt'   => esc_attr(sobakokoro_shop('shop_name')),
        ]);
        ?>
      <?php else : ?>
        <span class="brand__mark">そばこころ</span>
        <span class="brand__sub">SOBA KOKORO</span>
      <?php endif; ?>
    </a>

    <nav class="gnav">
      <a href="<?php echo esc_url(home_url('/')); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>>ホーム</a>
      <?php if ($sobakokoro_menu_url !== '') : ?>
        <a href="<?php echo esc_url($sobakokoro_menu_url); ?>"<?php echo is_page('menu') ? ' aria-current="page"' : ''; ?>>お品書き</a>
      <?php endif; ?>
      <?php if ($sobakokoro_access_url !== '') : ?>
        <a href="<?php echo esc_url($sobakokoro_access_url); ?>"<?php echo is_page('access') ? ' aria-current="page"' : ''; ?>>アクセス・営業時間</a>
      <?php endif; ?>
    </nav>

    <?php if (is_front_page() && $sobakokoro_menu_url !== '') : ?>
      <a class="header__cta" href="<?php echo esc_url($sobakokoro_menu_url); ?>">お品書きを見る</a>
    <?php else : ?>
      <a class="header__cta" href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>">電話で確認する</a>
    <?php endif; ?>
  </div>
</header>
