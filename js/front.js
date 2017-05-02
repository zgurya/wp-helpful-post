jQuery(document).ready(function($){
	if($('.wp-helpful-post .results span').length){
		$.ajax({
		    url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		    type: 'POST',
		    data:{
		      action: 'get_wp_helpful_post', // this is the function in your functions.php that will be triggered
		      postid: $('.wp-helpful-post').data('postid'),
		    },
		    success: function(response){
		    	$('.wp-helpful-post .results span').text(response);
		    }
		});
	}
	
	
	
	$( ".wp-helpful-post" ).on( "click", "span", function() {
		 $.ajax({
			    url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
			    type: 'POST',
			    data:{
			      action: 'set_wp_helpful_post', // this is the function in your functions.php that will be triggered
			      answers: $(this).data('helpful'),
			      postid: $('.wp-helpful-post').data('postid'),
			    },
			    success: function(response){
			    	$('.wp-helpful-post').html(response	).addClass('congrats');
			    }
		 });
	});
});
