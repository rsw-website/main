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
	    	    	<i class="fa-user-circle fas" data-name="user-circle"></i>
	    	    	<span class="menu-text">
	    	    		<?php echo $current_user->display_name; ?>
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
	    			<a href="<?php echo wp_logout_url('/client-login'); ?>" class="menu-item menu-item-type-custom menu-item-object-custom fusion-dropdown-submenu"><span>Logout</span></a>

	    		</ul>
			</li>   
	<?php
	} else{
		?>
		<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children fusion-dropdown-menu fusion-last-menu-item">
		        <a href="/client-login" class="fusion-bar-highlight">
	    	    	<span class="menu-text">
	    	    		Client Login
    	    		</span>
	    		</a>
			</li>
		<?php
	}
	?>
</ul>


