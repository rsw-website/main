<?php

/**
* Enqueue style sheet in the theme
*
* @return void
*/
function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' )  );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

/**
* Enqueue style sheet in the theme
*
* @return void
*/
function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

/**
* Enqueue script file in the theme
*
* @return void
*/
function add_custom_theme_scripts() {
  wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js', array ( 'jquery' ), null, true);
}
add_action( 'wp_enqueue_scripts', 'add_custom_theme_scripts' );

/**
* Configure SMTP credentials 
*
* @return void
*/
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
add_action( 'phpmailer_init', 'send_smtp_email' );

/**
* Create custom header for emails
*
* @return array
*/

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
* Create custom message template for approved user register
*
* @return string
*/
function custom_notification_message($message, $user) {
  $message = "<p>Hi ".$user->first_name.",</p>";
  $message .= "<p>We have excellent news! Your account has been approved for access to ".get_option('siteurl').".</p>";
  $message .= "<p>Please log in with your email address ";
  $message .= "<a href='".get_permalink( get_page_by_path( 'client-login' ) )."'>here</a>.</p>";
  $message .= "<p>If you would like to set or reset your password, you can do so by ";
  $message .= "<a href='".wc_lostpassword_url()."'>clicking here</a>.</p>";
  $message .= "<p>Best Regards,<br>".get_option('blogname')."</p>";
  return $message;
}
add_filter('new_user_approve_approve_user_message', 'custom_notification_message', 10, 2);

/**
* Create custom message template for denied user registration
*
* @return string
*/
function custom_denied_notification_message($message, $user) {
  $message = "<p>Hi ".$user->first_name.",</p>";
  $message .= "<p>We are sorry to inform you that your user access request for ".get_option('blogname')." has been denied.</p>";
  $message .= "<p>If you feel that this is an error, please have your company's main point of
  contact reach out to us.</p>";
  $message .= "<p>Sorry for any inconvenience.</p>";
  $message .= "<p>Best Regards,<br>".get_option('blogname')."</p>";
  return $message;
}
add_filter( 'new_user_approve_deny_user_message', 'custom_denied_notification_message', 10, 2 );

/**
* Create custom message template for new user register
*
* @return string
*/
function custom_default_notification_message($message, $user_login) {
  $user = get_user_by( 'login', $user_login );
  $message = "<p>Hi Admin,</p>";
  $message .= "<p>User(".$user->user_email.") has requested an account at ".get_option('blogname').".</p>";
  $message .= "<p>To approve or deny this user access to ".get_option('blogname')." go to ";
  $message .= "<a href='".admin_url('users.php?s&pw-status-query-submit=Filter&new_user_approve_filter=pending&paged=1')."'>".admin_url('users.php?s&pw-status-query-submit=Filter&new_user_approve_filter=pending&paged=1')."</a>.</p>";
  $message .= "<p>Best Regards,<br>".get_option('blogname')."</p>";
  return $message;
}
add_filter( 'new_user_approve_request_approval_message', 'custom_default_notification_message', 10, 2 );

/**
* Enqueue custom script file in custom plugin
*
* @return void
*/
function enqueue_custom_admin_script() {
  wp_enqueue_script( 'custom-script', plugins_url('documents-management/custom-script.js'), __FILE__);
}
add_action( 'admin_enqueue_scripts', 'enqueue_custom_admin_script' );
