<div class="wrap">
  <h1 class="wp-heading-inline">Upload New Document</h1>
  <form method="post" enctype="multipart/form-data">
      <p id="async-upload-wrap">
        <label class="screen-reader-text" for="async-upload">Upload</label>
        <input type="file" name="test_upload_pdf" id="test_upload_pdf"/>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Upload"> 
        <p class="upload-file-description">
        Allowed document types : <span>.pdf</span><span>.doc</span><span>.docx</span><span>.ppt</span><span>.pptx</span><span>.xls</span><span>.xlsx</span></p>     
      </p>
  </form>
</div>
  <style type="text/css">
      .upload-file-description span{
            background: #f5f5f5;
            color: #666;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            padding: .2em .5em;
            margin-right: 5px;
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