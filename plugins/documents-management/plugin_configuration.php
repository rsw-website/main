<?php
/*
Plugin Name: Documents Management
Description: A plugin to manage all the websist documents 
Author: Mindfire Solutions
Version: 1.0
*/

include_once('list-all-documents.php');

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
        'listAllDocuements'
    );

    add_submenu_page(
        'all-documents',
        'Add new',
        'Add new',
        'manage_options',
        'add-new',
        'addNewDocument'
    );
}
add_action( 'admin_menu', 'sd_register_top_level_menu' );

function listAllDocuements(){
    include_once('generate-table-list.php');
}

function addNewDocument(){
    include_once('upload-new-document.php');
    insertNewDocument();
}

function insertNewDocument(){
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    ?>
    <div class="error-page">
    <?php
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

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
                        <?php                    } else {
                        // The image was uploaded successfully!
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

