<?php
/**
 * Template Name: PDF Preview
 * Used for pages with a side-nav.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
if( !is_user_logged_in() ) {
	wp_redirect( home_url( '/client-login/' ) );
}
?>

<?php get_header(); 
if( !isset( $_GET['id'] ) ) {
	?>
	<h2>Invalid file path</h2>
	<?php
	get_footer();
	exit();
} else{
	$default_admin_roles = array( 'administrator', 'power_user' );
	global $wpdb;
	$hide = true;
	$document_id = base64_decode( $_GET['id'] );
	$current_user_id = get_current_user_id();
	$user_role = wp_get_current_user()->roles[0];
	$user_roles_list = $wpdb->get_var( "SELECT user_roles from wp_document_user_role WHERE document_id = ".$document_id );
	$user_roles_list = json_decode( $user_roles_list, true );
	if( !in_array( $user_role, $default_admin_roles ) ){
		if( array_key_exists( $user_role, $user_roles_list ) ) {
			if( $user_roles_list[$user_role] === 1 ) {
				$hide = false;
			}
		}
	} else{ 
		$hide = false;
	}

	if( $hide ) {
		?>
		<h2>You are not allowed to access this document.</h2>
		<?php
		get_footer();
		exit();
	}
	$attchmentData = get_post( $document_id );
}
$fileAccessUrl = add_query_arg( array( 'id' => $_GET['id'] ), get_permalink( get_page_by_path( 'preview-document' ) ) );
?>
<!-- <script src="http://mozilla.github.io/pdf.js/build/pdf.js"></script> -->
<section id="content" class="full-width">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo fusion_render_rich_snippets_for_pages(); // WPCS: XSS ok. ?>
			<?php avada_featured_images_for_pages(); ?>
			<div class="post-content">
				<div class="back-list"><a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/client-dashboard/my-document/'; ?>"><i class="fa-long-arrow-alt-left fas" data-name="angle-left"></i> Back to documents list</a></div>	
				<?php if($attchmentData->post_mime_type === 'video/mp4'): ?>
					<div class="video-container">
						<video controls controlsList="nodownload">
						  	<source src=<?php echo $attchmentData->guid; ?> type="<?php echo $attchmentData->post_mime_type; ?>">
						  	Your browser does not support HTML5 video.
						</video>
					</div>
				<?php else: ?>
					<div id='pdf-viewer'>
						<div class="loadersmall"></div>
					</div>
				<?php endif; ?>
				<?php the_content(); ?>
				<?php fusion_link_pages(); ?>
			</div>
			<?php if ( ! post_password_required( $post->ID ) ) : ?>
				<?php if ( Avada()->settings->get( 'comments_pages' ) ) : ?>
					<?php wp_reset_postdata(); ?>
					<?php comments_template(); ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</section>
<script>
	var unloaded = false;
	var access_type = 0;
	function store_document_meta(access_type = null){      
	    if(!unloaded){
	        jQuery.ajax({
	            type: 'post',
	            async: false,
	            url : custom_ajax_script.ajaxurl,
	            data : {action: "store_document_withdraw_time", 
	            user_id : '<?php echo get_current_user_id(); ?>', 
	            document_id : '<?php echo $_GET['id']; ?>', 
	        	'access_type' : access_type},
	            success:function(response){ 
	            	if(!access_type){
	            		unloaded = true; 
	            	}
	            },
	            timeout: 5000
	        });
	    }
	}   
	jQuery(window).on('beforeunload unload', function(){
		store_document_meta();
	});
	jQuery(window).load(function(){
		access_type = 1;
		store_document_meta(access_type);
	});
    url = '<?php echo $fileAccessUrl; ?>';
    var thePdf = null;
    var scale = 2;
    // Loaded via <script> tag, create shortcut to access PDF.js exports.
	var pdfjsLib = window['pdfjs-dist/build/pdf'];

	// The workerSrc property shall be specified.
	pdfjsLib.GlobalWorkerOptions.workerSrc = '//www.reliablesoftworks.com/wp-content/themes/Avada-Child-Theme/assets/js/pdf.worker.js';

    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        thePdf = pdf;
        viewer = document.getElementById('pdf-viewer');
    	if(pdf.numPages){
    		jQuery('.loadersmall').remove();
    	}
        for(page = 1; page <= pdf.numPages; page++) {
          canvas = document.createElement("canvas");    
          canvas.className = 'pdf-page-canvas';         
          viewer.appendChild(canvas);            
          renderPage(page, canvas);
        }
    });

    function renderPage(pageNumber, canvas) {
        thePdf.getPage(pageNumber).then(function(page) {
          viewport = page.getViewport(scale);
          canvas.height = viewport.height;
          canvas.width = viewport.width;          
          page.render({canvasContext: canvas.getContext('2d'), viewport: viewport});
    	});
    }
</script>
<?php
get_footer();

