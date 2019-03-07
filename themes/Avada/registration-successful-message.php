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
				<?php
					echo get_permalink( get_page_by_path( 'registration-successful' ));
					echo add_query_arg( array( 'ref_id' => '123' ), get_permalink( get_page_by_path( 'registration-successful' )) );
				?>
				<div class="woocommerce">
					<div class="woocommerce-message" role="alert">
						User Registration successful.	
					</div>

					<p>Hi User, </p>
					<p>Your account has been registered successfully. You will not be able to login as your account is held under moderation for admin's approval.<br>
					We will notify you with an email about your account status.	
					</p>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
</section>
<?php
get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
