<?php
if (!function_exists('siplbks')) {
    // Create a helper function for easy SDK access.
    function siplbks()
    {
        global $siplbks;

        if ( ! isset( $siplbks ) ) {
            // Activate multisite network integration.
            if ( ! defined( 'WP_FS__PRODUCT_3277_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_3277_MULTISITE', true );
            }

            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $siplbks = fs_dynamic_init( array(
                'id'                  => '3277',
                'slug'                => 'secure-ip-logins',
                'type'                => 'plugin',
                'public_key'          => 'pk_4944c824839358048d680550743da',
                'is_premium'          => true,
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'secure-ip-logins',
                    'support'        => false,
                ),
            ) );
        }

        return $siplbks;
    }

    // Init Freemius.
    siplbks();
    // Signal that SDK was initiated.
    do_action('siplbks_loaded');
}
