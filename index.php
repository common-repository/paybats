<?php
/**
 * Plugin Name: PAYBATS
 * Plugin URI: https://www.paybats.com
 * Description: Enable online payments using credit or debit cards and online banking. Currently PAYBATS service is only available to businesses that reside in Malaysia.
 * Version: 3.1
 * Author: PAYBATS
 * WC requires at least: 2.6.0
 * WC tested up to: 3.8.1
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# Include paybats Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'paybats_init', 0 );

function paybats_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/paybats.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_paybats_to_woocommerce' );
	function add_paybats_to_woocommerce( $methods ) {
		$methods[] = 'paybats';

		return $methods;
	}
}

# Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'paybats_links' );

function paybats_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=paybats' ) . '">' . __( 'Settings', 'paybats' ) . '</a>',
	);

	# Merge our new link with the default ones
	return array_merge( $plugin_links, $links );
}

add_action( 'init', 'paybats_check_response', 15 );

function paybats_check_response() {
	# If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/paybats.php' );

	$paybats = new paybats();
	$paybats->check_paybats_response();
}

function paybats_hash_error_msg( $content ) {
	return '<div class="woocommerce-error">The data that we received is invalid. Thank you.</div>' . $content;
}

function paybats_payment_declined_msg( $content ) {
	return '<div class="woocommerce-error">The payment was declined. Please check with your bank. Thank you.</div>' . $content;
}

function paybats_success_msg( $content ) {
	return '<div class="woocommerce-info">The payment was successful. Thank you.</div>' . $content;
}
