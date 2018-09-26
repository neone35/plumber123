<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package siteorigin-corp
 * @license GPL 2.0 
 */
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<?php if ( siteorigin_page_setting( 'page_title' ) ) : ?>
				<header class="page-header">
					<?php 
						the_archive_title( '<h1 class="page-title">', '</h1>' ); 
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->
			<?php endif; ?>

			<?php get_template_part( 'loops/loop', 'blog-' . siteorigin_setting( 'blog_archive_layout' ) ); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
