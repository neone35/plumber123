<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Carpenter Lite
 */

get_header(); ?>
  <div class="main-container">
<div class="content-area">
    <div class="middle-align content_sidebar">
        <div class="site-main" id="sitemain">
			<?php if ( have_posts() ) : ?>
                <header class="page-header">
                        <?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
                </header><!-- .page-header -->
				<?php /* Start the Loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', get_post_format() ); ?>
                <?php endwhile; ?>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <?php get_template_part( 'no-results'); ?>
            <?php endif; ?>
        </div>
        <?php get_sidebar();?>
        <div class="clear"></div>
    </div>
</div>

<?php get_footer(); ?>