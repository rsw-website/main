var timer;
var enableTimer = true;
jQuery('form.woocommerce-form-login .woocommerce-Button')
.on('click', function(e){
	var usernameCheck = true;
	var passwordCheck = true;
	var form = jQuery(this).parents('form.woocommerce-form-login');
	if(form.find('#username').val().length === 0){
		usernameCheck = false;
		form.find('#username').css('border-color', 'red');
	} else{
		usernameCheck = true;
		form.find('#username').css('border-color', '#d6d6d6');
	} 
	if(form.find('#password').val().length === 0){
		passwordCheck = false;
		form.find('#password').css('border-color', 'red');
	} else{
		passwordCheck = true;
		form.find('#password').css('border-color', '#d6d6d6');
	}
	if(!usernameCheck || !passwordCheck){
		e.preventDefault();
	} 
});


jQuery('form.woocommerce-form-register .woocommerce-Button')
.on('click', function(e){
	var firstnameCheck = true;
	var lastnameCheck = true;
	var emailCheck = true;
	var passwordCheck = true;
	var form = jQuery(this).parents('form.woocommerce-form-register');
	if(form.find('#reg_billing_first_name').val().length === 0){
		firstnameCheck = false;
		form.find('#reg_billing_first_name').css('border-color', 'red');
	} else{
		firstnameCheck = true;
		form.find('#reg_billing_first_name').css('border-color', '#d6d6d6');
	} 
	if(form.find('#reg_billing_last_name').val().length === 0){
		lastnameCheck = false;
		form.find('#reg_billing_last_name').css('border-color', 'red');
	} else{
		lastnameCheck = true;
		form.find('#reg_billing_last_name').css('border-color', '#d6d6d6');
	}
	if(form.find('#reg_email').val().length === 0){
		emailCheck = false;
		form.find('#reg_email').css('border-color', 'red');
	} else{
		emailCheck = true;
		form.find('#reg_email').css('border-color', '#d6d6d6');
	}
	if(form.find('#reg_password').val().length === 0){
		passwordCheck = false;
		form.find('#reg_password').css('border-color', 'red');
	} else{
		passwordCheck = true;
		form.find('#reg_password').css('border-color', '#d6d6d6');
	}
	if(!firstnameCheck || 
		!lastnameCheck || 
		!emailCheck || 
		!passwordCheck){
		e.preventDefault();
	} 
});

jQuery('.tab-link').on('click', function(e){
	enableTimer = false;

});

jQuery('.home-tabs-arrow').on('click', function(e){
	var activeTabIndex = jQuery(this).siblings('div#home-tabs')
	.children('div.nav').find('ul.nav-tabs').find('li.active').index(); //2
	var currentActive = jQuery(this).siblings('div#home-tabs')
	.children('div.nav').find('ul.nav-tabs').find('li.active');
	var homeTabs = jQuery(this).siblings('div#home-tabs');
	var nextActiveItem = '';
	var clickedArrow = jQuery(this).attr('id');
	if(clickedArrow == 'tab-arrow-right'){
		var tabsLastIndex = jQuery(this).siblings('div#home-tabs')
		.children('div.nav').find('ul.nav-tabs').find('li')
		.last().index();
		if(tabsLastIndex === activeTabIndex){
		// last index active
		// move to index 0
		var nextActiveItem = jQuery(this).siblings('div#home-tabs')
		.children('div.nav').find('ul.nav-tabs').find('li').first();
		} else{
			// any index active
			// select the next index
			var nextActiveItem = jQuery(this).siblings('div#home-tabs')
			.children('div.nav').find('ul.nav-tabs').find('li.active').next();
		}
	} else{
		var tabsFirstIndex = jQuery(this).siblings('div#home-tabs')
		.children('div.nav').find('ul.nav-tabs').find('li')
		.first().index();
		if(tabsFirstIndex === activeTabIndex){
		// last index active
		// move to index 0
		var nextActiveItem = jQuery(this).siblings('div#home-tabs')
		.children('div.nav').find('ul.nav-tabs').find('li').last();
		} else{
			// any index active
			// select the next index
			var nextActiveItem = jQuery(this).siblings('div#home-tabs')
			.children('div.nav').find('ul.nav-tabs').find('li.active').prev();
		}
	} 
	changeActiveTab(homeTabs, nextActiveItem);
	changeTabWithTimer();
});


function changeActiveTab(homeTabs, nextActiveItem){
	jQuery(homeTabs).children('div.nav').find('ul.nav-tabs')
	.find('li').removeClass('active');
	jQuery(nextActiveItem).addClass('active');
	var newActiveItemId = jQuery(nextActiveItem).find('a.tab-link').attr('href');
	newActiveItemId = newActiveItemId.replace('#', '');
	jQuery(homeTabs).children('div.tab-content')
	.find('.tab-pane').removeClass('active').removeClass('in');
	jQuery(homeTabs).children('div.tab-content')
	.find('div#'+newActiveItemId).addClass('in').addClass('active', 500);
}

function changeTabAfterInterval() {
  	return setInterval(function(){
  		if(jQuery(window).width() < 786 || !enableTimer){
  			return ;
  		}
	  	var homeTabs = jQuery('body.home div#home-tabs'); 
	  	if(homeTabs.length){
		  	var tabsLastIndex = jQuery('div#home-tabs')
				.children('div.nav').find('ul.nav-tabs').find('li')
				.last().index();
			var activeTabIndex = jQuery('div#home-tabs')
			.children('div.nav').find('ul.nav-tabs').find('li.active').index();
			if(tabsLastIndex === activeTabIndex){
			// last index active
			// move to index 0
			var nextActiveItem = jQuery('div#home-tabs')
			.children('div.nav').find('ul.nav-tabs').find('li').first();
			} else{
				// any index active
				// select the next index
				var nextActiveItem = jQuery('div#home-tabs')
				.children('div.nav').find('ul.nav-tabs').find('li.active').next();
			}
			changeActiveTab(homeTabs, nextActiveItem);
	  	}
   }, 20000);
}

function changeTabWithTimer(){
	console.log(timer);
	if (timer) clearInterval(timer);
	timer = changeTabAfterInterval();
}
changeTabWithTimer();

jQuery('div#home-tabs > div.nav > ul.nav-tabs li').on('click', function(e){
	changeTabWithTimer();
});

jQuery('#home-features').find('img').removeAttr('title');

jQuery('form#dashboard-document-filter').on('submit', function(event){
	event.preventDefault();
	var path = jQuery(this).find('select.action-filter')
	.children("option:selected").attr('path');
	if(path !== undefined){
		window.location.href = path;
	}
});

jQuery('body.home').ready( function() {
	ActiveSelectedTab(window.location.hash.substr(1));
});

jQuery('body.home ul.product-feature-links > li > a').on('click', function(e) {
	debugger;
	var hashId = jQuery(this).attr('href').replace('/#', '');
	if(jQuery(window).width() < 800){
  			hashId = 'mobile-'+hashId;
  		}
	ActiveSelectedTab(hashId);
});

function ActiveSelectedTab(hashId){
	if(hashId.length){
		jQuery('a.tab-link#'+hashId).click();
	}

}