<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' )  );
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
  $message = "<p>Hi ".$user->first_name.",</p>";
  $message .= "<p>We have excellent news! Your account has been approved for access to ".get_option('siteurl').".</p>";
  $message .= "<p>Please log in with your email address ";
  $message .= "<a href='".get_permalink( get_page_by_path( 'client-login' ) )."'>here</a>.</p>";
  $message .= "<p>If you would like to set or reset your password, you can do so by ";
  $message .= "<a href='".wc_lostpassword_url()."'>clicking here</a>.</p>";
  $message .= "<p>Best Regards,<br>".get_option('blogname')."</p>";
  $message = apply_filters( 'new_user_approve_approve_user_message_default', $message );
  return $message;
}
add_filter('new_user_approve_approve_user_message', 'custom_notification_message', 10, 2);

/**
* Modify the content on denied user notification mail
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
* Modify the content on ddefault notification meesage
*/
// function custom_default_notification_message($message, $user) {
//   $message = "<p>Hi Admin,</p>";
//   // $message .= "<p>$user->first_name $user->last_name has requested a username at ".get_option('blogname').".</p>";
//   // $message .= "<p>To approve or deny this user access to ".get_option('blogname')." go to ".admin_url()." </p>";
//   $message .= "<p>Best Regards,<br>".get_option('blogname')."</p>";
//   return $message;
// }
// add_filter( 'new_user_approve_notification_message_default', 'custom_default_notification_message', 10, 2 );

function wpdocs_enqueue_custom_admin_script() {
         wp_enqueue_script( 'custom-script', plugins_url('documents-management/custom-script.js'), __FILE__);
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_script' );
