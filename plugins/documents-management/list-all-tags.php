<?php

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
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'tag_name' => __( 'Name', 'document_tag_list' ),
            'tag_description'    => __( 'Description', 'document_tag_list' ),
            'tag_slug'      => __( 'Slug', 'document_tag_list' ),
        );
         return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'tag_name'     => array('tag_name', false),     //true means it's already sorted
            'tag_description'    => array('tag_description', false),
            'tag_slug'    => array('tag_slug', false),
        );
        return $sortable_columns;
    }

    function column_tag_name($item){
      //Return the title contents
        $delete_nonce = wp_create_nonce( 'wp_delete_tag' );
          $actions = array(
              'edit'    => sprintf('<a href="?page=%s&action=%s&tag_id=%s" class="submitdelete">Edit</a>',$_REQUEST['page'], 'edit', $item['ID']),
              'delete'    => sprintf('<a href="?page=%s&action=%s&document=%s&tag_wpnonce=%s" class="submitdelete" onclick="showConfirmBox()">%s</a>',$_REQUEST['page'],'delete',$item['ID'], $delete_nonce, 'Delete Permanently'),
          );
      return sprintf('%1$s <span style="color:silver"></span>%3$s',
          /*$1%s*/ $item['tag_name'],
          /*$2%s*/ $item['ID'],
          /*$3%s*/ $this->row_actions($actions)
      );
    }

    function column_tag_description($item){
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ $item['tag_description'] ? $item['tag_description'] : '<span aria-hidden="true">—</span>',
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_tag_slug($item){
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ $item['tag_slug'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
          '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'bulk-delete'    => 'Delete'
        );
        return $actions;
    }

    function prepare_items($search = '') {
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