<?php
// include_once('generate-table-list.php');
    $myListTable = new My_Example_List_Table();
  ?>
  <div class="wrap">
      <h1 class="wp-heading-inline">All Documents</h1>
      <a href="<?php echo admin_url('/admin.php?page=add-new'); ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>
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
