<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 0.0.1
 * @package Etherion_Tools
 *
 * @wordpress-plugin
 * Plugin Name:     Etherion Tools
 * Description:     Etherion Kit Tools
 * Version:         1.0.0
 * Author:          Sebastián Trujillo
 * Author URI:      http://sebastian-trujillo.me/cv
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     etherion-tools
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Direct access not permitted.' );
}

if ( ! class_exists( 'Etherion_Tools' ) ) {

    /**
     * main etherion_tools class
     *
     * @class etherion_tools
     * @since 0.0.1
     */
    class Etherion_Tools {

        /**
         * etherion_tools plugin version
         *
         * @var string
         */
        public $version = '1.0.0';

        /**
         * The single instance of the class.
         *
         * @var Etherion_Tools
         * @since 0.0.1
         */
        protected static $instance = null;

	    /**
	     * The instance of the Posts API class.
	     *
	     * @var Posts_API
	     * @since 1.0.0
	     */
	    private Posts_API $posts_API;

        /**
         * Main etherion_tools instance.
         *
         * @return Etherion_Tools - main instance.
         *@since 0.0.1
         * @static
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * etherion_tools class constructor.
         */
        public function __construct() {
            $this->define_constants();
	        $this->load_plugin_textdomain();
	        $this->includes();
			$this->init_classes();
            $this->define_actions();
        }

        public function load_plugin_textdomain(): void { 
            load_plugin_textdomain( 'etherion-tools', false, dirname(ETHERION_TOOLS_PLUGIN_BASENAME) . '/lang/' );
        }

        /**
         * Include required core files
         */
        public function includes(): void {
            // Load custom functions and hooks
            require_once __DIR__ . '/includes/includes.php';
        }

	    /**
	     * Init required classes
	     */
	    public function init_classes(): void {
		    $this->posts_API = new Posts_API();
	    }

        /**
         * Get the plugin path.
         *
         * @return string
         */
        public function plugin_path(): string {
            return untrailingslashit(plugin_dir_path(__FILE__ ));
        }


        /**
         * Define etherion_tools constants
         */
        private function define_constants(): void {
            define( 'ETHERION_TOOLS_PLUGIN_FILE', __FILE__ );
            define( 'ETHERION_TOOLS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            define( 'ETHERION_TOOLS_VERSION', $this->version );
            define( 'ETHERION_TOOLS_PATH', $this->plugin_path() );
        }

        /**
         * Define etherion_tools actions
         */
        public function define_actions(): void {
	        add_action('admin_menu', array($this, 'define_menus'));

	        register_activation_hook( ETHERION_TOOLS_PLUGIN_FILE, array( $this, 'activate_plugin' ) );
	        register_deactivation_hook( ETHERION_TOOLS_PLUGIN_FILE, array( $this, 'deactivate_plugin' ) );

	        add_action('rest_api_init', array($this->posts_API, 'register_routes'));

	        add_action('admin_init', array($this->posts_API, 'register_settings'));
        }

        /**
         * Define etherion_tools menus
         */
        public function define_menus(): void {
			add_options_page(
				'Etherion Tools',
				'Etherion Kit Tools',
				'manage_options',
				'etherion-tools-settings',
				'create_menu_UI'
			);
        }

	    /**
	     * Activate the plugin
	     */
	    public function activate_plugin(): void {
			// Register link analysis cron job
		    register_link_analysis_cron();
	    }

	    /**
	     * Deactivate the plugin
	     */
	    public function deactivate_plugin(): void {
		    // remove link analysis cron job
		    wp_clear_scheduled_hook('link_analysis_cron');
	    }
    }

    $etherion_tools = new Etherion_Tools();
}