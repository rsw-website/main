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
  <h1 class="wp-heading-inline">Upload New Document</h1>
  <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field( 'wp_add_document', 'document_upload' ); ?>
      <p id="async-upload-wrap">
        <label class="screen-reader-text" for="async-upload">Upload</label>
        <input type="file" name="test_upload_pdf[]" id="test_upload_pdf" multiple required/>
      </p>
      <p class="upload-file-description">
        Allowed document types : <span>.pdf</span><span>.mp4</span><span>.doc</span><span>.docx</span><span>.ppt</span><span>.pptx</span><span>.xls</span><span>.xlsx</span>
      </p>

      <p class="upload-file-description">Maximum upload file size: 500 MB.</p>
      <h2 class="hndle ui-sortable-handle"><span>Tags</span></h2>
      <div class="tags-list">
        <ul class="tag-list-ul">
        <?php foreach ($tags as $tag): ?>
          <li>
            <a href="javascript:void(0)" class="tag-list" title='<?php echo $tag['tag_name']; ?>'
             tag-slug='<?php echo $tag['tag_slug']; ?>' tag-id='<?php echo $tag['ID']; ?>'>
             <?php echo $tag['tag_name']; ?>
            </a>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <h2 class="hndle ui-sortable-handle"><span>User Roles</span></h2>
      <div class="tags-list">
        <ul class="tag-list-ul">
          <li>
            <a href="javascript:void(0)" class="role-name" title='Customer'
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
      <input type="hidden" id="tag-id-list" name="tag_ids"/>
      <input type="hidden" id="role-names" name="role_names"/>
      <p>
        <input type="submit" name="upload-document" id="add-document" class="button button-primary" value="Add Document">
      </p> 
  </form>
</div>
