<?php
/**
 * お品書きのページ（固定ページ slug: menu）
 *
 * 区分ごとに「写真のある品はカード」「写真のない品は価格表」で出し分ける。
 * 品が1つも登録されていない区分は、見出しごと出さない。
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$sobakokoro_sections = sobakokoro_menu_sections();

// 品が登録されている区分だけを先に集めておく（ジャンプリンクに空の区分を出さないため）
$sobakokoro_active = [];
foreach ($sobakokoro_sections as $slug => $section) {
    $items = sobakokoro_get_menu_items($slug);
    if ($items !== []) {
        $sobakokoro_active[$slug] = $section;
    }
}
?>

<main>

  <div class="page-head page-head--washi">
    <div class="page-head__text">
      <span class="page-head__en">MENU</span>
      <h1 class="page-head__ja"><?php the_title(); ?></h1>
    </div>
  </div>

  <div class="wrap crumb"><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a> ／ <?php the_title(); ?></div>

  <section class="section">
    <div class="wrap">

      <?php if (count($sobakokoro_active) > 1) : ?>
        <nav class="menu-jump">
          <?php foreach ($sobakokoro_active as $slug => $section) : ?>
            <a href="#<?php echo esc_attr($slug); ?>"><?php echo esc_html($section['nav']); ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>

      <?php foreach ($sobakokoro_active as $slug => $section) : ?>
        <?php
        $with_photo = sobakokoro_get_menu_items($slug, true);
        $no_photo   = sobakokoro_get_menu_items($slug, false);
        ?>
        <div class="menu-block" id="<?php echo esc_attr($slug); ?>">
          <div class="menu-block__head">
            <h2 class="menu-block__title"><?php echo esc_html($section['name']); ?></h2>
            <span class="menu-block__bar"></span>
            <span class="menu-block__en"><?php echo esc_html($section['en']); ?></span>
          </div>

          <?php if ($with_photo !== []) : ?>
            <div class="cards cards--big">
              <?php foreach ($with_photo as $item) : ?>
                <?php [$badge_label, $badge_class] = sobakokoro_badge_label(sobakokoro_badge($item->ID)); ?>
                <article class="card">
                  <?php if ($badge_label !== '') : ?>
                    <span class="<?php echo esc_attr($badge_class); ?> card__badge"><?php echo esc_html($badge_label); ?></span>
                  <?php endif; ?>
                  <?php echo get_the_post_thumbnail($item, 'sobakokoro-dish', [
                      'alt'     => esc_attr(get_the_title($item)),
                      'loading' => 'lazy',
                  ]); ?>
                  <div class="card__body">
                    <h3 class="card__name"><?php echo esc_html(get_the_title($item)); ?></h3>
                    <p class="card__price">
                      <?php echo esc_html(sobakokoro_price($item->ID)); ?> <span>税込</span>
                    </p>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if ($no_photo !== []) : ?>
            <?php if ($with_photo !== []) : ?>
              <p class="rest-label"><?php echo esc_html($section['rest']); ?></p>
            <?php endif; ?>
            <table class="menu-table">
              <tbody>
              <?php foreach ($no_photo as $item) : ?>
                <?php
                [$badge_label, $badge_class] = sobakokoro_badge_label(sobakokoro_badge($item->ID));
                $note = sobakokoro_note($item->ID);
                ?>
                <tr>
                  <td>
                    <?php echo esc_html(get_the_title($item)); ?>
                    <?php if ($badge_label !== '') : ?>
                      <span class="<?php echo esc_attr($badge_class); ?>"><?php echo esc_html($badge_label); ?></span>
                    <?php endif; ?>
                    <?php if ($note !== '') : ?>
                      <span class="note"><?php echo esc_html($note); ?></span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo esc_html(sobakokoro_price($item->ID)); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <?php
      // 固定ページの本文に書いた内容を注意書きとして出す。
      // 本文が空のときは何も出さない
      $sobakokoro_notice = get_the_content();
      if (trim($sobakokoro_notice) !== '') :
          ?>
          <div class="notice">
            <?php the_content(); ?>
          </div>
      <?php endif; ?>

      <?php if (sobakokoro_page_url('access') !== '') : ?>
        <p class="center">
          <a class="btn btn--primary" href="<?php echo esc_url(sobakokoro_page_url('access')); ?>">
            アクセス・営業時間を見る
          </a>
        </p>
      <?php endif; ?>

    </div>
  </section>

</main>

<?php get_footer(); ?>
