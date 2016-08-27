<?php
/**
 * @package   JiveDig_Restful_P2P
 * @author    The Stiz Media, LLC <mike@thestizmedia.com>
 * @license   GPL-2.0+
 * @link      http://thestizmedia.com.com
 * @copyright 2016 BizBudding, INC
 *
 * @wordpress-plugin
 * Plugin Name:        Restful P2P
 * Description: 	   Connect 2 objects (via Posts 2 Posts) with the Rest API
 * Plugin URI:         TBD
 * Author:             Mike Hemberger
 * Author URI:         http://thestizmedia.com
 * Text Domain:        restful-p2p
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:            1.0.0
 * GitHub Plugin URI:  TBD
 * GitHub Branch:	   master
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get an HTML (button) link to connect two objects via Posts 2 Posts plugin
 *
 * @param  array  $args {
 *     @type string   name       The Posts 2 Posts connection name
 *     @type integer  from       The Posts 2 Posts 'from' connection ID
 *     @type integer  to         The Posts 2 Posts 'to' connection ID
 *     @type string   connect    The connection button text to display if 2 objects are not connected
 *     @type string   connected  The connection button text to display if 2 objects are already connected
 * }
 *
 * @return  string
 */
function get_restful_p2p_link( $args ) {
	$restful_p2p = restful_p2p();
	return $restful_p2p->get_link( $args );
}

/**
 * Main JiveDig_Restful_P2P Class.
 *
 * @since 1.0.0
 */
class JiveDig_Restful_P2P {
	/**
	 * Singleton
	 * @var   JiveDig_Restful_P2P The one true JiveDig_Restful_P2P
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main JiveDig_Restful_P2P Instance.
	 *
	 * Insures that only one instance of JiveDig_Restful_P2P exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @uses    JiveDig_Restful_P2P::setup_constants() Setup the constants needed.
	 * @uses    JiveDig_Restful_P2P::includes() Include the required files.
	 * @uses    JiveDig_Restful_P2P::load_textdomain() load the language files.
	 * @see     Slim_Optin()
	 * @return  object | JiveDig_Restful_P2P The one true JiveDig_Restful_P2P
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new JiveDig_Restful_P2P;
			// Methods
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'restful-p2p' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'restful-p2p' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'RESTFUL_P2P_VERSION' ) ) {
			define( 'RESTFUL_P2P_VERSION', '1.0.0' );
		}
		// Plugin Folder Path.
		if ( ! defined( 'RESTFUL_P2P_PLUGIN_DIR' ) ) {
			define( 'RESTFUL_P2P_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		// Plugin Includes Path
		if ( ! defined( 'RESTFUL_P2P_INCLUDES_DIR' ) ) {
			define( 'RESTFUL_P2P_INCLUDES_DIR', RESTFUL_P2P_PLUGIN_DIR . 'includes/' );
		}
		// Plugin Folder URL.
		if ( ! defined( 'RESTFUL_P2P_PLUGIN_URL' ) ) {
			define( 'RESTFUL_P2P_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		// Plugin Root File.
		if ( ! defined( 'RESTFUL_P2P_PLUGIN_FILE' ) ) {
			define( 'RESTFUL_P2P_PLUGIN_FILE', __FILE__ );
		}
		// Plugin Base Name
		if ( ! defined( 'RESTFUL_P2P_BASENAME' ) ) {
			define( 'RESTFUL_P2P_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
		// Vendor
		require_once RESTFUL_P2P_INCLUDES_DIR . '/lib/class-tgm-plugin-activation.php';
	}

	/**
	 * Initiate all the things
	 *
	 * @return void
	 */
	public function init() {
		register_activation_hook( __FILE__,   array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		// Required plugins
		add_action( 'tgmpa_register', array( $this, 'required_plugins' ) );
		// Do the things
		add_action( 'rest_api_init', 	  array( $this, 'register_rest_endpoint' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	public function activate() {
		// Maybe later
	}

	public function deactivate() {
		// Maybe later
	}

	/**
	 * Register the required plugins for this plugin.
	 *
	 * In this example, we register five plugins:
	 * - one included with the TGMPA library
	 * - two from an external source, one from an arbitrary source, one from a GitHub repository
	 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
	 *
	 * The variables passed to the `tgmpa()` function should be:
	 * - an array of plugin arrays;
	 * - optionally a configuration array.
	 * If you are not changing anything in the configuration array, you can remove the array and remove the
	 * variable from the function call: `tgmpa( $plugins );`.
	 * In that case, the TGMPA default settings will be used.
	 *
	 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
	 */
	function required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
			array(
				'name'      => 'Posts 2 Posts',
				'slug'      => 'posts-to-posts',
				'required'  => true,
			),
			array(
				'name'      => 'WordPress REST API (Version 2)',
				'slug'      => 'rest-api',
				'required'  => true,
			),
		);

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = array(
			'id'           => 'restful-p2p',            // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                       // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',  // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'manage_options',    		// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                     // Show admin notices or not.
			'dismissable'  => true,                     // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                       // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                    // Automatically activate plugins after installation or not.
			'message'      => '',                       // Message to output right before the plugins table.
			'strings'      => array(
				'notice_can_install_required'     => _n_noop(
					'This plugin requires the following plugin: %1$s.',
					'This plugin requires the following plugins: %1$s.',
					'restful-p2p'
				),
				'notice_can_install_recommended'  => _n_noop(
					'This plugin recommends the following plugin: %1$s.',
					'This plugin recommends the following plugins: %1$s.',
					'restful-p2p'
				),
			),
		);
		tgmpa( $plugins, $config );
	}

	/**
	 * Register rest endpoint
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function register_rest_endpoint() {

	    register_rest_route( 'restful-p2p/v1', '/connect/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'connect' ),
			'args'	   => array(
	            'from' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	            'to' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	        ),
	    ));

	    register_rest_route( 'restful-p2p/v1', '/disconnect/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'disconnect' ),
			'args'	   => array(
	            'from' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	            'to' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
	            ),
	        ),
	    ));
	}

	/**
	 * Register and localize script to be enqueued later
	 * File should be in same directory as this 'class-restful-p2p.php' file
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
    function register_scripts() {
        wp_register_script( 'restful-p2p', RESTFUL_P2P_PLUGIN_URL . '/js/restful-p2p.js', array('jquery'), RESTFUL_P2P_VERSION, true );
        wp_localize_script( 'restful-p2p', 'restful_p2p_vars', array(
			'root'	=> esc_url_raw( rest_url() ),
			'nonce'	=> wp_create_nonce( 'wp_rest' ),
        ) );
    }

	/**
	 * Helper function to enqueue scripts
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
    function enqueue_scripts() {
    	wp_enqueue_script( 'restful-p2p' );
    }

	/**
	 * Get the link to connect objects
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $args  array of data to build the link
	 *
	 * @return string
	 */
	public function get_link( $args ) {
		$this->enqueue_scripts();
		return $this->get_connection_link_html( $args );
	}

	/**
	 * Get the html link to connect objects
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $args  array of data to build the link
	 *
	 * @return string
	 */
	function get_connection_link_html( $args ) {

		$defaults = array(
			'name'		=> null,
			'from'		=> null,
			'to'		=> null,
			'connect'	=> __( 'Connect', 'restful-p2p' ),
			'connected'	=> __( 'Connected', 'restful-p2p' ),
			'loading'	=> __( '&hellip;', 'restful-p2p' ),
		);
		$args = wp_parse_args( $args, $defaults );

		// Bail if we have enough data to build a link
		if ( ! ( $args['name'] && $args['from'] && $args['to'] ) ) {
			return;
		}

		$text  = $args['connect'];
		$class = ' connect';

		/**
		 * If connection already exists, change the class and the text
		 * This is only for initial page load, JS will have to update after click
		 */
		if ( $this->connection_exists( $args['name'], $args['from'], $args['to'] ) ) {
			$text  = $args['connected'];
			$class = ' connected';
		}

		/**
		 * Build the HTML/button
		 * Hidden by default, and made visible by JS
		 * This ensures the button is only shown if JS is enabled
		 */
		return sprintf('<button style="display:none;" data-name="%s" data-from-id="%s" data-to-id="%s" data-connect="%s" data-connected="%s" data-loading="%s" class="button btn restful-p2p-button %s">%s</button>',
			esc_attr($args['name']),
			esc_attr($args['from']),
			esc_attr($args['to']),
			esc_attr($args['connect']),
			esc_attr($args['connected']),
			esc_attr($args['loading']),
			$class,
			esc_attr($text)
		);
	}

	/**
	 * Connect 2 objects
	 *
	 * @since   1.0.0
	 *
	 * @param 	array  $data  Associative array containing 'from' and 'to' connection IDs and 'name' p2p connection name
	 *
	 * @return  array
	 */
	function connect( $data ) {
		// Create connection
		$p2p = p2p_type( $data['name'] )->connect( $data['from'], $data['to'], array(
		    'date' => current_time('mysql')
		) );
		// Success/Fail
		if ( is_wp_error( $p2p ) ) {
			// Fail
			return array(
				'success' => false,
				'message' => $p2p->get_error_message(),
			);
		} else {
			// Success
			return array(
				'success' => true,
			);
		}
	}

	/**
	 * Disconnect 2 objects
	 *
	 * @since   1.0.0
	 *
	 * @param 	array  $data  Associative array containing 'from' and 'to' connection IDs and 'name' p2p connection name
	 *
	 * @return  array
	 */
	function disconnect( $data ) {
		// Remove connection
		$p2p = p2p_type( $data['name'] )->disconnect( $data['from'], $data['to'] );
		// Success/Fail
		if ( is_wp_error( $p2p ) ) {
			// Fail
			return array(
				'success' => false,
				'message' => $p2p->get_error_message(),
			);
		} else {
			// Success
			return array(
				'success' => true,
			);
		}
	}

    /**
     * Check if a connection exists
     *
     * @since   1.0.0
     *
     * @param   string  $name  name of p2p connection
     * @param  	string  $from  object connecting from
     * @param   string  $to    object connecting to
     *
     * @return  bool
     */
    function connection_exists( $connection_name, $from, $to ) {
		$data = array(
			'from' => $from,
			'to'   => $to,
		);
		return p2p_connection_exists( $connection_name, $data );
    }

    /**
     * Get the post ID sent by the AJAX request.
     *
     * @return integer
     */
    function get_from() {
        return $this->sanitize_id( $_POST['from'] );
    }


    /**
     * Get the post ID sent by the AJAX request.
     *
     * @return integer
     */
    function get_to() {
        return $this->sanitize_id( $_POST['to'] );
    }


    /**
     * Sanitize an ID sent by the AJAX request.
     *
     * @param  integer  $id  The ID to sanitize
     *
     * @return integer
     */
    function sanitize_id( $id ) {
    	$post_id = 0;
        if ( isset($id) ) {
            $post_id = absint(filter_var($id, FILTER_SANITIZE_NUMBER_INT));
        }
        return $post_id;
    }

}

/**
 * The main function for that returns JiveDig_Restful_P2P
 *
 * The main function responsible for returning the one true JiveDig_Restful_P2P
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $restful_p2p = JiveDig_Restful_P2P(); ?>
 *
 * @since 1.0.0
 *
 * @return object|JiveDig_Restful_P2P The one true JiveDig_Restful_P2P Instance.
 */
function restful_p2p() {
	return JiveDig_Restful_P2P::instance();
}
// Get JiveDig_Restful_P2P Running.
restful_p2p();
