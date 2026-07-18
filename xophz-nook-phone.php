<?php
/**
 * Plugin Name:       Nook OS
 * Description:       Standalone WordPress backend and router for the Nook OS (NookPhone) web app.
 * Version:           26.7.18.157
 * Author:            Hall of the Gods, Inc.
 * Text Domain:       xophz-nook-phone
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'XOPHZ_NOOK_PHONE_VERSION', '26.7.18.157' );
define( 'XOPHZ_NOOK_PHONE_PATH', plugin_dir_path( __FILE__ ) );
define( 'XOPHZ_NOOK_PHONE_URL', plugin_dir_url( __FILE__ ) );

require_once XOPHZ_NOOK_PHONE_PATH . 'admin/class-xophz-nook-phone-admin.php';
require_once XOPHZ_NOOK_PHONE_PATH . 'public/class-xophz-nook-phone-public.php';
require_once XOPHZ_NOOK_PHONE_PATH . 'includes/class-xophz-nook-phone-rest.php';
require_once XOPHZ_NOOK_PHONE_PATH . 'includes/class-xophz-nook-phone-cpt.php';

function run_xophz_nook_phone() {
    $cpt = new Xophz_Nook_Phone_CPT();
    add_action( 'init', array( $cpt, 'register_post_types' ) );
    add_action( 'init', array( $cpt, 'populate_default_apps' ), 20 );

    $admin = new Xophz_Nook_Phone_Admin( 'xophz-nook-phone', XOPHZ_NOOK_PHONE_VERSION );
    add_action( 'admin_menu', array( $admin, 'add_plugin_admin_menu' ) );
    add_action( 'admin_init', array( $admin, 'register_settings' ) );
    add_action( 'update_option_xophz_nook_phone_load_mode', array( $admin, 'flush_rewrites_on_save' ), 10, 2 );
    add_action( 'update_option_xophz_nook_phone_custom_slug', array( $admin, 'flush_rewrites_on_save' ), 10, 2 );
    add_action( 'update_option_xophz_nook_phone_designer_slug', array( $admin, 'flush_rewrites_on_save' ), 10, 2 );

    $public = new Xophz_Nook_Phone_Public( 'xophz-nook-phone', XOPHZ_NOOK_PHONE_VERSION );
    add_action( 'init', array( $public, 'register_endpoints' ) );
    add_filter( 'query_vars', array( $public, 'register_query_vars' ) );
    add_action( 'template_redirect', array( $public, 'template_redirect' ) );

    $rest = new Xophz_Nook_Phone_REST();
    add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
}

add_action( 'plugins_loaded', 'run_xophz_nook_phone' );

function xophz_nook_phone_plugin_action_links( $links ) {
    $settings_link = '<a href="admin.php?page=xophz-nook-phone">' . __( 'Settings', 'xophz-nook-phone' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'xophz_nook_phone_plugin_action_links' );
