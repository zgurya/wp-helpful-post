<?php 
add_shortcode( 'wp_helpful_post', 'wp_helpful_post_init' );
function wp_helpful_post_init(){
	global $post;
	$output='<div class="wp-helpful-post" data-postid="'.$post->ID.'"><div class="question">Статья была полезной?</div><div class="answers"><span class="yes" data-helpful="yes">Да</span><span class="no" data-helpful="no">Нет</span></div><div class="results"><span>'.get_wp_helpful_post_results().'</span> Пользователей считают статью полезной</div></div>';
	return $output;
}
?>