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
    // First check if the file appears on the _FILES array
    if(isset($_FILES['test_upload_pdf'])){
        $document = $_FILES['test_upload_pdf'];
        if($document['type'] === 'application/pdf' ||
        $document['type'] === 'text/plain' || 
        $document['type'] === 'application/vnd.oasis.opendocument.spreadsheet' || 
        $document['type'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        $document['type'] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){ 
            // Use the wordpress function to upload
            // test_upload_pdf corresponds to the position in the $_FILES array
            // 0 means the content is not associated with any other posts
            $uploaded=media_handle_upload('test_upload_pdf', 0);
            ?>
            <div class="error-page"><p>
            <?php
            // Error checking using WP functions
            if(is_wp_error($uploaded)){
                    echo "Error uploading file: " . $uploaded->get_error_message();
            }else{
                    echo "File upload successfully!";
            }
            ?>
            </p></div>
            <?php
        } else{
            ?>
            <div class="error-page">
                <p>Sorry, this file type is not permitted for security reasons.</p>
            </div>
            <?php
        }
    }
}

