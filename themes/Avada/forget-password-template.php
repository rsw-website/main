<?php
/**
 * Template Name: Lost Password
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
				<?php wc_get_template( 'myaccount/form-lost-password.php', array( 'form' => 'lost_password' ) ); ?>
			</div>
		</div>
	<?php endwhile; ?>
</section>
<?php
get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
