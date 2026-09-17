<?php
/**
 * 全ページ共通のフッターと、スマホ画面下に固定するボタン
 */

if (!defined('ABSPATH')) {
    exit;
}

$sobakokoro_menu_url   = sobakokoro_page_url('menu');
$sobakokoro_access_url = sobakokoro_page_url('access');
?>

<footer class="footer">
  <div class="wrap footer__inner">
    <div>
      <p class="footer__brand">そばこころ</p>
      <p class="footer__addr">
        <?php echo esc_html(sobakokoro_shop('postal') . '　' . sobakokoro_shop('address')); ?><br>
        TEL <a href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>"><?php echo esc_html(sobakokoro_shop('tel')); ?></a><br>
        <?php echo esc_html(sobakokoro_shop('hours') . '（' . sobakokoro_shop('lo') . '）'); ?><br>
        定休 <?php echo esc_html(sobakokoro_shop('closed')); ?>
      </p>
    </div>
    <nav class="footer__nav">
      <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a>
      <?php if ($sobakokoro_menu_url !== '') : ?>
        <a href="<?php echo esc_url($sobakokoro_menu_url); ?>">お品書き</a>
      <?php endif; ?>
      <?php if ($sobakokoro_access_url !== '') : ?>
        <a href="<?php echo esc_url($sobakokoro_access_url); ?>">アクセス・営業時間</a>
      <?php endif; ?>
    </nav>
  </div>
  <p class="footer__copy">&copy; そばこころ</p>
</footer>

<div class="sp-cta">
  <a href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>">電話をかける</a>
  <?php if ($sobakokoro_access_url !== '') : ?>
    <a href="<?php echo esc_url($sobakokoro_access_url); ?>">地図・営業時間</a>
  <?php endif; ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
