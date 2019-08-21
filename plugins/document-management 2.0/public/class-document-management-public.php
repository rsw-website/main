<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       document-management
 * @since      1.0.0
 *
 * @package    Document_Management
 * @subpackage Document_Management/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Document_Management
 * @subpackage Document_Management/public
 * @author     Mindfire Solutions <ayushs@mindfiresolutions.com>
 */
class Document_Management_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	public function register_shortcodes() {
  		add_shortcode( 'client-documents-list', array( $this, 'client_documents_list_function') );
	}

	public function client_documents_list_function() {
	    ob_start();
	    $this->client_documents_list_table();
	    $output = ob_get_contents();
	    ob_end_clean();
	    return $output;
	}
	
	public function client_documents_list_table(){
		global $wpdb; //This is used only if making any database queries
	    global $wp;
	    $current_user_id = get_current_user_id();
	    $user_role = wp_get_current_user()->roles[0];
	    if($user_role !== 'administrator' && $user_role !== 'power_user'){
	      $user_role_query = " AND json_extract(user_roles, '$.".$user_role."') = 1 ";
	    } else{
	      $user_role_query = '';
	    }
	    $argsArray = array();
	    if(get_query_var('paged', 1) && get_query_var('paged', 1) > 1){
	      $CurrentPage = get_query_var('paged', 1);
	    } else{
	      $CurrentPage = 1; 
	    }
	    $custom_key = ($CurrentPage - 1) * 10;

	    if(isset($_GET['skey'])){
	      $argsArray['skey'] = $_GET['skey'];
	      $search = $_GET['skey'];
	    } else{
	      $search = '';
	    }
	    if(get_query_var('order', 1) == 'ASC'){
	      $order = get_query_var('order', 1);
	      $newOrder = 'DESC';
	    } elseif(get_query_var('order', 1) == 'DESC'){
	      $order = get_query_var('order', 1);
	      $newOrder = 'ASC';
	    } else{
	      $order = '';
	      $newOrder = 'ASC';
	    }
	    if(get_query_var('orderby', 1) == 'post_title'){
	      $orderBy = get_query_var('orderby', 1);
	      if($newOrder == 'ASC'){
	        $titleOrder = 'fa-sort-down fas';
	      } elseif ($newOrder == 'DESC') {
	        $titleOrder = 'fa-sort-up fas';
	      } else{
	        $titleOrder = 'fa-sort fas';
	      }
	      // $titleOrder = $newOrder;
	      $dateOrder = 'fa-sort fas';

	    } elseif(get_query_var('orderby', 1) == 'post_modified'){
	      $orderBy = get_query_var('orderby', 1);
	      if($newOrder == 'ASC'){
	        $dateOrder = 'fa-sort-down fas';
	      } elseif ($newOrder == 'DESC') {
	        $dateOrder = 'fa-sort-up fas';
	      } else{
	        $dateOrder = 'fa-sort fas';
	      }
	      // $dateOrder = $newOrder;
	      $titleOrder = 'fa-sort fas';
	    } else{
	      $orderBy = '';
	      $dateOrder = 'fa-sort fas';
	      $titleOrder = 'fa-sort fas';
	    }
	    $titleArgsArray = array('orderby' => 'post_title', 'order' => $newOrder);
	    $dateArgsArray = array('orderby' => 'post_modified', 'order' => $newOrder);
	    $limit = 10;
	    $startFrom = ($CurrentPage-1) * $limit;
	    $preQuery = "SELECT DISTINCT(wp_documents_meta.document_id), wp_posts.ID, wp_posts.post_title, wp_posts.post_modified , wp_documents_meta.is_bookmarked FROM wp_posts LEFT JOIN wp_documents_meta ON wp_documents_meta.document_id = wp_posts.ID AND wp_documents_meta.user_id = ".$current_user_id." LEFT JOIN wp_document_tags_log ON wp_posts.ID = wp_document_tags_log.document_id LEFT JOIN wp_document_tags ON wp_document_tags.ID = wp_document_tags_log.tag_id INNER JOIN wp_document_user_role ON wp_posts.ID = wp_document_user_role.document_id WHERE post_mime_type IN ('application/pdf', 'text/plain', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'video/mp4')".$user_role_query." AND post_status = 'inherit' AND (wp_posts.post_title LIKE '%".$search."%' OR wp_document_tags.tag_name LIKE '%".$search."%' OR wp_document_tags.tag_description LIKE '%".$search."%')";

	    if(isset($_GET['type'])){
	      $argsArray['type'] = $_GET['type'];
	      $titleArgsArray['type'] = $_GET['type'];
	      $dateArgsArray['type'] = $_GET['type'];
	      $preQuery = $preQuery . " AND wp_documents_meta.is_bookmarked = 1";
	    }

	    $query = $preQuery;
	    if($order && $orderBy){
	      $argsArray['orderby'] = $orderBy;
	      $argsArray['order'] = $order;
	      $query = $query . " ORDER BY $orderBy $order";
	    } else{
	        $query = $query . " ORDER BY wp_posts.ID desc";
	    }

	    $query = $query . " LIMIT $startFrom, $limit";
	    $tableListData = $wpdb->get_results($query);
	    ?>
	    <form action="" method="get" id="dashboard-document-filter">
	        <select class="action-filter" name="action-filter">
	          <option>Select action</option>
	          <option path="<?php echo home_url( add_query_arg( array(), $wp->request ) ); ?>">Reset all filters</option>
	          <option path="<?php echo home_url( add_query_arg( array('type' => 'bookmark-documents'), $wp->request ) ); ?>">Show bookmarked documents</option>
	        </select>
	        <input type="submit" class="button apply-button" name="apply" value="apply"/>
      	</form>
      	<form action="" method="get" id="dashboard-document-search">
	        <?php if($orderBy && $order): ?>
	            <input type="hidden" name="orderby" value="<?php echo $orderBy; ?>" />
	            <input type="hidden" name="order" value="<?php echo $order; ?>" />
	        <?php endif; ?>
	        <input type="text" name="skey" id="search" placeholder="Search" value="<?php echo $search; ?>" />
	        <span class="search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
      	</form>
      	<div class="responsive-table">
	        <table class="table" id="document-list-table"> 
	         	<thead> 
		          <tr> 
		            <th></th>
		            <th>S.No.</th>
		            <th> 
		              <a href="<?php echo home_url(add_query_arg($titleArgsArray, $wp->request)); ?>">
		                Title <i class="fa <?php echo $titleOrder; ?>" aria-hidden="true"></i>
		                </a>
		              </th> 
		            <th><a href="<?php echo home_url(add_query_arg($dateArgsArray, $wp->request)); ?>"">
		              Modified Date <i class="fa <?php echo $dateOrder; ?>" aria-hidden="true"></i>
		              </a></th> 
		              <th>Action</th>
		          </tr> 
	          	</thead> 
	          	<tbody> 
	          		<?php foreach ($tableListData as $tableData) : ?>   
			          <tr> 
			            <td class="desktop-column column-bookmark" data-attribute="Bookmakred">
			              <a href="javascript:void(0)" class="toggle-bookmark <?php echo $tableData->is_bookmarked ? 'solid-star' : 'empty-star'; ?>" title="<?php echo $tableData->is_bookmarked ? 'Marked' : 'Mark'; ?> as favourite" document-id="<?php echo base64_encode($tableData->ID); ?>" _nonce="<?php echo wp_create_nonce("bookmark_status"); ?>">
			                <i class="fa-star" data-name="star"></i>
			              </a>
			            </td>  
			            <td class="column-count" data-attribute="S.No.">
			              <?php echo ++$custom_key; ?>
			              </td>   
			            <td class="column-title" data-attribute="Title">
			              <a target="_blank" title="<?php echo $tableData->post_title; ?>" href="<?php echo add_query_arg(array('id' => base64_encode($tableData->ID)), get_permalink( get_page_by_path( 'documents' ))); ?>"><?php echo $tableData->post_title; ?></a>
			              <i class="fa-chevron-circle-down fas column-icon" data-name="chevron-circle-down"></i>
			            </td>
			            <td class="mobile-column column-bookmark responsive-column hidden-column" data-attribute="Bookmakred">
			              <a href="javascript:void(0)" class="toggle-bookmark <?php echo $tableData->is_bookmarked ? 'solid-star' : 'empty-star'; ?>" title="<?php echo $tableData->is_bookmarked ? 'Marked' : 'Mark'; ?> as favourite" document-id="<?php echo base64_encode($tableData->ID); ?>" _nonce="<?php echo wp_create_nonce("bookmark_status"); ?>">
			                <i class="fa-star" data-name="star"></i>
			              </a>
			            </td> 
			            <td class="column-date responsive-column hidden-column" data-attribute="Modified Date">
			              <?php echo date('F j, Y', strtotime($tableData->post_modified)); ?>
			              </td>
			            <td class="column-action responsive-column hidden-column" data-attribute="">
			              <a class="preview-link btn-c2" target="_blank" href="<?php echo add_query_arg(array('id' => base64_encode($tableData->ID)), get_permalink( get_page_by_path( 'documents' ))); ?>">View</a>
			            </td> 
			          </tr>   
	          		<?php endforeach; ?>   
	          	</tbody> 
	        </table> 
      	</div>
      	<ul class="custom-pagination">
      		<?php   
	        $tableListCount = $wpdb->get_results($preQuery); 
	        $totalRecords = count($tableListCount);
	        if($totalRecords > $limit){
	            // Number of pages required. 
	            $totalPages = ceil($totalRecords / $limit);
	            if($CurrentPage == 1){
	              $firstLink = 'javascript:void(0)';
	              $firstClass = 'disabled';

	              $previousLink = 'javascript:void(0)';
	              $previousClass = 'disabled';
	            } else{
	              $firstLink = home_url(add_query_arg($argsArray, $wp->request));
	              $firstClass = '';

	              $argsArray['paged'] = $CurrentPage - 1;
	              $previousLink = home_url(add_query_arg($argsArray, $wp->request));
	              $previousClass = '';
	            }

	            if($CurrentPage == $totalPages){
	              $lastLink = 'javascript:void(0)';
	              $lastClass = 'disabled';

	              $nextLink = 'javascript:void(0)';
	              $nextClass = 'disabled';
	            } else{
	              $argsArray['paged'] = $totalPages;
	              $lastLink = home_url(add_query_arg($argsArray, $wp->request));
	              $lastClass = '';

	              $argsArray['paged'] = $CurrentPage + 1;
	              $nextLink = home_url(add_query_arg($argsArray, $wp->request));
	              $nextClass = '';
	            }
	            ?>
	            <li class="<?php echo $firstClass; ?>">
	              <a href="<?php echo $firstLink; ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <span>First</span></a>
	            </li>
	            <li class="<?php echo $previousClass; ?>">
	              <a href="<?php echo $previousLink; ?>"><i class="fa fa-angle-left" aria-hidden="true"></i> <span>Previous</span></a>
	            </li>
	            <li class="<?php echo $nextClass; ?>">
	              <a href="<?php echo $nextLink; ?>"><span>Next</span> <i class="fa fa-angle-right" aria-hidden="true"></i> </a>
	            </li>
	            <li class="<?php echo $lastClass; ?>">
	              <a href="<?php echo $lastLink; ?>"><span>Last</span> <i class="fa fa-angle-double-right" aria-hidden="true"></i> </a>
	            </li>
	        <?php 
	    	} 
	    	?>
  		</ul>
        	<?php
	}
}
