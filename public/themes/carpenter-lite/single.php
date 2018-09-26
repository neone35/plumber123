<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Carpenter Lite
 */

get_header(); ?>
  <div class="main-container">
<div class="content-area">
    <div class="middle-align content_sidebar">
        <div class="site-main" id="sitemain">
			<?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'content', 'single' ); ?>
                <?php the_post_navigation(); ?>
                <?php
                // If comments are open or we have at least one comment, load up the comment template
                if ( comments_open() || '0' != get_comments_number() )
                    comments_template();
                ?>
            <?php endwhile; // end of the loop. ?>
        </div>
        <?php get_sidebar();?>
        <div class="clear"></div>
    </div>
</div>

<?php get_footer(); ?>