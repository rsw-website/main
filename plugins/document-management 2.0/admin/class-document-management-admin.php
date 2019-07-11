<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       document-management
 * @since      1.0.0
 *
 * @package    Document_Management
 * @subpackage Document_Management/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Document_Management
 * @subpackage Document_Management/admin
 * @author     Mindfire Solutions <ayushs@mindfiresolutions.com>
 */
class Document_Management_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		global $wpdb;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Document_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Document_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/document-management-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Document_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Document_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/document-management-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register admin menu links to the dashboard
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function admin_menu_list(){
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
	        array($this, 'listAllDocuments')
	    );
	    add_submenu_page(
	        'all-documents',
	        'Add new',
	        'Add new',
	        'manage_options',
	        'add-new',
	        array($this, 'addNewDocument')
	    );
	    add_submenu_page(
	        'all-documents',
	        'Document Tags',
	        'Document Tags',
	        'manage_options',
	        'document-tags',
	        array($this, 'addNewtag')
	    );
	}

	/**
	 * Get list of all the available documents
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function listAllDocuments(){
	    global $wpdb;
	    if(!isset($_GET['edit'])){
	    	$documentsList = new Documents_list_table();
	        include_once('partials/list-all-documents.php');
	    } else{
	        $response = $this->updateDocument($_GET['edit']);
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
	        include_once('partials/edit-document.php');
	    }
	}

	/**
	 * Call add new document view on the page
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function addNewDocument(){
	    global $wpdb;
	    $tags = $wpdb->get_results(
	     "SELECT * FROM wp_document_tags ORDER BY ID DESC", ARRAY_A
	    );
	    include_once('partials/add-new-document.php');
	    $this->insertNewDocument();
	}

	/**
	 * Function to add new document tag in the database
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function addNewtag(){
	    global $wpdb;
	    if(!$_GET['tag_id']){
	        $response = $this->insertNewTag();
	        $tags_list_table = new Tags_list_table();
	        include_once('partials/add-new-tag.php');
	    } else{
	        $response = $this->updateTag($_GET['tag_id']);
	        $tag_details = $wpdb->get_row(
	         "SELECT * FROM wp_document_tags WHERE ID = ".$_GET['tag_id'], ARRAY_A
	        );
	        include_once('partials/edit-tag.php');
	    }
	}

	/**
	 * Function to update document name, tags and role in database
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function updateDocument($document_id){
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
	            $this->insert_record($tag_ids, $role_names, $document_id);

	            $response['status'] = 'updated';
	            $response['message'] = 'Document updated successfully.';
	            return $response;
	        }
	    }
	}

	/**
	 * Insert new document rocord in the database
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function insertNewDocument(){
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
                                        $this->insert_record($tag_ids, $role_names, $attachment_id);
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

	/**
	 * wordpress database queries to insert record
	 *
	 * @since 1.0.0
	 *
	 * @return  boolean true or false
	 */
	public function insert_record($tag_ids, $role_names, $attachment_id) {
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

	/**
	 *Update document tag record in database
	 *
	 * @since 1.0.0
	 *
	 * @param int $tag_id Tag Id
	 *
	 * @return  array $errorStatus
	 */
	public function updateTag($tag_id){
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

	/**
	 * Insert new tag record in the database
	 *
	 * @since 1.0.0
	 *
	 * @return  array $errorStatus
	 */
	public function insertNewTag(){
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

}
