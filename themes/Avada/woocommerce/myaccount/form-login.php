<?php
if(is_page('client-login')){
    wc_get_template( 'myaccount/form-login-single.php' );
} elseif(is_page('client-registration')){
	?>
	<p class="registration">Send in your registration application today!<br /> NOTE: Your account will be held for moderation and you will be unable to login until it is approved.</p>
	<?php
	wc_get_template( 'myaccount/form-register-single.php' );
} else{
	wp_redirect('client-login');
}