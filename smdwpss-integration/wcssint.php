<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the
 * plugin admin area. This file also defines a function that starts the plugin.
 *
 * @link              http://smdesign-studio.com/
 * @since             1.0.0
 * @package           WooCommerceSharpSpringIntegration
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce SharpSpring Integration
 * Description:       Integrate SharpSpring analytics into any WooCommerce shop.
 * Version:           1.0.0
 * Author:            SMDesign Studio
 * Author URI:        http://smdesign-studio.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	 die;
}

// Include the dependencies needed to instantiate the plugin.
foreach ( glob( plugin_dir_path( __FILE__ ) . 'admin/*.php' ) as $file ) {
	include_once $file;
}

add_action( 'plugins_loaded', 'wcssint_admin_settings' );
add_action( 'wp_head', 'load_wcssint_script', 1);


// Create a function that will be called via wp_head hook,
// and print javascript within <head>. This code is used
// for tracking website visitors.

function load_wcssint_script() {
	
	// Fetch plugin options
	$wcssint_options = get_option( 'wcssint-options' );

    if ( !is_admin() ) {  
        $headscript = <<<WCSSINTINLINEHEAD
         	<script type="text/javascript">
				var _ss = _ss || [];
				_ss.push(['_setDomain', 'https://{$wcssint_options['domain_number']}.marketingautomation.services/net']);
				_ss.push(['_setAccount', '{$wcssint_options['account_number']}']);
				_ss.push(['_trackPageView']);
				(function() {
					var ss = document.createElement('script');
					ss.type = 'text/javascript'; ss.async = true;
					ss.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + '{$wcssint_options['domain_number']}.marketingautomation.services/client/ss.js?ver=1.1.1';
					var scr = document.getElementsByTagName('script')[0];
					scr.parentNode.insertBefore(ss, scr);
				})();
			</script>
WCSSINTINLINEHEAD;

		echo $headscript;
    }  
}  

add_action( 'woocommerce_thankyou', 'wcssint_ecomm_enabled' );

// Create a function that will be called on WooCommerce Thank You! page,
// ie. when the purchase has been marked as successful. This function
// adds another piece of JavaScript that tracks purchases on the website

function wcssint_ecomm_enabled( $order_id ) {

	// Fetch plugin options
	$wcssint_options = get_option( 'wcssint-options' );

	// Check whether SharpSpring should track purchases or not. If so,
	// add another script - if not, return(exit function).
	if($wcssint_options['ecomm_enable'] != "yes") {
		return;
	}

	// Grab order and its data
	$order = wc_get_order( $order_id );
	$total = $order->get_total();
	$tax = $order->get_total_tax();
	$order_data = $order->get_data();
    
    $sitename = get_bloginfo('name');
    $shipping_city = $order_data["billing"]["city"];
    $shipping_state = $order_data["billing"]["state"];
    $shipping_zip = $order_data["billing"]["postcode"];
    $shipping_country = $order_data["billing"]["country"];
    $shipping_fname = $order_data["billing"]["first_name"];
    $shipping_lname = $order_data["billing"]["last_name"];

	$thankyouscript_intro = "
         	<script type=\"text/javascript\">
				_ss.push(['_setTransaction', {
				'transactionID': '$order_id',
				'storeName': '$sitename',
				'total': '$total',
				'tax': '$tax',
				'shipping': '0.00',
				'city': '$shipping_city',
				'state': '$shipping_state',
				'zipcode': '$shipping_zip',
				'country': '$shipping_country',
				'firstName' : '$shipping_fname', // optional parameter
				'lastName' : '$shipping_lname', // optional parameter
				}]);";
	$thankyouscript_items = "";

	// Add a JS snippet for each and every item in the given order
	// Load product to get its original price

	foreach ($order->get_items() as $item_id => $item_data) {
		$_product = wc_get_product( $item_data['product_id'] );
		$item_name = $item_data["name"];
		$item_price = $item_data["total"];
		$item_q = $item_data["quantity"];
		$item_price = $_product->get_price();

		$thankyouscript_items .= "
			_ss.push(['_addTransactionItem', {
			'transactionID': '$order_id',
			'itemCode': '$item_id',
			'productName': '$item_name',
			'category': '',
			'price': '$item_price',
			'quantity': '$item_q'
			}]);
		";
	}

	$thankyouscript_outtro = "
				_ss.push(['_completeTransaction', {
				'transactionID': '$order_id'
				}]);
			</script>";

	// Output script
	echo $thankyouscript_intro . $thankyouscript_items . $thankyouscript_outtro;

}


/**
 * Starts the plugin.
 *
 * @since 1.0.0
 */

function wcssint_admin_settings() {

	$plugin = new WCSSINTSettingsPage( new Menu_Page() );
	$plugin->init();

}
