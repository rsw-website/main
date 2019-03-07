<?php

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class My_Example_List_Table extends WP_List_Table {
    function __construct(){
    global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'document', 'documentslist' ),     //singular name of the listed records
            'plural'    => __( 'documents', 'documentslist' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
    ) );
    }
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
function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'post_title' => __( 'Title', 'documentslist' ),
            'guid'    => __( 'URL', 'documentslist' ),
            'post_date_gmt'      => __( 'Published Date', 'documentslist' )
        );
         return $columns;
    }

function get_sortable_columns() {
        $sortable_columns = array(
            'post_title'     => array('post_title',false),     //true means it's already sorted
            'post_date_gmt'    => array('post_date_gmt',false),
        );
        return $sortable_columns;
    }

function get_bulk_actions() {
        $actions = array(
            'bulk-delete'    => 'Delete'
        );
        return $actions;
    }

function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'=== $this->current_action() ) {
          $nonce = esc_attr( $_REQUEST['document_wpnonce'] );
          // die($nonce);
          if ( ! wp_verify_nonce( $nonce, 'wp_delete_document' ) ) {
            die( 'Go get a life script kiddies' );
          } else {
            self::delete_document( absint( $_GET['document'] ) );
            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect('?page=all-document');
            exit;
          }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {
          $delete_ids = esc_sql( $_POST['bulk-delete'] );

          // loop over the array of record IDs and delete them
          foreach ( $delete_ids as $id ) {
            self::delete_document( $id );

          }

          // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect('?page=all-document');
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

function column_post_title($item){
        //Build row actions
        $delete_nonce = wp_create_nonce( 'wp_delete_document' );
        $actions = array(
            'delete'    => sprintf('<a href="?page=%s&action=%s&document=%s&document_wpnonce=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID'], $delete_nonce),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ $item['post_title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

  function column_post_date_gmt($item){
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ date('F j, Y', strtotime($item['post_date_gmt'])),
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

  function column_guid($item){
        
        //Return the title contents
        return sprintf('<a target="_blank" href="http://localhost/reliablesoftworks/preview-document/?id=%1$s">http://localhost/reliablesoftworks/preview-document/?id=%1$s</a> <span style="color:silver"></span>%3$s',
            /*$1%s*/ base64_encode($item['ID']),
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
          '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

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


function prepare_items($search = '') {
  global $wpdb; //This is used only if making any database queries
  $tableListData = $wpdb->get_results
  ( "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type IN ('application/pdf', 'text/plain', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') AND post_title LIKE '%".$search."%'", ARRAY_A ); 
    $per_page = 10;
  $columns  = $this->get_columns();
  $hidden   = array();
  $this->process_bulk_action();
  function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'post_title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($tableListData, 'usort_reorder');
  $sortable = $this->get_sortable_columns();
  $this->_column_headers = array( $columns, $hidden, $sortable );
  $current_page = $this->get_pagenum();
  $total_items = count($tableListData);
  $data = array_slice($tableListData,(($current_page-1)*$per_page),$per_page);
  $this->items = $data;

  $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
}

} //class