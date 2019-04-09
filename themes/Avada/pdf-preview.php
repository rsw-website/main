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
?>
<?php get_header(); 
if(!isset($_GET['id'])){
	?>
	<h2>Invalid file path</h2>
	<?php
	get_footer();
	exit();
} else{
	$documentId = base64_decode($_GET['id']);
	$attchmentData = get_post($documentId);
}
$fileAccessUrl = add_query_arg(array('id' => $_GET['id']), get_permalink( get_page_by_path( 'preview-document' )));
?>
<script src="http://mozilla.github.io/pdf.js/build/pdf.js"></script>
<section id="content" class="full-width">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo fusion_render_rich_snippets_for_pages(); // WPCS: XSS ok. ?>
			<?php avada_featured_images_for_pages(); ?>
			<div class="post-content">	
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
    url = '<?php echo $fileAccessUrl; ?>';
    var thePdf = null;
    var scale = 2;
    // Loaded via <script> tag, create shortcut to access PDF.js exports.
var pdfjsLib = window['pdfjs-dist/build/pdf'];

// The workerSrc property shall be specified.
pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        thePdf = pdf;
        viewer = document.getElementById('pdf-viewer');
        debugger;
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
    	debugger;
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

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
