(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	function showConfirmBox(){
		var choice = confirm("You are about to permanently delete these items from your site.\n This action cannot be undone.\n 'Cancel' to stop, 'OK' to delete.");
	    if(choice === true) {
	        return true;
	    } else{
	      	event.preventDefault();
	      	return false;
	    }
	    return false;
	}

	jQuery(document).ready(function(){
  		var searchButton = false;
		jQuery(document).on('submit', '#document-filter, #tag-filter', function(event){
			if(!searchButton){
	        	var topSelect = jQuery(this).find('select#bulk-action-selector-top')
		        .find('option:selected').val();
		        var bottomSelect = jQuery(this).find('select#bulk-action-selector-bottom')
		        .find('option:selected').val();
		        if(topSelect === 'bulk-delete' || bottomSelect === 'bulk-delete'){
		          showConfirmBox();
		        } else{
		          event.preventDefault();
		        }
	      	}
	  	});
	  	
	  	jQuery('#search-submit').on('click', function(event){
			searchButton = true;
	  	});
	  	
	  	jQuery('a.tag-list').bind('click', function(event) {
	  		debugger;
		    var tagsList = jQuery('#tag-id-list').val();
		    if(!tagsList.length){
		    	tagsList = [];
		    } else{
		    	tagsList = JSON.parse(tagsList);
		    }
		    event.preventDefault();
		    var tagId = jQuery(this).attr('tag-id');
		    if ( jQuery(this).hasClass('selected-tag') ) {
		    	jQuery(this).removeClass('selected-tag');
		      	var index = tagsList.indexOf(tagId);
	      		if (index > -1) {
	         		tagsList.splice(index, 1);
	      		}
	    	} else {
	      		jQuery(this).addClass('selected-tag');
	      		tagsList.push(tagId);
	    	} 
		    jQuery('#tag-id-list').val(JSON.stringify(tagsList));
	  	});

	  	jQuery('a.role-name').bind('click', function(event) {
	  		debugger;
    		var rolesList = jQuery('#role-names').val();
		    if(!rolesList.length){
		    	rolesList = {};
		    } else{
		      	rolesList = JSON.parse(rolesList);
		    }
    		event.preventDefault();
		    var roleName = jQuery(this).attr('role-slug');
		    if ( jQuery(this).hasClass('selected-tag') ) {
		    	jQuery(this).removeClass('selected-tag');
		      	// delete rolesList;
		      	rolesList[roleName] = 0;
		    } else {
		      	jQuery(this).addClass('selected-tag');
		      	rolesList[roleName] = 1;
		    } 
    		jQuery('#role-names').val(JSON.stringify(rolesList));
  		});
  	});


})( jQuery );
