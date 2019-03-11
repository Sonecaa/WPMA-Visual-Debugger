<?php
/*
Plugin Name: WPMA Visual Debugger
Description: Visualize var_dumps, json pretty of all post types of your site.
Version: 0.1.0
Author: WPMA
Author URI: https://github.com/Sonecaa
License: GPLv2 or later
*/

if (!class_exists('WPMA_Visual_Debugger')) :
    /**
     * WPMA_Visual_Debugger - Class
     */
	class WPMA_Visual_Debugger {

		/**
		 * Version of the Plugin
		 *
		 * @var string
		 */
		public $version = '1.3.0';

		/**
		 * Makes sure we are only using one instance of the plugin
		 *
		 * @var object WPMA_Visual_Debugger
		 */
		public static $instance;

		/**
		 * Returns the instance of WP_Ultimo_APC
		 *
		 * @return object A WP_Ultimo_APC instance
		 */
		public static function get_instance() {
			if (null === self::$instance) {
				self::$instance = new self();
			} // end if;
			return self::$instance;
		} // end get_instance;

		/**
		 * Initializes the plugins
		 */
		public function __construct() {

            // Set the plugins_path
			$this->plugins_path = plugin_dir_path(__DIR__);
            $this->file         = __FILE__;

			// add_action('admin_init', function() {
				// wu_console_log('string', array('1', '2', '2'), WPMA_Visual_Debugger::get_instance(), PHP_VERSION);
            // });
            // hooks
            add_action( 'admin_menu', array($this, 'wpma_add_menu_admin'));

            add_action('admin_enqueue_scripts', array($this, 'wpma_load_assets'), 10);

            add_action('wp_ajax_wpma_get_posts_by_post_type', array($this, 'wpma_get_posts_by_post_type'));

            add_action('wp_ajax_wpma_get_var_dump_post', array($this, 'wpma_get_var_dump_post'));

            add_action('wp_ajax_wpma_has_meta', array($this, 'wpma_has_meta'));

            add_action('wp_ajax_wpma_already_publish', array($this, 'wpma_already_publish'));

		} // end __construct;

        /**
         * Add menu admin
         *
         * @return void
         */
        public function wpma_add_menu_admin() {

            add_menu_page( 'WPMA Visual Debugger', 'WPMA Visual Debugger', 'manage_options', 'wpma-visual-debugger', array($this, 'wpma_display_main_view'), 'dashicons-editor-code', 1  );

        } // end wpma_add_menu_admin;

        /**
         * Display main view
         *
         * @return void
         */
        public function wpma_display_main_view() {
            include dirname( __FILE__ ) . '/views/view_main.php';
        }  // end wpma_display_main_view;

        /**
         * Load Assets
         *
         * @return void
         */
        public function wpma_load_assets() {

            wp_enqueue_style('wpma-stylesheet', plugins_url(null, __FILE__) . '/assets/main.css');
            wp_enqueue_style('wpma-stylesheet-loader', plugins_url(null, __FILE__) . '/assets/loader.css');
            wp_enqueue_style('wpma-stylesheet-tabs-results', plugins_url(null, __FILE__) . '/assets/tabs-results.css');
            wp_enqueue_style('wpma-stylesheet-tabs-metas', plugins_url(null, __FILE__) . '/assets/tabs-metas.css');
            wp_register_script('wpma-javascript', plugins_url(null, __FILE__) . '/assets/main.js', array( 'jquery' ), true );
            wp_localize_script( 'wpma-javascript', 'wpma_vars', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
            wp_enqueue_script( 'wpma-javascript');

        }  // end wpma_load_assets;

        /**
         * Return all post types
         *
         * @return array
         */
        public function wpma_get_post_types() {

            return get_post_types();
        } // end wpma_get_post_types;

        /**
         * Return posts by post type
         *
         * @return array
         */
        public function wpma_get_posts_by_post_type() {

            if (!isset($_POST['post-type'])) {
                wp_send_json_error('WPMA_wpma_get_posts_by_post_type ERROR: Not set $_POST');
            } // end if;

            $args = array(
                'numberposts' => -1,
                'post_type'   => $_POST['post-type'],
            );

            $posts = get_posts( $args );

            wp_send_json_success(array(
                'posts' => $posts,
            ));
        } // end wpma_get_posts_by_post_type;

        /**
         * Return var dump of post type
         *
         * @return void
         */
        public function wpma_get_var_dump_post() {

            if (!isset($_POST['post'])) {
                wp_send_json_error('WPMA_wpma_get_var_dump_post ERROR: Not set $_POST');
            } // end if;

            $post = get_post( $_POST['post'] );

            wp_send_json_success(array(
                'id'               => $post->ID,
                'var_dump'         => $post,
                'json'             => json_encode($post),
                'json_pretty'      => json_encode($post, JSON_PRETTY_PRINT),
                'var_dump_meta'    => get_post_meta($post->ID),
                'json_meta'        => json_encode(get_post_meta($post->ID)),
                'json_pretty_meta' => json_encode(get_post_meta($post->ID), JSON_PRETTY_PRINT),
            ));
        } // end wpma_get_var_dump_post;

        /**
         * Checks post has meta
         *
         * @return mixed
         */
        public function wpma_has_meta() {
            if (!isset($_POST['id'])) {
                wp_send_json_error('WPMA_wpma_has_meta ERROR: Not set $_POST');
            } // end if;

            if (!empty( get_post_meta( $_POST['id'] ) ) ) {
                wp_send_json_success(array(
                    'boolean' => 'true',
                ));
            } else {
                wp_send_json_success(array(
                    'boolean' => 'false',
                ));
            } // end if;

        } // end wpma_has_meta;

        /**
         * Checks post already publish
         *
         * @return mixed
         */
        public function wpma_already_publish() {
            if (!isset($_POST['id'])) {
                wp_send_json_error('WPMA_wpma_already_publish ERROR: Not set $_POST');
            } // end if;

            if (get_post($_POST['id'])->post_status == 'publish' ) {
                wp_send_json_success(array(
                    'boolean' => 'true',
                ));
            } else {
                wp_send_json_success(array(
                    'boolean' => 'false',
                ));
            } // end if;

        }  // end wpma_already_publish;

	}  // end class WPMA_Visual_Debugger;


	/**
	 * Returns the active instance of the plugin
	 *
	 * @return WPMA_Visual_Debugger
	 */
	function wpa_visual_debugger() {
		return WPMA_Visual_Debugger::get_instance();
	} // end wpa_visual_debugger;

    wpa_visual_debugger(); // init;
endif;

