<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

/**
* Add new custom script file
*
* @return void
*/

function add_custom_theme_scripts() {
  wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js', array ( 'jquery' ), null, true);
}
add_action( 'wp_enqueue_scripts', 'add_custom_theme_scripts' );

add_action( 'phpmailer_init', 'send_smtp_email' );
function send_smtp_email( $phpmailer ) {
  $phpmailer->isSMTP();
  $phpmailer->Host       = 'smtp.gmail.com';
  $phpmailer->SMTPAuth   = true;
  $phpmailer->Port       = '587';
  $phpmailer->Username   = 'sachdevaayush.sachdeva39@gmail.com';
  $phpmailer->Password   = '@yush1004';
  $phpmailer->SMTPSecure = 'tls';
  $phpmailer->From       = 'sachdevaayush.sachdeva39@gmail.com';
  $phpmailer->FromName   = 'Reliable Softworks';
}

function my_account_menu_order() {
  $menuOrder = array(
    'edit-account'      => __( 'Account Details', 'woocommerce' ),
    'my-document'          => __( 'My Documents', 'woocommerce' ),
    'customer-logout'    => __( 'Logout', 'woocommerce' ),
  );
  return $menuOrder;
 }
 add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );

 /*
 * Step 2. Register Permalink Endpoint
 */
add_action('woocommerce_init', 'custom_add_endpoint');
function custom_add_endpoint() {
 
  // WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
  add_rewrite_endpoint( 'my-document', EP_PAGES );
}

$endpoint = 'my-document';
 
add_action( 'woocommerce_account_' . $endpoint .  '_endpoint', 'wk_endpoint_content' );
 
function wk_endpoint_content() {
    //content goes here
    ?>
      <h2 class="avada-woocommerce-myaccount-heading">
        My Documents     
      </h2>
      <div class="document-request-text">
        <p>You do not have permission to access the documents added by the admin.<br/>
        Click on the below button to request access for all the documents.</p>
        <button class="document-request-access">Request Document Access</button>
      </div>
    <?php 
}
