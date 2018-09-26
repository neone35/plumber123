<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Carpenter Lite
 */

get_header(); ?>
  <div class="main-container">
<div class="content-area">
    <div class="middle-align content_sidebar">
        <div class="site-main" id="sitemain">
			<?php if ( have_posts() ) : ?>
                <header>
                    <h1 class="entry-title"><?php printf( esc_html_e( 'Search Results for: %s', 'carpenter-lite' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
                </header>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', 'search' ); ?>
                <?php endwhile; ?>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <?php get_template_part( 'no-results' ); ?>
            <?php endif; ?>
        </div>
        <?php get_sidebar();?>
        <div class="clear"></div>
    </div>
</div>

<?php get_footer(); ?>