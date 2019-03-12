<?php
/**
 * Template Name: Registration Successfull Message
 * A simple template for Lost Password.
 *
 * @package Avada
 * @subpackage Templates
 */

?>

<?php

// check for valid user Id
if(isset($_GET['ref_id'])){
	$userId = base64_decode($_GET['ref_id']);
	$userDetails = get_userdata( $userId );
	if($userDetails){
		$approvedStatus = get_user_meta($userId, 'pw_user_status', true);
		if($approvedStatus !== 'pending'){
			wp_redirect('client-login');
		}
	} else{
		wp_redirect('client-login');
	} 
} else{
	wp_redirect('client-login');
} 



// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>
<section id="content" class="full-width">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo fusion_render_rich_snippets_for_pages(); // WPCS: XSS ok. ?>
			<?php avada_featured_images_for_pages(); ?>
			<div class="post-content">
				<div class="woocommerce">
					<div class="woocommerce-message" role="alert">
						User Registration successful.	
					</div>

					<p>Hi <?php echo $userDetails->first_name; ?>, </p>
					<p>An email has been sent to the site administrator. The administrator will review the information that has been submitted and either approve or deny your request.<br>
					You will receive an email with instructions on what you will need to do next. Thanks for your patience.	
					</p>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
</section>
<?php
get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
