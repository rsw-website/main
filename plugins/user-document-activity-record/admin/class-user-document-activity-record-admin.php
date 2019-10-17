<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       user-document-activity-record
 * @since      1.0.0
 *
 * @package    User_Document_Activity_Record
 * @subpackage User_Document_Activity_Record/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    User_Document_Activity_Record
 * @subpackage User_Document_Activity_Record/admin
 * @author     Mindfire Solutions <ayushs@mindfiresolutions.com>
 */
class User_Document_Activity_Record_Admin {

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
		 * defined in User_Document_Activity_Record_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_Document_Activity_Record_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/user-document-activity-record-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register admin menu page to the dashboard
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function user_activity_menu() {
  		add_menu_page('Client User Activity', 'User Activities', 'read', 'user-activity',array($this, 'get_user_activity'));
  		remove_menu_page('user-activity');
	}

	/**
	 * View user activity table list on admin end
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function get_user_activity(){
  		include_once('partials/user-document-activity-record-admin-display.php');
	}

	/**
	 * Store last logged in user time stap and IP address
	 *
	 * @since 1.0.0
	 *
	 * @param  $array $user_login
	 *
	 * @param  $array $user user details
	 *
	 * @return  void
	 */
	public function user_last_login( $user_login, $user ) {
    	update_user_meta( $user->ID, 'last_login', gmdate("Y-m-d h:i:s") );
    	update_user_meta( $user->ID, 'user_login_ip', $this->get_the_user_ip() );
	}

	/**
	 * Get logged in user IP address
	 *
	 * @since 1.0.0
	 *
	 * @return  string $ip
	 */
	public function get_the_user_ip() {
	  foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
	     if (array_key_exists($key, $_SERVER) === true) {
	         foreach (explode(',', $_SERVER[$key]) as $ip) {
	            if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
	            return $ip;
	          }
	        }
	     }
	  }
	}
}
