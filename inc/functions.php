<?php 
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