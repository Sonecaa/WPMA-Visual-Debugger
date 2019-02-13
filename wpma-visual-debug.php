<?php
/*
Plugin Name: WPMA: Visual Debugger
Description: Visualize var_dumps of all post types of your site.
Version: 1.0
Author: WPMA
Author URI: https://github.com/Sonecaa
License: GPLv2 or later
*/

if (!class_exists('WPMA_Visual_Debugger')) :
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

            // hooks
		} // end __construct;

	} // end class WPMA_Visual_Debugger;

	/**
	 * Returns the active instance of the plugin
	 *
	 * @return WPMA_Visual_Debugger
	 */
	function WPMA_Visual_Debugger() {
		return WPMA_Visual_Debugger::get_instance();
	} // end WPMA_Visual_Debugger;

    WPMA_Visual_Debugger(); // init;
endif;

// add_action('before_render_selector_language', 'wu_display_icon_selector_language');
// add_action('after_render_selector_language', 'wu_add_script_selector_language');
add_action('wp_print_scripts', 'wu_localize_script_selector_language');
// add_action('wp_ajax_get_url_args_selector_language', 'wu_get_url_args_selector_language');
// add_action('wp_ajax_nopriv_get_url_args_selector_language', 'wu_get_url_args_selector_language');

/**
 * Localizes scripts
 *
 * @return void
 */
function wu_localize_script_selector_language() {

    wp_localize_script('wu-selector-language', 'wu_selector_language_vars', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ));

}  // end wu_localize_script_selector_language;

/**
 * Add script after selector
 *
 * @return void
 */
function wu_add_script_selector_language() {
    global $wp;
    $request = $wp->request;
    $message = __('Changing the language...', 'wp-ultimo');
    ?>
    <script>
        jQuery(document).ready(function($){

            function blockui_selector_language(){
                $('.login').block({
                  message: '<b><?php echo $message; ?></b>',
                  css: {
                    padding: '30px',
                    background: 'transparent',
                    border: 'none',
                    color: '#444',
                    top: '150px',
                  },
                  overlayCSS: {
                    background: '#F1F1F1',
                    opacity: 0.75,
                    cursor: 'initial',
                  }
                });
            }// end blockui_selector_language;

            $( "#locale" ).change(function() {

                console.log($('#locale').val());

                //loader
                blockui_selector_language();

                if($('#locale').val() || $('#locale option:selected').attr("lang")) {
                    var data = {
                    'action': 'get_url_args_selector_language',
                    'locale': $('#locale').val() ? $('#locale').val() : $('#locale option:selected').attr("lang"),
                    'request': '<?php echo $request; ?>',
                    };
                    jQuery.ajax({
                        url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        type: 'post',
                        data: data,
                    success: function (response) {
                        if (response.success && response.data.url != '') {
                            window.location = response.data.url;
                        } else {
                            console.error(response);
                            jQuery('body').unblock();
                        }
                    },// end success;
                    error: function (error) {
                        console.error(error);
                        jQuery('body').unblock();
                    }// end error;
                    }) // end ajax;
                }// end if;
            });// end change();
        });
    </script>
    <?php

}  // end wu_add_script_selector_language;

/**
 * Ajax callback function
 *
 * @return void
 */
function wu_get_url_args_selector_language() {

	$current_url = home_url(add_query_arg(array('locale' => $_POST['locale']), $_POST['request']));

	setcookie('wu_selector_language', $_POST['locale'], time() + WEEK_IN_SECONDS, '/');

	wp_send_json_success(array(
		'url' => $current_url,
	));

}  // end wu_get_url_args_selector_language;

