<?php

/*
Plugin Name: Disable Auto Updates
Plugin URI: http://exeebit.com/wordpress-plugin/disable-automatic-updates
Description: A simple plugin to disable plugin, theme or core automatic updates
Version: 1.4
Author: Exeebit
Author URI: http://exeebit.com
License: GPLv3
*/

/**
 *
 * @package dau
 *
 */

defined('ABSPATH') or die('Unauthorized Access');

if(!class_exists('Da_updates')){

	class Da_updates{

		public $dau_dir = WP_CONTENT_DIR . '/uploads/disable-auto-updates';

		public function register() {
			add_action('admin_menu', array($this, 'add_admin_pages'));
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_filter('clean_url', [$this, 'script_async'], 11, 1);
			add_filter("plugin_row_meta", [$this, "meta"], 10, 2);
			add_filter( 'plugin_action_links', [$this, 'ads_action_links'], 10, 5 );
			// add_filter( 'plugin_auto_update_setting_html', [ $this, 'replace_auto_update_text' ], 10, 2 );
		}

		public function add_admin_pages() {
			add_submenu_page('tools.php', 'Disable Auto Updates', 'Disable Auto Updates', 'manage_options', 'disable-auto-updates', [$this, 'view']);
			if(file_exists("$this->dau_dir/disable-all.php") || file_exists("$this->dau_dir/hide-notification.php") || file_exists("$this->dau_dir/disable-core.php") && file_exists("$this->dau_dir/disable-theme.php") && file_exists("$this->dau_dir/disable-plugin.php")) {
				remove_submenu_page( 'index.php', 'update-core.php');
			}
		}

		public function view() {
			require_once plugin_dir_path( __FILE__ ) . 'view/view.php';
		}

		public function activate() {
			flush_rewrite_rules();
		}

		public function deactivate() {
			flush_rewrite_rules();
		}
		public function enqueue() {
			wp_enqueue_style('dau-plugin', plugins_url( 'css/styles.css', __FILE__ ));
			wp_enqueue_script('dau-plugin', plugin_dir_url(__FILE__) . 'js/scripts.min.js#async');
		}
		public function script_async($url) {
			if(strpos($url, '#async') === false) {
				return $url;
			} else {
				return str_replace('#async', '', $url) . "' async='async";
			}
		}
		public function footer_notice(){
			echo '<span id="footer-thankyou">Thank you for using <a href="https://exeebit.com">Exeebit</a>\'s product. <a href="https://www.paypal.com/donate?hosted_button_id=LV33MVDQUBSYY" target="_blank">Buy Me a Coffee <span style="color: red">&#x2764;</span></a></span>';
		}

		public function thankyou() {
			add_filter("admin_footer_text", [$this, 'footer_notice']);
		}

		public function meta($links = [], $file = "") {
			if(strpos($file, "disable-auto-updates/disable-auto-updates.php") !== false) {
				$new_link = [
					"donation" => '<a href="https://www.paypal.com/donate?hosted_button_id=LV33MVDQUBSYY" target="_blank">Buy Me a Coffee <span style="color: red">&#x2764;</span></a>'
				];

				$links = array_merge($links, $new_link);
			}

			return $links;

		}


		public function replace_auto_update_text() {
			return "Auto update has been disabled by <a href='https://wordpress.org/plugins/disable-auto-updates'>Disable Auto Updates</a>";
		}


		public function ads_action_links( $links, $plugin_file ) {

			$plugin = plugin_basename( __FILE__ );

			if($plugin === $plugin_file) {
				$ads_links = [
					'<a href="' . admin_url( 'tools.php?page=disable-auto-updates' ) . '">Settings</a>',
				];
				$links = array_merge($ads_links, $links);
			}
			return $links;
		}

	}

	if(class_exists( 'Da_updates' )) $disable_auto_updates = new Da_updates();
	else die('Plugin internal code conflict');


	$disable_auto_updates->register();


	register_activation_hook(__FILE__, [$disable_auto_updates, 'activate']);
	register_deactivation_hook(__FILE__, [$disable_auto_updates, 'deactivate']);


	$dau_services = ['disable-all', 'disable-plugin', 'disable-theme', 'disable-core', 'disable-admin-notice', 'hide-notification'];

	foreach ($dau_services as $service) {
		if(file_exists("$disable_auto_updates->dau_dir/$service.php")) {
			include_once "$disable_auto_updates->dau_dir/$service.php";
		}
	}


}
