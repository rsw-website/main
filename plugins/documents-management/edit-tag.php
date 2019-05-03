<?php
$tags_list_table = new Tags_list_table();
?>
<div class="wrap">
  <h1 class="wp-heading-inline">Edit Tag</h1>
  <hr class="wp-header-end">
  <div id="col-container" class="wp-clearfix">
    <!-- <div id="col-left"> -->
      <div class="col-wrap">
        <div class="form-wrap">
            <?php
            if(count($response)){
            ?>
            <div class="<?php echo $response['hasError']; ?>">
              <p> <?php echo $response['message']; ?></p>
            </div>
            <?php
            } 
            ?>
          <form id="edittag" method="post" action="" class="validate">
            <?php wp_nonce_field( 'wp_add_tag', 'document_tag' ); ?>
            <table class="form-table">
              <tbody>
                <tr class="form-field form-required term-name-wrap">
                  <th scope="row">
                    <label for="name">Name</label>
                  </th>
                  <td>
                    <input name="tag-name" id="tag-name" type="text" value="<?php echo $tag_details['tag_name']; ?>" size="40" aria-required="true" required>
                    <p class="description">The name is how it appears on your site.</p>
                  </td>
                </tr>
                <tr class="form-field term-description-wrap">
                  <th scope="row"><label for="description">Description</label></th>
                  <td>
                    <?php wp_editor(  $tag_details['tag_description'], 'description' ); ?>
                  </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="update-tag" id="submit" class="button button-primary" value="Update"></p>
          </form>
        </div>
      </div>
    <!-- </div> -->
  </div>
</div>