<?php
/**
 * Plugin Name: WordPress Helpful Post
 * Plugin URI: https://github.com/zgurya/wp-helpful-post
 * Description: This plugin allows you to understand which post is most helpful.
 * Version: 0.1
 * Author: a.zgurya
 * Author URI: https://www.facebook.com/a.zgurya
 */

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

add_shortcode( 'wp_helpful_post', 'wp_helpful_post_init' );
function wp_helpful_post_init(){
	global $post;
	$output='<div class="wp-helpful-post" data-postid="'.$post->ID.'"><div class="question">Статья была полезной?</div><div class="answers"><span class="yes" data-helpful="yes">Да</span><span class="no" data-helpful="no">Нет</span></div><div class="results"><span>'.get_wp_helpful_post_results().'</span> Пользователей считают статью полезной</div></div>';
	return $output;
}

add_action( 'wp_ajax_set_wp_helpful_post', 'set_wp_helpful_post_ajax' );
add_action( 'wp_ajax_nopriv_set_wp_helpful_post','set_wp_helpful_post_ajax' );
function set_wp_helpful_post_ajax(){
	if(isset($_POST['answers']) && !empty($_POST['answers']) && isset($_POST['postid']) && !empty($_POST['postid'])){
		$set_wp_helpful_post=true;
		
		if(isset($_COOKIE['wp_helpful_post_ip']) && isset($_COOKIE['wp_helpful_post_key'])) {
			if(get_post_meta( $_POST['postid'], 'wp_helpful_post_ip', true)){
				$wp_helpful_post_ip=unserialize(get_post_meta( $_POST['postid'], 'wp_helpful_post_ip', true));
				if(is_array($wp_helpful_post_ip) && array_key_exists($_COOKIE['wp_helpful_post_key'],$wp_helpful_post_ip)){
					if($wp_helpful_post_ip[$_COOKIE['wp_helpful_post_key']]==$_COOKIE['wp_helpful_post_ip']){
						$set_wp_helpful_post=false;
					}
				}
			}
		}
		
		if($set_wp_helpful_post){
			if($_POST['answers']=='yes'){
				if(get_post_meta( $_POST['postid'], 'wp_helpful_post_yes', true)){
					$yes=get_post_meta( $_POST['postid'], 'wp_helpful_post_yes', true)+1;
					update_post_meta($_POST['postid'], 'wp_helpful_post_yes',$yes);
				}else{
					update_post_meta($_POST['postid'], 'wp_helpful_post_yes',1);
				}
			}else{
				if(get_post_meta( $_POST['postid'], 'wp_helpful_post_no', true)){
					$no=get_post_meta( $_POST['postid'], 'wp_helpful_post_no', true)+1;
					update_post_meta($_POST['postid'], 'wp_helpful_post_no',$no);
				}else{
					update_post_meta($_POST['postid'], 'wp_helpful_post_no',1);
				}
			}
			
			$random_key=get_wp_helpful_client_random();
			$user_ip=get_wp_helpful_client_ip();
			
			if(!empty($user_ip)){
				if(!get_post_meta( $_POST['postid'], 'wp_helpful_post_ip', true) || !is_array($wp_helpful_post_ip)){
					$wp_helpful_post_ip=array($random_key=>$user_ip);
					update_post_meta($_POST['postid'], 'wp_helpful_post_ip', serialize($wp_helpful_post_ip));
				}else{
					$wp_helpful_post_ip[$random_key]=$user_ip;
					update_post_meta($_POST['postid'], 'wp_helpful_post_ip', serialize($wp_helpful_post_ip));
				}
				setcookie("wp_helpful_post_key", $random_key, strtotime( '+30 days' ), apply_filters('wp_helpful_post_cookiepath', SITECOOKIEPATH));
				setcookie("wp_helpful_post_ip", $user_ip, strtotime( '+30 days' ), apply_filters('wp_helpful_post_cookiepath', SITECOOKIEPATH));
			}
			echo "Спасибо, что оценили эту статью";
		}else{
			echo "Вы уже оценили эту статью";
		}
		
	}
	
	die();
}

function get_wp_helpful_post_results(){
	global $post;
	if(isset($post)&&!empty($post)){
		if(get_post_meta( $post->ID, 'wp_helpful_post_yes', true)){
			$yes=get_post_meta( $post->ID, 'wp_helpful_post_yes', true);
		}else{
			$yes=0;
		}
		if(get_post_meta( $post->ID, 'wp_helpful_post_no', true)){
			$no=get_post_meta( $post->ID, 'wp_helpful_post_no', true);
		}else{
			$no=0;
		}
		if($yes>0 && $no==0) return '100%';
		if(($no>0 && $yes==0) || $no>$yes || ($yes==0 && $no==0)) return '0%';
		if($yes>0 && $no>0 && $yes>=$no){
			return ceil(($yes/($yes+$no))*100).'%';
		}
	}else{
		return '0%';
	}
}

function get_wp_helpful_client_ip() {
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if(isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = '';
	return $ipaddress;
}

function get_wp_helpful_client_random($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
?>