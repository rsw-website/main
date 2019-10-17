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
  <h1 class="wp-heading-inline">Edit Document</h1>
  <a href="<?php echo admin_url( '/admin.php?page=add-new' ); ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>
  <hr class="wp-header-end"/>
  <?php
  if ( count( $response ) ) {
    ?>
  <div class="<?php echo $response['status']; ?>">
    <p> <?php echo $response['message']; ?></p>
  </div>
    <?php
  }
  ?>
  <div id="poststuff">
  <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content" style="position: relative;">
    <form method="post" enctype="multipart/form-data">
      <div id="titlediv">
      <div id="titlewrap">
        <label class="screen-reader-text" id="title-prompt-text" for="title">Enter title here</label>
        <input type="text" name="post_title" size="30" id="title" spellcheck="true" autocomplete="off" value="<?php echo $attchment_data->post_title; ?>">
        <div class="inside">
        <strong>Preview Link:</strong>
        <span id="sample-permalink">
          <a href="<?php echo add_query_arg( 'id', base64_encode( $document_id ), get_permalink( get_page_by_title( 'documents' ) ) ); ?>" target="_blank"><?php echo add_query_arg( 'id', base64_encode( $document_id ), get_permalink( get_page_by_title( 'documents' ) ) ); ?></a>
        </span>
        </div>
      </div>
      </div>
      <h1 class="wp-heading-inline">Tags</h1>
      <div class="tags-list">
      <ul class="tag-list-ul">
        <?php foreach ( $tags as $tag ) : ?>
        <li>
           <a href="javascript:void(0)" class="tag-list <?php echo in_array( $tag['ID'], $selected_tags ) ? 'selected-tag' : ''; ?>" title='<?php echo $tag['tag_name']; ?>'
           tag-slug='<?php echo $tag['tag_slug']; ?>' tag-id='<?php echo $tag['ID']; ?>'>
          <?php echo $tag['tag_name']; ?>
         </a>
        </li>
        <?php endforeach; ?>
      </ul>
      </div>
      <h1 class="hndle ui-sortable-handle"><span>User Roles</span></h2>
      <div class= "tags-list">
      <ul class="tag-list-ul">
        <li>
        <a href="javascript:void(0)" class="role-name <?php echo $user_roles['customer'] === 1 ? 'selected-tag' : ''; ?>" title='Customer'
         role-slug='customer'>
         Customer
        </a>
        </li>
        <li>
        <a href="javascript:void(0)" class="default-role selected-tag" title='Power User'
         role-slug='power_user'>
         Power User
        </a>
        </li>
        <li>
        <a href="javascript:void(0)" class="default-role selected-tag" title='Administrator'
         role-slug='administrator'>
         Administrator
        </a>
        </li>
      </ul>
      </div>
      <input type="hidden" id="tag-id-list" name="tag_ids" value='<?php echo json_encode( $selected_tags ); ?>'/>
      <input type="hidden" id="role-names" name="role_names" value='<?php echo $user_roles_var; ?>'/>
      
      <?php wp_nonce_field( 'wp_edit_document', 'document_edit' ); ?>
      <p>
      <input type="submit" name="update-document" id="add-document" class="button button-primary" value="Update Document">
      </p> 
    </form>
    </div>
  </div>
  </div>    
</div>
