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

class Tags_list_table extends WP_List_Table {
    function __construct(){
        global $status, $page;
            parent::__construct( array(
                'singular'  => __( 'tag_list', 'document_tag_list' ),     //singular name of the listed records
                'plural'    => __( 'tags_list', 'document_tag_list' ),   //plural name of the listed records
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
            case 'tag_name':
            case 'tag_description':
            case 'tag_slug':
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
            'tag_name' => __( 'Name', 'document_tag_list' ),
            'tag_description'    => __( 'Description', 'document_tag_list' ),
            'tag_slug'      => __( 'Slug', 'document_tag_list' ),
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
            'tag_name'     => array('tag_name', false),     //true means it's already sorted
            'tag_description'    => array('tag_description', false),
            'tag_slug'    => array('tag_slug', false),
        );
        return $sortable_columns;
    }

    /**
     * Get column tag name content
     *
     * @param array $item tags data
     *
     * @return  string tag title
     */
    function column_tag_name($item){
      //Return the title contents
        $delete_nonce = wp_create_nonce( 'wp_delete_tag' );
          $actions = array(
              'edit'    => sprintf('<a href="?page=%s&action=%s&tag_id=%s" class="submitdelete">Edit</a>',$_REQUEST['page'], 'edit', $item['ID']),
              'delete'    => sprintf('<a href="?page=%s&action=%s&tag=%s&tag_wpnonce=%s" class="submitdelete" onclick="showConfirmBox()">%s</a>',$_REQUEST['page'],'delete',$item['ID'], $delete_nonce, 'Delete Permanently'),
          );
      return sprintf('%1$s <span style="color:silver"></span>%3$s',
          /*$1%s*/ $item['tag_name'],
          /*$2%s*/ $item['ID'],
          /*$3%s*/ $this->row_actions($actions)
      );
    }

    /**
     * Get column tag description content
     *
     * @param array $item tags data
     *
     * @return  string tag description
     */
    function column_tag_description($item){
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ $item['tag_description'] ? $item['tag_description'] : '<span aria-hidden="true">—</span>',
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    /**
     * Get column tag slug content
     *
     * @param array $item tags data
     *
     * @return  string tag slug
     */
    function column_tag_slug($item){
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ $item['tag_slug'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    /**
     * Add checkbox column for bulk actions
     *
     * @param array $item tags data
     *
     * @return string check box html
     */
    function column_cb($item){
        return sprintf(
          '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
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
        $nonce = esc_attr( $_REQUEST['tag_wpnonce'] );
        if( 'delete'=== $this->current_action() ) {
          if ( ! wp_verify_nonce( $nonce, 'wp_delete_tag' ) ) {
            die( 'Something went wrong. Unable to delete file.' );
          } else {
            self::delete_tag( absint( $_GET['tag'] ) );
            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect('?page=document-tags&deleted=1');
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
            self::delete_tag( $id );

          }

          // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect('?page=document-tags&deleted='.count($delete_ids));
          exit;
        }
        
    }

    /**
     * Delete the document.
     *
     * @param int $id post ID
     */
    public static function delete_tag( $id ) {
        global $wpdb;
        $deleted_tag = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}document_tags WHERE ID = %s", 
            $id)
        );
        return $deleted_tag;
    }

    /**
     * Function to gather all documents list data and list at one place
     * @param string $search
     *
     * @return void
     */
    public function prepare_items($search = '') {
        global $wpdb; //This is used only if making any database queries
        $columns  = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $tableListData = $wpdb->get_results( "SELECT * FROM wp_document_tags WHERE tag_name LIKE '%".$search."%'", ARRAY_A );
        function usort_reorder($a, $b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'post_title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($tableListData, 'usort_reorder');
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $per_page = 10;
        $hidden   = array();
        $this->process_bulk_action();
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
}