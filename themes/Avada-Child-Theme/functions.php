<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' )  );
    wp_enqueue_style( 'jquery_datatables_css', 'http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css', array( 'avada-stylesheet' )  );
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
  wp_enqueue_script( 'jquery_datatables_js', 'http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js', array ( 'jquery' ), null, true);

}
add_action( 'wp_enqueue_scripts', 'add_custom_theme_scripts' );

add_action( 'phpmailer_init', 'send_smtp_email' );
function send_smtp_email( $phpmailer ) {
  $phpmailer->isSMTP();
  $phpmailer->Host       = 'mail.reliablesoftworks.info';
  $phpmailer->SMTPAuth   = true;
  $phpmailer->Port       = '465';
  $phpmailer->Username   = 'web@reliablesoftworks.info';
  $phpmailer->Password   = 'Z@n3*Gs!gV';
  $phpmailer->SMTPSecure = 'ssl';
  $phpmailer->From       = 'web@reliablesoftworks.info';
  $phpmailer->FromName   = 'Reliable Softworks';
}

function custom_email_headers() {
  $admin_email = get_option( 'admin_email' );
  if ( empty( $admin_email ) ) {
    $admin_email = 'support@' . $_SERVER['SERVER_NAME'];
  }

  $from_name = get_option( 'blogname' );

  $headers = array(
    "From: \"{$from_name}\" <{$admin_email}>\n",
  );
  return $headers;
}

/**
* Modify the notification message
* $content the notification message
*/
function custom_notification_message($message, $user) {
    $message = __( 'You have been approved to access '.get_option('blogname'), 'new-user-approve' ) . "\r\n\r\n";
  $message .= "username : ".$user->display_name." \r\n\r\n";
  $message .= get_permalink( get_page_by_path( 'client-login' ) )." \r\n\r\n";
    $message .= __( 'To set or reset your password, visit the following address:', 'new-user-approve' ) . "\r\n\r\n";
    $message .= wc_lostpassword_url();

  $message = apply_filters( 'new_user_approve_approve_user_message_default', $message );
  return $message;
}
add_filter('new_user_approve_approve_user_message', 'custom_notification_message', 10, 2);

