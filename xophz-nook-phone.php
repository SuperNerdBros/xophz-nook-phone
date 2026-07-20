<?php
/**
 * Plugin Name:       Nook OS
 * Description:       Standalone WordPress backend and router for the Nook OS (NookPhone) web app.
 * Version:           26.7.20.147
 * Author:            Hall of the Gods, Inc.
 * Text Domain:       xophz-nook-phone
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'XOPHZ_NOOK_PHONE_VERSION', '26.7.20.147' );
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

    // Register nightly background sync for the Nookipedia catalog
    add_action( 'xophz_nook_sync_catalog_cron', array( $rest, 'sync_nookipedia_catalog' ) );
    if ( ! wp_next_scheduled( 'xophz_nook_sync_catalog_cron' ) ) {
        wp_schedule_event( time(), 'daily', 'xophz_nook_sync_catalog_cron' );
    }

    // Register with WP Connectors API
    add_action( 'wp_connectors_init', function( $registry ) {
        if ( method_exists( $registry, 'register' ) ) {
            $registry->register(
                'patreon_client_id',
                array(
                    'name'           => 'Patreon Client ID',
                    'description'    => 'Client ID for Patreon OAuth integration.',
                    'type'           => 'oauth',
                    'authentication' => array(
                        'method'       => 'api_key',
                        'setting_name' => 'xophz_nook_phone_patreon_client_id',
                    ),
                )
            );
            $registry->register(
                'patreon_client_secret',
                array(
                    'name'           => 'Patreon Client Secret',
                    'description'    => 'Client Secret for Patreon OAuth.',
                    'type'           => 'oauth',
                    'authentication' => array(
                        'method'       => 'api_key',
                        'setting_name' => 'xophz_nook_phone_patreon_client_secret',
                    ),
                )
            );
        }
    } );
}

add_action( 'plugins_loaded', 'run_xophz_nook_phone' );

function xophz_nook_phone_plugin_action_links( $links ) {
    $settings_link = '<a href="admin.php?page=xophz-nook-phone">' . __( 'Settings', 'xophz-nook-phone' ) . '</a>';
    $update_url = wp_nonce_url( admin_url( 'admin-post.php?action=xophz_nook_phone_force_update' ), 'xophz_nook_phone_force_update' );
    $update_link = '<a href="' . esc_url( $update_url ) . '" style="color: #d63638;">' . __( 'Force Update', 'xophz-nook-phone' ) . '</a>';
    
    array_unshift( $links, $update_link );
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'xophz_nook_phone_plugin_action_links' );

function xophz_nook_phone_handle_force_update() {
    if ( ! current_user_can( 'update_plugins' ) ) {
        wp_die( 'You do not have permission to update plugins.' );
    }
    
    check_admin_referer( 'xophz_nook_phone_force_update' );
    
    $version = XOPHZ_NOOK_PHONE_VERSION;
    
    $response = wp_remote_get( 'https://github.com/SuperNerdBros/xophz-nook-phone/tags' );
    if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
        $body = wp_remote_retrieve_body( $response );
        if ( preg_match( '/\/SuperNerdBros\/xophz-nook-phone\/releases\/tag\/(v?[0-9\.]+)/i', $body, $matches ) ) {
            $version = ltrim( $matches[1], 'v' );
        }
    }
    
    $zip_url = "https://github.com/SuperNerdBros/xophz-nook-phone/releases/download/v{$version}/xophz-nook-phone-{$version}.zip";
    
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/misc.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    
    add_filter( 'site_transient_update_plugins', function( $transient ) use ( $zip_url ) {
        $plugin_slug = plugin_basename( XOPHZ_NOOK_PHONE_PATH . 'xophz-nook-phone.php' );
        
        $obj = new stdClass();
        $obj->slug = 'xophz-nook-phone';
        $obj->plugin = $plugin_slug;
        $obj->new_version = XOPHZ_NOOK_PHONE_VERSION . '.' . time();
        $obj->url = '';
        $obj->package = $zip_url;
        
        if ( ! is_object( $transient ) ) {
            $transient = new stdClass();
        }
        if ( ! isset( $transient->response ) ) {
            $transient->response = array();
        }
        $transient->response[$plugin_slug] = $obj;
        
        return $transient;
    } );
    
    $upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( array(
        'title'  => 'Force Updating Nook OS Plugin',
        'plugin' => plugin_basename( XOPHZ_NOOK_PHONE_PATH . 'xophz-nook-phone.php' ),
    ) ) );
    
    $upgrader->upgrade( plugin_basename( XOPHZ_NOOK_PHONE_PATH . 'xophz-nook-phone.php' ) );
    
    echo '<p><a href="' . admin_url( 'plugins.php' ) . '" class="button button-primary">Return to Plugins</a></p>';
    exit;
}
add_action( 'admin_post_xophz_nook_phone_force_update', 'xophz_nook_phone_handle_force_update' );
