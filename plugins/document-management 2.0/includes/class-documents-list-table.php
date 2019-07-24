<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       document-management
 * @since      1.0.0
 *
 * @package    Document_Management
 * @subpackage Document_Management/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Document_Management
 * @subpackage Document_Management/includes
 * @author     Mindfire Solutions <ayushs@mindfiresolutions.com>
*/

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Documents_list_table extends WP_List_Table {
    
  function __construct(){
    global $status, $page;
      parent::__construct( array(
          'singular'  => __( 'document', 'documentslist' ),     //singular name of the listed records
          'plural'    => __( 'documents', 'documentslist' ),   //plural name of the listed records
          'ajax'      => false        //does this table support ajax?
      ) );
  }

  /**
   * Get table column details
   *
   * @param array $item
   * @param string $coulumn_name
   *
   * @return string $item[ $column_name ]
   */
  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'post_title':
        case 'guid':
        case 'post_date_gmt':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

  /**
   * Register column names in array
   *
   * @return array $column column names
   */
  function get_columns(){
    $columns = array(
        'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
        'post_title' => __( 'Title', 'documentslist' ),
        'guid'    => __( 'Preview URL', 'documentslist' ),
        'post_permalink'    => __( 'Permalink', 'documentslist' ),
        'post_date_gmt'      => __( 'Published Date', 'documentslist' ),
        'user_roles'      => __( 'User Roles', 'documentslist' )
    );
    return $columns;
  }

  /**
   * Create array of sortable columns on which the tables data will be sorted
   *
   * @return array $sortable_columns
   */
  function get_sortable_columns() {
    $sortable_columns = array(
        'post_title'     => array('post_title',false),     //true means it's already sorted
        'post_date_gmt'    => array('post_date_gmt',false),
    );
    return $sortable_columns;
  }

  /**
   * Create array of options required to perform bulk action
   *
   * @return array $actions
   */
  function get_bulk_actions() {
    $actions = array(
        'bulk-delete'    => 'Delete'
    );
    return $actions;
  }

  /**
   * Function to perform bulk actions like delelte multiple records or single record
   *
   * @return void
   */
  function process_bulk_action() {
    //Detect when a bulk action is being triggered...
    $nonce = esc_attr( $_REQUEST['document_wpnonce'] );
    if( 'inactive'=== $this->current_action() ) {
      if ( ! wp_verify_nonce( $nonce, 'wp_delete_document' ) ) {
        die( 'Something went wrong. Unable to delete file.' );
      } else {
        $status = 'trash';
        self::update_document_status($status, absint( $_GET['document'] ));
        wp_redirect('?page=all-document&post-status='.$_GET['post-status'].'&trashed=1');
        exit;
      }
    }
    if( 'active'=== $this->current_action() ) {
      if ( ! wp_verify_nonce( $nonce, 'wp_delete_document' ) ) {
        die( 'Something went wrong. Unable to delete file.' );
      } else {
        $status = 'inherit';
        self::update_document_status($status, absint( $_GET['document'] ));
        wp_redirect('?page=all-document&post-status='.$_GET['post-status'].'&trashed=1');
        exit;
      }
    }
    if( 'delete'=== $this->current_action() ) {
      if ( ! wp_verify_nonce( $nonce, 'wp_delete_document' ) ) {
        die( 'Something went wrong. Unable to delete file.' );
      } else {
        self::delete_document( absint( $_GET['document'] ) );
        // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
        // add_query_arg() return the current url
        wp_redirect('?page=all-document&post-status='.$_GET['post-status'].'&deleted=1');
        exit;
      }
    }

    // If the delete bulk action is triggered
    if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
         || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
    ) {
      $delete_ids = esc_sql( $_POST['bulk-delete'] );
      print_r($delete_ids);

      // loop over the array of record IDs and delete them
      foreach ( $delete_ids as $id ) {
        self::delete_document( $id );

      }

      // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect('?page=all-document&post-status='.$_GET['post-status'].'&deleted='.count($delete_ids));
      exit;
    }
      
  }

  /**
   * Delete the document.
   *
   * @param int $id post ID
   */
  public static function delete_document( $id ) {
    global $wpdb;

    $wpdb->delete(
      "{$wpdb->prefix}posts",
      [ 'ID' => $id ],
      [ '%d' ]
    );
  }

  /**
   * Update document status.
   *
   * @param int $id post ID
   * @param string $status post status
   */
  public static function update_document_status( $status, $id ) {
    global $wpdb;
    $table_name = 'wp_posts';
    $wpdb->query($wpdb->prepare(
      "UPDATE $table_name SET post_status='%s' WHERE ID=%s", $status, $id));
  }

  /**
   * Get list of vies to be displayed on top of table like All, active, inactive
   *
   * @return array $views
   */
  protected function get_views() { 
    $views = array();
    $current = ( !empty($_REQUEST['post-status']) ? $_REQUEST['post-status'] : 'all');

    //All link
    $class = ($current == 'all' ? ' class="current"' :'');
    $all_url = remove_query_arg('post-status');
    $views['all'] = "<a href='{$all_url }' {$class} >All</a>";

    //Active link
    $foo_url = add_query_arg('post-status','active');
    $class = ($current == 'active' ? ' class="current"' :'');
    $views['active'] = "<a href='{$foo_url}' {$class} >Active</a>";

     //Abandon
    $bar_url = add_query_arg('post-status','inactive');
    $class = ($current == 'inactive' ? ' class="current"' :'');
    $views['inactive'] = "<a href='{$bar_url}' {$class} >Inactive</a>";

     return $views;
  }

  /**
   * Get column post title content
   *
   * @param array $item post data
   *
   * @return  string post title
   */
  function column_post_title($item){
    //Build row actions
    if(isset($_GET['post-status'])){
      $post_status = $_GET['post-status'];
    } else{
      $post_status = 'all';
    }
    $delete_nonce = wp_create_nonce( 'wp_delete_document' );
    $actions = array(
        'edit'    => sprintf('<a href="?page=%s&edit=%s">%s</a>',$_REQUEST['page'], $item['ID'], 'Edit'),
        'status'    => sprintf('<a href="?page=%s&post-status=%s&action=%s&document=%s&document_wpnonce=%s" class="submitdelete">%s</a>',$_REQUEST['page'], $post_status, $item['post_status'] === 'inherit' ? 'inactive' : 'active' ,$item['ID'], $delete_nonce, $item['post_status'] === 'inherit' ? 'Inactive' : 'Active'),
        'delete'    => sprintf('<a href="?page=%s&action=%s&document=%s&document_wpnonce=%s" class="submitkdelete" onclick="showConfirmBox()">%s</a>',$_REQUEST['page'],'delete',$item['ID'], $delete_nonce, 'Delete Permanently'),
    );
    
    //Return the title contents
    return sprintf('%1$s <span style="color:silver"></span>%3$s',
        /*$1%s*/ $item['post_title'],
        /*$2%s*/ $item['ID'],
        /*$3%s*/ $this->row_actions($actions)
    );
  }

  /**
   * Get column post modified date content
   *
   * @param array $item post data
   *
   * @return string post modified date
   */
  function column_post_date_gmt($item){
    //Return the title contents
    return sprintf('%1$s <span style="color:silver"></span>%3$s',
        /*$1%s*/ date('F j, Y', strtotime($item['post_modified'])),
        /*$2%s*/ $item['ID'],
        /*$3%s*/ $this->row_actions($actions)
    );
  }

  /**
   * Get column post url content
   *
   * @param array $item post data
   *
   * @return string post preview url
   */
  function column_guid($item){
    //Return the title contents
    return sprintf('<a target="_blank" href="%1$s">%1$s</a> <span style="color:silver"></span>%3$s',
        /*$1%s*/ add_query_arg(array('id' => base64_encode($item['ID'])), get_permalink( get_page_by_path( 'documents' ))),
        /*$2%s*/ $item['ID'],
        /*$3%s*/ $this->row_actions($actions)
    );
  }

  /**
   * Get column post permalink
   *
   * @param array $item post data
   *
   * @return string post modified date
   */
  function column_post_permalink($item){
    //Return the title contents
    return sprintf('<a target="_blank" href="%1$s">%1$s</a> <span style="color:silver"></span>%3$s',
        /*$1%s*/ $item['guid'],
        /*$2%s*/ $item['ID'],
        /*$3%s*/ $this->row_actions($actions)
    );
  }

  /**
   * Get column user roles content
   *
   * @param array $item post data
   *
   * @return string user roles list
   */
  function column_user_roles($item){
    // return the document user roles
    $user_roles = json_decode($item['user_roles'], true);
    if($user_roles['customer'] === 1 || $user_roles['subscriber'] === 1){
        $user_roles_list = 'Customer, ';
    }
    $user_roles_list .= 'Power User, Administrator';
    return sprintf('%1$s <span style="color:silver"></span>%3$s',
        /*$1%s*/ $user_roles_list,
        /*$2%s*/ $item['ID'],
        /*$3%s*/ $this->row_actions($actions)
    );
  }

  /**
   * Add checkbox column for bulk actions
   *
   * @param array $item post data
   *
   * @return string check box html
   */
  function column_cb($item){
      return sprintf(
        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
      );
  }

  /**
   * Add doucment search field in the table
   *
   * @param string $text
   * @param int $input_id
   *
   * @return string input fields html
   */
  public function search_box( $text, $input_id ) {
    if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
        return;
    }
    $input_id = $input_id . '-search-input';

    if ( ! empty( $_REQUEST['orderby'] ) ) {
        echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
    }
    if ( ! empty( $_REQUEST['order'] ) ) {
        echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
    }
    if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
        echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
    }
    if ( ! empty( $_REQUEST['detached'] ) ) {
        echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
    }
    ?>
    <p class="search-box">
      <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
      <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
          <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
    </p>
    <?php
  }

  /**
   * Function to gather all documents list data and list at one place
   * @param string $search
   *
   * @return void
   */
  function prepare_items($search = '') {
    $hidden   = array();
    $per_page = 10;
    if($_GET['post-status'] == 'active'){
      $post_status = "AND post_status = 'inherit'";
    } elseif ($_GET['post-status'] == 'inactive') {
      $post_status = "AND post_status = 'trash'";
    } else{
      $post_status = '';
    }
    $sortable = $this->get_sortable_columns();
    $columns  = $this->get_columns();
    $tableListData = $this->get_documents_result($post_status, $search);
    $this->process_bulk_action();

    usort($tableListData, 'static::document_list_reorder');
    $this->_column_headers = array( $columns, $hidden, $sortable );
    $current_page = $this->get_pagenum();
    $total_items = count($tableListData);
    $data = array_slice($tableListData, (($current_page-1) * $per_page),$per_page);
    $this->items = $data;

    $this->set_pagination_args( array(
        'total_items' => $total_items,                  //WE have to calculate the total number of items
        'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
        'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
    ) );
  }

  /**
   * Function to get all documents list from database
   *
   * @param string $post_status
   * @param string $search
   *
   * @return array $tableListData
   */
  public function get_documents_result($post_status, $search){
    global $wpdb; //This is used only if making any database queries
    $tableListData = $wpdb->get_results
    ( "SELECT DISTINCT({$wpdb->prefix}posts.ID), wp_posts.post_title, wp_posts.post_modified, wp_posts.post_status, wp_posts.guid, wp_document_user_role.user_roles FROM {$wpdb->prefix}posts LEFT JOIN wp_document_tags_log ON wp_posts.ID = wp_document_tags_log.document_id LEFT JOIN wp_document_tags ON wp_document_tags.ID = wp_document_tags_log.tag_id INNER JOIN wp_document_user_role ON wp_posts.ID = wp_document_user_role.document_id WHERE post_type = 'attachment' 
      ".$post_status." AND post_mime_type IN ('application/pdf', 'text/plain', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'video/mp4') AND (wp_posts.post_title LIKE '%".$search."%' OR wp_document_tags.tag_name LIKE '%".$search."%' OR wp_document_tags.tag_description LIKE '%".$search."%')", ARRAY_A ); 
    return $tableListData;
  }

  /**
   * Sort document list array
   *
   * @param array $tableListData
   *
   * @return array $result
   */
  public function document_list_reorder($a,$b){
    $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'post_modified'; //If no sort, default to title
    $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
    $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
    return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
  }

} //class