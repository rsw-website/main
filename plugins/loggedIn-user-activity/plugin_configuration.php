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
function new_modify_user_table( $column ) {
  $column = array(
    "cb" => "<input type=\"checkbox\" />",
    "username" => __('Username'),
    "name" => __('Name'),
    "company" => __('Company'),
    "email" => __('E-mail'),
    "role" => __('Role'),
    "last_login_activity" => __('Last login activity'),
    "pw_user_status" => __('Status')
  );
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'company' :
            return get_user_meta( $user_id, 'billing_company', true );
            break;
        case 'last_login_activity' :
            $login_date = get_user_meta( $user_id, 'last_login', true );
            if($login_date){
              $formated_date = "<a href='".admin_url('users.php?page=user-activity&id='.$user_id)."'>".date_format(
              date_create($login_date),
               "F j, Y g:i A")."</a>";
            } else{
              $formated_date = '<span aria-hidden="true">—</span>';
            }
            return $formated_date;
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

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

/*** Sort and Filter Users ***/
add_action('restrict_manage_users', 'filter_by_company_name');

function filter_by_company_name($which){
  global $wpdb;
  $companies = $wpdb->get_col("SELECT DISTINCT(meta_value) FROM $wpdb->usermeta WHERE meta_key = 'billing_company' AND meta_value > ''" ); 
  // template for filtering
  $st = '<select name="company_%s" style="float:none;margin-left:10px;">
      <option value="">%s</option>%s</select>';

  // generate options
  $options = '';
  foreach ($companies as $key => $company) {
   $options .= '<option value="'.$company.'">'.$company.'</option>';
  }
   
  // combine template and options
  $select = sprintf( $st, $which, __( 'Company Name' ), $options );

  // output <select> and submit button
  echo $select;
  submit_button(__( 'Filter' ), null, $which, false);
}

add_filter('pre_get_users', 'filter_users_by_job_role_section');

function filter_users_by_job_role_section($query){
  global $pagenow;
  if (is_admin() && 'users.php' == $pagenow) {
    // figure out which button was clicked. The $which in filter_by_job_role()
    $top = $_GET['company_top'];
    $bottom = $_GET['company_bottom'];
    if (!empty($top) OR !empty($bottom)){
      $section = !empty($top) ? $top : $bottom;
     // change the meta query based on which option was chosen
     $meta_query = array (array (
        'key' => 'billing_company',
        'value' => $section,
        'compare' => 'LIKE'
     ));
     $query->set('meta_query', $meta_query);
    }
  }
}