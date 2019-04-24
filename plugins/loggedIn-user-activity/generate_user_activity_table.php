<?php

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class User_activity_list_table extends WP_List_Table {
    function __construct(){
	    global $status, $page;
	        parent::__construct( array(
	            'singular'  => __( 'user_activity', 'user_activity_list' ),     //singular name of the listed records
	            'plural'    => __( 'user_activities', 'user_activity_list' ),   //plural name of the listed records
	            'ajax'      => false        //does this table support ajax?
	    ) );
    }

    function column_default( $item, $column_name ) {
	    switch( $column_name ) { 
	        case 'post_title':
	        case 'guid':
	        case 'is_bookmarked':
	        case 'last_access_time':
	        case 'last_withdraw_time':
	        case 'time_duration':
	        case 'no_of_times':
	            return $item[ $column_name ];
	        default:
	            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	    }
  	}
  	
  	function get_columns(){
        $columns = array(
            'post_title' => __( 'Title', 'user_activity_list' ),
            'guid'    => __( 'URL', 'user_activity_list' ),
            'is_bookmarked'      => __( 'Favourite', 'user_activity_list' ),
            'last_access_time'      => __( 'Last Access Time', 'user_activity_list' ),
            'last_withdraw_time'      => __( 'Last Withdraw Time', 'user_activity_list' ),
            'time_duration'      => __( 'Time Duration', 'user_activity_list' ),
            'no_of_times'      => __( 'Number Of Times', 'user_activity_list' )
        );
         return $columns;
    }

    function column_post_title($item){
	  //Return the title contents
	  return sprintf('%1$s <span style="color:silver"></span>%3$s',
	      /*$1%s*/ $item['post_title'],
	      /*$2%s*/ $item['ID'],
	      /*$3%s*/ $this->row_actions($actions)
	  );
	}

	function column_guid($item){
        //Return the title contents
        return sprintf('<a target="_blank" href="%1$s">%1$s</a> <span style="color:silver"></span>%3$s',
            /*$1%s*/ add_query_arg(array('id' => base64_encode($item['ID'])), get_permalink( get_page_by_path( 'documents' ))),
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_is_bookmarked($item){
        //Return the title contents
        return sprintf('%1$s%2$s',
            /*$1%s*/ $item['is_bookmarked'] ? 'Yes' : 'No',
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function column_last_access_time($item){
        //Return the title contents
        $access_date = $item['last_access_time'] ? date_format(
              date_create($item['last_access_time']),
               "F j, Y g:i A") : '<span aria-hidden="true">—</span>';
        return sprintf('%1$s%2$s',
            /*$1%s*/ $access_date,
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function column_last_withdraw_time($item){
        //Return the title contents
        $withdraw_date = $item['last_withdraw_time'] ? date_format(
              date_create($item['last_withdraw_time']),
               "F j, Y g:i A") : '<span aria-hidden="true">—</span>';
        return sprintf('%1$s%2$s',
            /*$1%s*/ $withdraw_date,
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function column_time_duration($item){
    	$time_duration = '';
    	if($item['last_withdraw_time'] && $item['last_access_time']){
	    	$withdraw_time = date_create($item['last_withdraw_time']);
	    	$access_time = date_create($item['last_access_time']);
	    	$diff = date_diff($withdraw_time, $access_time);
	    	if($diff->d){
	    		$time_duration .= $diff->d.' days ';
	    	}
	    	if($diff->h){
	    		$time_duration .= $diff->h.' hours ';
	    	}
	    	if($diff->i){
	    		$time_duration .= $diff->i.' minutes ';
	    	}
	    	if($diff->s){
	    		$time_duration .= $diff->s.' seconds ';
	    	}
    	}
        //Return the title contents
        return sprintf('%1$s%2$s',
            /*$1%s*/ $time_duration ? $time_duration : '<span aria-hidden="true">—</span>',
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function column_no_of_times($item){
        //Return the title contents
        return sprintf('%1$s%2$s',
            /*$1%s*/ $item['no_of_times'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }
    function prepare_items($user_id, $search = '') {
    	global $wpdb; //This is used only if making any database queries
    	$columns  = $this->get_columns();
    	$sortable = $this->get_sortable_columns();
		 $tableListData = $wpdb->get_results( "SELECT wp_posts.ID, wp_posts.post_title, wp_posts.post_modified, wp_posts.post_mime_type, wp_documents_meta.user_id, wp_documents_meta.* FROM wp_posts INNER JOIN wp_documents_meta ON wp_posts.ID = wp_documents_meta.document_id WHERE wp_documents_meta.user_id = $user_id", ARRAY_A );
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