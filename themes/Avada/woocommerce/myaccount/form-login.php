<?php
if(is_page('client-login')){
    wc_get_template( 'myaccount/form-login-single.php' );
} elseif(is_page('client-registration')){
	wc_get_template( 'myaccount/form-register-single.php' );
} else{
	die("redirect client login");
	wp_redirect('client-login');
}