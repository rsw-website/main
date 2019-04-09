<?php
// include_once('generate-table-list.php');
    $myListTable = new My_Example_List_Table();
  ?>
  <div class="wrap">
      <h1 class="wp-heading-inline">All Documents</h1>
      <a href="<?php echo admin_url('/admin.php?page=add-new'); ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>
      <hr class="wp-header-end"/>
      <?php
      if(isset($_GET['deleted'])){
        if(intval($_GET['deleted']) === 1){
          $message = 'Media file permanently deleted.';
        } elseif (intval($_GET['deleted']) > 1) {
          $message = $_GET['deleted'].' media files permanently deleted.';
        } else{
          $message = '';
        }
        ?>
          <div id="message" class="updated notice is-dismissible"><p><?php echo $message; ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
        <?php
      }
      ?>
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
          $myListTable->prepare_items($searchString);
          $myListTable->search_box('Search', 'search');
          $myListTable->display(); ?>
      </form>
  </div>
