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
	$a =  base64_encode('123');
	echo $a;
	echo " - ".base64_decode($a);

?>

<h1>Preview document</h1>


