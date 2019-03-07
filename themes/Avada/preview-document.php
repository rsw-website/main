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

<?php //get_header(); ?>
<?php
	if(isset($_GET['id'])){
		$documentId = base64_decode($_GET['id']);
		// echo wp_get_attachment_url($documentId);
		$attchmentData = get_post($documentId);
		$file = 'http://54.80.170.170/wp-content/uploads/2019/02/How_a_Service_Technician_Completes_a_Service_Order.pdf';
		
		$post_mime_type = 'application/pdf';
		$fp = fopen($file, "r") ;

		header("Cache-Control: maxage=1");
		header("Pragma: public");
		header("Content-type: ".$post_mime_type);
		header("Content-Disposition: inline; filename=".$file."");
		header("Content-Description: PHP Generated Data");
		header("Content-Transfer-Encoding: binary");
		header('Content-Length:' . filesize($file));
		ob_clean();
		flush();
		while (!feof($fp)) {
		   $buff = fread($fp, 1024);
		   print $buff;
		}
		exit;
	}
	// $a =  base64_encode('123');
	// echo $a;
	// echo " - ".base64_decode($a);

?>



