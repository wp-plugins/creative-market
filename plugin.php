<?php
/*
Plugin Name: Creative Market
Plugin URI: http://github.com/chrismccoy
Description: A widget to show off a creative market item
Version: 1.1
Author: Chris McCoy
Author URI: http://github.com/chrismccoy
License: GPL2
*/

class Creative_Market {

	function __construct() {
		add_action( 'plugins_loaded', array( &$this, 'lang'), 2);
		add_action( 'plugins_loaded', array( &$this, 'includes'), 3);
		add_action('widgets_init', array( &$this, 'widget_register'));
	}

	function lang() {
		load_plugin_textdomain( 'creativemarket-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function includes() {
		require_once( plugin_dir_path(__FILE__) . 'inc/widget.php');
	}

	function widget_register() {
		register_widget( 'Creative_Market_Widget' );
	}

}

$Creative_Market = new Creative_Market;
