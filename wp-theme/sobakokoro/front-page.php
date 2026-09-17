<?php
/**
 * トップページ
 *
 * お知らせは通常の「投稿」の最新1件を出す。投稿が無ければ帯ごと出さない。
 * 「よく出るお品」は、お品の編集画面でチェックを入れたものが並ぶ。
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$sobakokoro_menu_url   = sobakokoro_page_url('menu');
$sobakokoro_access_url = sobakokoro_page_url('access');
$sobakokoro_hero_id    = (int) get_theme_mod('sobakokoro_hero_image');
$sobakokoro_news       = get_posts(['posts_per_page' => 1]);
?>

<main>

  <?php
  // ヒーローは4枚を順に切り替える。1枚目だけ先に読み込み、残りは hero-slider.js が読む。
  // スマホは枠が縦長になり、横長の写真だと中央3割しか映らないため、縦構図の版を別に用意している
  $sobakokoro_slides = [
      ['file' => 'hero-1-field',    'alt' => '島根県三瓶の在来種を育てるそば畑'],
      ['file' => 'hero-2-exterior', 'alt' => 'そばこころ 日生中央店の入口。若草色の暖簾がかかる'],
      ['file' => 'hero-3-tenzaru',  'alt' => '天ざる。天ぷらと朝打ちの自家製麺'],
      ['file' => 'hero-4-noodles',  'alt' => '箸で持ち上げた朝打ちの自家製麺'],
  ];
  $sobakokoro_img_dir = get_template_directory_uri() . '/assets/img/photo/';
  ?>
  <section class="hero">
    <div class="hero__slides">
      <?php foreach ($sobakokoro_slides as $i => $slide) : ?>
        <?php
        $wide = esc_url($sobakokoro_img_dir . $slide['file'] . '.jpg');
        $tall = esc_url($sobakokoro_img_dir . $slide['file'] . '-sp.jpg');
        ?>
        <picture>
          <?php if ($i === 0) : ?>
            <source media="(max-width: 640px)" srcset="<?php echo $tall; ?>">
            <img class="hero__img is-active" src="<?php echo $wide; ?>"
                 alt="<?php echo esc_attr($slide['alt']); ?>" fetchpriority="high">
          <?php else : ?>
            <source media="(max-width: 640px)" data-srcset="<?php echo $tall; ?>">
            <img class="hero__img" data-src="<?php echo $wide; ?>"
                 alt="<?php echo esc_attr($slide['alt']); ?>" aria-hidden="true">
          <?php endif; ?>
        </picture>
      <?php endforeach; ?>
    </div>

    <div class="hero__dots" aria-hidden="true">
      <?php foreach ($sobakokoro_slides as $i => $slide) : ?>
        <span class="hero__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
      <?php endforeach; ?>
    </div>

    <?php // 画面には出さないが、ページの見出しとして店名を残す（検索エンジンと読み上げ用） ?>
    <h1 class="sr-only"><?php echo esc_html(sobakokoro_shop('shop_name')); ?></h1>
  </section>

  <?php if ($sobakokoro_news !== []) : ?>
    <?php $sobakokoro_post = $sobakokoro_news[0]; ?>
    <div class="news">
      <div class="news__inner">
        <span class="news__label">お知らせ</span>
        <span class="news__date"><?php echo esc_html(get_the_date('Y.m.d', $sobakokoro_post)); ?></span>
        <span class="news__text"><?php echo esc_html(get_the_title($sobakokoro_post)); ?></span>
      </div>
    </div>
  <?php endif; ?>

  <div class="hours-bar">
    <div class="wrap hours-bar__inner">
      <span><strong><?php echo esc_html(sobakokoro_shop('hours')); ?></strong>（<?php echo esc_html(sobakokoro_shop('lo')); ?>）</span>
      <span>定休 <strong><?php echo esc_html(sobakokoro_shop('closed')); ?></strong></span>
      <span><?php echo esc_html(sobakokoro_shop('place')); ?></span>
      <a href="tel:<?php echo esc_attr(sobakokoro_tel_link()); ?>">☎ <?php echo esc_html(sobakokoro_shop('tel')); ?></a>
    </div>
  </div>

  <section class="section section--ichimatsu">
    <div class="wrap">
      <div class="section__head">
        <span class="section__en">OUR CRAFT</span>
        <h2 class="section__title">そばこころのこだわり</h2>
        <p class="section__lead">そば粉も、お米も、選んだものを使っています。</p>
      </div>

      <?php
      // 内容は店舗の看板（看板用_0810.pdf）の記載に合わせてある
      $sobakokoro_crafts = [
          [
              // 産地のイメージ写真（当店の契約農家の畑ではないため、本文でも産地の説明に留めている）
              'img'   => 'buckwheat-field.jpg',
              'alt'   => '朝霧の立つそば畑',
              'num'   => '01',
              'title' => '島根県三瓶（さんべ）在来種のそば粉',
              'text'  => 'そば粉は島根県三瓶の在来種を使っています。小粒で香りが高く、甘みの濃い希少な在来種です。',
          ],
          [
              // 実際の自家製麺。天ざるの原本から麺だけを切り出したもの
              'img'   => 'homemade-noodles.jpg',
              'alt'   => '朝打ちの自家製麺',
              'num'   => '02',
              'title' => '毎朝この店で打つ、自家製麺',
              'text'  => 'お蕎麦は毎朝、この店で打っています。打ちたてならではの香りと喉ごしを、ぜひそのまま召し上がってください。',
          ],
          [
              'img'   => 'badge-okome.png',
              'alt'   => 'そばこころのお米　「いのちの壱」を使用',
              'num'   => '03',
              'title' => 'ごはんは「いのちの壱」',
              'text'  => '丼もののお米には「いのちの壱」を使っています。粒が大きく、噛むほどに甘みの出るお米です。ミニ丼は香の物付きで、お蕎麦とご一緒にどうぞ。',
              'badge' => true,
          ],
      ];
      ?>

      <?php foreach ($sobakokoro_crafts as $i => $craft) : ?>
        <div class="feature<?php echo $i === 1 ? ' feature--reverse' : ''; ?>">
          <div class="feature__img<?php echo !empty($craft['badge']) ? ' feature__img--badge' : ''; ?>">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/photo/' . $craft['img']); ?>"
                 alt="<?php echo esc_attr($craft['alt']); ?>" loading="lazy">
          </div>
          <div>
            <span class="feature__num"><?php echo esc_html($craft['num']); ?></span>
            <h3 class="feature__title"><?php echo esc_html($craft['title']); ?></h3>
            <p class="feature__text"><?php echo esc_html($craft['text']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if ($sobakokoro_menu_url !== '') : ?>
        <p class="center">
          <a class="btn btn--primary" href="<?php echo esc_url($sobakokoro_menu_url); ?>">お品書きを見る</a>
        </p>
      <?php endif; ?>
    </div>
  </section>

  <section class="section section--soft">
    <div class="wrap">
      <div class="section__head">
        <span class="section__en">SHOP</span>
        <h2 class="section__title">明るい店内で、ゆっくりと</h2>
        <p class="section__lead">テーブル席の広々とした店内です。<br>おひとりのお昼にも、ご家族でのお食事にも。</p>
      </div>
      <div class="gallery">
        <figure>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/photo/shop-interior.jpg'); ?>"
               alt="そばこころ 日生中央店の店内。木のテーブル席が並ぶ" loading="lazy">
          <figcaption>店内の様子</figcaption>
        </figure>
        <figure>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/photo/shop-exterior.jpg'); ?>"
               alt="そばこころ 日生中央店の入口。若草色の暖簾がかかる" loading="lazy">
          <figcaption>お店の入口</figcaption>
        </figure>
      </div>
      <?php if ($sobakokoro_access_url !== '') : ?>
        <p class="center">
          <a class="btn btn--outline" href="<?php echo esc_url($sobakokoro_access_url); ?>">アクセス・営業時間を見る</a>
        </p>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
