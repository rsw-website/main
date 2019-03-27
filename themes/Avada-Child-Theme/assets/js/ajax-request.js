jQuery(document).ready( function($) {
	$(".document-request-access").click( function() {
		$('.document-request-text').find('p.error-text').remove();
		var documentRequest = $(this);
		$(documentRequest).siblings('.custom-loader').removeClass('hidden');
		debugger;
		var data = {
			action: 'submit_acces_request',
			type: "POST",
			dataType: 'JSON',
		};
		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	$.post(custom_ajax_script.ajaxurl, data, function(response) {
	 		debugger;
	 		if(parseInt(response) === 1){ //success
	 			$('.document-request-text')
	 			.empty().html(
	 				'<p>Your request have been submitted succesfully. An email has been sent to admin for document request approval.</br>You will be notified about the status of your submitted requested.</p>'
	 				);
	 			// 
	 		} else{
	 			// show error text 
	 			$('.document-request-text').find('p.error-text').remove();
		 		$('.document-request-text')
		 			.append(
		 				'<p class="error-text">Unable to submit the request. Please try again!</p>'
				);
	 		}
	 		$(documentRequest).siblings('.custom-loader').addClass('hidden');
	 	});
	 	return false;
	});

	jQuery('a.toggle-bookmark').on('click', function(){
		debugger;
		var isBookmarked = 0;
		var documentId = jQuery(this).attr('document-id');
		var nonce = jQuery(this).attr('_nonce');
		var currentElement = jQuery(this);
		if(jQuery(this).hasClass('empty-star')){
			isBookmarked = 1; 
		} else{
			isBookmarked = 0;
		}
		var data = {
			action: 'document_bookmarked_request',
			dataType: 'JSON',
			document_id : documentId,
		    book_marked: isBookmarked,
		    nonce : nonce,
		};

		// the_ajax_script.ajaxurl is a variable that will
		 // contain the url to the ajax processing file

	 	$.post(custom_ajax_script.ajaxurl, data, function(response) {
	 		debugger;
	 		if(parseInt(response) === 1){ //success
	 			if(isBookmarked === 1){
	 				jQuery(currentElement).removeClass('empty-star').addClass('solid-star');
	 				jQuery(currentElement).attr('title', 'Marked as favourite');
	 			} else{
	 				jQuery(currentElement).removeClass('solid-star').addClass('empty-star');
	 				jQuery(currentElement).attr('title', 'Mark as favourite');
	 			}
	 		} else{
	 			alert('Something went wrong');
	 		}
	 	});
	});

});