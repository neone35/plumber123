 <?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Carpenter Lite
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
        
<div id="header">
            <div class="header-inner">	
				<div class="logo">
					<?php carpenter_lite_the_custom_logo(); ?>
						<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_attr(bloginfo( 'name' )); ?></a></h1>

					<?php $description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p><?php echo esc_html($description); ?></p>
					<?php endif; ?>
				</div><!-- logo -->
                <div class="header_right">  
                <?php if(get_theme_mod('street-txt') != '' || get_theme_mod('city-txt') != ''){ ?>
                    <div class="right-box">
                        <div class="hright-icon">            	
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                    	</div><!-- hright-icon -->
                        <div class="hright-content">
                            <span class="bold-text"><?php echo esc_attr(get_theme_mod('street-txt')); ?></span><!-- boldtext -->
                            <span class="small-text"><?php echo esc_attr(get_theme_mod('city-txt')); ?></span><!-- smalltext -->
                        </div><!-- hright-content --><div class="clear"></div>
                    </div><!-- right-box -->
       			 <?php } ?>
                 <?php if(get_theme_mod('phone-txt') != '' || get_theme_mod('email-txt') != ''){ ?>
                    <div class="right-box">
                        <div class="hright-icon">            	
                            <i class="fa fa-phone" aria-hidden="true"></i>
                        </div><!-- hright-icon -->
                        <div class="hright-content">
                            <span class="bold-text"><?php echo esc_attr(get_theme_mod('phone-txt')); ?></span><!-- boldtext -->
                            <span class="small-text"><a href="<?php echo esc_attr(esc_html('mailto:','carpenter-lite').get_theme_mod('email-txt')); ?>"><?php echo esc_attr(get_theme_mod('email-txt')); ?></a></span><!-- smalltext -->
                        </div><!-- hright-content --><div class="clear"></div>
                    </div><div class="clear"></div>
        		<?php } ?> 		
    </div><!--header_right--><div class="clear"></div>
            </div><!-- header-inner --> 
            <div id="navigation">
            <div class="toggle">
						<a class="toggleMenu" href="#"><?php esc_html_e('Menu','carpenter-lite'); ?></a>
				</div> 						
				<div class="sitenav">
						<?php wp_nav_menu( array('theme_location' => 'primary') ); ?>							
				</div>
                <?php if(get_theme_mod('top-link') != '') { ?>						
                    <div class="get_a_quote">
                        <a href="<?php echo esc_url(get_theme_mod('top-link')); ?>"><?php esc_attr_e('Get a Quote','carpenter-lite') ;?></a>
                    </div><!-- get_a_quote --><div class="clear"></div>
                <?php } ?>
                </div><!-- navigation -->             
		</div><!-- header -->