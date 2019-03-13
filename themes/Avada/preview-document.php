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
		$rootPath = str_replace('/wp-content/themes', '', get_theme_root());
		$relativePath = str_replace($homeUrl, $rootPath, $file);
		$post_mime_type = $attchmentData->post_mime_type;
		$fp = fopen($file, "r") ;

		header("Cache-Control: maxage=1");
		header("Pragma: public");
		header("Content-type: ".$post_mime_type);
		header("Content-Disposition: inline; filename=".$file."");
		header("Content-Description: PHP Generated Data");
		header("Content-Transfer-Encoding: binary");
		header('Content-Length:' . filesize($relativePath));
		ob_clean();
		flush();
		while (!feof($fp)) {
		   $buff = fread($fp, 1024);
		   print $buff;
		}
		exit;
	}

?>



