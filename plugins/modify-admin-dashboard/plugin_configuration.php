<?php
/*
Plugin Name: Update wordpress admin dashboard
Description: A plugin to customize wordpress admin dashboard 
Author: Mindfire Solutions
Version: 1.0
*/

/**
* Add new register fields for WooCommerce registration.
*
* @return string Register fields HTML.
*/

function wooc_extra_register_fields() {
    ?>
    <p class="form-row form-row-first">
      <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?>
        <span class="required">*</span>
      </label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?>
          <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_company"><?php _e( 'Company', 'woocommerce' ); ?>
          <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?>
          <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
    <div class="clear"></div>
    <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

/**
* Validate the extra register fields.
*
* @param string $username Current username.
* @param string $email Current email.
* @param object $validation_errors WP_Error object.
*
* @return void
*/

function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
              $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_company'] ) && empty( $_POST['billing_company'] ) ) {
        $validation_errors->add( 'billing_company_error', __( '<strong>Error</strong>: Company name is required!.', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
        $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone number is required!.', 'woocommerce' ) );
    }
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );


/**
* Save the extra register fields.
*
* @param int $customer_id Current customer ID.
*
* @return void
*/

function wooc_save_extra_register_fields( $customer_id ) {
  if ( isset( $_POST['billing_first_name'] ) ) {
      // WordPress default first name field.
      update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
      
      // WooCommerce billing first name.
      update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
  }
  if ( isset( $_POST['billing_last_name'] ) ) {
      // WordPress default last name field.
      update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
      
      // WooCommerce billing last name.
      update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
  }
}

add_action( 'woocommerce_created_customer', 
  'wooc_save_extra_register_fields' );

/**
 * Redirect after registration.
 *
 * @return string
 */

function user_autologout(){
  if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $approved_status = get_user_meta($user_id, 'pw_user_status', true);
    //if the user hasn't been approved yet by WP Approve User plugin, destroy the cookie to kill the session and log them out
    if ( $approved_status == 'approved' ){
        return $redirect_url;
    } else{
        wp_logout();
        return wc_get_page_permalink( 'myaccount' ).
        'registration-successful/?ref_id='.base64_encode($user_id);
    }
  }
}
add_action('woocommerce_registration_redirect', 'user_autologout', 2);

/**
 * Redirect after login.
 *
 * @param $redirect
 *
 * @return string
 */

function custom_login_redirect( $redirect ) {
  wp_redirect(get_permalink( get_page_by_path( 'client-dashboard' ) ));
}
add_filter( 'woocommerce_login_redirect', 'custom_login_redirect' );

/**
* Create custom footer logo widgit.
*
* @return void
*/

function custom_footer_logo() {
  register_sidebar( array(
    'name'          => 'Footer Logo',
    'id'            => 'footer_logo',
    'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4 class="widget-title">',
    'after_title'   => '</h4>',
  ) );
}
add_action( 'widgets_init', 'custom_footer_logo' );

/**
* Create custom footer quote widgit.
*
* @return void
*/

function custom_footer_quote() {
  register_sidebar( array(
    'name'          => 'Footer Quote',
    'id'            => 'footer_quote',
    'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4 class="widget-title">',
    'after_title'   => '</h4>',
  ) );
}
add_action( 'widgets_init', 'custom_footer_quote' );

function new_modify_user_table( $column ) {
    $column['document_access'] = 'Access';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'document_access' :
            $documentAccess = intval(get_user_meta
              ( $user_id, 'document_access', true ));
            if($documentAccess === 1){
                $accessText = 'Granted';
            } elseif ($documentAccess === 2) {
              $accessText = 'Revoked';
            } elseif ($documentAccess === 3) {
              $accessText = 'Pending';
            } else{
              $accessText = '<span aria-hidden="true">—</span>';
            }
            return $accessText;
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { 
  $documentAccess = intval(get_user_meta( $user->ID, 'document_access', true )); ?>
<h3 id="document-access-request"><?php _e("Document Access Permission", "blank"); ?></h3>
  <select name="document_access">
  <option value="0" <?php echo $documentAccess === 0 ? 'selected' : '' ?> ><span aria-hidden="true">—</span></option>
  <option value="1" <?php echo $documentAccess === 1 ? 'selected' : '' ?>>Grant</option>
  <option value="2" <?php echo $documentAccess === 2 ? 'selected' : '' ?>>Revoke</option>
  <option value="3" <?php echo $documentAccess === 3 ? 'selected' : '' ?>>Pending</option>
</select>
<?php }

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
  $userData = get_userdata($user_id); 
  if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
  if(get_user_meta( $user_id, 'document_access', true) !== $_POST['document_access']){
    // send mail
    if($_POST['document_access'] == 1){
      $status = 'Approved';
      $message = "You have been approved to access documents on ".get_option('blogname').".\n\n";
      $message .= "To access the document, login to you your account and vist the dashboard page.";
    } elseif($_POST['document_access'] == 2){
      $status = 'Denied';
      $message = "You have been denied to access docuements on Reliable Softworks.\n";
    }
    $to = $userData->user_email;  
    $subject = '['.get_option('blogname').'] - Document Request '.$status;
    $headers = custom_email_headers();
    if($_POST['document_access'] == 1 || $_POST['document_access'] == 2){
      wp_mail( $to, $subject, $message, $headers );
    }
    update_user_meta( $user_id, 'document_access', $_POST['document_access'] );
  }
}

function my_account_menu_order() {
  $menuOrder = array(
    'edit-account'      => __( 'Account Details', 'woocommerce' ),
    'my-document'          => __( 'My Documents', 'woocommerce' ),
    'customer-logout'    => __( 'Logout', 'woocommerce' ),
  );
  return $menuOrder;
 }
 add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );

 /*
 * Step 2. Register Permalink Endpoint
 */
add_action('woocommerce_init', 'custom_add_endpoint');
function custom_add_endpoint() {
 
  // WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
  add_rewrite_endpoint( 'my-document', EP_PAGES );
}

$endpoint = 'my-document';
 
add_action( 'woocommerce_account_' . $endpoint .  '_endpoint', 'wk_endpoint_content' );
 
function wk_endpoint_content() {
    //content goes here
  ?>
  <h2 class="avada-woocommerce-myaccount-heading">
        My Documents     
  </h2>
  <?php
  echo do_shortcode( '[request-access-button showTitle = 1]' );
  echo do_shortcode( '[list-staff]' );
}

add_shortcode( 'request-access-button', 'custom_document_request_access' );
function custom_document_request_access($atts){
  ob_start();
  $currentUserId = get_current_user_id();
  $accessStatus = intval(get_user_meta( $currentUserId, 'document_access', true ));
  if($accessStatus === 0){
    ?>
      <div class="document-request-text">
        <p>You do not have permission to access the documents added by the admin.</p>
        <div class="custom-loader lds-dual-ring hidden"></div>
        <button class="document-request-access btn-c2">Request Document Access</button>
      </div>
  <?php
  }
  $requestData = ob_get_clean();
  return $requestData;
}


function custom_ajax_request() {
  // load our jquery file that sends the $.post request
  wp_enqueue_script( "ajax-request", get_stylesheet_directory_uri() . '/assets/js/ajax-request.js', array ( 'jquery' ) );
 
  // make the ajaxurl var available to the above script
  wp_localize_script( 'ajax-request', 'custom_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );  
}
add_action('wp_print_scripts', 'custom_ajax_request');

function submit_document_access_request() {
    $currentUserId = get_current_user_id();
    $userData = get_userdata($currentUserId);
    $newStatus = 3;
    $to = get_option( 'admin_email' );  
    $subject = '['.get_option('blogname').'] - Document Access Request';
    $message = "Username: ".$userData->display_name." (".$userData->user_email.") has requested to access documents at ".get_option('blogname').".\n\n";
    $message .= "To approve or deny the request go to \n";
    $message .= get_edit_user_link( $currentUserId );
    $headers = custom_email_headers();
    wp_mail( $to, $subject, $message, $headers );
  if($newStatus === intval(get_user_meta( $currentUserId, 'document_access', true ))){
    echo 0;
  } else{
    update_user_meta( $currentUserId, 'document_access', 3 );
    // send mail
    echo 1;
  }
  die();
}
add_action('wp_ajax_submit_acces_request', 'submit_document_access_request');


function list_staff() {
    global $wpdb; //This is used only if making any database queries
    global $wp;
    $argsArray = array();
    if(get_query_var('paged', 1) && get_query_var('paged', 1) > 1){
      $CurrentPage = get_query_var('paged', 1);
    } else{
      $CurrentPage = 1; 
    }
    if(isset($_GET['skey'])){
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


    $limit = 10;
    $startFrom = ($CurrentPage-1) * $limit; 
    $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type IN ('application/pdf', 'text/plain', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') AND post_title LIKE '%".$search."%'";
    if($order && $orderBy){
      $argsArray = array('orderby' => $orderBy, 'order' => $order);
      $query = $query . " ORDER BY $orderBy $order";
    }
    $query = $query . " LIMIT $startFrom, $limit";

  $tableListData = $wpdb->get_results($query); 
  ?>
      <form action="" method="get" id="dashboard-document-search">
        <?php if($orderBy && $order): ?>
            <input type="hidden" name="orderby" value="<?php echo $orderBy; ?>" />
            <input type="hidden" name="order" value="<?php echo $order; ?>" />
          <?php endif; ?>
        <input type="text" name="skey" id="search" placeholder="Search" value="<?php echo $search; ?>" />
        <span class="search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
      </form>
      <table class="table" id="document-list-table"> 
        <thead> 
        <tr> 
          <th> 
            <a href="<?php echo home_url(add_query_arg(array('orderby' => 'post_title', 'order' => $newOrder), $wp->request)); ?>">
              Title <i class="fa <?php echo $titleOrder; ?>" aria-hidden="true"></i>
              </a>
            </th> 
          <th>URL</th> 
          <th><a href="<?php echo home_url(add_query_arg(array('orderby' => 'post_modified', 'order' => $newOrder), $wp->request)); ?>"">
            Modified Date <i class="fa <?php echo $dateOrder; ?>" aria-hidden="true"></i>
            </a></th> 
        </tr> 
        </thead> 
        <tbody> 
        <?php   
        foreach ($tableListData as $key => $tableData) {
        ?>   
        <tr>   
          <td><?php echo $tableData->post_title; ?></td>   
          <td><a target="_blank" href="<?php echo add_query_arg(array('id' => base64_encode($tableData->ID)), get_permalink( get_page_by_path( 'preview-document' ))); ?>"><?php echo add_query_arg(array('id' => base64_encode($tableData->ID)), get_permalink( get_page_by_path( 'preview-document' ))); ?></a></td> 
          <td><?php echo date('F j, Y', strtotime($tableData->post_modified)); ?></td> 
        </tr>   
        <?php   
        }
        ?>   
        </tbody> 
      </table> 
       <ul class="custom-pagination"> 
      <?php   
        $tableListCount = $wpdb->get_results
        ( "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type IN ('application/pdf', 'text/plain', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') AND post_title LIKE '%".$search."%'" );   
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
              <a href="<?php echo $firstLink; ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> First</a>
            </li>
            <li class="<?php echo $previousClass; ?>">
              <a href="<?php echo $previousLink; ?>"><i class="fa fa-angle-left" aria-hidden="true"></i> Previous</a>
            </li>
            <li class="<?php echo $nextClass; ?>">
              <a href="<?php echo $nextLink; ?>">Next <i class="fa fa-angle-right" aria-hidden="true"></i> </a>
            </li>
            <li class="<?php echo $lastClass; ?>">
              <a href="<?php echo $lastLink; ?>">Last <i class="fa fa-angle-double-right" aria-hidden="true"></i> </a>
            </li>
          <?php
          }


 
}

function list_staff_obj($atts, $content=null) {
    ob_start();
    $currentUserId = get_current_user_id();
  $accessStatus = intval(get_user_meta( $currentUserId, 'document_access', true ));
  $user=wp_get_current_user();
  if($accessStatus === 1 || in_array("administrator", $user->roles)){
    list_staff($atts, $content=null);
    $output=ob_get_contents();
    ob_end_clean();
    return $output;
  } else if($accessStatus === 2){
    ?>
    <p>Your document access request has been denied by administrator.<br>
    If you further want to access the documents, then write us at : <a href="mailto:support@reliablesoftworks.com">support@reliablesoftworks.com</a> 
    </p>
    <?php
  } else if($accessStatus === 3){
    ?>
    <p>Your document access request has been submitted to administrator. The administrator can either approve or deny your request.<br>
    You will receive an email with instructions on what you will need to do next. Thanks for your patience. 
    </p>
    <?php
  }
}

add_shortcode( 'list-staff', 'list_staff_obj' );