<?php
/**
 * Plugin Name: 	MailCheck for WordPress
 * Plugin URI:
 * Description:
 * Version: 		1.0.0
 * Author: 			Astoundify
 * Author URI: 		http://www.astoundify.com/
 * Text Domain: 	mailcheck-wordpress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class MailCheck_WordPress.
 *
 * Main WCMSA class initializes the plugin.
 *
 * @class		MailCheck_WordPress
 * @version		1.0.0
 * @author		Jeroen Sormani
 */
class MailCheck_WordPress {


	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.0.0';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Instace of MailCheck_WordPress.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of MailCheck_WordPress.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Initialize plugin parts
		$this->init();

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Add mailcheck.js
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Print mailcheck script
		add_action( 'wp_footer', array( $this, 'print_mailcheck_script' ) );
		add_action( 'login_footer', array( $this, 'print_mailcheck_script' ) );

		add_action( 'wp_head', array( $this, 'print_css' ) );
		add_action( 'login_head', array( $this, 'print_css' ) );

		// Load textdomain
		$this->load_textdomain();

	}


	/**
	 * Enqueue scripts.
	 *
	 * Enqueue script as javascript and style sheets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'mailcheck-for-wordpress', plugins_url( 'assets/js/mailcheck' . $suffix . '.js', __FILE__ ), array( 'jquery' ), $this->version, true );

	}


	/**
	 * Print CSS.
	 *
	 * Prince a little bit of CSS to the head.
	 *
	 * @since 1.0.0
	 */
	public function print_css() {

		?><style>
			#mailcheck-for-wordpress-suggest {
				position: absolute;
				font-size: 12px;
				color: #F00;
			}
			body.login #mailcheck-for-wordpress-suggest {
				position: relative;
			}
			.suggestion-link {
				font-weight: bold;
			}
		</style><?php

	}


	/**
	 * Print mailcheck script.
	 *
	 * Print the mailcheck configuration script.
	 *
	 * @since 1.0.0
	 */
	public function print_mailcheck_script() {

		$mailcheck_elements = apply_filters( 'mailcheck_for_wp_elements', '#user_email, #reg_email, #billing_email' );

		?><script type='text/javascript'>
			jQuery( document ).ready( function( $ ) {
				var last_suggestion = '';
				$( 'body' ).on( 'blur', '<?php echo $mailcheck_elements; ?>', function() {
					$( this ).mailcheck({
						suggested: function( element, suggestion ) {
							last_suggestion = suggestion.full;
							$( '#mailcheck-for-wordpress-suggest' ).remove();
							$( '<?php echo $mailcheck_elements; ?>' ).after( '<div id="mailcheck-for-wordpress-suggest"><span class="suggestion-text"><?php _e( 'Did you mean', 'mailcheck-wordpress' ); ?> <a href="javascript:void(0);" class="suggestion-link">' + suggestion.full + '</a>?</span></div>');
						},
					});
				});

				$( 'body' ).on( 'click', '.suggestion-link', function() {
					$( '<?php echo $mailcheck_elements; ?>' ).val( last_suggestion );
					$( '#mailcheck-for-wordpress-suggest' ).remove();
				});
			});
		</script><?php

	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {

		// Load textdomain
		load_plugin_textdomain( 'mailcheck-wordpress', false, basename( dirname( __FILE__ ) ) . '/languages' );

	}


}


/**
 * The main function responsible for returning the MailCheck_WordPress object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php MailCheck_WordPress()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object MailCheck_WordPress class object.
 */
if ( ! function_exists( 'MailCheck_WordPress' ) ) :

 	function MailCheck_WordPress() {
		return MailCheck_WordPress::instance();
	}

endif;

MailCheck_WordPress();