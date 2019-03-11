jQuery(document).ready( function($) {
	$(".document-request-access").click( function() {
		$('.document-request-text').find('p.error-text').remove()
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
	 		if(response === 1){ //success
	 			$('.document-request-text')
	 			.empty().text(
	 				'<p>Your request have been submitted succesfully. An email has been sent to admin for document request approval.</br>You will be notified about the status of your submitted requested.</p>'
	 				);
	 			// 
	 		} else{
	 			// show error text 
		 		$('.document-request-text')
		 			.append(
		 				'<p class="error-text">Unable to submit the request. Please try again!</p>'
				);
	 		}
	 		$(documentRequest).siblings('.custom-loader').addClass('hidden');
	 	});
	 	return false;
	});
});