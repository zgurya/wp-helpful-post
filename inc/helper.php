<?php 
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
?>