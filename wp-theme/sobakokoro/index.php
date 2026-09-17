<?php
/**
 * どのテンプレートにも当てはまらないときの受け皿
 *
 * このサイトは固定ページ3枚とお知らせで完結するので、通常ここは使われない。
 * WordPress がテーマとして認識するために必要なファイル。
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main>

  <div class="page-head page-head--washi">
    <div class="page-head__text">
      <h1 class="page-head__ja"><?php echo is_singular() ? esc_html(get_the_title()) : 'お知らせ'; ?></h1>
    </div>
  </div>

  <section class="section">
    <div class="wrap">
      <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
          <article class="menu-block">
            <div class="menu-block__head">
              <h2 class="menu-block__title">
                <?php if (is_singular()) : ?>
                  <?php the_title(); ?>
                <?php else : ?>
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php endif; ?>
              </h2>
              <span class="menu-block__bar"></span>
              <span class="menu-block__en"><?php echo esc_html(get_the_date('Y.m.d')); ?></span>
            </div>
            <?php the_content(); ?>
          </article>
        <?php endwhile; ?>
      <?php else : ?>
        <p>お知らせはまだありません。</p>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
