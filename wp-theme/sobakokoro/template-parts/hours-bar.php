<?php
/**
 * 営業時間の帯（トップページのみ・ヒーローとお知らせの下）
 *
 * 表示する内容は管理画面の「店舗情報」から出している。
 * ヘッダー上にも同じ内容の細い帯を出していたが、定休日が入らず文字も小さいため廃止し、
 * こちらに一本化した（2026-09-18）。
 * 下層ページでは出さない方針のため、営業時間はフッターとアクセスページで見せている。
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="hours-bar">
  <div class="wrap hours-bar__inner">
    <span><strong><?php echo esc_html(sobakokoro_shop('hours')); ?></strong>（<?php echo esc_html(sobakokoro_shop('lo')); ?>）</span>
    <span>定休 <strong><?php echo esc_html(sobakokoro_shop('closed')); ?></strong></span>
    <span><?php echo esc_html(sobakokoro_shop('place')); ?></span>
    <a href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>">☎ <?php echo esc_html(sobakokoro_shop('tel')); ?></a>
  </div>
</div>
