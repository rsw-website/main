<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       document-management
 * @since      1.0.0
 *
 * @package    Document_Management
 * @subpackage Document_Management/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
  <div class="wrap">
      <h1 class="wp-heading-inline">All Documents</h1>
      <a href="<?php echo admin_url('/admin.php?page=add-new'); ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>
      <hr class="wp-header-end"/>
      <?php
      if(isset($_GET['deleted'])){
        if(intval($_GET['deleted']) === 1){
          $message = 'Document permanently deleted.';
        } elseif (intval($_GET['deleted']) > 1) {
          $message = $_GET['deleted'].' documents permanently deleted.';
        } else{
          $message = '';
        }
        ?>
          <div id="message" class="updated notice is-dismissible"><p><?php echo $message; ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
        <?php
      }
      ?>
      <?php $documentsList->views(); ?>
      <form id="document-filter" method="post">
          <!-- For plugins, we also need to ensure that the form posts back to our current page -->
          <?php wp_nonce_field( 'wp_delete_document', 'document_wpnonce' ); ?>
          <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
          <!-- Now we can render the completed list table -->
          <?php
          if( isset($_POST['s']) ){
           $searchString = $_POST['s'];
           } else {
           $searchString = '';
           }
          $documentsList->prepare_items($searchString);
          $documentsList->search_box('Search', 'search');
          $documentsList->display(); ?>
      </form>
  </div>