<?php
/*
Plugin Name: Secure IP Logins
Description: This Plugin allows you to access your WordPress sites only with the Whitelisted IP Addresses that you have allowed yourself.
Author: Ivacy
Version: 0.1
Author URI: https://www.ivacy.com
*/



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'siplbks' ) ) {

    include('inc/freemius.php');
    include('inc/constants.php');
	include('inc/admin_view.php');
	include('inc/checklogin.php');

    $siplbksConstants = new SecureIPLoginsConstants();
    if( is_admin() ){
        $siplbksAdminPage = new SecureIPLoginsAdminPage();
    }
    $siplbksCheckLogin = new SecureIPLoginsCheckLogin();
}
