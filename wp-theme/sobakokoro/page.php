<?php
/**
 * 汎用の固定ページ
 *
 * お品書き（page-menu.php）とアクセス（page-access.php）以外の固定ページで使う。
 * プライバシーポリシーなどを足したくなったときの受け皿。
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main>

  <div class="page-head page-head--washi">
    <div class="page-head__text">
      <h1 class="page-head__ja"><?php the_title(); ?></h1>
    </div>
  </div>

  <div class="wrap crumb"><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a> ／ <?php the_title(); ?></div>

  <section class="section">
    <div class="wrap">
      <?php
      while (have_posts()) :
          the_post();
          the_content();
      endwhile;
      ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
