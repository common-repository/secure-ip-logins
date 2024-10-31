<?php
class SecureIPLoginsAdminPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'siplbks_admin_page' ) );
	}


    /**
     * Add options page
     */
    public function siplbks_admin_page()
    {
        register_setting(
            'siplbks_option_group', // Option group
            'siplbks_option' // Option name
            // array( $this, 'sanitize' ) // Sanitize
        );
        add_settings_section(
            'siplbks_iplist', // ID
            'Whitelisted IPs List', // Title
            array( $this, 'siplbks_option_group_info' ), // Callback
            'siplbks' // Page
        );

        add_settings_field(
            'siplbks_iplist_key', // ID
            '', // Title
            array( $this, 'siplbks_iplist_callback' ), // Callback
            'secure-ip-logins', // Page
            'siplbks_iplist' // Section
        );
        add_settings_field(
            'siplbks_enabled', // ID
            '', // Title
            array( $this, 'siplbks_iplist_callback' ), // Callback
            'secure-ip-logins', // Page
            'siplbks_iplist' // Section
        );

        // This page will be under "Settings"
        add_menu_page(
            'Secure IP Logins',
            'Secure IP Logins',
            'manage_options',
            'secure-ip-logins',
            array( $this, 'create_admin_page' ),
			'dashicons-lock',
			2
        );

		wp_enqueue_style( 'siplbks_toggle_switch', plugin_dir_url( __FILE__ ) . 'css/toggle-switch.css', WP_SIPLBKS_VER );
		wp_enqueue_style( 'siplbks_style', plugin_dir_url( __FILE__ ) . 'css/style.css', WP_SIPLBKS_VER );
		wp_enqueue_script( 'siplbks_masked_input', plugin_dir_url( __FILE__ ) . 'js/jquery.maskedinput.min.js', 'jquery', WP_SIPLBKS_VER );
		wp_enqueue_script( 'siplbks_libs', plugin_dir_url( __FILE__ ) . 'js/libs.js', 'jquery', WP_SIPLBKS_VER );
		wp_enqueue_script( 'siplbks_script', plugin_dir_url( __FILE__ ) . 'js/main.js', 'jquery', WP_SIPLBKS_VER );
		wp_localize_script( 'siplbks_script', 'siplbks_js_var_obj', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( "siplbks_nonce" ),
			'ipListCount' => $this->siplbks_iplist_count() >= WP_SIPLBKS_ALLOWED_IPS ? WP_SIPLBKS_ALLOWED_IPS : $this->siplbks_iplist_count(),
			'allowedIPs' => WP_SIPLBKS_ALLOWED_IPS
		) );

    }
	
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'siplbks_option' );
        ?>
        <div class="wrap siplbks-wrap">
            <?php if( isset($_GET['settings-updated']) ) { ?>
			<div id="message" class="updated settings-error notice is-dismissible"> 
			<p><strong><?php _e('Settings saved.') ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>
			<?php } ?>
			<h1>
                Secure IP Logins <?php echo (siplbks()->is_plan('premium', true)) ? '' : '<small>'.WP_SIPLBKS_ALLOWED_IPS.' Whitelisted IPs allowed</small>'; ?>
                <?php
				if ( !siplbks()->is__premium_only() ) {
					if ( siplbks()->is_plan('starter', true) ) {
						echo '<small class="main-upgrade-msg">Free Version. <a href="' . siplbks()->get_upgrade_url() . '">' .
							__('Click to upgrade to Premium Version!', 'secure-ip-logins') .
							'</a></small>';
					}
					else if ( siplbks()->is_plan('free', true) ) {
						echo '<small class="main-upgrade-msg">Free Version. <a href="' . siplbks()->get_upgrade_url() . '">' .
							__('Click to upgrade!', 'secure-ip-logins') .
							'</a></small>';
					}
				}else{
					echo '';
				}
                ?>
            </h1>
            <form method="post" action="options.php">
				<div class="postbox">
					<h2 class="enable-widget">
						<span>Enable Secure IP Logins:&nbsp;&nbsp;<br/><small>Enable to start filtering whitelisted IPs on login. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small></span>
					</h2>
					<div class="switch-toggle switch-candy switch-siplbks">
						<input type="radio" id="keyword_analysis_active-on" checked="checked" value="1" name="siplbks_option[siplbks_enabled]" <?php echo ((isset($this->options['siplbks_enabled']) && $this->options['siplbks_enabled']) == 1 ? 'checked' : ''); ?> />
						<label for="keyword_analysis_active-on">ENABLE</label>
						<input type="radio" id="keyword_analysis_active-off" value="0" name="siplbks_option[siplbks_enabled]" <?php echo ((isset($this->options['siplbks_enabled']) && $this->options['siplbks_enabled']) == 0 ? 'checked' : ''); ?> />
						<label for="keyword_analysis_active-off">DISABLE</label>
						<a></a>
					</div>
					<div class="clearfix"></div>
				</div>
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'siplbks_option_group' );
                do_settings_sections( __FILE__ );
		$cou=0;
				?>
		<div class="iplist-container">
			<div class="postbox">
				<h3>Add your whitelisted IPs here:</h3>
				<table width="100%">
					<tr>
						<td>
							<div class="iplist-input-list">
							<?php

				do{
					?>
								<div class="ilist-div">
									<input pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" autocomplete="off" class="input siplbks_ip_input" id="siplbks_iplist_key<?php echo $cou; ?>.'" name="siplbks_option[siplbks_iplist_key][]" type="text" value="<?php echo $this->options['siplbks_iplist_key'][$cou]; ?>" placeholder="xxx.xxx.xxx.xxx" />
									<span class="remove-me"><i class="dashicons dashicons-trash"></i></span>
								</div>
						<?php
					$cou++;
				}while($cou < $this->siplbks_iplist_count());
		?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="add-more-btn-div">
								<button class="button button-primary add-more" type="button">Add More IPs</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<?php
			submit_button(__('Save Changes'), 'button button-primary button-hero');
			?>
		</div>
		<div class="remaining-container">
			<div class="inner-div postbox">
				<h2>Tips</h2>
				<p>To make the most of this plugin, following are some recommendations to take your wordpress security to the next level:</p>
				<ul>
					<li>Get a dedicated IP </li>
					<li>Add that IP here to your whitelist IPs list</li>
					<li>Enable Secure IP Logins</li>
					<li>Save changes</li>
				</ul>
				<p>Now you will be able to log in with that listed IP only. No other user/hacker will be able to log in to your wordpress admin panel from anywhere, except from your own dedicated IP.</p>
			</div>
		</div>
		<div class="clearfix" />
		<div>
			<br/><br/><br/>
			<p style="text-align:right">For help contact the developers at <a href="mailto:developers@ivacy.com"><strong><em>developers@ivacy.com</em></strong></a> or visit <a href="https://www.ivacy.com"><strong><em>www.ivacy.com</em></strong></a></p>
		</div>
	</form>
<?php add_thickbox(); ?>

			<div id="go-premium-popup" style="display:none;max-width:90%;">
				<div class="go-premium-popup-container Aligner">
					<div class="Aligner-item Aligner-item--fixed">
						<div class="text-center">
							<h3>Whitelist IP Limit Exceeds</h3>
							<p>You can only add <?php echo WP_SIPLBKS_ALLOWED_IPS; ?> with your current package.</p>
							<a href="<?php echo siplbks()->get_upgrade_url(); ?>" class="button button-primary button-hero">UPGRADE PLAN NOW!</a>
						</div>
					</div>
				</div>
			</div>


        </div>
        <?php
    }

    /** 
     * Print the Section text
     */
    public function siplbks_iplist_callback()
    {
        return '';
    }

    public function siplbks_iplist_count()
    {
		$cou = 0;
        $this->options = get_option( 'siplbks_option' );
		if(isset($this->options['siplbks_iplist_key']))
			$ipCount = count($this->options['siplbks_iplist_key']) == 0 ? 1 : count($this->options['siplbks_iplist_key']);
		else
			$ipCount = 1;
        return $ipCount;
    }

    /**
     * Print the Section text
     */
    public function siplbks_option_group_info()
    {

    }

}