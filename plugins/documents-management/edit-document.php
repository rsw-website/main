<div class="wrap">
  <h1 class="wp-heading-inline">Edit Document</h1>
  <a href="<?php echo admin_url('/admin.php?page=add-new'); ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">Add New</a>
  <hr class="wp-header-end"/>
  <?php
  if(count($response)){
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
                  <a href="<?php echo add_query_arg( 'id', base64_encode($document_id), get_permalink( get_page_by_title( 'documents' ) ) ); ?>" target="_blank"><?php echo add_query_arg( 'id', base64_encode($document_id), get_permalink( get_page_by_title( 'documents' ) ) ); ?></a>
                </span>
              </div>
            </div>
          </div>
          <h1 class="wp-heading-inline">Tags</h1>
          <div class="tags-list">
            <ul class="tag-list-ul">
              <?php foreach ($tags as $tag): ?>
                <li>
                   <a href="javascript:void(0)" class="tag-list <?php echo in_array($tag['ID'], $selected_tags) ? 'selected-tag' : ''?>" title='<?php echo $tag['tag_name']; ?>'
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
                <a href="javascript:void(0)" class="role-name <?php echo $user_roles['subscriber'] === 1 ? 'selected-tag' : ''; ?>" title='Subscriber'
                 role-slug='subscriber'>
                 Subscriber
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
          <input type="hidden" id="tag-id-list" name="tag_ids" value='<?php echo json_encode($selected_tags); ?>'/>
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
  <style type="text/css">
      ul.tag-list-ul{
        list-style: none;
        margin: 0;
        overflow: hidden; 
        padding: 0;
      }
      ul.tag-list-ul li {
        float: left;
      }
      a.tag-list {
        background: #fff;
        border-radius: 3px 0 0 3px;
        color: #0085ba;
        display: inline-block;
        height: 26px;
        line-height: 26px;
        padding: 0 20px 0 23px;
        position: relative;
        margin: 0 10px 10px 0;
        text-decoration: none;
        -webkit-transition: color 0.2s;
      }
      a.role-name, a.default-role{
        background: #fff;
        border-radius: 3px 0 0 3px;
        color: #0085ba;
        display: inline-block;
        height: 26px;
        line-height: 26px;
        padding: 0 20px 0 23px;
        position: relative;
        margin: 0 10px 10px 0;
        text-decoration: none;
        -webkit-transition: color 0.2s;
      }

      a.tag-list::before {
        background: #f1f1f1;
        border-radius: 10px;
        box-shadow: inset 0 1px rgba(0, 0, 0, 0.25);
        content: '';
        height: 6px;
        left: 10px;
        position: absolute;
        width: 6px;
        top: 10px;
      }

      a.tag-list::after {
        background: #f1f1f1;
        border-bottom: 13px solid transparent;
        border-left: 10px solid #fff;
        border-top: 13px solid transparent;
        content: '';
        position: absolute;
        right: 0;
        top: 0;
      }

      a.tag-list:hover, a.role-name:hover {
        color: #000;
      }
      a.selected-tag{
        color: #fff !important;
        background: #0085ba !important;
        border-color: #0085ba !important;
      }
      a.selected-tag::after{
        border-left-color: #0085ba; 
      }
      a.tag-list:focus, a.role-name:focus{
        outline: none;
        box-shadow: none;
      }

      .upload-file-description span{
            background: #f5f5f5;
            color: #666;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            padding: .2em .5em;
            margin-right: 5px;
            text-decoration: none;
      }
      .error-page{
        background: #fff;
        color: #444;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        padding: 1em 2em;
        max-width: 700px;
        -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);
        box-shadow: 0 1px 3px rgba(0,0,0,0.13);
      }
      .error-page p{font-size: 14px;}
      
  </style>