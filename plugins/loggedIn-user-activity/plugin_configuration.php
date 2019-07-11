<?php
/*
Plugin Name: Logged In User Activity
Description: This plugin is used to track all the documents accessed by user. It will list the document access time, withdraw time, total time for which the document is open and it will also list whether the document is marked as favourite or not. 
Author: Mindfire Solutions
Version: 1.0
*/

/**
* Add new register fields for WooCommerce registration.
*
* @return string Register fields HTML.
*/

include_once('generate_user_activity_table.php');




add_action('admin_menu', 'user_activity_menu');

function user_activity_menu() {
  add_menu_page('Client User Activity', 'User Activities', 'read', 'user-activity', 'get_user_activity');
  remove_menu_page('user-activity');
}

function get_the_user_ip() {
  foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
     if (array_key_exists($key, $_SERVER) === true) {
         foreach (explode(',', $_SERVER[$key]) as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return $ip;
          }
        }
     }
  }
}

function get_user_activity(){
  	include_once('user_activity_table.php');
}

/**
 * Capture user login and add it as timestamp in user meta data
 *
 */
 
function user_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_login', gmdate("Y-m-d h:i:s") );
    update_user_meta( $user->ID, 'user_login_ip', get_the_user_ip() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );



