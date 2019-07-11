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
  <h1 class="wp-heading-inline">Document Tags</h1>
  <hr class="wp-header-end">
  <div id="col-container" class="wp-clearfix">
    <div id="col-left">
      <div class="col-wrap">
        <div class="form-wrap">
          <h2>Add New Tag</h2>
            <?php
            if(count($response)){
            ?>
            <div class="error">
              <p> <?php echo $response['message']; ?></p>
            </div>
            <?php
            } 
            ?>
          <form id="addtag" method="post" action="<?php echo esc_html( admin_url( 'admin.php?page=document-tags' ) ); ?>" class="validate">
            <?php wp_nonce_field( 'wp_add_tag', 'document_tag' ); ?>
            <div class="form-field form-required term-name-wrap">
              <label for="tag-name">Name</label>
              <input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" required>
              <p>The name is how it appears on your site.</p>
            </div>
            <div class="form-field term-slug-wrap">
              <label for="tag-slug">Slug</label>
              <input name="slug" id="tag-slug" type="text" value="" size="40">
              <p>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p>
            </div>
            <div class="form-field term-description-wrap">
              <label for="tag-description">Description</label>
              <textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
              <p>The description is not prominent by default; however, some themes may show it.</p>
            </div>
            <p class="submit"><input type="submit" name="create-tag" id="submit" class="button button-primary" value="Add New Tag"></p>
          </form>
        </div>
      </div>
    </div>
    <div id="col-right">
      <div class="col-wrap">
        <?php
          if(isset($_GET['deleted'])){
            if(intval($_GET['deleted']) === 1){
              $message = 'Tag permanently deleted.';
            } elseif (intval($_GET['deleted']) > 1) {
              $message = $_GET['deleted'].' tags permanently deleted.';
            } else{
              $message = '';
            }
            ?>
              <div id="message" class="updated notice is-dismissible"><p><?php echo $message; ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
            <?php
          }
        ?>
        <form id="tag-filter" method="post">
          <?php wp_nonce_field( 'wp_delete_tag', 'tag_delete_wpnonce' ); ?>
          <input type="hidden" name="page" value="<?php //echo $_REQUEST['page'] ?>" />
          <!-- Now we can render the completed list table -->
          <?php
          if( isset($_POST['s']) ){
           $searchString = $_POST['s'];
           ?>
           <span class="subtitle">Search results for “<?php echo $searchString; ?>”</span>
           <?php
           } else {
           $searchString = '';
           }
          $tags_list_table->prepare_items($searchString);
          $tags_list_table->search_box('Search', 'search');
          $tags_list_table->display();
           ?>
      </form>
      </div>
    </div>
  </div>
</div>