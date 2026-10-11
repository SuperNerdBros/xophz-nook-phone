<?php
/**
 * Reads credentials for connectors registered with the WordPress core Connectors API.
 *
 * Core exposes no public function that returns a key's value, so this follows core's own
 * precedence (environment variable, PHP constant, then the option) and takes every name from
 * the connector's `authentication` definition. Values are for server-side use only: never
 * echo them, log them, or return them from a REST route or render them into a page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Xophz_Nook_Phone_Connectors {

	const PATREON_CLIENT_ID     = 'patreon_client_id';
	const PATREON_CLIENT_SECRET = 'patreon_client_secret';

	/**
	 * The credential for a registered connector, or '' when it is unregistered or unset.
	 */
	public static function get_key( string $connector_id ): string {
		if ( ! function_exists( 'wp_get_connector' ) || ! wp_is_connector_registered( $connector_id ) ) {
			return '';
		}

		$connector = wp_get_connector( $connector_id );
		$auth      = is_array( $connector ) ? ( $connector['authentication'] ?? array() ) : array();

		$env_name      = (string) ( $auth['env_var_name'] ?? '' );
		$constant_name = (string) ( $auth['constant_name'] ?? '' );
		$setting_name  = (string) ( $auth['setting_name'] ?? '' );

		$candidates = array(
			'' !== $env_name ? getenv( $env_name ) : '',
			'' !== $constant_name && defined( $constant_name ) ? constant( $constant_name ) : '',
			'' !== $setting_name ? get_option( $setting_name, '' ) : '',
		);

		foreach ( $candidates as $candidate ) {
			$value = is_scalar( $candidate ) ? trim( (string) $candidate ) : '';
			if ( '' !== $value ) {
				return $value;
			}
		}

		return '';
	}

	public static function is_configured( string $connector_id ): bool {
		return '' !== self::get_key( $connector_id );
	}

	public static function connectors_url(): string {
		return admin_url( 'options-connectors.php' );
	}
}
