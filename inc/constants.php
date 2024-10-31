<?php

class SecureIPLoginsConstants
{
    public function __construct()
    {
        define('WP_SIPLBKS_VER', '0.0.2');

		if ( siplbks()->is__premium_only() ) {
			if ( siplbks()->is_plan('premium', true) ) {
				// This IF will be executed only if the user in a trial mode or have a valid license.
				define('WP_SIPLBKS_ALLOWED_IPS', 100000);
			}
			else if ( siplbks()->is_plan('starter', true) ) {
				// This IF will be executed only if the user in a trial mode or have a valid license.
				define('WP_SIPLBKS_ALLOWED_IPS', 100);
			}
			else{
				define('WP_SIPLBKS_ALLOWED_IPS', 3);
			}
        }else{
            define('WP_SIPLBKS_ALLOWED_IPS', 3);
        }
    }
}