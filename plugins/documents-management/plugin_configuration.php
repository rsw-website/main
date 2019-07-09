<?php
/*
Plugin Name: Documents Management
Description: A plugin to manage all the websist documents 
Author: Mindfire Solutions
Version: 1.0
*/

include_once('list-all-documents.php');
include_once('list-all-tags.php');

function sd_register_top_level_menu(){
    add_menu_page(
        'Documents',
        'Documents',
        '',
        'all-documents',
        'listUploadedDocuments',
        '',
        6
    );

    add_submenu_page(
        'all-documents',
        'All Documents',
        'All Documents',
        'manage_options',
        'all-document',
        'listAllDocuments'
    );

    add_submenu_page(
        'all-documents',
        'Add new',
        'Add new',
        'manage_options',
        'add-new',
        'addNewDocument'
    );
    add_submenu_page(
        'all-documents',
        'Document Tags',
        'Document Tags',
        'manage_options',
        'document-tags',
        'addNewtag'
    );
}
// add_action( 'admin_menu', 'sd_register_top_level_menu' );

function listAllDocuments(){
    global $wpdb;
    if(!isset($_GET['edit'])){
        include_once('generate-table-list.php');
    } else{
        $response = updateDocument($_GET['edit']);
        $document_id = $_GET['edit'];
        $attchment_data = get_post($document_id);
        $document_data = $wpdb->get_results(
         "SELECT * FROM wp_posts WHERE post_type = 'attachment' AND post_status = 'inherit' AND ID = '".$document_id."'", ARRAY_A);
        $tags = $wpdb->get_results(
         "SELECT * FROM wp_document_tags ORDER BY ID DESC", ARRAY_A
        );
        $selected_tags = $wpdb->get_results(
         "SELECT tag_id FROM wp_document_tags_log WHERE document_id = '".$document_id."'", ARRAY_A
        );
        $selected_tags = wp_list_pluck( $selected_tags, 'tag_id' );
        $user_roles_var = $wpdb->get_var(
         "SELECT user_roles FROM wp_document_user_role WHERE document_id = '".$document_id."'");
        $user_roles = json_decode($user_roles_var, true);
        include_once('edit-document.php');
    }
}

function addNewDocument(){
    global $wpdb;
    $tags = $wpdb->get_results(
     "SELECT * FROM wp_document_tags ORDER BY ID DESC", ARRAY_A
    );
    include_once('upload-new-document.php');
    insertNewDocument();
}

function addNewtag(){
    global $wpdb;
    if(!$_GET['tag_id']){
        $response = insertNewTag();
        include_once('add-new-tag.php');
    } else{
        $response = updateTag($_GET['tag_id']);
        $tag_details = $wpdb->get_row(
         "SELECT * FROM wp_document_tags WHERE ID = ".$_GET['tag_id'], ARRAY_A
        );
        include_once('edit-tag.php');
    }
}

function insertNewDocument(){
    if (isset($_POST['upload-document'])) {
        ?>
        <div class="error-page">
        <?php
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $nonce = esc_attr( $_REQUEST['document_upload'] );
        if ( ! wp_verify_nonce( $nonce, 'wp_add_document' ) ) {
            echo "Something went wrong. Unable to add tag.";
        } else{
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                if(strlen($_POST['tag_ids'])){
                    $tag_ids = json_decode(stripslashes($_POST['tag_ids']), true);
                } else{
                    $tag_ids = [];
                }
                if(strlen($_POST['role_names'])){
                    $role_names = json_encode(json_decode(stripslashes($_POST['role_names']),true));
                } else{
                    $role_names = array(
                        'customer' => 0
                    );
                    $role_names = json_encode($role_names);
                }
                $files = $_FILES["test_upload_pdf"];
                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        if($files['type'][$key] === 'application/pdf' ||
                            $files['type'][$key] === 'text/plain' || 
                            $files['type'][$key] === 'application/vnd.oasis.opendocument.spreadsheet' || 
                            $files['type'][$key] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                            $files['type'][$key] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                            $files['type'][$key] === 'video/mp4' ||
                            $files['type'][$key] === 'video/x-matroska'){
                                $file = array(
                                    'name' => $files['name'][$key],
                                    'type' => $files['type'][$key],
                                    'tmp_name' => $files['tmp_name'][$key],
                                    'error' => $files['error'][$key],
                                    'size' => $files['size'][$key]
                                );
                                $_FILES = array("upload_file" => $file);
                                $attachment_id = media_handle_upload("upload_file", 0);

                                if (is_wp_error($attachment_id)) {
                                    // There was an error uploading the image.
                                    ?>
                                    <p><?php echo $files['name'][$key]; ?> - Error uploading File!!
                                    </p>
                                    <?php 
                                } else {
                                    // The image was uploaded successfully!
                                    // insert tag in database of current document ID
                                        insert_record($tag_ids, $role_names, $attachment_id);
                                    ?>
                                    <p><?php echo $files['name'][$key]; ?> - File uploaded successfully!!
                                    </p>
                                    <?php
                                }
                        } else{
                            ?>
                            <p><?php echo $files['name'][$key]; ?> - This file type is not permitted for security reasons.</p>
                            <?php
                        }
                    }
                }
                ?>
        </div>
        <?php
        }
    }
}

function insert_record($tag_ids, $role_names, $attachment_id) {
    global $wpdb;
    $document_tags_data = array();
    $place_holders = array();
    // insert documents user role data
    $document_user_role = 'wp_document_user_role';
    $insert_record = $wpdb->insert( 
      $document_user_role, 
      array( 
        'document_id' => $attachment_id, 
        'user_roles' => $role_names,
    ));
    if(count($tag_ids)){
        foreach($tag_ids as $key => $tag_id){
            array_push($document_tags_data,
             $attachment_id, $tag_id);
            $place_holders[] = "( %s, %s)";
        }
        $query = "INSERT INTO wp_document_tags_log (document_id, tag_id) VALUES ";
        $query .= implode( ', ', $place_holders );
        $sql = $wpdb->prepare( "$query ", $document_tags_data );
        if ( $wpdb->query( $sql ) ) {
            return true;
        } else {
            return false;
        }
    }
}

function insertNewTag(){
    global $wpdb;
    $table_name = 'wp_document_tags';
    $errorStatus = [];
    if (isset($_POST['create-tag'])) {
        $nonce = esc_attr( $_REQUEST['document_tag'] );
        if ( ! wp_verify_nonce( $nonce, 'wp_add_tag' ) ) {
            echo "Something went wrong. Unable to add tag.";
        } else{
            // sanatize tag name
            $tag_name = ( isset( $_POST['tag-name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['tag-name'] ) ) : ''; // WPCS: CSRF ok.
            $query = $wpdb->prepare(
                "SELECT ID FROM $table_name WHERE tag_name = %s", $tag_name
            );
            if(strlen($tag_name)){
                $wpdb->query( $query );
                if ( $wpdb->num_rows ) {
                    $errorStatus['hasError'] = 1;
                    $errorStatus['message'] = 
                    'A term with the name provided already exists.';
                }
            } else{
                $errorStatus['hasError'] = 1;
                $errorStatus['message'] = 'Tag name cannot be empty';
            }
            if(!count($errorStatus)){
                // create tag slug
                $tag_slug = ( strlen( $_POST['slug'] ) ) ? sanitize_title( $_POST['slug'] ) : sanitize_title( $_POST['tag-name'] );
                $slug_text = $tag_slug; 
                $count = 2;
                while($wpdb->get_var("SELECT tag_slug FROM $table_name WHERE tag_slug = '".$tag_slug."'")){
                    $tag_slug = $slug_text.'-'.$count++;
                }
                // get tag description
                if ( function_exists( 'sanitize_textarea_field' ) ) {
                $description = ( isset( $_POST['description'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : ''; // WPCS: CSRF ok.
                } else {
                    $description = ( isset( $_POST['description'] ) ) ? wp_unslash( $_POST['description'] ) : ''; // WPCS: CSRF ok sanitization ok.
                }

                echo "store data";
                // insert record in DB
                $insert_record = $wpdb->insert( 
                  $table_name, 
                  array( 
                    'tag_name' => $tag_name, 
                    'tag_slug' => $tag_slug,
                    'tag_description' => $description
                ));
                if($insert_record){
                    $errorStatus['hasError'] = 0;
                    $errorStatus['message'] = 'Tag added successfully.';
                } else{
                    $errorStatus['hasError'] = 1;
                    $errorStatus['message'] = 'Unable to create tag. Please try again.';
                }
                return $errorStatus;
            } else{
                return $errorStatus;
            }

        }
    }
}

function updateTag($tag_id){
    global $wpdb;
    $table_name = 'wp_document_tags';
    $errorStatus = [];
    if (isset($_POST['update-tag'])) {
        $nonce = esc_attr( $_REQUEST['document_tag'] );
        if ( ! wp_verify_nonce( $nonce, 'wp_add_tag' ) ) {
            echo "Something went wrong. Unable to update tag.";
        } else{
            // sanatize tag name
            $tag_name = ( isset( $_POST['tag-name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['tag-name'] ) ) : ''; // WPCS: CSRF ok.
            if(!strlen($tag_name)){
                $errorStatus['hasError'] = 'error';
                $errorStatus['message'] = 'Tag name cannot be empty';
            }
            if(!count($errorStatus)){
                // get tag description
                if ( function_exists( 'sanitize_textarea_field' ) ) {
                $description = ( isset( $_POST['description'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : ''; // WPCS: CSRF ok.
                } else {
                    $description = ( isset( $_POST['description'] ) ) ? wp_unslash( $_POST['description'] ) : ''; // WPCS: CSRF ok sanitization ok.
                }
                // Update record in DB
                $updated_tag = $wpdb->query($wpdb->prepare("UPDATE $table_name SET tag_name = %s, tag_description = %s, tag_modified = %s WHERE ID = %s",
                    $tag_name, $description ,date("Y-m-d H:i:s"), $tag_id)
                );
                $errorStatus['hasError'] = 'updated';
                $errorStatus['message'] = 'Tag updated successfully.';
            }
            return $errorStatus;

        }
    }
}

function updateDocument($document_id){
    global $wpdb;
    $response = [];
    if (isset($_POST['update-document'])) {
        $nonce = esc_attr( $_REQUEST['document_edit'] );
        if ( ! wp_verify_nonce( $nonce, 'wp_edit_document' ) ) {
            $response['status'] = 'error';
            $response['message'] = 'Something went wrong. Unable to update document.';
        } else{
            $post_table = 'wp_posts'; 
            $tags_relation_table = 'wp_document_tags_log'; 
            $document_user_role = 'wp_document_user_role';

            $post_title = sanitize_text_field( wp_unslash( $_POST['post_title'] ) );

            if(strlen($_POST['tag_ids'])){
                $tag_ids = json_decode(stripslashes($_POST['tag_ids']), true);
            } else{
                $tag_ids = [];
            }
            if(strlen($_POST['role_names'])){
                $role_names = json_encode(json_decode(stripslashes($_POST['role_names']),true));
            } else{
                $role_names = array(
                    'customer' => 0
                );
                $role_names = json_encode($role_names);
            }
            // update post title
            $updated_post = $wpdb->query($wpdb->prepare("UPDATE $post_table 
                    SET post_title = %s WHERE ID = %s",
                    $post_title, $document_id)
            );
            // remove all tags relation
            $deleted_tags = $wpdb->query($wpdb->prepare(
                "DELETE FROM $tags_relation_table WHERE document_id = %s", 
                $document_id)
            );
            $delete_document_user_role = $wpdb->query($wpdb->prepare(
                "DELETE FROM $document_user_role WHERE document_id = %s", 
                $document_id)
            );
            insert_record($tag_ids, $role_names, $document_id);

            $response['status'] = 'updated';
            $response['message'] = 'Document updated successfully.';
            return $response;
        }
    }
}



