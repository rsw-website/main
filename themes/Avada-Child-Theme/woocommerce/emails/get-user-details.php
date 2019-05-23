<?php
	$current_user = get_user_by('login', $user_login);
	$first_name = $current_user->first_name;
	$current_email = $current_user->user_email;
?>