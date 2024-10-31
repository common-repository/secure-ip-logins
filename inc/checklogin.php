<?php

class SecureIPLoginsCheckLogin
{
    /**
     * Start up
     */
    public function __construct()
    {
		add_filter('wp_authenticate_user', array( $this, 'siplbks_login_auth' ),99,2);
	}

	public function siplbks_login_auth ($user, $password) {
		 //do any extra validation stuff here
		 // return $user;
		$this->options = get_option( 'siplbks_option' );
		$siplbks_enabled = isset($this->options['siplbks_enabled']) ? (int)$this->options['siplbks_enabled'] : 0;

		if($siplbks_enabled){
			$new_array = array();
			foreach ($this->options['siplbks_iplist_key'] as $key => $value) {
				if (trim($value) != "") {
					$new_array[$key] = $value;
				}
			}
			$siplbks_enabled = !empty($new_array) ? 1 : 0;
		}
		if($siplbks_enabled && !in_array($this->getIP(), $this->options['siplbks_iplist_key'])){
			return $user = new WP_Error('incorrect_password', __("ERROR: Your IP is blacklisted to log into admin panel."));
		}
		else{
			return $user;
		}
	}

	public function getIP()
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return apply_filters('wpb_get_ip', $ip);
    }

}