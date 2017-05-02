jQuery(document).ready(function($){
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
