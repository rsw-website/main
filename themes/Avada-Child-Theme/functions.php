<?php

/**
 * Enqueue style sheet in the theme
 *
 * @return void
 */
function theme_enqueue_styles() {
  wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

/**
 * Enqueue style sheet in the theme
 *
 * @return void
 */
function avada_lang_setup() {
  $lang = get_stylesheet_directory() . '/languages';
  load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

/**
 * Enqueue script file in the theme
 *
 * @return void
 */
function add_custom_theme_scripts() {
  wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js', array( 'jquery' ), null, true );
  wp_enqueue_script( 'pdf-build', get_stylesheet_directory_uri() . '/assets/js/pdf.js', array( 'jquery' ), null, false );
}

add_action( 'wp_enqueue_scripts', 'add_custom_theme_scripts' );

/**
 * Configure SMTP credentials
 *
 * @return void
 */
function send_smtp_email( $phpmailer ) {
  $phpmailer->isSMTP();
  $phpmailer->Host       = SMTP_HOST;
  $phpmailer->SMTPAuth   = SMTP_AUTH;
  $phpmailer->Port       = SMTP_PORT;
  $phpmailer->Username   = SMTP_USER;
  $phpmailer->Password   = SMTP_PASS;
  $phpmailer->SMTPSecure = SMTP_SECURE;
  $phpmailer->From       = SMTP_FROM;
  $phpmailer->FromName   = SMTP_NAME;
}
add_action( 'phpmailer_init', 'send_smtp_email' );

/**
 * Create custom header for emails
 *
 * @return array $headers
 */

function custom_email_headers() {
  $admin_email = get_option( 'admin_email' );
  if ( empty( $admin_email ) ) {
    $admin_email = 'support@' . $_SERVER['SERVER_NAME'];
  }

  $from_name = get_option( 'blogname' );

  $headers = array(
    "From: \"{$from_name}\" <{$admin_email}>\n",
  );
  return $headers;
}

/**
 * Create custom message template for approved user register
 *
 * @param string $message
 *
 * @param array  $user
 *
 * @return string $message
 */
function custom_notification_message( $message, $user ) {
  $message  = '<p>Hi ' . $user->first_name . ',</p>';
  $message .= '<p>We have excellent news! Your account has been approved for access to ' . get_option( 'siteurl' ) . '.</p>';
  $message .= '<p>Please log in with your email address ';
  $message .= "<a href='" . get_permalink( get_page_by_path( 'client-login' ) ) . "'>here</a>.</p>";
  $message .= '<p>If you would like to set or reset your password, you can do so by ';
  $message .= "<a href='" . wc_lostpassword_url() . "'>clicking here</a>.</p>";
  $message .= '<p>Best Regards,<br>' . get_option( 'blogname' ) . '</p>';
  return $message;
}
add_filter( 'new_user_approve_approve_user_message', 'custom_notification_message', 10, 2 );

/**
 * Create custom message template for denied user registration
 *
 * @return string $message
 */
function custom_denied_notification_message( $message, $user ) {
  $message  = '<p>Hi ' . $user->first_name . ',</p>';
  $message .= '<p>We are sorry to inform you that your user access request for ' . get_option( 'blogname' ) . ' has been denied.</p>';
  $message .= "<p>If you feel that this is an error, please have your company's main point of
  contact reach out to us.</p>";
  $message .= '<p>Sorry for any inconvenience.</p>';
  $message .= '<p>Best Regards,<br>' . get_option( 'blogname' ) . '</p>';
  return $message;
}
add_filter( 'new_user_approve_deny_user_message', 'custom_denied_notification_message', 10, 2 );

/**
 * Create custom message template for new user register
 *
 * @return string $message
 */
function custom_default_notification_message( $message, $user_login ) {
  $user     = get_user_by( 'login', $user_login );
  $message  = '<p>Hi Admin,</p>';
  $message .= '<p>User(' . $user->user_email . ') has requested an account at ' . get_option( 'blogname' ) . '.</p>';
  $message .= '<p>To approve or deny this user access to ' . get_option( 'blogname' ) . ' go to ';
  $message .= "<a href='" . admin_url( 'users.php?s&pw-status-query-submit=Filter&new_user_approve_filter=pending&paged=1' ) . "'>" . admin_url( 'users.php?s&pw-status-query-submit=Filter&new_user_approve_filter=pending&paged=1' ) . '</a>.</p>';
  $message .= '<p>Best Regards,<br>' . get_option( 'blogname' ) . '</p>';
  return $message;
}
add_filter( 'new_user_approve_request_approval_message', 'custom_default_notification_message', 10, 2 );


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
   <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="
   <?php
    if ( ! empty( $_POST['billing_first_name'] ) ) {
      esc_attr_e( $_POST['billing_first_name'] );}
    ?>
    " />
  </p>
  <p class="form-row form-row-last">
  <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?>
    <span class="required">*</span>
  </label>
  <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="
  <?php
  if ( ! empty( $_POST['billing_last_name'] ) ) {
    esc_attr_e( $_POST['billing_last_name'] );}
  ?>
  " />
  </p>
  <p class="form-row form-row-last">
  <label for="reg_billing_company"><?php _e( 'Company', 'woocommerce' ); ?>
    <span class="required">*</span>
  </label>
  <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="
  <?php
  if ( ! empty( $_POST['billing_company'] ) ) {
    esc_attr_e( $_POST['billing_company'] );}
  ?>
  " />
  </p>
  <p class="form-row form-row-last">
  <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?>
    <span class="required">*</span>
  </label>
  <input type="text" class="input-text isPhoneNumber" name="billing_phone" id="reg_billing_phone" maxlength="10" value="
  <?php
  if ( ! empty( $_POST['billing_phone'] ) ) {
    esc_attr_e( $_POST['billing_phone'] );}
  ?>
  " />
  </p>
  <div class="clear"></div>
  <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

/**
 * Validate the extra register fields.
 *
 * @param string $username Current username.
 *
 * @param string $email Current email.
 *
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
  if ( isset( $_POST['billing_company'] ) ) {
    // WordPress default last name field.
    update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
  }
  if ( isset( $_POST['billing_phone'] ) ) {
    // WordPress default last name field.
    update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
  }
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );

/**
 * Validate company name field in edit account page
 *
 * @param array $errors Errors.
 *
 * @return array $errors
 */

function wooc_validate_custom_field( $errors ) {
  if ( isset( $_POST['billing_company'] ) ) {
    if ( strlen( $_POST['billing_company'] ) < 4 ) { // condition to be adapted
      $errors->add( 'error', __( '<strong>Company</strong> is a required field.', 'woocommerce' ), '' );
    }
  }
  return $errors;
}
add_action( 'woocommerce_save_account_details_errors', 'wooc_validate_custom_field', 10, 1 );

/**
 * Unset account display name from required fields array
 *
 * @param array $required_fields Required fields.
 *
 * @return array $required_fields
 */
function unset_account_display_name( $required_fields ) {
  unset( $required_fields['account_display_name'] );
  return $required_fields;
}
add_filter( 'woocommerce_save_account_details_required_fields', 'unset_account_display_name' );

/**
 * Save user additional account details
 *
 * @param int $user_id Registered User Id.
 *
 * @return void
 */
function save_additional_account_details( $user_id ) {
  $user_details                  = get_userdata( $user_id );
  $_POST['account_email']        = $user_details->user_email;
  $_POST['account_display_name'] = $user_details->display_name;
  if ( isset( $_POST['billing_company'] ) ) {
    // WordPress default last name field.
    update_user_meta( $user_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
  }
}
add_action( 'woocommerce_save_account_details', 'save_additional_account_details' );

/**
 * Redirect after registration.
 *
 * @return string URL
 */
function user_autologout() {
  if ( is_user_logged_in() ) {
    $current_user    = wp_get_current_user();
    $user_id         = $current_user->ID;
    $approved_status = get_user_meta( $user_id, 'pw_user_status', true );
    $redirect_url = home_url( '/client-dashboard/' );
    // if the user hasn't been approved yet by WP Approve User plugin, destroy the cookie to kill the session and log them out
    if ( $approved_status == 'approved' ) {
      return $redirect_url;
    } else {
      wp_logout();
      return home_url( '/client-dashboard/' ) .
      'registration-successful/?ref_id=' . base64_encode( $user_id );
    }
  }
}
add_action( 'woocommerce_registration_redirect', 'user_autologout', 2 );

/**
 * Redirect after login.
 *
 * @param $redirect
 *
 * @return string URL
 */

function custom_login_redirect( $redirect, $user ) { 
  if(in_array('administrator', $user->roles)){
   wp_logout();
   $message = 'Admin user not allowed to login from here. Please, login from wordpress admin dashboard.';
   throw new Exception( $message );
  } else{
    wp_redirect( home_url( '/client-dashboard/' ) );
  }
} 
add_filter( 'woocommerce_login_redirect', 'custom_login_redirect', 10, 2 ); 


/**
 * Create custom footer logo widgit.
 *
 * @return void
 */
function custom_footer_logo() {
  register_sidebar(
    array(
      'name'          => 'Footer Logo',
      'id'            => 'footer_logo',
      'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
      'after_widget'  => '</section>',
      'before_title'  => '<h4 class="widget-title">',
      'after_title'   => '</h4>',
    )
  );
}
add_action( 'widgets_init', 'custom_footer_logo' );

/**
 * Create custom footer quote widgit.
 *
 * @return void
 */
function custom_footer_quote() {
  register_sidebar(
    array(
      'name'          => 'Footer Quote',
      'id'            => 'footer_quote',
      'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
      'after_widget'  => '</section>',
      'before_title'  => '<h4 class="widget-title">',
      'after_title'   => '</h4>',
    )
  );
}
add_action( 'widgets_init', 'custom_footer_quote' );

/**
 * Create partner logo footer widgit.
 *
 * @return void
 */
function custom_partner_logo_widgit() {
  register_sidebar(
    array(
      'name'          => 'Partner Logo',
      'id'            => 'partner_logo_widgit',
      'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
      'after_widget'  => '</section>',
      'before_title'  => '<h4 class="widget-title">',
      'after_title'   => '</h4>',
    )
  );
}
add_action( 'widgets_init', 'custom_partner_logo_widgit' );

/**
 * Create partner logo footer widgit.
 *
 * @return void
 */
function testimonial_widgit() {
  register_sidebar(
    array(
      'name'          => 'Testimonial Widgit',
      'id'            => 'testimonial_widgit',
      'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
      'after_widget'  => '</section>',
      'before_title'  => '<h4 class="widget-title">',
      'after_title'   => '</h4>',
    )
  );
}
add_action( 'widgets_init', 'testimonial_widgit' );

/**
 * Reorder my account page links
 *
 * @return array $menuOrder
 */
function my_account_menu_order() {
  $menuOrder = array(
    'edit-account'    => __( 'Account Details', 'woocommerce' ),
    'my-document'     => __( 'My Documents', 'woocommerce' ),
    'customer-logout' => __( 'Logout', 'woocommerce' ),
  );
  return $menuOrder;
}
 add_filter( 'woocommerce_account_menu_items', 'my_account_menu_order' );

/**
 * Register my account page permalink end point
 *
 * @return void
 */
function custom_add_endpoint() {
  add_rewrite_endpoint( 'my-document', EP_PAGES );
}
add_action( 'woocommerce_init', 'custom_add_endpoint' );
$endpoint = 'my-document';

/**
 * Add content on registered endpoint page
 *
 * @return void
 */
function wk_endpoint_content() {
  ?>
  <h2 class="avada-woocommerce-myaccount-heading">My Documents
  <span class="document-desc"> ( Search document by Document Name, Tag Name and Tag Description )</span></h2>
  <?php
  echo do_shortcode( '[client-documents-list]' );
}
add_action( 'woocommerce_account_' . $endpoint . '_endpoint', 'wk_endpoint_content' );

/**
 * Enqueue script file in the theme
 *
 * @return void
 */
function custom_ajax_request() {
  // load our jquery file that sends the $.post request
  wp_enqueue_script( 'ajax-request', get_stylesheet_directory_uri() . '/assets/js/ajax-request.js', array( 'jquery' ) );

  // make the ajaxurl var available to the above script
  wp_localize_script( 'ajax-request', 'custom_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_print_scripts', 'custom_ajax_request' );

/**
 * Store document close time in database for every user
 *
 * @return string
 */
function document_withdraw_time() {
  global $wpdb;
  $user_id     = $_POST['user_id'];
  $table_name  = 'wp_documents_meta';
  $document_id = base64_decode( $_POST['document_id'] );
  $access_type = (int) $_POST['access_type'];
  if ( is_user_logged_in() ) {
    $query         = "SELECT * FROM $table_name WHERE user_id = '" . $user_id . "' AND document_id = '" . $document_id . "'";
    $document_meta = $wpdb->get_row( $query );
    if ( $wpdb->num_rows ) {
      if ( $access_type === 1 ) {
        $wpdb->query(
          $wpdb->prepare(
            "UPDATE $table_name 
                    SET last_access_time = %s, last_withdraw_time = NULL, 
                    no_of_times = %s, is_active = 1 
                    WHERE user_id = %s AND document_id = %s",
            gmdate( 'Y-m-d h:i:s' ),
            intval( $document_meta->no_of_times ) + 1,
            $user_id,
            $document_id
          )
        );
        echo $wpdb->last_query;
      } else {
        $wpdb->query(
          $wpdb->prepare(
            "UPDATE $table_name 
                      SET last_withdraw_time = %s, is_active = 0
                   WHERE user_id = %s AND document_id = %s",
            gmdate( 'Y-m-d h:i:s' ),
            $user_id,
            $document_id
          )
        );
      }
    } else {
      $wpdb->insert(
        $table_name,
        array(
          'user_id'          => $user_id,
          'document_id'      => $document_id,
          'last_access_time' => gmdate( 'Y-m-d h:i:s' ),
          'no_of_times'      => 1,
          'is_active'        => 1,
        )
      );
    }
  }
  echo 1;
  die();
}
add_action( 'wp_ajax_store_document_withdraw_time', 'document_withdraw_time' );

/**
 * Store favourite/bookmarked document status in database
 *
 * @return string
 */
function update_document_bookmarked_status() {
  global $wpdb;
  if ( ! wp_verify_nonce( $_POST['nonce'], 'bookmark_status' ) ) {
    exit( 'No naughty business please' );
    $response = 0;
  }
  $table_name         = 'wp_documents_meta';
  $current_user_id    = get_current_user_id();
  $document_id        = intval( base64_decode( $_POST['document_id'] ) );
  $book_marked        = intval( $_POST['book_marked'] );
  $query              = "SELECT * FROM $table_name WHERE user_id = '" . $current_user_id . "' AND document_id = '" . $document_id . "'";
  $book_marked_result = $wpdb->get_results( $query );
  if ( $wpdb->num_rows ) {
    // update record
    $wpdb->query(
      $wpdb->prepare(
        "UPDATE $table_name 
              SET is_bookmarked = %s 
           WHERE user_id = %s AND document_id = %s",
        $book_marked,
        $current_user_id,
        $document_id
      )
    );
  } else {
    $wpdb->insert(
      $table_name,
      array(
        'user_id'       => $current_user_id,
        'document_id'   => $document_id,
        'is_bookmarked' => $book_marked,
      )
    );
    // insert record
  }
  echo 1;
  die();
}
add_action( 'wp_ajax_document_bookmarked_request', 'update_document_bookmarked_status' );

/**
 * Store document close time in database when user logged out
 *
 * @return void
 */
function update_document_access_record() {
  global $wpdb;
  $table_name = 'wp_documents_meta';
  $user_info  = wp_get_current_user();
  $wpdb->query(
    $wpdb->prepare(
      "UPDATE $table_name 
              SET last_withdraw_time = %s, is_active = 0
           WHERE user_id = %s AND is_active = 1",
      gmdate( 'Y-m-d h:i:s' ),
      $user_info->ID
    )
  );
}
add_action( 'clear_auth_cookie', 'update_document_access_record', 10 );

/**
 * Reorder user list table coumns and add Company name in the list
 *
 * @param array $column columns name.
 *
 * @return array $column
 */
function new_modify_user_table( $column ) {
  $column = array(
    'cb'                  => '<input type="checkbox" />',
    'username'            => __( 'Username' ),
    'name'                => __( 'Name' ),
    'company'             => __( 'Company' ),
    'email'               => __( 'E-mail' ),
    'role'                => __( 'Role' ),
    'last_login_activity' => __( 'Last login activity' ),
    'pw_user_status'      => __( 'Status' ),
  );
  return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

/**
 * Modify user's list table content in admin end
 *
 * @param string $val.
 *
 * @param string $column_name column name.
 *
 * @param int    $user_id User ID.
 *
 * @return string $val/$formated_date
 */
function new_modify_user_table_row( $val, $column_name, $user_id ) {
  $result = $val;
  switch ( $column_name ) {
    case 'company':
      $result = get_user_meta( $user_id, 'billing_company', true );
      break;
    case 'last_login_activity':
      $login_date = get_user_meta( $user_id, 'last_login', true );
      if ( $login_date ) {
        $result = "<a href='" . admin_url( 'users.php?page=user-activity&id=' . $user_id ) . "'>" . date_format(
          date_create( $login_date ),
          'F j, Y g:i A'
        ) . '</a>';
      } else {
        $result = '<span aria-hidden="true">—</span>';
      }
      break;
    default:
  }
  return $result;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

/**
 * Add compnay name filter in admin's user section
 *
 * @param string $which.
 *
 * @return string $select
 */
function filter_by_company_name( $which ) {
  global $wpdb;
  $companies = $wpdb->get_col( "SELECT DISTINCT( meta_value ) FROM $wpdb->usermeta WHERE meta_key = 'billing_company' AND meta_value > ''" );
  // template for filtering
  $st = '<select name="company_%s" style="float:none;margin-left:10px;">
      <option value="">%s</option>%s</select>';

  // generate options
  $options = '';
  foreach ( $companies as $key => $company ) {
    $options .= '<option value="' . $company . '">' . $company . '</option>';
  }

  // combine template and options
  $select = sprintf( $st, $which, __( 'Company Name' ), $options );

  // output <select> and submit button
  echo $select;
  submit_button( __( 'Filter' ), null, $which, false );
}
add_action( 'restrict_manage_users', 'filter_by_company_name' );

/**
 * Filter user's list by compnay name
 *
 * @param $query.
 *
 * @return void
 */
function filter_users_by_company_name( $query ) {
  global $pagenow;
  if ( is_admin() && 'users.php' == $pagenow ) {
    // figure out which button was clicked. The $which in filter_by_job_role()
    $top    = $_GET['company_top'];
    $bottom = $_GET['company_bottom'];
    if ( ! empty( $top ) or ! empty( $bottom ) ) {
      $section = ! empty( $top ) ? $top : $bottom;
      // change the meta query based on which option was chosen
      $meta_query = array(
        array(
          'key'     => 'billing_company',
          'value'   => $section,
          'compare' => 'LIKE',
        ),
      );
      $query->set( 'meta_query', $meta_query );
    }
  }
}
add_filter( 'pre_get_users', 'filter_users_by_company_name' );


function check_attempted_login( $user, $username, $password ) {
    if ( get_transient( 'attempted_login' ) ) {
        $datas = get_transient( 'attempted_login' );

        if ( $datas['tried'] >= 5 ) {
            $until = get_option( '_transient_timeout_' . 'attempted_login' );
            $time = time_to_go( $until );

            return new WP_Error( 'too_many_tried',  sprintf( __( '<strong>ERROR</strong>: You have reached authentication limit, you will be able to try again in %1$s.' ) , $time ) );
        }
    }

    return $user;
}
add_filter( 'authenticate', 'check_attempted_login', 30, 3 ); 


function login_failed( $username ) {
    if ( get_transient( 'attempted_login' ) ) {
        $datas = get_transient( 'attempted_login' );
        $datas['tried']++;

        if ( $datas['tried'] <= 5 )
            set_transient( 'attempted_login', $datas , 300 );
    } else {
        $datas = array(
            'tried'     => 1
        );
        set_transient( 'attempted_login', $datas , 300 );
    }
}
add_action( 'wp_login_failed', 'login_failed', 10, 1 ); 

function time_to_go($timestamp)
{
    // converting the mysql timestamp to php time
    $periods = array(
        "second",
        "minute",
        "hour",
        "day",
        "week",
        "month",
        "year"
    );
    $lengths = array(
        "60",
        "60",
        "24",
        "7",
        "4.35",
        "12"
    );
    $current_timestamp = time();
    $difference = abs($current_timestamp - $timestamp);
    for ($i = 0; $difference >= $lengths[$i] && $i < count($lengths) - 1; $i ++) {
        $difference /= $lengths[$i];
    }
    $difference = round($difference);
    if (isset($difference)) {
        if ($difference != 1)
            $periods[$i] .= "s";
            $output = "$difference $periods[$i]";
            return $output;
    }
}

function custom_password_validation ($user, $password) {
  if( strlen($password) < 8 ){
    return new WP_Error( 'invalid_password',  sprintf( __( '<strong>ERROR</strong>: Password length must be greater than 8 characters.' ) ) );
  }
  return $user; 
}

add_filter('wp_authenticate_user', 'custom_password_validation',10,2);

// Disable theme an plugis auto update

add_filter( 'auto_update_theme', '__return_false' );
add_filter( 'auto_update_plugin', '__return_false' );