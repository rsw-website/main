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
      <?php
      $html = '';
        foreach ($tags as $tag){
          
          $html .= "<a href='javascript:void(0)' title='{$tag['tag_name']}' class='tag-list' tag-slug='{$tag['tag_slug']}' tag-id='{$tag['ID']}'>";
          $html .= "{$tag['tag_name']}</a>";
        }
      echo $html;
      ?>
      </div>
      <input type="hidden" id="tag-id-list" name="tag_ids"/>
      <p>
        <input type="submit" name="upload-document" id="add-document" class="button button-primary" value="Add Document">
      </p> 
  </form>
</div>
  <style type="text/css">
      .tags-list > a{
        background: #fff;
        color: #0085ba;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        padding: .2em .5em;
        margin-right: 5px;
        text-decoration: none;
        outline: none;
      }
      .tags-list > a:hover{
        color: #000;
      }

      a.selected-tag{
        color: #fff !important;
        background: #0085ba;
        border-color: #0085ba;
      }

      .tags-list > a:focus{
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