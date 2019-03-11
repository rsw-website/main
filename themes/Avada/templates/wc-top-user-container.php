<?php
/**
 * WooCommere Top User Container.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1
 */

global $woocommerce, $current_user;
?>
<div class="avada-myaccount-user">
	<div class="avada-myaccount-user-column username">
		<div class="profile-details">
			<img src="<?php echo get_avatar_url($current_user->user_email); ?>">
			<span class="hello">
				<?php
				printf(
					/* translators: %1$s: Username. %2$s: Username (same as %1$s). %3$s: "Sign Out" link. */
					esc_attr__( 'Hello %1$s %2$s', 'Avada' ),
					'' . esc_html( $current_user->first_name ) . '',
					'<span class="user-email-field">(' . esc_html( $current_user->user_email ) . ')</span>'
				);
				?>
			</span>
		</div>
		<div class="request-access-details">
			<div class="document-request-text">
		        <p>You do not have permission to access the documents added by the admin.<br/>
		        Click on the below button to request access for all the documents.</p>
		        <button class="document-request-access">Request Document Access</button>
      		</div>
		</div>
	</div>
</div>
