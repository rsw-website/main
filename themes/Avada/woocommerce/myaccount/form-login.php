<?php
if(is_page('login')){
    wc_get_template( 'myaccount/form-login-single.php' );
} elseif(is_page('register')){
	wc_get_template( 'myaccount/form-register-single.php' );
} else{
	wp_redirect('login');
}


if( isset($_GET['action']) == 'register' ) {
	// die("register");	
    wc_get_template( 'myaccount/form-register-single.php' );
} else {
}