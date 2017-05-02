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
register_activation_hook(__FILE__, 'wp_helpful_post_activation');
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

function wp_helpful_post_activation(){
	if(!get_option('wp_helpful_post')) add_option('wp_helpful_post');
}

function wp_helpful_post_uninstall(){
	delete_option('wp_helpful_post');
}

add_shortcode( 'wp_helpful_post', 'wp_helpful_post_init' );
function wp_helpful_post_init(){
	global $post;
	$output='<div class="wp-helpful-post" data-postid="'.$post->ID.'"><div class="question">Статья была полезной?</div><div class="answers"><span class="yes" data-helpful="yes">Да</span><span class="no" data-helpful="no">Нет</span></div><div class="results"><span></span> Пользователей считают статью полезной</div></div>';
	return $output;
}

add_action( 'wp_ajax_set_wp_helpful_post', 'set_wp_helpful_post_ajax' );
add_action( 'wp_ajax_nopriv_set_wp_helpful_post','set_wp_helpful_post_ajax' );
function set_wp_helpful_post_ajax(){
	if(isset($_POST['answers']) && !empty($_POST['answers']) && isset($_POST['postid']) && !empty($_POST['postid'])){
		if($_POST['answers']=='yes'){
			if(get_post_meta( $_POST['postid'], 'post_rating_yes', true)){
				$yes=get_post_meta( $_POST['postid'], 'post_rating_yes', true)+1;
				update_post_meta($_POST['postid'], 'post_rating_yes',$yes);
			}else{
				update_post_meta($_POST['postid'], 'post_rating_yes',1);
			}
		}else{
			if(get_post_meta( $_POST['postid'], 'post_rating_no', true)){
				$no=get_post_meta( $_POST['postid'], 'post_rating_no', true)+1;
				update_post_meta($_POST['postid'], 'post_rating_no',$no);
			}else{
				update_post_meta($_POST['postid'], 'post_rating_no',1);
			}
		}
	}
	echo "Спасибо, что оценили эту статью";
	die();
}

add_action( 'wp_ajax_get_wp_helpful_post', 'get_wp_helpful_post_ajax' );
add_action( 'wp_ajax_nopriv_get_wp_helpful_post','get_wp_helpful_post_ajax' );
function get_wp_helpful_post_ajax(){
	if(isset($_POST['postid']) && !empty($_POST['postid'])){
		echo get_wp_helpful_post();
	}
	die();
}

function get_wp_helpful_post(){
	if(get_post_meta( $_POST['postid'], 'post_rating_yes', true)){
		$yes=get_post_meta( $_POST['postid'], 'post_rating_yes', true);
	}else{
		$yes=0;
	}
	if(get_post_meta( $_POST['postid'], 'post_rating_no', true)){
		$no=get_post_meta( $_POST['postid'], 'post_rating_no', true);
	}else{
		$no=0;
	}
	if($yes>0 && $no==0) return '100%';
	if(($no>0 && $yes==0) || $no>$yes) return '0%';
	if($yes>0 && $no>0 && $yes>=$no){
		return ceil(($yes/($yes+$no))*100).'%';
	}
}


