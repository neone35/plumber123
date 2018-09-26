<?php
/**
 * @package Carpenter Lite
 */
?>
<div class="blog-post-repeat">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
            <?php if ( 'post' == get_post_type() ) : ?>
                <div class="postmeta">
                    <div class="post-date"><?php echo esc_url(carpenter_lite_time_link()); ?></div><!-- post-date -->
                    <div class="post-comment"> <a href="<?php comments_link(); ?>"><?php comments_number(); ?></a></div>
                    <div class="post-categories"> | <?php the_category( __( ', ', 'carpenter-lite' ));?></div>
                    <div class="clear"></div>
                </div><!-- postmeta -->
            <?php endif; ?>
	        <?php if ( is_search() || !is_single() ) : // Only display Excerpts for Search ?>
	            <div class="post-thumb"><?php the_post_thumbnail(); ?>
	        <?php else : ?>
	            <div class="post-thumb"><?php the_post_thumbnail(); ?>
	        <?php endif; ?>
            </div><!-- post-thumb -->
        </header><!-- .entry-header -->
    
        <?php if ( is_search() || !is_single() ) : // Only display Excerpts for Search ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
                <p class="read-more"><a href="<?php the_permalink(); ?>"><?php esc_html_e('Read More &raquo;','carpenter-lite'); ?></a></p>
            </div><!-- .entry-summary -->
        <?php else : ?>
            <div class="entry-content">
                <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'carpenter-lite' ) ); ?>
            </div><!-- .entry-content --><div class="clear"></div>
            <?php
                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . __( 'Pages:', 'carpenter-lite' ),
                        'after'  => '</div>',
                    ) );
                ?>
        <?php endif; ?>
    </article><!-- #post-## -->
    <div class="spacer20"></div>
</div><!-- blog-post-repeat -->