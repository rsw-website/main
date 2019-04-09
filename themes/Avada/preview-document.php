<?php
/**
 * Template Name: Preview Document
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
<?php
	if(isset($_GET['id'])){
	$documentId = base64_decode($_GET['id']);
	$attchmentData = get_post($documentId);
	$file = $attchmentData->guid;
	$homeUrl = get_home_url();
	$filename = $attchmentData->post_title;
	$filePath = str_replace($homeUrl, "",$file);
	if($attchmentData->post_mime_type === 'video/mp4'){
	} elseif ($attchmentData->post_mime_type === 'application/pdf') {
	  	header('Content-type: '.$attchmentData->post_mime_type);
	  	header('Content-Disposition: inline; filename="' . $filename . '"');
	  	header('Content-Transfer-Encoding: binary');
	  	header('Accept-Ranges: bytes');
	  	@readfile($file);
	}
}

?>



