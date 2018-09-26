<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Carpenter Lite
 */
?>
</div><!-- main-container -->

<div class="copyright-wrapper">
        	<div class="container">
                <div class="copyright">
                    	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_attr(bloginfo( 'name' )); ?>  <?php echo esc_attr(date_i18n( __( 'Y', 'carpenter-lite' ) )); ?> <?php esc_html_e('. Powered by WordPress','carpenter-lite'); ?></p>               
                </div><!-- copyright --><div class="clear"></div>           
            </div><!-- container -->
        </div>
    </div>
        
<?php wp_footer(); ?>

</body>
</html>