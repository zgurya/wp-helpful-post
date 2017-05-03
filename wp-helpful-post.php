<?php
/**
 * Plugin Name: WordPress Helpful Post
 * Plugin URI: https://github.com/zgurya/wp-helpful-post
 * Description: This plugin allows you to understand which post is most helpful.
 * Version: 0.1
 * Author: a.zgurya
 * Author URI: https://www.facebook.com/a.zgurya
 */

require_once (plugin_dir_path(__FILE__).'/inc/functions.php');
require_once (plugin_dir_path(__FILE__).'/inc/helper.php');
require_once (plugin_dir_path(__FILE__).'/inc/shortcode.php');

add_action('admin_menu', 'wp_helpful_post_menu');
add_action('wp_enqueue_scripts', 'wp_helpful_post_scripts');
register_uninstall_hook(__FILE__, 'wp_helpful_post_uninstall');

function wp_helpful_post_menu(){
	add_submenu_page('tools.php', 'Helpful Post', 'Helpful Post','manage_options','wp_helpful_post', 'wp_helpful_post_page');
}

function wp_helpful_post_page(){
	echo '<h1>Use our shortcode</h1>';
	echo '<b>[wp_helpful_post]</b>';
}

function wp_helpful_post_scripts(){
	wp_enqueue_style('wp-helpful-post-front-css', plugin_dir_url( __FILE__ ).('css/front.css'));
	wp_enqueue_script('wp-helpful-post-front-js', plugin_dir_url( __FILE__ ).('js/front.js'), array('jquery'), null, true );
	wp_localize_script('wp-helpful-post-front-js', 'ajax_object',array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

function wp_helpful_post_uninstall(){
	global $wpdb;
	$table_name = $wpdb->prefix . "postmeta";
	$query='DELETE FROM '.$table_name.' WHERE wp_postmeta="wp_helpful_post_no" OR wp_postmeta="wp_helpful_post_yes" OR wp_postmeta="wp_helpful_post_ip"';
	require_once ABSPATH.'wp-admin/includes/upgrade.php';
	dbDelta($query);
}
?>