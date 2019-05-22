<?php
/**
 * Template for the secondary header.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php

$content_1 = avada_secondary_header_content( 'header_left_content' );
$content_2 = avada_secondary_header_content( 'header_right_content' );
?>

<div class="fusion-secondary-header">
	<div class="fusion-row">
		<?php if ( $content_1 ) : ?>
			<div class="fusion-alignleft">
				<?php echo $content_1; // WPCS: XSS ok. ?>
			</div>
		<?php endif; ?>
		<?php if ( $content_2 ) : ?>
			<div class="fusion-alignright">
				<nav class="fusion-secondary-menu" role="navigation" aria-label="Secondary Menu">
					<ul class="fusion-menu">
						<?php
						if(is_user_logged_in()){
							$current_user = wp_get_current_user();
							$menuitems = wp_get_nav_menu_items(17);
						?>
						<?php
				    		$count = 0;
				    		$submenu = false;
						?>
						<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children fusion-dropdown-menu fusion-last-menu-item">
					        <a href="javascript:void(0)" class="fusion-bar-highlight">
				    	    	<i class="fa fa-user"></i>
				    	    	<span class="menu-text">
				    	    		<?php echo $current_user->user_firstname; ?>
			    	    		</span>
				    		</a>
				    		<ul role="menu" class="sub-menu fusion-switched-side">
				    			<?php foreach( $menuitems as $item ): ?>
					    			<li class="menu-item menu-item-type-custom menu-item-object-custom fusion-dropdown-submenu">
								        <a href="<?php echo $item->url; ?>" class="fusion-bar-highlight">
							    	    	<span><?php echo $item->title; ?></span>
							    		</a>
					    			</li>
				    			<?php $count++; endforeach; ?>
				    			<a href="<?php echo wp_logout_url(home_url('/client-login/')); ?>" class="menu-item menu-item-type-custom menu-item-object-custom fusion-dropdown-submenu"><span>Logout</span></a>

				    		</ul>
						</li>   
						<?php
						} else{
							?>
							<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children fusion-dropdown-menu fusion-last-menu-item">
							        <a href="<?php echo home_url('/client-login/'); ?>" class="fusion-bar-highlight">
						    	    	<i class="fa fa-user"></i>
						    	    	<span class="menu-text">
						    	    		Client Login
					    	    		</span>
						    		</a>
								</li>
							<?php
						}
						?>
					</ul>
				</nav>
				<?php echo $content_2; // WPCS: XSS ok. ?>	
			</div>
		<?php endif; ?>
	</div>
</div>
