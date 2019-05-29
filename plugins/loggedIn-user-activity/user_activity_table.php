  <style type="text/css">
    .error-page{
        background: #fff;
        color: #444;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        padding: 1em 2em;
        max-width: 700px;
        -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);
        box-shadow: 0 1px 3px rgba(0,0,0,0.13);
        margin: 50px auto;
      }
      .error-page p{font-size: 14px;}
      #setting-error-tgmpa, .update-nag{display: none !important;}
  </style>
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
          // $userActivityTable->search_box('Search', 'search');
          $userActivityTable->display();
           ?>
      </form>
  </div>
  
