<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       user-document-activity-record
 * @since      1.0.0
 *
 * @package    User_Document_Activity_Record
 * @subpackage User_Document_Activity_Record/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
  <?php
    if(!isset($_GET['id'])){
  ?>
      <div class="error-page">
        <p>Invalid user ID.</p>
      </div>
  <?php
      die();
    }  
    $userActivityTable = new User_activity_list_table();
    $userData = get_user_by('ID', $_GET['id']);
  ?>
  <div class="wrap">
      <h1 class="wp-heading-inline">User Activity - <?php echo get_user_meta( $_GET['id'], 'first_name', true ).' '.get_user_meta( $_GET['id'], 'last_name', true ) . ' - ' . get_user_meta( $_GET['id'], 'user_login_ip', true );  ?></h1>
      <form id="document-filter" method="post">
        <button name="refresh" style="float: right;" onClick="window.location.reload()" class="page-title-action aria-button-if-js">Refresh</button>
          <!-- For plugins, we also need to ensure that the form posts back to our current page -->
          <?php //wp_nonce_field( 'wp_delete_document', 'document_wpnonce' ); ?>
          <input type="hidden" name="page" value="<?php //echo $_REQUEST['page'] ?>" />
          <!-- Now we can render the completed list table -->
          <?php
          if( isset($_POST['s']) ){
           $searchString = $_POST['s'];
           } else {
           $searchString = '';
           }
          $userActivityTable->prepare_items($_GET['id'], $searchString);
          $userActivityTable->display();
           ?>
      </form>
  </div>
  
