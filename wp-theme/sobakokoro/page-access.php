<?php
/**
 * アクセス・営業時間のページ（固定ページ slug: access）
 *
 * 営業時間・電話・住所は「店舗情報」の設定から出す。
 * 地図は住所から自動で組み立てるので、住所を直せば地図も一緒に動く。
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$sobakokoro_map_query = rawurlencode(sobakokoro_shop('map_query'));
$sobakokoro_menu_url  = sobakokoro_page_url('menu');
?>

<main>

  <div class="page-head page-head--washi">
    <div class="page-head__text">
      <span class="page-head__en">ACCESS &amp; HOURS</span>
      <h1 class="page-head__ja"><?php the_title(); ?></h1>
    </div>
  </div>

  <div class="wrap crumb"><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a> ／ <?php the_title(); ?></div>

  <section class="section">
    <div class="wrap">
      <div class="section__head">
        <span class="section__en">HOURS</span>
        <h2 class="section__title">営業時間</h2>
      </div>

      <div class="scroll-x">
        <table class="hours-table">
          <thead>
            <tr><th>曜日</th><th>営業時間</th></tr>
          </thead>
          <tbody>
            <tr>
              <th>月〜日・祝</th>
              <td>
                <?php // 折り返すときは必ず「（L.O. …）」の前で切れるよう、ひとまとまりにしてある ?>
                <span class="hours-time"><?php echo esc_html(sobakokoro_shop('hours')); ?></span><span class="hours-lo">（<?php echo esc_html(sobakokoro_shop('lo')); ?>）</span>
              </td>
            </tr>
            <tr>
              <th>定休日</th>
              <td><em><?php echo esc_html(sobakokoro_shop('closed')); ?></em></td>
            </tr>
          </tbody>
        </table>
      </div>

      <ul class="notice" style="margin-top:1.5rem">
        <li>ご予約は承っておりません。先着順でのご案内となります。</li>
        <li>お休みの日は月ごとに変わります。店頭の掲示またはお電話でご確認ください。</li>
        <li>臨時休業は前日までに店頭・お電話でご案内します。</li>
      </ul>
    </div>
  </section>

  <section class="section section--soft">
    <div class="wrap">
      <div class="section__head">
        <span class="section__en">ACCESS</span>
        <h2 class="section__title">アクセス</h2>
      </div>

      <div class="access-grid">
        <div>
          <div class="map">
            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr($sobakokoro_map_query); ?>&amp;z=16&amp;output=embed"
                    loading="lazy" title="<?php echo esc_attr(sobakokoro_shop('shop_name')); ?>の地図"></iframe>
          </div>
          <div class="map-links">
            <a class="btn btn--primary"
               href="https://www.google.com/maps/dir/?api=1&amp;destination=<?php echo esc_attr($sobakokoro_map_query); ?>"
               target="_blank" rel="noopener">経路を調べる</a>
            <a class="btn btn--tel" href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>">
              ☎ <?php echo esc_html(sobakokoro_shop('tel')); ?>
            </a>
          </div>
        </div>

        <div>
          <dl class="info-list">
            <div><dt>店名</dt><dd><?php echo esc_html(sobakokoro_shop('shop_name')); ?></dd></div>
            <div><dt>住所</dt><dd><?php echo esc_html(sobakokoro_shop('postal') . '　' . sobakokoro_shop('address')); ?></dd></div>
            <div><dt>電話</dt><dd><a href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>"><?php echo esc_html(sobakokoro_shop('tel')); ?></a></dd></div>
            <div><dt>電車</dt><dd>能勢電鉄日生線 日生中央駅</dd></div>
            <div><dt>お車</dt><dd>日生中央サピエの駐車場をご利用ください</dd></div>
            <div><dt>お支払い</dt><dd>現金・クレジットカード・電子マネー・各種QRコード決済</dd></div>
            <div><dt>ご予約</dt><dd>ご予約は承っておりません（先着順）</dd></div>
            <div><dt>設備</dt><dd>全席禁煙／お子様連れ可</dd></div>
          </dl>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <div class="section__head">
        <span class="section__en">GUIDE</span>
        <h2 class="section__title">お越しの際は</h2>
        <p class="section__lead"><?php echo esc_html(sobakokoro_shop('place')); ?>です。<br>駅からも、お車でもお立ち寄りいただけます。</p>
      </div>
      <div class="gallery">
        <figure>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/photo/shop-exterior.jpg'); ?>"
               alt="そばこころ 日生中央店の入口。若草色の暖簾がかかる" loading="lazy">
          <figcaption>お店の入口</figcaption>
        </figure>
        <figure>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/photo/shop-interior.jpg'); ?>"
               alt="そばこころ 日生中央店の店内。木のテーブル席が並ぶ" loading="lazy">
          <figcaption>店内の様子</figcaption>
        </figure>
      </div>
      <?php if ($sobakokoro_menu_url !== '') : ?>
        <p class="center">
          <a class="btn btn--outline" href="<?php echo esc_url($sobakokoro_menu_url); ?>">お品書きを見る</a>
        </p>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
