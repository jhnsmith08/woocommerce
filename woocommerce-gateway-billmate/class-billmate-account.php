<?php

class WC_Gateway_Billmate_Partpayment extends WC_Gateway_Billmate {
	
	/**
     * Class for Billmate Part Payment payment.
     *
     */
     
	public function __construct() {
		global $woocommerce;
		
		parent::__construct();
		
		$this->id			= 'billmate_partpayment';
		$this->method_title = __('Billmate Part Payment', 'billmate');
		$this->has_fields 	= true;
		
		
		// Billmate warning banner - used for NL only
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Load shortcodes. 
		// This is used so that the merchant easily can modify the displayed monthly cost text (on single product and shop page) via the settings page.
		require_once('shortcodes.php');
		
		

		// Define user set variables
		$this->enabled							= ( isset( $this->settings['enabled'] ) ) ? $this->settings['enabled'] : '';
		$this->title 							= ( isset( $this->settings['title'] ) ) ? $this->settings['title'] : '';
		$this->description  					= ( isset( $this->settings['description'] ) ) ? $this->settings['description'] : '';
		$this->eid								= ( isset( $this->settings['eid'] ) ) ? $this->settings['eid'] : '';
		$this->secret							= ( isset( $this->settings['secret'] ) ) ? $this->settings['secret'] : '';
		$this->lower_threshold					= ( isset( $this->settings['lower_threshold'] ) ) ? $this->settings['lower_threshold'] : '';
		$this->upper_threshold					= ( isset( $this->settings['upper_threshold'] ) ) ? $this->settings['upper_threshold'] : '';
		$this->show_monthly_cost				= ( isset( $this->settings['show_monthly_cost'] ) ) ? $this->settings['show_monthly_cost'] : '';
		$this->show_monthly_cost_info			= ( isset( $this->settings['show_monthly_cost_info'] ) ) ? $this->settings['show_monthly_cost_info'] : '';
		$this->show_monthly_cost_prio			= ( isset( $this->settings['show_monthly_cost_prio'] ) ) ? $this->settings['show_monthly_cost_prio'] : '15';
		$this->show_monthly_cost_shop			= ( isset( $this->settings['show_monthly_cost_shop'] ) ) ? $this->settings['show_monthly_cost_shop'] : '';
		$this->show_monthly_cost_shop_info		= ( isset( $this->settings['show_monthly_cost_shop_info'] ) ) ? $this->settings['show_monthly_cost_shop_info'] : '';
		$this->show_monthly_cost_shop_prio		= ( isset( $this->settings['show_monthly_cost_shop_prio'] ) ) ? $this->settings['show_monthly_cost_shop_prio'] : '15';
		$this->testmode							= ( isset( $this->settings['testmode'] ) ) ? $this->settings['testmode'] : '';
		$this->de_consent_terms					= ( isset( $this->settings['de_consent_terms'] ) ) ? $this->settings['de_consent_terms'] : '';
		$this->lower_threshold_monthly_cost		= ( isset( $this->settings['lower_threshold_monthly_cost'] ) ) ? $this->settings['lower_threshold_monthly_cost'] : '';
		$this->upper_threshold_monthly_cost		= ( isset( $this->settings['upper_threshold_monthly_cost'] ) ) ? $this->settings['upper_threshold_monthly_cost'] : '';
		$this->allowed_countries		= ( isset( $this->settings['billmateaccount_allowed_countries'] ) ) ? $this->settings['billmateaccount_allowed_countries'] : '';
		$this->shop_country				= strlen($this->shop_country) ? $this->shop_country: 'SE';
		
		if ($this->lower_threshold_monthly_cost == '') $this->lower_threshold_monthly_cost = 0;
		if ($this->upper_threshold_monthly_cost == '') $this->upper_threshold_monthly_cost = 10000000;
		
		
		// Country and language
		$countrytmp = $this->shop_country;
		
		switch ( $countrytmp )
		{
		case 'DK':
			$billmate_country = 'DK';
			$billmate_language = 'DA';
			$billmate_currency = 'DKK';
			$billmate_partpayment_info = 'https://online.billmate.com/account_dk.yaws?eid=' . $this->eid;
			break;
		case 'DE' :
			$billmate_country = 'DE';
			$billmate_language = 'DE';
			$billmate_currency = 'EUR';
			$billmate_partpayment_info = 'https://online.billmate.com/account_de.yaws?eid=' . $this->eid;
			break;
		case 'NL' :
			$billmate_country = 'NL';
			$billmate_language = 'NL';
			$billmate_currency = 'EUR';
			$billmate_partpayment_info = 'https://online.billmate.com/account_nl.yaws?eid=' . $this->eid;
			$billmate_partpayment_icon = 'https://cdn.billmate.com/public/images/NL/badges/v1/account/NL_account_badge_std_blue.png?width=60&eid=' . $this->eid;
			$billmate_basic_icon = 'https://cdn.billmate.com/public/images/NL/logos/v1/basic/NL_basic_logo_std_blue-black.png?width=60&eid=' . $this->eid;
			break;
		case 'NO' :
			$billmate_country = 'NO';
			$billmate_language = 'NB';
			$billmate_currency = 'NOK';
			$billmate_partpayment_info = 'https://online.billmate.com/account_no.yaws?eid=' . $this->eid;
			break;
		case 'FI' :
			$billmate_country = 'FI';
			$billmate_language = 'FI';
			$billmate_currency = 'EUR';
			$billmate_partpayment_info = 'https://online.billmate.com/account_fi.yaws?eid=' . $this->eid;
			break;
		case 'SE' :
			$billmate_country = 'SE';
			$billmate_language = 'SV';
			$billmate_currency = 'SEK';
			$billmate_partpayment_info = 'https://online.billmate.com/account_se.yaws?eid=' . $this->eid;
			break;
		default:
			$billmate_country = '';
			$billmate_language = '';
			$billmate_currency = '';
			$billmate_partpayment_info = '';
		}
		
		
		
		$billmate_partpayment_icon = plugins_url( '/images/bm_delbetalning_l.png', __FILE__ );
		$billmate_basic_icon = plugins_url( '/images/bm_delbetalning_l.png', __FILE__ );

		// Apply filters to Country and language
		$this->billmate_country 					= apply_filters( 'billmate_country', $billmate_country );
		$this->billmate_language 					= apply_filters( 'billmate_language', $billmate_language );
		$this->billmate_currency 					= apply_filters( 'billmate_currency', $billmate_currency );
		$this->billmate_partpayment_info 			= apply_filters( 'billmate_partpayment_info', $billmate_partpayment_info );
		$this->icon 							    = apply_filters( 'billmate_partpayment_icon', $billmate_partpayment_icon );
		$this->icon_basic						    = apply_filters( 'billmate_basic_icon', $billmate_basic_icon );
		
		
				
		// Actions
		
		/* 1.6.6 */
		add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
 
		/* 2.0.0 */
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action('woocommerce_receipt_billmate_partpayment', array(&$this, 'receipt_page'));
		
		add_action('admin_init', array(&$this, 'update_billmatepclasses_from_billmate'));
		
		add_action('woocommerce_single_product_summary', array(&$this, 'print_product_monthly_cost'), $this->show_monthly_cost_prio);
		
		add_action('woocommerce_checkout_process', array(&$this, 'billmate_partpayment_checkout_field_process'));
		
		add_action('wp_footer', array(&$this, 'billmate_partpayment_terms_js'));
		
	}
	
		
	/**
	 * Initialise Gateway Settings Form Fields
	 */
	function init_form_fields() {
		global $woocommerce;
		
		$available = array(
			'SE' =>__( 'Sweden','woocommerce'),
			'FI' =>__( 'Finland', 'woocommerce'),
			'DK' =>__( 'Danmark', 'woocommerce'),
			'NO' =>__( 'Norway' ,'woocommerce')
		);
		
	   	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'billmate' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Billmate Part Payment', 'billmate' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'billmate' ), 
							'default' => __( 'Billmate - Part Payment', 'billmate' )
						),
			'description' => array(
							'title' => __( 'Description', 'billmate' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during checkout.', 'billmate' ), 
							'default' => ''
						), 
			'eid' => array(
							'title' => __( 'Eid', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Billmate Eid; this is needed in order to take payment!', 'billmate' ), 
							'default' => ''
						),
			'secret' => array(
							'title' => __( 'Shared Secret', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Billmate Shared Secret; this is needed in order to take payment!', 'billmate' ), 
							'default' => ''
						),
			'lower_threshold' => array(
							'title' => __( 'Lower threshold', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'Disable Billmate Part Payment if Cart Total is higher than the specified value. Leave blank to disable this feature.', 'billmate' ), 
							'default' => ''
						),
			'upper_threshold' => array(
							'title' => __( 'Upper threshold', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'Disable Billmate Part Payment if Cart Total is higher than the specified value. Leave blank to disable this feature.', 'billmate' ), 
							'default' => ''
						),
			'show_monthly_cost' => array(
							'title' => __( 'Display monthly cost - product page', 'billmate' ), 
							'type' => 'checkbox',
							'label' => __( 'Display monthly cost on single products page.', 'billmate' ), 
							'default' => 'yes'
						),
			'show_monthly_cost_info' => array(
							'title' => __( 'Text for Monthly cost - product page', 'billmate' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the Monthly cost text displayed on the single product page. You can use the following shortcodes: [billmate_img] [billmate_price] [billmate_currency] & [billmate_partpayment_info_link].', 'billmate' ), 
							'default' => __('[billmate_img]<br/>Part pay from [billmate_price] [billmate_currency]/month.<br/>[billmate_partpayment_info_link]', 'billmate')
						),
			'show_monthly_cost_prio' => array(
								'title' => __( 'Placement of monthly cost - product page', 'billmate' ), 
								'type' => 'select',
								'options' => array('4'=>__( 'Above Title', 'billmate' ), '7'=>__( 'Between Title and Price', 'billmate'), '15'=>__( 'Between Price and Excerpt', 'billmate'), '25'=>__( 'Between Excerpt and Add to cart-button', 'billmate'), '35'=>__( 'Between Add to cart-button and Product meta', 'billmate'), '45'=>__( 'Between Product meta and Product sharing-buttons', 'billmate'), '55'=>__( 'After Product sharing-buttons', 'billmate' )),
								'description' => __( 'Select where on the products page the Monthly cost information should be displayed.', 'billmate' ), 
								'default' => '15'
							),
			'show_monthly_cost_shop' => array(
							'title' => __( 'Display monthly cost - shop page', 'billmate' ), 
							'type' => 'checkbox',
							'label' => __( 'Display monthly cost next to each product on shop page.', 'billmate' ), 
							'default' => 'no'
						),
			'show_monthly_cost_shop_info' => array(
							'title' => __( 'Text for Monthly cost - shop page', 'billmate' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the text displayed next to each product on shop page. You can use the following shortcodes: [billmate_img] [billmate_price] [billmate_currency] & [billmate_partpayment_info_link].', 'billmate' ), 
							'default' => __('From [billmate_price] [billmate_currency]/month', 'billmate')
						),
			'show_monthly_cost_shop_prio' => array(
								'title' => __( 'Placement of monthly cost - shop page', 'billmate' ), 
								'type' => 'select',
								'options' => array('0'=>__( 'Above Add to cart button', 'billmate' ), '15'=>__( 'Below Add to cart button', 'billmate')),
								'description' => __( 'Select where on the shop page the Monthly cost information should be displayed.', 'billmate' ), 
								'default' => '15'
							),
			'lower_threshold_monthly_cost' => array(
							'title' => __( 'Lower threshold for monthly cost', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'Disable the monthly cost feature if <i>Product price</i> is higher than the specified value. Leave blank to disable.', 'billmate' ), 
							'default' => ''
						),
			'upper_threshold_monthly_cost' => array(
							'title' => __( 'Upper threshold for monthly cost', 'billmate' ), 
							'type' => 'text', 
							'description' => __( 'Disable the monthly cost feature if <i>Product price</i> is higher than the specified value. Leave blank to disable.', 'billmate' ), 
							'default' => ''
						),
			'billmateaccount_allowed_countries' => array(
				'title' 		=> __( 'Allowed Countries', 'woocommerce' ),
				'type' 			=> 'multiselect',
				'description' 	=> __( 'Billmate Partpayment activated for customers in these countries', 'billmate' ),
				'class'			=> 'chosen_select',
				'css' 			=> 'min-width:350px;',
				'options'		=> $available
			),

			'testmode' => array(
							'title' => __( 'Testläge', 'billmate' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Billmate Test Mode.', 'billmate' ), 
							'default' => 'no'
						)
		);
	    
	} // End init_form_fields()
	
	
	/**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {

    	?>
    	<h3><?php _e('Billmate Part Payment', 'billmate'); ?></h3>
	    	<p><?php _e('With Billmate your customers can pay by partpayment. Billmate works by adding extra personal information fields and then sending the details to Billmate for verification.', 'billmate'); ?></p>
	    	
		    
		    <?php
		    // Check if the billmatepclasses.json file exist
		    $billmate_filename = BILLMATE_DIR . 'srv/billmatepclasses.json';
		    $billmate_filename_path = BILLMATE_DIR . 'srv/';

			if (is_writable($billmate_filename)) {
				
    			echo '<p>';
    			echo sprintf(__('The file billmatepclasses.json does exist on your web server. You can update the file by clicking the button below or create the file manually and upload it to <i>%s</i>. Note that read and write permissions for the directory <i>srv</i> and the containing file <i>billmatepclasses.json</i> must be set to 777 in order to fetch the available PClasses from Billmate. This does not apply if you manually upload your billmatepclasses.json file via ftp.', 'billmate'),$billmate_filename_path);
				echo '</p>';

			} else {
				echo '<div class="error inline"><b>';
    			echo sprintf(__('Filen billmatepclasses.json existerar ej på din webbserver. Denna behövs för att du ska kunna spara dina Billmate PClasses. Skapa filen genom att klicka på knappen nedanför eller ladda upp en tidigare skapad fil manuellt till <i>%s</i>. Observera att skriv- och läsrättigheterna för mappen <i>srv</i> och den innehållande filen <i>billmatepclasses.json</i> måste sättas till 777 för att kunna hämta de tillgängliga PClasses från Billmate. Detta gäller ej om du laddar upp filen manuellt via ftp.', 'billmate'),$billmate_filename_path);
    			echo '</b></div>';
			}
			if(is_readable($billmate_filename)){
				$content = file_get_contents($billmate_filename);
				if( strlen( $content ) > 0 ){
					$data = (array)json_decode( $content );
					$data = current($data);
					$fields = array_keys((array)$data[0]);
					?>
					<table border="0" style="border:1px solid #000">
						<tr>
						<?php foreach($fields as $field ): ?>
							<th><?php echo ucfirst($field )?></th>
						<?php endforeach; ?>
						</tr>
						<?php foreach( $data as $terms ):?>
						<tr>
							<?php $term = (array)$terms;
							if( empty($term['description'])) continue;
							
							foreach($term as $key => $col ): ?>
								<td><?php echo $key == 'expire'? date('Y-m-d', $col) :  $col ?></td>
							<?php endforeach; ?>
						</tr>
						<?php endforeach;?>
					</table>
					<?php
				}
			}
			if (isset($_GET['billmate_error_status']) && $_GET['billmate_error_status'] == '0') {
				// billmatepclasses.json file saved sucessfully
				echo '<div class="updated">'.__('Filen billmatepclasses.json har uppdaterats.','billmate').'</div>';
			}
			
			if (isset($_GET['billmate_error_status']) && $_GET['billmate_error_status'] == '1') {
				// billmatepclasses.json file could not be updated
				echo '<div class="error">'.__('Filen billmatepclasses.json kunde inte uppdateras. Billmate felmeddelande','billmate').': ' . $_GET['billmate_error_code'] . '</div>';
			}
			?>
			<p>
		    <a class="button" href="<?php echo admin_url('admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Gateway_Billmate_Partpayment&billmatePclassListener=1');?>"><?php _e('Uppdatera PClass filen', 'billmate'); ?> billmatepclasses.json</a>
		    
		    </p>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
		
    	<?php
    } // End admin_options()
	
	
	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
		
	function is_available() {
		global $woocommerce;

		if ($this->enabled=="yes") :
			if(!in_array($woocommerce->customer->get_country() , $this->allowed_countries)){
				return false;
			}
			
			// PClass check
			
			// Check if the billmatepclasses.json file exist
		    $billmate_filename = BILLMATE_DIR . 'srv/billmatepclasses.json';
			
			if (file_exists($billmate_filename)) {
		  	
		  		require_once(BILLMATE_LIB . 'Billmate.php');
				require_once(BILLMATE_LIB . 'billmatepclasses/storage.intf.php');
				require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
				require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
				
				// Test mode or Live mode		
				if ( $this->testmode == 'yes' ):
					// Disable SSL if in testmode
					$billmate_ssl = 'false';
					$billmate_mode = Billmate::BETA;
				else :
					// Set SSL if used in webshop
					if (is_ssl()) {
						$billmate_ssl = 'true';
					} else {
						$billmate_ssl = 'false';
					}
					$billmate_mode = Billmate::LIVE;
				endif;
		   		if( empty( $this->settings['eid']) ){
		   		    return false;
		   		}
	  			$k = new Billmate();
				
				$k->config(
				    $eid = $this->settings['eid'],
				    $secret = $this->settings['secret'],
				    $country = $this->billmate_country,
				    $language = $this->billmate_language,
				    $currency = $this->billmate_currency,
				    $mode = $billmate_mode,
				    $pcStorage = 'json',
				    $pcURI = BILLMATE_DIR . 'srv/billmatepclasses.json',
				    $ssl = $billmate_ssl,
				    $candice = true
				);
		
				Billmate::$xmlrpcDebug = false;
				Billmate::$debug = false;

				$billmate_cart_total = $woocommerce->cart->total;
				$sum = apply_filters( 'billmate_cart_total', $billmate_cart_total ); // Cart total.
				
				$pclasses_not_available = true;
				foreach ($k->getPClasses() as $pclass) {
					if (strlen($pclass->getDescription()) > 0 && ( $pclass->getType() == 0 || $pclass->getType() == 1 )) {
						if($sum >= $pclass->getMinAmount()) {
							$pclasses_not_available = false;
							break;
						}
					}
				}
				if( $pclasses_not_available ){
					return false;
				}
			} else {
				
				// billmatepclasses.json does not exist
				return false;
				
			} // End file_exists
			
			// Required fields check
			if (!$this->eid || !$this->secret) return false;
			
			// Cart totals check - Lower threshold
			if ( $this->lower_threshold !== '' ) {
				if ( $woocommerce->cart->total < $this->lower_threshold ) return false;
			}
			
			// Cart totals check - Upper threshold
			if ( $this->upper_threshold !== '' ) {
				if ( $woocommerce->cart->total > $this->upper_threshold ) return false;
			}
			
			
			// Only activate the payment gateway if the customers country is the same as the filtered shop country ($this->billmate_country)
	   		//if ( $woocommerce->customer->get_country() == true && $woocommerce->customer->get_country() != $this->billmate_country ) return false;
			
			return true;
					
		endif;	
	
		return false;
	}
	

		/**
 		* Retrieve the PClasses from Billmate and store it in the file billmatepclasses.json.
 		*/
 		function update_billmatepclasses_from_billmate( ) {
 		
 		global $woocommerce;

 		if (isset($_GET['billmatePclassListener']) && $_GET['billmatePclassListener'] == '1'):
 		
 			// Get PClasses so that the customer can chose between different payment plans.
	  		require_once(BILLMATE_LIB . 'Billmate.php');
			require_once(BILLMATE_LIB . 'billmatepclasses/storage.intf.php');
			require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
			require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
			
			// Test mode or Live mode		
			if ( $this->testmode == 'yes' ):
				// Disable SSL if in testmode
				$billmate_ssl = 'false';
				$billmate_mode = Billmate::BETA;
			else :
				// Set SSL if used in webshop
				if (is_ssl()) {
					$billmate_ssl = 'true';
				} else {
					$billmate_ssl = 'false';
				}
				$billmate_mode = Billmate::LIVE;
			endif;
		   		if( empty( $this->settings['eid']) ){
		   		    return false;
		   		}	   		
  			$k = new Billmate();
			unlink(BILLMATE_DIR . 'srv/billmatepclasses.json');
			$k->config(
			    $eid = $this->settings['eid'],
			    $secret = $this->settings['secret'],
			    $country = $this->billmate_country,
			    $language = $this->billmate_language,
			    $currency = $this->billmate_currency,
			    $mode = $billmate_mode,
			    $pcStorage = 'json',
			    $pcURI = BILLMATE_DIR . 'srv/billmatepclasses.json',
			    $ssl = $billmate_ssl,
			    $candice = true
			);
	
			Billmate::$xmlrpcDebug = false;
			Billmate::$debug = false;
	   
			// Check if the billmatepclasses.json file exist
		    $billmate_pclass_file = BILLMATE_DIR . 'srv/billmatepclasses.json';

			if (!file_exists($billmate_pclass_file)) {
    			$file=fopen($billmate_pclass_file,"w") or exit(__("Kunde inte skapa/uppdatera filen!",'billmate'));
    			fclose($file);
    			/*
    			if (is_writable ( $billmate_filename )) {
    				echo __("Writable.", 'billmate');
    			} else {
	    			echo __("NOT Writable.", 'billmate');
    			}
    			*/
    		}else{
				@chmod(dirname($billmate_pclass_file), 0777);
				@chmod($billmate_pclass_file, 0777);
			}
			
							
			try {
			    $k->fetchPClasses($this->billmate_country); //You can specify country (and language, currency if you wish) if you don't want to use the configured country.
			    /* PClasses successfully fetched, now you can use getPClasses() to load them locally or getPClass to load a specific PClass locally. */
				// Redirect to settings page
				wp_redirect(admin_url('admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Gateway_Billmate_Partpayment&billmate_error_status=0'));
				}
				catch(Exception $e) {
				    //Something went wrong, print the message:
				    // $woocommerce->add_error( sprintf(__('Billmate PClass problem: %s. Error code: ', 'billmate'), utf8_encode($e->getMessage()) ) . '"' . $e->getCode() . '"' );
				    //$billmate_error_code = utf8_encode($e->getMessage()) . 'Error code: ' . $e->getCode();
				    
				    $redirect_url = 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Gateway_Billmate_Partpayment&billmate_error_status=1&billmate_error_code=' . $e->getCode();
				    
				    //wp_redirect(admin_url($redirect_url));
				    wp_redirect(admin_url($redirect_url));
				}
				
			endif;
				
			}

	
	
	
	/**
	 * Payment form on checkout page
	 */
	
	function payment_fields( ) {
	   	global $woocommerce;
	   	
	   	// Get PClasses so that the customer can chose between different payment plans.
	  	require_once(BILLMATE_LIB . 'Billmate.php');
		require_once(BILLMATE_LIB . 'billmatepclasses/storage.intf.php');
		require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
		require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
		$enabled_plcass = 'no';
		// Test mode or Live mode		
		if ( $this->testmode == 'yes' ):
			// Disable SSL if in testmode
			$billmate_ssl = 'false';
			$billmate_mode = Billmate::BETA;
		else :
			// Set SSL if used in webshop
			if (is_ssl()) {
				$billmate_ssl = 'true';
			} else {
				$billmate_ssl = 'false';
			}
			$billmate_mode = Billmate::LIVE;
		endif;
	   	
   		if( empty( $this->settings['eid']) ){
   		    return false;
   		}
  		$k = new Billmate();
		
		$k->config(
		    $eid = $this->settings['eid'],
		    $secret = $this->settings['secret'],
		    $country = $this->billmate_country,
		    $language = $this->billmate_language,
		    $currency = $this->billmate_currency,
		    $mode = $billmate_mode,
		    $pcStorage = 'json',
		    $pcURI = BILLMATE_DIR . 'srv/billmatepclasses.json',
		    $ssl = $billmate_ssl,
		    $candice = true
		);

		Billmate::$xmlrpcDebug = false;
		Billmate::$debug = false;
		
		// apply_filters to cart total so we can filter this if needed
		$billmate_cart_total = $woocommerce->cart->total;
		$sum = apply_filters( 'billmate_cart_total', $billmate_cart_total ); // Cart total.
		$flag = BillmateFlags::CHECKOUT_PAGE; //or BillmateFlags::PRODUCT_PAGE, if you want to do it for one item.
	   	
	   	?>
	   	
	   	<?php if ($this->testmode=='yes') : ?><p><?php _e('TEST MODE ENABLED', 'billmate'); ?></p><?php endif; ?>
		<?php
		
		// Description
		if ($this->description) :
			// apply_filters to the description so we can filter this if needed
			$billmate_description = $this->description;
			echo '<p>' . apply_filters( 'billmate_partpayment_description', $billmate_description ) . '</p>';
		endif; 
		
		// Show billmate_warning_banner if NL
		?>
		
		<fieldset>
			<p class="form-row form-row-first">
			
				<?php
				// Check if we have any PClasses
				// TODO Deactivate this gateway if the file billmatepclasses.json doesn't exist 
				if($k->getPClasses()) {
				?>
					<label for="billmate_partpayment_pclass"><?php echo __("Payment plan", 'billmate') ?> <span class="required">*</span></label><br/>
					<select style="width:auto" id="billmate_partpayment_pclass" name="billmate_partpayment_pclass" class="woocommerce-select">
						
					<?php
				   	// Loop through the available PClasses stored in the file srv/billmatepclasses.json
					foreach ($k->getPClasses() as $pclass) {
						
						if (strlen($pclass->getDescription()) > 0 && ( $pclass->getType() == 0 || $pclass->getType() == 1 )) {
						
							// Get monthly cost for current pclass
							$monthly_cost = BillmateCalc::calc_monthly_cost(
    	    									$sum,
    	    									$pclass,
    	    									$flag
    										);
    										
    						// Get total credit purchase cost for current pclass
    						// Only required in Norway
							$total_credit_purchase_cost = BillmateCalc::total_credit_purchase_cost(
    	    									$sum,
    	    									$pclass,
    	    									$flag
    										);
    						
    						// Check that Cart total is larger than min amount for current PClass				
			   				if($sum >= $pclass->getMinAmount()) {
								$enabled_plcass = 'yes';
			   					echo '<option value="' . $pclass->getId() . '">';
			   					if ($this->billmate_country == 'NO') {
									if ( $pclass->getType() == 1 ) {
										//If Account - Do not show startfee. This is always 0.
										echo sprintf(__('%s - %s %s/month', 'billmate'), $pclass->getDescription(), $monthly_cost, $this->billmate_currency);
										} else {
											// Norway - Show total cost
											echo sprintf(__('%s - %s %s/month - %s%s - Start %s - Tot %s %s', 'billmate'), $pclass->getDescription(), $monthly_cost, $this->billmate_currency, $pclass->getInterestRate(), '%', $pclass->getStartFee(), $total_credit_purchase_cost, $this->billmate_currency );
										}
									} else {
										if ( $pclass->getType() == 1 ) {
											//If Account - Do not show startfee. This is always 0.
											echo sprintf(__('%s - %s %s/month', 'billmate'), $pclass->getDescription(), $monthly_cost, $this->billmate_currency );
										} else {
											// Sweden, Denmark, Finland, Germany & Netherlands - Don't show total cost
											echo sprintf(__('%s - %s %s/month - %s%s - Start %s', 'billmate'), $pclass->getDescription(), $monthly_cost, $this->billmate_currency, $pclass->getInterestRate(), '%', $pclass->getStartFee() );
										}
									}
								echo '</option>';
							
							} // End if ($sum > $pclass->getMinAmount())
							
			   			} // End if $pclass->getType() == 0 or 1
					
					} // End foreach
					?>
						
					</select>
				
					<?php
				} else {
					echo __('Billmate PClasses seem to be missing. Billmate Part Payment does not work.', 'billmate');
				}
				?>				
				
			</p>
			<?php
			// Calculate lowest monthly cost and display it
			if( $enabled_plcass == 'no' ) return false;
			$pclass = $k->getCheapestPClass($sum, $flag);
	
			//Did we get a PClass? (it is false if we didn't)
			if($pclass) {
	    		//Here we reuse the same values as above:
    			$value = BillmateCalc::calc_monthly_cost(
    	    	$sum,
    	    	$pclass,
    	    	$flag
    			);
	
	    		/* $value is now a rounded monthly cost amount to be displayed to the customer. */
	    		// apply_filters to the monthly cost message so we can filter this if needed
	    		
	    		$billmate_partpayment_monthly_cost_message = sprintf(__('From %s %s/month', 'billmate'), $value, $this->billmate_currency );
	    		echo '<p class="form-row form-row-last billmate-monthly-cost">' . apply_filters( 'billmate_partpayment_monthly_cost_message', $billmate_partpayment_monthly_cost_message ) . '</p>';
	    		
			
			}
			?>
			<div class="clear"></div>
			
			<p class="form-row form-row-first">
				<?php if ( $this->shop_country == 'NL' || $this->shop_country == 'DE' ) : ?>
				
				<label for="billmate_pno"><?php echo __("Personal / Corporate ", 'billmate') ?> <span class="required">*</span></label>
                    <select class="dob_select dob_day" name="date_of_birth_day" style="width:60px;">
                        <option value="">
                        <?php echo __("Day", 'billmate') ?>
                        </option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
                    </select>
                    <select class="dob_select dob_month" name="date_of_birth_month" style="width:80px;">
                        <option value="">
                        <?php echo __("Month", 'billmate') ?>
                        </option>
                        <option value="01"><?php echo __("Jan", 'billmate') ?></option>
                        <option value="02"><?php echo __("Feb", 'billmate') ?></option>
                        <option value="03"><?php echo __("Mar", 'billmate') ?></option>
                        <option value="04"><?php echo __("Apr", 'billmate') ?></option>
                        <option value="05"><?php echo __("May", 'billmate') ?></option>
                        <option value="06"><?php echo __("Jun", 'billmate') ?></option>
                        <option value="07"><?php echo __("Jul", 'billmate') ?></option>
                        <option value="08"><?php echo __("Aug", 'billmate') ?></option>
                        <option value="09"><?php echo __("Sep", 'billmate') ?></option>
                        <option value="10"><?php echo __("Oct", 'billmate') ?></option>
                        <option value="11"><?php echo __("Nov", 'billmate') ?></option>
                        <option value="12"><?php echo __("Dec", 'billmate') ?></option>
                    </select>
                    <select class="dob_select dob_year" name="date_of_birth_year" style="width:60px;">
                        <option value="">
                        <?php echo __("Year", 'billmate') ?>
                        </option>
                        <option value="1920">1920</option>
                        <option value="1921">1921</option>
                        <option value="1922">1922</option>
                        <option value="1923">1923</option>
                        <option value="1924">1924</option>
                        <option value="1925">1925</option>
                        <option value="1926">1926</option>
                        <option value="1927">1927</option>
                        <option value="1928">1928</option>
                        <option value="1929">1929</option>
                        <option value="1930">1930</option>
                        <option value="1931">1931</option>
                        <option value="1932">1932</option>
                        <option value="1933">1933</option>
                        <option value="1934">1934</option>
                        <option value="1935">1935</option>
                        <option value="1936">1936</option>
                        <option value="1937">1937</option>
                        <option value="1938">1938</option>
                        <option value="1939">1939</option>
                        <option value="1940">1940</option>
                        <option value="1941">1941</option>
                        <option value="1942">1942</option>
                        <option value="1943">1943</option>
                        <option value="1944">1944</option>
                        <option value="1945">1945</option>
                        <option value="1946">1946</option>
                        <option value="1947">1947</option>
                        <option value="1948">1948</option>
                        <option value="1949">1949</option>
                        <option value="1950">1950</option>
                        <option value="1951">1951</option>
                        <option value="1952">1952</option>
                        <option value="1953">1953</option>
                        <option value="1954">1954</option>
                        <option value="1955">1955</option>
                        <option value="1956">1956</option>
                        <option value="1957">1957</option>
                        <option value="1958">1958</option>
                        <option value="1959">1959</option>
                        <option value="1960">1960</option>
                        <option value="1961">1961</option>
                        <option value="1962">1962</option>
                        <option value="1963">1963</option>
                        <option value="1964">1964</option>
                        <option value="1965">1965</option>
                        <option value="1966">1966</option>
                        <option value="1967">1967</option>
                        <option value="1968">1968</option>
                        <option value="1969">1969</option>
                        <option value="1970">1970</option>
                        <option value="1971">1971</option>
                        <option value="1972">1972</option>
                        <option value="1973">1973</option>
                        <option value="1974">1974</option>
                        <option value="1975">1975</option>
                        <option value="1976">1976</option>
                        <option value="1977">1977</option>
                        <option value="1978">1978</option>
                        <option value="1979">1979</option>
                        <option value="1980">1980</option>
                        <option value="1981">1981</option>
                        <option value="1982">1982</option>
                        <option value="1983">1983</option>
                        <option value="1984">1984</option>
                        <option value="1985">1985</option>
                        <option value="1986">1986</option>
                        <option value="1987">1987</option>
                        <option value="1988">1988</option>
                        <option value="1989">1989</option>
                        <option value="1990">1990</option>
                        <option value="1991">1991</option>
                        <option value="1992">1992</option>
                        <option value="1993">1993</option>
                        <option value="1994">1994</option>
                        <option value="1995">1995</option>
                        <option value="1996">1996</option>
                        <option value="1997">1997</option>
                        <option value="1998">1998</option>
                        <option value="1999">1999</option>
                        <option value="2000">2000</option>
                    </select>
					
				<?php else : ?>
					<label for="billmate_pno"><?php echo __("Personal / Corporate ", 'billmate') ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="billmate_pno" />
				<?php endif; ?>
			</p>
			
			<?php if ( $this->shop_country == 'NL' || $this->shop_country == 'DE' ) : ?>
				<p class="form-row form-row-last">
					<label for="billmate_partpayment_gender"><?php echo __("Kön", 'billmate') ?> <span class="required">*</span></label>
					<select id="billmate_partpayment_gender" name="billmate_partpayment_gender" class="woocommerce-select" style="width:120px;">
						<option value=""><?php echo __("Select gender", 'billmate') ?></options>
						<option value="0"><?php echo __("Female", 'billmate') ?></options>
						<option value="1"><?php echo __("Male", 'billmate') ?></options>
					</select>
				</p>
			<?php endif; ?>
						
			<div class="clear"></div>
		
<p><a id="billmate_partpayment" href="javascript://"><?php echo $this->get_account_terms_link_text($this->billmate_country); ?></a></p>

<script type="text/javascript">
jQuery( document).ready(function(){
	window.$ = $ = jQuery;

    $.getScript("https://efinance.se/billmate/base.js", function(){
		    $("#billmate_partpayment").Terms("villkor_delbetalning",{eid: billmate_eid,effectiverate:34}, "#billmate_partpayment");
    });
});
</script>
<?php
$datatemp = array();
parse_str($_POST['post_data'], $datatemp);
?>
		<div class="clear"></div>
			<p class="form-row">
				<input type="checkbox" class="input-checkbox" checked="checked" value="yes" name="valid_email_it_is" id="valid_email_it_is" style="float:left;margin-top:6px" />
				<label for="valid_email_it_is" ><?php echo sprintf(__('Min e-postadress %s är korrekt och får användas för fakturering', 'billmate'), $datatemp['billing_email']) ?></label>
			</p>
							
			<?php if ( $this->shop_country == 'DE' && $this->de_consent_terms == 'yes' ) : ?>
				<p class="form-row">
					<label for="billmate_de_terms"></label>
					<input type="checkbox" class="input-checkbox" value="yes" name="billmate_de_consent_terms" />
					<?php echo sprintf(__('Mit der Übermittlung der für die Abwicklungdes Rechnungskaufes und einer Identitäts-und Bonitätsprüfung erforderlichen Daten an Billmate bin ich einverstanden. Meine <a href="%s" target="_blank">Einwilligung</a> kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.', 'billmate'), 'https://online.billmate.com/consent_de.yaws') ?>
					
				</p>
			<?php endif; ?>
			<div class="clear"></div>
		
		</fieldset>
		<?php	
	}
	
	
	/**
 	 * Process the gateway specific checkout form fields
 	**/
	function billmate_partpayment_checkout_field_process() {
    	global $woocommerce;
    	
 		
 		// Only run this if Billmate Delbetalning is the choosen payment method
 		if ($_POST['payment_method'] == 'billmate_partpayment') {
 		
 			// SE, NO, DK & FI
 			if ( $this->shop_country == 'SE' || $this->shop_country == 'NO' || $this->shop_country == 'DK' || $this->shop_country == 'FI' ){
 			
    			// Check if set, if its not set add an error.
    			if (!$_POST['billmate_pno'])
        		 	$woocommerce->add_error( __('Ej giltigt organisations-/personnummer. Kontrollera numret.', 'billmate') );
        	 	
			}
			
			// NL & DE
	 		if ( $this->shop_country == 'NL' || $this->shop_country == 'DE' ){
	    		// Check if set, if its not set add an error.
	    		
	    		// Gender
	    		if (!isset($_POST['billmate_partpayment_gender']))
	        	 	$woocommerce->add_error( __('Ej giltigt organisations-/personnummer. Kontrollera numret.', 'billmate') );
	         	
	         	// Personal / Corporate 
				if (!$_POST['date_of_birth_day'] || !$_POST['date_of_birth_month'] || !$_POST['date_of_birth_year'])
	         		$woocommerce->add_error( __('Ej giltigt organisations-/personnummer. Kontrollera numret.', 'billmate') );
	         	
	         	// Shipping and billing address must be the same
	         	$billmate_shiptobilling = ( isset( $_POST['shiptobilling'] ) ) ? $_POST['shiptobilling'] : '';
	         	
	         	if ($billmate_shiptobilling !=1 && isset($_POST['shipping_first_name']) && $_POST['shipping_first_name'] !== $_POST['billing_first_name'])
	        	 	$woocommerce->add_error( __('Shipping and billing address must be the same when paying via Billmate.', 'billmate') );
	        	 
	        	 if ($billmate_shiptobilling !=1 && isset($_POST['shipping_last_name']) && $_POST['shipping_last_name'] !== $_POST['billing_last_name'])
	        	 	$woocommerce->add_error( __('Shipping and billing address must be the same when paying via Billmate.', 'billmate') );
	        	 
	        	 if ($billmate_shiptobilling !=1 && isset($_POST['shipping_address_1']) && $_POST['shipping_address_1'] !== $_POST['billing_address_1'])
	        	 	$woocommerce->add_error( __('Shipping and billing address must be the same when paying via Billmate.', 'billmate') );
	        	 
	        	 if ($billmate_shiptobilling !=1 && isset($_POST['shipping_postcode']) && $_POST['shipping_postcode'] !== $_POST['billing_postcode'])
	        	 	$woocommerce->add_error( __('Shipping and billing address must be the same when paying via Billmate.', 'billmate') );
	        	 	
	        	 if ($billmate_shiptobilling !=1 && isset($_POST['shipping_city']) && $_POST['shipping_city'] !== $_POST['billing_city'])
	        	 	$woocommerce->add_error( __('Shipping and billing address must be the same when paying via Billmate.', 'billmate') );
			}
			
			// DE
			if ( $this->shop_country == 'DE' && $this->de_consent_terms == 'yes'){
	    		// Check if set, if its not set add an error.
	    		if (!isset($_POST['billmate_de_consent_terms']))
	        	 	$woocommerce->add_error( __('You must accept the Billmate consent terms.', 'billmate') ); 	
			}
		}
	}
	
	
	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		global $woocommerce;
		
		$order = new WC_order( $order_id );
		if(empty($_POST['valid_email_it_is'])){
            $woocommerce->add_error( sprintf( __('Vänligen bekräfta att e-postadressen "%s" är korrekt. Denna kommer att användas för fakturering.', 'billmate'), $order->billing_email ));
            return;
		}		

		require_once(BILLMATE_LIB . 'Billmate.php');
		require_once(BILLMATE_LIB . 'billmatepclasses/storage.intf.php');
		require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
		require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
		
		// Get values from billmate form on checkout page
		
		// Collect the dob different depending on country
		if ( $this->shop_country == 'NL' || $this->shop_country == 'DE' ) :
			$billmate_pno_day 			= isset($_POST['date_of_birth_day']) ? woocommerce_clean($_POST['date_of_birth_day']) : '';
			$billmate_pno_month 			= isset($_POST['date_of_birth_month']) ? woocommerce_clean($_POST['date_of_birth_month']) : '';
			$billmate_pno_year 			= isset($_POST['date_of_birth_year']) ? woocommerce_clean($_POST['date_of_birth_year']) : '';
			$billmate_pno 				= $billmate_pno_day . $billmate_pno_month . $billmate_pno_year;
		else :
			$billmate_pno 			= isset($_POST['billmate_pno']) ? woocommerce_clean($_POST['billmate_pno']) : '';
		endif;
		
		$billmate_pclass 				= isset($_POST['billmate_partpayment_pclass']) ? woocommerce_clean($_POST['billmate_partpayment_pclass']) : '';
		$billmate_gender 				= isset($_POST['billmate_partpayment_gender']) ? woocommerce_clean($_POST['billmate_partpayment_gender']) : '';
		
		$billmate_de_consent_terms	= isset($_POST['billmate_de_consent_terms']) ? woocommerce_clean($_POST['billmate_de_consent_terms']) : '';
		
		
		// Split address into House number and House extension for NL & DE customers
		if ( $this->shop_country == 'NL' || $this->shop_country == 'DE' ) :
		
			require_once('split-address.php');
			
			$billmate_billing_address				= $order->billing_address_1;
			$splitted_address 					= splitAddress($billmate_billing_address);
			
			$billmate_billing_address				= $splitted_address[0];
			$billmate_billing_house_number		= $splitted_address[1];
			$billmate_billing_house_extension		= $splitted_address[2];
			
			$billmate_shipping_address			= $order->shipping_address_1;
			$splitted_address 					= splitAddress($billmate_shipping_address);
			
			$billmate_shipping_address			= $splitted_address[0];
			$billmate_shipping_house_number		= $splitted_address[1];
			$billmate_shipping_house_extension	= $splitted_address[2];
		
		else :
			
			$billmate_billing_address				= $order->billing_address_1;
			$billmate_billing_house_number		= '';
			$billmate_billing_house_extension		= '';
			
			$billmate_shipping_address			= $order->shipping_address_1;
			$billmate_shipping_house_number		= '';
			$billmate_shipping_house_extension	= '';
			
		endif;
				
		// Store Billmate specific form values in order as post meta
		update_post_meta( $order_id, 'billmate_pno', $billmate_pno);
		
		
		// Test mode or Live mode		
		if ( $this->testmode == 'yes' ):
			// Disable SSL if in testmode
			$billmate_ssl = 'false';
			$billmate_mode = Billmate::BETA;
		else :
			// Set SSL if used in webshop
			if (is_ssl()) {
				$billmate_ssl = 'true';
			} else {
				$billmate_ssl = 'false';
			}
			$billmate_mode = Billmate::LIVE;
		endif;

   		if( empty( $this->settings['eid']) ){
   		    return false;
   		}			
		$k = new Billmate();
		
		$k->config(
		    $eid = $this->settings['eid'],
		    $secret = $this->settings['secret'],
		    $country = $this->billmate_country,
		    $language = $this->billmate_language,
		    $currency = $this->billmate_currency,
		    $mode = $billmate_mode,
		    $pcStorage = 'json',
		    $pcURI = '/srv/billmatepclasses.json',
		    $ssl = $billmate_ssl,
		    $candice = false
		);
		
		Billmate::$xmlrpcDebug = false;
		Billmate::$debug = false;
		
		// Cart Contents
		if (sizeof($order->get_items())>0) : foreach ($order->get_items() as $item) :
			$_product = $order->get_product_from_item( $item );
			if ($_product->exists() && $item['qty']) :
			
				// We manually calculate the tax percentage here
				if ($order->get_line_tax($item) !==0) :
					// Calculate tax percentage
					$item_tax_percentage = @number_format( ( $order->get_line_tax($item) / $order->get_line_total( $item, false ) )*100, 2, '.', '');
				else :
					$item_tax_percentage = 0.00;
				endif;
				
				// apply_filters to item price so we can filter this if needed
				$billmate_item_price_including_tax = $order->get_item_total( $item, true );
				$item_price = apply_filters( 'billmate_item_price_including_tax', $billmate_item_price_including_tax );
					
					if ( $_product->get_sku() ) {
						$sku = $_product->get_sku();
					} else {
						$sku = $_product->id;
					}
					
					$k->addArticle(
		    		$qty = $item['qty'], 					//Quantity
		    		$artNo = $sku,		 					//Article number
		    		$title = $item['name'], 	//Article name/title
		    		$price = $item_price, 					// Price including tax
		    		$vat = $item_tax_percentage,			// Tax
		    		$discount = 0, 
		    		$flags = BillmateFlags::INC_VAT 			//Price is including VAT.
				);
									
			endif;
		endforeach; endif;
		 
		// Discount
		if ($order->order_discount>0) :
			
			// apply_filters to order discount so we can filter this if needed
			$billmate_order_discount = $order->order_discount;
			$order_discount = apply_filters( 'billmate_order_discount', $billmate_order_discount );
		
			$k->addArticle(
			    $qty = 1,
			    $artNo = "",
			    $title = __('Discount', 'billmate'),
			    $price = -$order_discount,
			    $vat = 0,
			    $discount = 0,
			    $flags = BillmateFlags::INC_VAT //Price is including VAT
			);
		endif;
		
		// Shipping
		if ($order->order_shipping>0) :
			
			// We manually calculate the shipping tax percentage here
			$calculated_shipping_tax_percentage = ($order->order_shipping_tax/$order->order_shipping)*100; //25.00
			$calculated_shipping_tax_decimal = ($order->order_shipping_tax/$order->order_shipping)+1; //0.25
			
			// apply_filters to Shipping so we can filter this if needed
			$billmate_shipping_price_including_tax = $order->order_shipping*$calculated_shipping_tax_decimal;
			$shipping_price = apply_filters( 'billmate_shipping_price_including_tax', $billmate_shipping_price_including_tax );
			
			$k->addArticle(
			    $qty = 1,
			    $artNo = "",
			    $title = __('Shipping cost', 'billmate'),
			    $price = $shipping_price,
			    $vat = $calculated_shipping_tax_percentage,
			    $discount = 0,
			    $flags = BillmateFlags::INC_VAT + BillmateFlags::IS_SHIPMENT //Price is including VAT and is shipment fee
			);
		endif;
		
        require_once dirname( __FILE__ ) .'/utf8.php';
		try{
			$addr = $k->getAddresses($billmate_pno);
		}catch( Exception $ex ){
			$woocommerce->add_error( $ex->getMessage() );
            return;
		}
        $addr = $addr[0];
        if( empty( $addr ) ) {
            $woocommerce->add_error( __('Unable to find address.', 'billmate') );
            return;
        }
        $fullname = $order->billing_last_name.' '.$order->billing_first_name.' '.$order->billing_company;
        if( strlen( $addr->getFirstname() ) <= 0){
            $addr->setFirstName($order->billing_first_name);
            $addr->setLastName($order->billing_last_name);
        }
        $apiName  = $addr->getFirstName().' '.$addr->getLastName().' '.$addr->getCompanyName();
        
        $usership = $order->billing_last_name.' '.$order->billing_first_name.' '.$order->billing_company;
        $userbill = $order->shipping_last_name.' '.$order->shipping_first_name.' '.$order->shipping_company;

		$addressNotMatched = !match_usernamevp( $usership, $userbill ) ||
		    !isEqual($addr->getStreet(), $billmate_billing_address ) ||
		    !isEqual($addr->getZipCode(), $order->shipping_postcode) || 
		    !isEqual($addr->getCity(), $order->shipping_city) || 
		    !isEqual($addr->getCountryCode(), $order->shipping_country);

        $shippingAndBilling =  !match_usernamevp( $fullname , $apiName) ||
		    !isEqual($order->billing_address_1, $order->shipping_address_1 ) ||
		    !isEqual($order->billing_postcode, $order->shipping_postcode) || 
		    !isEqual($order->billing_city, $order->shipping_city) || 
		    !isEqual($order->billing_country, $order->shipping_country) ;
		
		$shippingAndBilling = $order->get_shipping_method() == '' ? false : $shippingAndBilling;
		
		$name = strlen( $addr->getFirstName() ) ? $addr->getFirstName() : $addr->getCompanyName();
		global $woocommerce;
		$displayname = $addr->getCompanyName();
		if(empty($displayname)){
			$displayname = $addr->getFirstName().' '.$addr->getLastName();
		}
		
		$importedCountry = '';
		if(!(strtolower($addr->getCountryCode()) == 'se' && WPLANG == 'sv_SE' )){
			$importedCountry = $woocommerce->countries->countries[$addr->getCountryCode()];
		}
		
		if( $addressNotMatched || $shippingAndBilling ){
		    if( empty($_POST['geturl'] ) ){
			    $html = $displayname.'<br>'.$addr->getStreet().'<br>'.$addr->getZipCode().'<br>'.$addr->getCity().'<br/>'.$importedCountry.'<div style="margin-top:1em"><input type="button" value="'.__('Yes, make purchase with this address','billmate').'" onclick="ajax_load(this);modalWin.HideModalPopUp(); " class="billmate_button"/></div><a onclick="noPressButton()" class="linktag">'.__('No, I want to specify a different number or change payment method','billmate').'</a>';
			    $html.= '<span id="hidden_data"><input type="hidden" id="_first_name" value="'.$name.'" />';
			    $html.= '<input type="hidden" id="_last_name" value="'.$addr->getLastName().'" />';
			    $html.= '<input type="hidden" id="_company" value="'.$addr->getCompanyName().'" />';
			    $html.= '<input type="hidden" id="_address_1" value="'.$addr->getStreet().'" />';
			    $html.= '<input type="hidden" id="_postcode" value="'.$addr->getZipCode().'" />';
			    $html.= '<input type="hidden" id="_city" value="'.$addr->getCity().'" /></span>';
			    echo $code = '<script type="text/javascript">setTimeout(function(){modalWin.ShowMessage(\''.$html.'\',350,500,\''.__('Pay by invoice can be made only to the address listed in the National Register. Would you like to make the purchase with address:','billmate').'\');},1000);</script>';
			    die;
			}
		} 
        		
		//Create the address object and specify the values.
		
		// Billing address
		$addr_billing = new BillmateAddr(
    		$email = $order->billing_email,
    		$telno = '', //We skip the normal land line phone, only one is needed.
    		$cellno = $order->billing_phone,
    		//$company = $order->billing_company,
    		$fname = utf8_decode ($order->billing_first_name),
    		$lname = utf8_decode ($order->billing_last_name),
    		$careof = utf8_decode ($order->billing_address_2),  //No care of, C/O.
    		$street = utf8_decode ($billmate_billing_address), //For DE and NL specify street number in houseNo.
    		$zip = utf8_decode ($order->billing_postcode),
    		$city = utf8_decode ($order->billing_city),
    		$country = 'SE',
    		$houseNo = utf8_decode ($billmate_billing_house_number), //For DE and NL we need to specify houseNo.
    		$houseExt = utf8_decode ($billmate_billing_house_extension) //Only required for NL.
		);
		
		
		// Shipping address
		if ( $order->get_shipping_method() == '' ) {
			
			// Use billing address if Shipping is disabled in Woocommerce
			$addr_shipping = new BillmateAddr(
    			$email = $order->billing_email,
    			$telno = '', //We skip the normal land line phone, only one is needed.
    			$cellno = $order->billing_phone,
    			//$company = $order->shipping_company,
    			$fname = utf8_decode ($order->billing_first_name),
    			$lname = utf8_decode ($order->billing_last_name),
    			$careof = utf8_decode ($order->billing_address_2),  //No care of, C/O.
    			$street = utf8_decode ($billmate_billing_address), //For DE and NL specify street number in houseNo.
    			$zip = utf8_decode ($order->billing_postcode),
    			$city = utf8_decode ($order->billing_city),
    			$country = 'SE',
    			$houseNo = utf8_decode ($billmate_billing_house_number), //For DE and NL we need to specify houseNo.
    			$houseExt = utf8_decode ($billmate_billing_house_extension) //Only required for NL.
			);
		
		} else {
		
			$addr_shipping = new BillmateAddr(
    			$email = $order->billing_email,
    			$telno = '', //We skip the normal land line phone, only one is needed.
    			$cellno = $order->billing_phone,
    			//$company = $order->shipping_company,
    			$fname = utf8_decode ($order->shipping_first_name),
    			$lname = utf8_decode ($order->shipping_last_name),
    			$careof = utf8_decode ($order->shipping_address_2),  //No care of, C/O.
    			$street = utf8_decode ($billmate_shipping_address), //For DE and NL specify street number in houseNo.
    			$zip = utf8_decode ($order->shipping_postcode),
    			$city = utf8_decode ($order->shipping_city),
    			$country = 'SE',
    			$houseNo = utf8_decode ($billmate_shipping_house_number), //For DE and NL we need to specify houseNo.
    			$houseExt = utf8_decode ($billmate_shipping_house_extension) //Only required for NL.
			);
		
		}

		
		//Next we tell the Billmate instance to use the address in the next order.
		$k->setAddress(BillmateFlags::IS_BILLING, $addr_billing); //Billing / invoice address
		$k->setAddress(BillmateFlags::IS_SHIPPING, $addr_shipping); //Shipping / delivery address

		//Set store specific information so you can e.g. search and associate invoices with order numbers.
		$k->setEstoreInfo(
		    $orderid1 = $order_id, //Maybe the estore's order number/id.
		    $orderid2 = $order->order_key, //Could an order number from another system?
		    $user = '' //Username, email or identifier for the user?
		);
		
		/** Shipment type? **/

		//Normal shipment is defaulted, delays the start of invoice expiration/due-date.
		// $k->setShipmentInfo('delay_adjust', BillmateFlags::EXPRESS_SHIPMENT);		    
		try {
    		//Transmit all the specified data, from the steps above, to Billmate.
    		$result = $k->addTransaction(
    		    $pno = $billmate_pno, //Personal / Corporate .
    		    
    		    $gender = intval($billmate_gender),//Gender.
    		    $flags = BillmateFlags::NO_FLAG, //No specific behaviour like RETURN_OCR or TEST_MODE.
    		    $pclass = $billmate_pclass // Get the pclass object that the customer has choosen.
    		);
    		
    		// Retreive response

    		$invno = $result[0];
    		switch($result[2]) {
            case BillmateFlags::ACCEPTED:
                $order->add_order_note( __('Billmate payment completed. Billmate Invoice number:', 'billmate') . $invno );
                
                // Payment complete
				$order->payment_complete();		
				
				// Remove cart
				$woocommerce->cart->empty_cart();			
				
				// Return thank you redirect
				return array(
						'result' 	=> 'success',
						'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(get_option('woocommerce_thanks_page_id'))))
				);
						
                break;
            case BillmateFlags::PENDING:
                $order->add_order_note( __('Order is PENDING APPROVAL by Billmate. Please visit Billmate Online for the latest status on this order. Billmate Invoice number: ', 'billmate') . $invno );
                
                // Payment complete
				$order->payment_complete();
				
				// Remove cart
				$woocommerce->cart->empty_cart();
				
				// Return thank you redirect
				return array(
						'result' 	=> 'success',
						'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(get_option('woocommerce_thanks_page_id'))))
				);
				
                break;
            case BillmateFlags::DENIED:
                //Order is denied, store it in a database.
				$order->add_order_note( __('Billmate payment denied.', 'billmate') );
				$woocommerce->add_error( __('Billmate payment denied.', 'billmate') );
                return;
                break;
            default:
            	//Unknown response, store it in a database.
				$order->add_order_note( __('Unknown response from Billmate.', 'billmate') );
				$woocommerce->add_error( __('Unknown response from Billmate.', 'billmate') );
                return;
                break;
        	}
 			
 	   		
			}
		
		catch(Exception $e) {
    		//The purchase was denied or something went wrong, print the message:
			echo '<ul class="woocommerce-error"><li>';
			echo sprintf(__('%s (Error code: %s)', 'billmate'), utf8_encode($e->getMessage()), $e->getCode() );
			echo '<script type="text/javascript">jQuery("#billmategeturl").remove();</script></li></ul>';
			die;
		}

	
	}
	
	/**
	 * receipt_page
	 **/
	function receipt_page( $order ) {
		
		echo '<p>'.__('Thank you for your order.', 'billmate').'</p>';
		
	}
	
	/**
	 * Calc monthly cost on single Product page and print it out
	 **/
	 
	function print_product_monthly_cost() {
		
		if ( $this->enabled!="yes" ) return;
			
		//global $woocommerce, $product, $billmate_partpayment_shortcode_currency, $billmate_partpayment_shortcode_price, $billmate_partpayment_shortcode_img, $billmate_partpayment_shortcode_info_link;
		global $woocommerce, $product, $billmate_partpayment_shortcode_currency, $billmate_partpayment_shortcode_price, $billmate_shortcode_img, $billmate_partpayment_country,$billmate_partpayment_eid;
		
		$billmate_filename = BILLMATE_DIR . 'srv/billmatepclasses.json';
		$billmate_partpayment_eid = $this->settings['eid'];
		
	 	// Only execute this if the feature is activated in the gateway settings
		if ( $this->show_monthly_cost == 'yes' && file_exists($billmate_filename) ) {
	 		// Get the lib files and set up a new Billmate() instance.
	  		require_once(BILLMATE_LIB . 'Billmate.php');
			require_once(BILLMATE_LIB . 'billmatepclasses/storage.intf.php');
			require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
			require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
			
			// Test mode or Live mode		
			if ( $this->testmode == 'yes' ):
				// Disable SSL if in testmode
				$billmate_ssl = 'false';
				$billmate_mode = Billmate::BETA;
			else :
				// Set SSL if used in webshop
				if (is_ssl()) {
					$billmate_ssl = 'true';
				} else {
					$billmate_ssl = 'false';
				}
				$billmate_mode = Billmate::LIVE;
			endif;

	   		if( empty( $this->settings['eid']) ){
	   		    return false;
	   		}
		   			   		
			$k = new Billmate();
			
			$k->config(
			    $eid = $this->settings['eid'],
			    $secret = $this->settings['secret'],
			    $country = $this->billmate_country,
			    $language = $this->billmate_language,
			    $currency = $this->billmate_currency,
			    $mode = $billmate_mode,
			    $pcStorage = 'json',
			    $pcURI = BILLMATE_DIR . 'srv/billmatepclasses.json',
			    $ssl = $billmate_ssl,
			    $candice = true
			);
	
			Billmate::$xmlrpcDebug = false;
			Billmate::$debug = false;
			
			// apply_filters to product price so we can filter this if needed
			$billmate_product_total = $product->get_price();
			$sum = apply_filters( 'billmate_product_total', $billmate_product_total ); // Product price.
			$flag = BillmateFlags::PRODUCT_PAGE; //or BillmateFlags::PRODUCT_PAGE, if you want to do it for one item.
			$pclass = $k->getCheapestPClass($sum, $flag);
			
			
			//Did we get a PClass? (it is false if we didn't)
			if($pclass) {
	    		//Here we reuse the same values as above:
   				$value = BillmateCalc::calc_monthly_cost(
   		    	$sum,
   		    	$pclass,
   		    	$flag
   				);
				
	    		// Asign values to variables used for shortcodes.
	    		$billmate_partpayment_shortcode_currency = $this->billmate_currency;
	    		$billmate_partpayment_shortcode_price = $value;
	    		$billmate_shortcode_img = $this->icon_basic;
	    		$billmate_partpayment_country = $this->billmate_country;
	    		//$billmate_partpayment_shortcode_info_link = $this->billmate_partpayment_info;
	    		
	    		
	    		
	    		/* $value is now a rounded monthly cost amount to be displayed to the customer. */
	    		// apply_filters to the monthly cost message so we can filter this if needed
	    		
	    		//$billmate_partpayment_product_monthly_cost_message = sprintf(__('<img src="%s" /> <br/><a href="%s" target="_blank">Part pay from %s %s/month</a>', 'billmate'), $this->icon, $this->billmate_partpayment_info, $value, $this->billmate_currency );
	    		
	    		// Monthly cost threshold check. This is done after apply_filters to product price ($sum).
		    	if ( $this->lower_threshold_monthly_cost < $sum && $this->upper_threshold_monthly_cost > $sum ) {
		    		
		    		echo '<div class="billmate-product-monthly-cost">' . do_shortcode( $this->show_monthly_cost_info );
		    		
		    		// Show billmate_warning_banner if NL
					if ( $this->shop_country == 'NL' ) {
						echo '<img src="' . $this->billmate_wb_img_single_product . '" class="billmate-wb" style="max-width: 100%;"/>';	
					}
		    		echo '</div>';
		    				    	
		    	}
	    		
			} // End pclass check
		
		} // End show_monthly_cost check
		
	}
	
	
	/**
	 * Calc monthly cost on Shop page and print it out
	 **/
	 
 	function print_product_monthly_cost_shop() {
 		
 		if ( $this->enabled!="yes" ) return;
 		
 		//global $woocommerce, $product, $billmate_partpayment_shortcode_currency, $billmate_partpayment_shortcode_price, $billmate_partpayment_shortcode_img, $billmate_partpayment_shortcode_info_link;
 		global $woocommerce, $product, $billmate_partpayment_shortcode_currency, $billmate_partpayment_shortcode_price, $billmate_shortcode_img, $billmate_partpayment_country;
	 	
	 	$billmate_filename = BILLMATE_DIR . 'srv/billmatepclasses.json';

	 	// Only execute this if the feature is activated in the gateway settings		
		if ( $this->show_monthly_cost_shop == 'yes' && file_exists($billmate_filename) ) {
			
	 		// Get the lib files and set up a new Billmate() instance.
	  		require_once(BILLMATE_LIB . 'Billmate.php');
			require_once(BILLMATE_LIB . 'billmatepclasses/storage.intf.php');
			require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
			require_once(BILLMATE_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
			
			// Test mode or Live mode		
			if ( $this->testmode == 'yes' ):
				// Disable SSL if in testmode
				$billmate_ssl = 'false';
				$billmate_mode = Billmate::BETA;
			else :
				// Set SSL if used in webshop
				if (is_ssl()) {
					$billmate_ssl = 'true';
				} else {
					$billmate_ssl = 'false';
				}
				$billmate_mode = Billmate::LIVE;
			endif;
	   		if( empty( $this->settings['eid']) ){
	   		    return false;
	   		}
	   		
  			$k = new Billmate();
			
			$k->config(
			    $eid = $this->settings['eid'],
			    $secret = $this->settings['secret'],
			    $country = $this->billmate_country,
			    $language = $this->billmate_language,
			    $currency = $this->billmate_currency,
			    $mode = $billmate_mode,
			    $pcStorage = 'json',
			    $pcURI = BILLMATE_DIR . 'srv/billmatepclasses.json',
			    $ssl = $billmate_ssl,
			    $candice = true
			);
	
			Billmate::$xmlrpcDebug = false;
			Billmate::$debug = false;
			
			// apply_filters to product price so we can filter this if needed
			$billmate_product_total = $product->get_price();
			$sum = apply_filters( 'billmate_product_total', $billmate_product_total ); // Product price.
			$flag = BillmateFlags::PRODUCT_PAGE; //or BillmateFlags::PRODUCT_PAGE, if you want to do it for one item.
			$pclass = $k->getCheapestPClass($sum, $flag);
			
			
			//Did we get a PClass? (it is false if we didn't)
			if($pclass) {
	    		//Here we reuse the same values as above:
   				$value = BillmateCalc::calc_monthly_cost(
   		    	$sum,
   		    	$pclass,
   		    	$flag
   				);
	
	    		
	    		// Asign values to variables used for shortcodes.
	    		$billmate_partpayment_shortcode_currency = $this->billmate_currency;
	    		$billmate_partpayment_shortcode_price = $value;
	    		$billmate_shortcode_img = $this->icon_basic;
	    		$billmate_partpayment_country = $this->billmate_country;
	    		//$billmate_partpayment_shortcode_info_link = $this->billmate_partpayment_info;
				
	    		
	    		//$billmate_partpayment_product_monthly_cost_message = sprintf(__('<img src="%s" /> <br/><a href="%s" target="_blank">Part pay from %s %s/month</a>', 'billmate'), $this->icon, $this->billmate_partpayment_info, $value, $this->billmate_currency );
		    		
	    		// Monthly cost threshold check. This is done after apply_filters to product price ($sum).
		    	if ( $this->lower_threshold_monthly_cost < $sum && $this->upper_threshold_monthly_cost > $sum ) {

		    		echo '<div class="billmate-product-monthly-cost-shop-page">' . do_shortcode( $this->show_monthly_cost_shop_info );
		    		
		    		// Show billmate_warning_banner if NL
					if ( $this->shop_country == 'NL' ) {
						echo '<img src="' . $this->billmate_wb_img_product_list . '" class="billmate-wb" style="max-width: 100%;"/>';	
					}
		    		
		    		echo '</div>';
		    		
	    		}
	    		
			} // End pclass check
		
		} // End show_monthly_cost_shop check
	}
	
	
	/**
	 * Javascript for Account info/terms popup on checkout page
	 **/
	function billmate_partpayment_terms_js() {
		
		if ( is_checkout() || is_product() || is_shop() || is_product_category() || is_product_tag() ) {
			?>
			<script type="text/javascript">
				var billmate_eid = "<?php echo $this->eid; ?>";
				var billmate_partpayment_linktext = "<?php echo $this->get_account_terms_link_text($this->billmate_country); ?>";
				var billmate_partpayment_country = "<?php echo $this->get_terms_country(); ?>";
				//addBillmatePartPaymentEvent(function(){InitBillmatePartPaymentElements('billmate_partpayment', billmate_eid, billmate_partpayment_country, billmate_partpayment_linktext, 0); });
			</script>
			<?php
		}
	}
	
	
	
	/**
	* get_terms_country function.
 	* Helperfunction - Get Terms Country based on selected Billing Country in the Ceckout form
 	* Defaults to $this->billmate_country
 	* At the moment $this->billmate_country is allways returned. This will change in the next update.
 	**/
	
	function get_terms_country() {
		global $woocommerce;
			
		if ( $woocommerce->customer->get_country() == true && in_array( $woocommerce->customer->get_country(), array('SE', 'NO', 'DK', 'DE', 'FI', 'NL') ) ) {
			
			// 
			//return strtolower($woocommerce->customer->get_country());
			return strtolower($this->billmate_country);
			
		} else {
		
			return strtolower($this->billmate_country);
		
		}
	} // End function get_terms_country()
	
	
	/**
	 * get_account_terms_link_text function.
	 * Helperfunction - Get Terms link text based on selected Billing Country in the Ceckout form
	 * Defaults to $this->billmate_country
	 * At the moment $this->billmate_country is allways returned. This will change in the next update.
	 **/
	 
	function get_account_terms_link_text($country) {
				
		switch ( $country )
		{
		case 'SE':
			$term_link = 'Villkor delbetalning';
			break;
		case 'NO':
			$term_link = 'Les mer';
			break;
		case 'DK':
			$term_link = 'L&aelig;s mere';
			break;
		case 'DE':
			$term_link = 'Lesen Sie mehr!';
			break;
		case 'FI':
			$term_link = 'Lue lis&auml;&auml;';
			break;
		case 'NL':
			$term_link = 'Lees meer!';
			break;
		default:
			$term_link = __('Read more', 'billmate');
		}
		
		return $term_link;
	} // end function get_account_terms_link_text()
	
	
	// Get Monthly cost prio - product page
	function get_monthly_cost_prio() {
		return $this->show_monthly_cost_prio;
	}
	
	// Get Monthly cost prio - shop base page (and archives)
	function get_monthly_cost_shop_prio() {
		return $this->show_monthly_cost_shop_prio;
	}
	
	
		
			 
} // End class WC_Gateway_Billmate_Partpayment



/**
 * Class 
 * @class 		WC_Gateway_Billmate_Partpayment_Extra
 * @since		1.5.4 (WC 2.0)
 *
 **/
 
class WC_Gateway_Billmate_Partpayment_Extra {
	
	public function __construct() {
		
		$data = new WC_Gateway_Billmate_Partpayment;
		$this->show_monthly_cost_shop_prio = $data->get_monthly_cost_shop_prio();
		$this->show_monthly_cost_prio = $data->get_monthly_cost_prio();
		
		// Actions
		add_action('woocommerce_after_shop_loop_item', array(&$this, 'print_product_monthly_cost_shop'), $this->show_monthly_cost_shop_prio);
	}
	
	function print_product_monthly_cost_shop() {
		$data = new WC_Gateway_Billmate_Partpayment;
		$data->print_product_monthly_cost_shop();
	}
} // End class WC_Gateway_Billmate_Partpayment_Extra

$wc_gateway_billmate_partpayment_extra = new WC_Gateway_Billmate_Partpayment_Extra;