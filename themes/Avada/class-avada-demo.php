<?php
/**
 * Handler for contact pages.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle contact pages.
 */
class Avada_Demo {
	/**
	 * The recaptcha class instance.
	 *
	 * @access public
	 * @var bool|object
	 */
	public $re_captcha = false;

	/**
	 * Do we have an error?
	 *
	 * @access public
	 * @var bool
	 */
	public $has_error = false;

	/**
	 * Contact name
	 *
	 * @since 5.8
	 * @access public
	 * @var string
	 */
	public $error_message = '';

	/**
	 * Contact name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = '';

	/**
	 * Phone.
	 *
	 * @access public
	 * @var string
	 */
	public $phone = '';

	/**
	 * Email address.
	 *
	 * @access public
	 * @var string
	 */
	public $email = '';

	/**
	 * The message.
	 *
	 * @access public
	 * @var string
	 */
	public $message = '';

	/**
	 * Data privacy confirmation checkbox text.
	 *
	 * @access public
	 * @var int
	 */
	public $data_privacy_confirmation = 0;

	/**
	 * Has the email been sent?
	 *
	 * @access public
	 * @var bool
	 */
	public $email_sent = false;

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		$this->init_recaptcha();
		if ( isset( $_POST['submit'] ) ) { // WPCS: CSRF ok.
			$this->set_error_message();
			$this->process_name();
			$this->process_phone();
			$this->process_email();
			$this->process_message();
			$this->process_company();
			$this->process_service();

			if ( Avada()->settings->get( 'contact_form_privacy_checkbox' ) ) {
				$this->process_data_privacy_confirmation();
			}
			$this->process_recaptcha();

			if ( ! $this->has_error ) {
				$this->send_email();
			}
		}
	}

	/**
	 * Setup ReCaptcha.
	 *
	 * @access private
	 * @return void
	 */
	private function init_recaptcha() {
		$options = get_option( Avada::get_option_name() );
		if ( $options['recaptcha_public'] && $options['recaptcha_private'] && ! function_exists( 'recaptcha_get_html' ) ) {
			if ( version_compare( PHP_VERSION, '5.3' ) >= 0 && ! class_exists( 'ReCaptcha' ) ) {
				require_once Avada::$template_dir_path . '/includes/recaptcha/src/autoload.php';
				// We use a wrapper class to avoid fatal errors due to syntax differences on PHP 5.2.
				require_once Avada::$template_dir_path . '/includes/recaptcha/class-avada-recaptcha.php';
				// Instantiate ReCaptcha object.
				$re_captcha_wrapper = new Avada_ReCaptcha( $options['recaptcha_private'] );
				$this->re_captcha   = $re_captcha_wrapper->recaptcha;
			}
		}
	}

	/**
	 * Init and set the error message.
	 *
	 * @since 5.8
	 * @access private
	 * @param string|false $message The message we want to set.
	 * @return void
	 */
	private function set_error_message( $message = false ) {

		if ( $message ) {
			$this->error_message = $message;
		} else {
			$this->error_message = __( 'Please check if you\'ve filled all the fields with valid information. Thank you.', 'Avada' );
			if ( Avada()->settings->get( 'contact_form_privacy_checkbox' ) ) {
				$this->error_message = __( 'Please check if you\'ve filled all the fields with valid information and that the data privacy terms confirmation box is checked. Thank you.', 'Avada' );
			}
		}
	}

	/**
	 * Check to make sure that the name field is not empty.
	 *
	 * @access private
	 * @return void
	 */
	private function process_name() {
		$post_contact_name = ( isset( $_POST['contact_name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : ''; // WPCS: CSRF ok.
		if ( '' === $post_contact_name || esc_attr__( 'Name (required)', 'Avada' ) === $post_contact_name ) {
			$this->has_error = true;
		} else {
			$this->name = $post_contact_name;
		}
	}

	/**
	 * Phone field is required.
	 *
	 * @access private
	 * @return void
	 */
	private function process_phone() {
		$post_url = ( isset( $_POST['url'] ) ) ? sanitize_text_field( wp_unslash( $_POST['url'] ) ) : ''; // WPCS: CSRF ok.
		if ( '' === $post_url || esc_attr__( 'Phone (required)', 'Avada' ) === $post_url ) {
			$this->has_error = true;
		} else {
			$this->phone = $post_url;
		}
	}

	/**
	 * Company field is required.
	 *
	 * @access private
	 * @return void
	 */
	private function process_company() {
		$post_company = ( isset( $_POST['company'] ) ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : ''; // WPCS: CSRF ok.
		if ( '' === $post_company || esc_attr__( 'Company (required)', 'Avada' ) === $post_company ) {
			$this->has_error = true;
		} else {
			$this->company = $post_company;
		}
	}

	/**
	 * Check to make sure sure that a valid email address is submitted.
	 *
	 * @access private
	 * @return void
	 */
	private function process_email() {
		$email = ( isset( $_POST['email'] ) ) ? trim( sanitize_email( wp_unslash( $_POST['email'] ) ) ) : ''; // WPCS: CSRF ok.

		if ( '' === $email || esc_attr__( 'Email (required)', 'Avada' ) === $email ) {
			$this->has_error = true;
		} elseif ( false === filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$this->has_error = true;
		} else {
			$this->email = trim( $email );
		}
	}


	/**
	 * Get all selected services
	 *
	 * @access private
	 * @return void
	 */
	private function process_service() {
		$post_service = '';
		if ( '' === $_POST['service'] || count($_POST['service']) < 1 ) {
			$this->has_error = true;
		} else {
			foreach ($_POST['service'] as $key => $service_data) {
				if($key === 0){
					$post_service = $service_data;
				}
				if($key === 1){
					$post_service = $post_service.' , '.$service_data;
				}
			}
			$this->service = trim( $post_service );
		}
	}

	/**
	 * Check to make sure a message was entered.
	 *
	 * @access private
	 * @return void
	 */
	private function process_message() {
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			$message = ( isset( $_POST['msg'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['msg'] ) ) : ''; // WPCS: CSRF ok.
		} else {
			$message = ( isset( $_POST['msg'] ) ) ? wp_unslash( $_POST['msg'] ) : ''; // WPCS: CSRF ok sanitization ok.
		}
		if ( '' === $message || esc_attr__( 'Message', 'Avada' ) === $message ) {
			$this->has_error = true;
		} else {
			$this->message = ( function_exists( 'stripslashes' ) ) ? stripslashes( $message ) : $message;
		}
	}

	/**
	 * Check privacy data checkbox.
	 *
	 * @since 5.5
	 * @access private
	 * @return void
	 */
	private function process_data_privacy_confirmation() {
		$data_privacy_confirmation = ( isset( $_POST['data_privacy_confirmation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data_privacy_confirmation'] ) ) : 0; // WPCS: CSRF ok.

		if ( ! $data_privacy_confirmation ) {
			$this->has_error = true;
		} else {
			$this->data_privacy_confirmation = (int) $data_privacy_confirmation;
		}
	}

	/**
	 * Check recaptcha.
	 *
	 * @access private
	 * @return void
	 */
	private function process_recaptcha() {
		if ( $this->re_captcha ) {
			$re_captcha_response = null;
			// Was there a reCAPTCHA response?
			if ( 'v2' === Avada()->settings->get( 'recaptcha_version' ) ) {
				$post_recaptcha_response = ( isset( $_POST['g-recaptcha-response'] ) ) ? trim( wp_unslash( $_POST['g-recaptcha-response'] ) ) : ''; // WPCS: CSRF ok sanitization ok.
			} else {
				$post_recaptcha_response = ( isset( $_POST['fusion-recaptcha-response'] ) ) ? trim( wp_unslash( $_POST['fusion-recaptcha-response'] ) ) : ''; // WPCS: CSRF ok sanitization ok.
			}

			$server_remote_addr = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? trim( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : ''; // WPCS: sanitization ok.

			if ( $post_recaptcha_response && ! empty( $post_recaptcha_response ) ) {
				if ( 'v2' === Avada()->settings->get( 'recaptcha_version' ) ) {
					$re_captcha_response = $this->re_captcha->verify( $post_recaptcha_response, $server_remote_addr );
				} else {
					$site_url = get_option( 'siteurl' );
					$url_parts = parse_url( $site_url );
					$site_url = isset( $url_parts['host'] ) ? $url_parts['host'] : $site_url;
					$re_captcha_response = $this->re_captcha->setExpectedHostname( apply_filters( 'avada_recaptcha_hostname', $site_url ) )->setExpectedAction( 'contact_form' )->setScoreThreshold( Avada()->settings->get( 'recaptcha_score' ) )->verify( $post_recaptcha_response, $server_remote_addr );
				}
			}

			// Check the reCaptcha response.
			if ( null === $re_captcha_response || ! $re_captcha_response->isSuccess() ) {
				$this->has_error = true;

				$error_codes = array();
				if ( null !== $re_captcha_response ) {
					$error_codes = $re_captcha_response->getErrorCodes();
				}

				if ( empty( $error_codes ) || in_array( 'score-threshold-not-met', $error_codes ) ) {
					$this->error_message = __( 'Sorry, ReCaptcha could not verify that you are a human. Please try again.', 'Avada' );
				} else {
					$this->error_message = __( 'ReCaptcha configuration error. Please check the Theme Option settings and your Recaptcha account settings.', 'Avada' );
				}
			}
		}
	}

	/**
	 * Send the email.
	 *
	 * @access private
	 * @return void
	 */
	private function send_email() {
		$name                      = esc_html( $this->name );
		$email                     = sanitize_email( $this->email );
		$phone                   = wp_filter_kses( $this->phone );
		$company 				= wp_filter_kses($this->company);
		$message                   = wp_filter_kses( $this->message );
		$service                   = wp_filter_kses( $this->service );
		$data_privacy_confirmation = ( $this->data_privacy_confirmation ) ? esc_html__( 'confirmed', 'Avada' ) : '';

		if ( function_exists( 'stripslashes' ) ) {
			$phone = stripslashes( $phone );
			$message = stripslashes( $message );
		}

		$message = html_entity_decode( $message );

		$email_to = Avada()->settings->get( 'email_address' );
		/* translators: The name. */
		$body = sprintf( esc_attr__( 'Name: %s', 'Avada' ), " $name \n\n" );
		/* translators: The email. */
		$body .= sprintf( esc_attr__( 'Email: %s', 'Avada' ), " $email \n\n" );
		/* translators: The Phone. */
		$body .= sprintf( esc_attr__( 'Phone: %s', 'Avada' ), " $phone \n\n" );
		/* translators: The Company. */
		$body .= sprintf( esc_attr__( 'Company: %s', 'Avada' ), " $company \n\n" );
		/* translators: The Services. */
		$body .= sprintf( esc_attr__( 'Services: %s', 'Avada' ), " $service \n\n" );

		$body .= sprintf( esc_attr__( 'Message: %s', 'Avada' ), "\n$message \n\n" );


		if ( Avada()->settings->get( 'contact_form_privacy_checkbox' ) ) {
			/* translators: The data privacy terms. */
			$body .= sprintf( esc_attr__( 'Data Privacy Terms: %s', 'Avada' ), " $data_privacy_confirmation" );
		}
		$subject = get_option('blogname').' Free Demo Request';
		$headers = 'Reply-To: ' . $name . ' <' . $email . '>' . "\r\n";
		wp_mail( $email_to, $subject, $body, $headers );

		$this->email_sent = true;

		if ( $this->email_sent ) {
			$_POST['contact_name']              = '';
			$_POST['email']                     = '';
			$_POST['url']                       = '';
			$_POST['msg']                       = '';
			$_POST['data_privacy_confirmation'] = 0;

			$this->name                      = '';
			$this->email                     = '';
			$this->phone                   = '';
			$this->message                   = '';
			$this->data_privacy_confirmation = 0;
		}
	}
}
