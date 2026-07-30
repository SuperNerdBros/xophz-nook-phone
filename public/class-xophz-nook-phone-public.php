<?php

class Xophz_Nook_Phone_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function register_endpoints() {
		$loadMode = get_option( 'xophz_nook_phone_load_mode', 'routes_only' );
		$custom_slug = get_option( 'xophz_nook_phone_custom_slug', 'nookphone' );

		if ( $loadMode === 'custom_slug' && ! empty( $custom_slug ) ) {
			add_rewrite_rule( '^' . preg_quote( $custom_slug, '/' ) . '(/.*)?$', 'index.php?xophz_nook_phone=1', 'top' );
		}

		$designer_slug = get_option( 'xophz_nook_phone_designer_slug', 'island-designer' );
		if ( ! empty( $designer_slug ) ) {
			add_rewrite_rule( '^' . preg_quote( $designer_slug, '/' ) . '(/.*)?$', 'index.php?xophz_nook_phone_designer=1', 'top' );
		}

		add_rewrite_rule( '^sw\.js$', 'index.php?xophz_nook_phone_sw=1', 'top' );
		add_rewrite_rule( '^(workbox-.*\.js)$', 'index.php?xophz_nook_phone_workbox=$matches[1]', 'top' );
	}

	public function register_query_vars( $vars ) {
		$vars[] = 'xophz_nook_phone';
		$vars[] = 'xophz_nook_phone_designer';
		$vars[] = 'xophz_nook_phone_sw';
		$vars[] = 'xophz_nook_phone_workbox';
		return $vars;
	}

	public function template_redirect() {
		global $wp_query;

		// Do not intercept WordPress admin or login routes.
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $request_uri, '/wp-admin' ) === 0 || strpos( $request_uri, '/wp-login.php' ) === 0 ) {
			return;
		}

		if ( get_query_var( 'xophz_nook_phone_sw' ) ) {
			$file = XOPHZ_NOOK_PHONE_PATH . 'public/dist/sw.js';
			if ( file_exists( $file ) ) {
				header( 'Content-Type: application/javascript' );
				header( 'Service-Worker-Allowed: /' );
				echo file_get_contents( $file );
				exit;
			}
		}

		$workbox_file = get_query_var( 'xophz_nook_phone_workbox' );
		if ( ! empty( $workbox_file ) ) {
			$workbox_file = preg_replace( '/[^a-zA-Z0-9_-]/', '', str_replace( '.js', '', $workbox_file ) ) . '.js';
			$file = XOPHZ_NOOK_PHONE_PATH . 'public/dist/' . $workbox_file;
			if ( file_exists( $file ) ) {
				header( 'Content-Type: application/javascript' );
				echo file_get_contents( $file );
				exit;
			}
		}
		
		$isRouteMatch = isset( $wp_query->query_vars['xophz_nook_phone'] );
		$isDesignerRouteMatch = isset( $wp_query->query_vars['xophz_nook_phone_designer'] );
		$isConfiguredPageMatch = $this->is_configured_page();
		
		$loadMode = get_option( 'xophz_nook_phone_load_mode', 'routes_only' );
		$isHomepage404Fallback = ( $loadMode === 'homepage' && is_404() );

		if ( $isDesignerRouteMatch ) {
			status_header( 200 );
			$wp_query->is_404 = false;
			$designer_slug = get_option( 'xophz_nook_phone_designer_slug', 'island-designer' );
			$this->render_island_designer_shell( $designer_slug );
			exit;
		}

		if ( $isRouteMatch || $isConfiguredPageMatch || $isHomepage404Fallback ) {
			status_header( 200 );
			$wp_query->is_404 = false;

			$app_base = $this->resolve_app_base( $isRouteMatch );
			$this->render_nook_phone_shell( $app_base );
			exit;
		}
	}

	private function is_configured_page() {
		$loadMode = get_option( 'xophz_nook_phone_load_mode', 'routes_only' );
		$isHomepageMode = $loadMode === 'homepage' && is_front_page();

		$targetPageId = (int) get_option( 'xophz_nook_phone_load_page_id', 0 );
		$isSpecificPageMode = $loadMode === 'specific_page' && $targetPageId > 0 && is_page( $targetPageId );

		return $isHomepageMode || $isSpecificPageMode;
	}

	private function resolve_app_base( $isRouteMatch ) {
		if ( $isRouteMatch ) {
			$loadMode = get_option( 'xophz_nook_phone_load_mode', 'routes_only' );
			$custom_slug = get_option( 'xophz_nook_phone_custom_slug', 'nookphone' );
			$requestPath = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ?: '', '/' );
			
			if ( $loadMode === 'custom_slug' && ! empty( $custom_slug ) && strpos( $requestPath, $custom_slug ) === 0 ) {
				return $custom_slug;
			}
			return 'nookphone';
		}
		
		$loadMode = get_option( 'xophz_nook_phone_load_mode', 'routes_only' );
		if ( $loadMode === 'homepage' ) {
			return '';
		}

		return trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ?: '', '/' );
	}

	private function is_dev_mode() {
		return ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

	private function render_nook_phone_shell( $app_base ) {
		$base_url = home_url( '/' . ltrim( $app_base, '/' ) );
		$is_dev = $this->is_dev_mode();
		
		// Setup Vite Dev server URL dynamically based on WP host
		$wp_host = wp_parse_url( home_url(), PHP_URL_HOST );
		$vite_port = '8085';
		$vite_url = "//" . $wp_host . ":" . $vite_port;

		if ( $is_dev ) {
			$internal_host = 'compass';
			$dev_html = @file_get_contents("http://{$internal_host}:{$vite_port}/");
			if ($dev_html) {
				// Rewrite relative src/href for dev server
				$dev_html = str_replace('src="/', 'src="' . $vite_url . '/', $dev_html);
				$dev_html = str_replace('href="/', 'href="' . $vite_url . '/', $dev_html);
				$dev_html = str_replace('import("/', 'import("' . $vite_url . '/', $dev_html);
				
				// Inject Vite client
				if (strpos($dev_html, '/@vite/client') === false) {
					$vite_client = '<script type="module" src="' . esc_url($vite_url) . '/@vite/client"></script>';
					$dev_html = str_replace('</head>', $vite_client . "\n</head>", $dev_html);
				}

				$nonce = wp_create_nonce('wp_rest');
				$user_id = get_current_user_id();
				$wp_api_settings = "<script>window.wpApiSettings = { root: '" . esc_url_raw(rest_url()) . "', nonce: '" . $nonce . "', pluginUrl: '" . esc_url_raw(XOPHZ_NOOK_PHONE_URL) . "', version: '" . esc_js($this->version) . "', userId: " . $user_id . " };</script>";
				$dev_html = str_replace('</head>', $wp_api_settings . "\n</head>", $dev_html);
				
				echo $dev_html;
				exit;
			}
		}

		// Production Mode: Load the index.html generated by Vite/SvelteKit/React
		$index_file = XOPHZ_NOOK_PHONE_PATH . 'public/dist/index.html';
		if ( file_exists( $index_file ) ) {
			$html = file_get_contents( $index_file );
			$dist_url = XOPHZ_NOOK_PHONE_URL . 'public/dist/';
			
			// Replace hardcoded domain with actual dynamic URL for social/SEO tags
			// DO THIS FIRST so we don't accidentally replace the domain inside $dist_url
			$current_url = home_url( $_SERVER['REQUEST_URI'] );
			$html = str_replace( 'https://nookphone.app/', esc_url( $current_url ), $html );

			// Replace standard dist paths
			$html = str_replace( '"/assets/', '"' . $dist_url . 'assets/', $html );
			$html = str_replace( "'/assets/", "'" . $dist_url . "assets/", $html );
			$html = str_replace( '"/_app/', '"' . $dist_url . '_app/', $html );
			$html = str_replace( "'/_app/", "'" . $dist_url . "_app/", $html );
			$html = str_replace( '"/favicon', '"' . $dist_url . 'favicon', $html );
			$html = str_replace( '"/manifest.webmanifest"', '"' . $dist_url . 'manifest.webmanifest"', $html );

			
			// Dynamically update router base if needed (e.g. for SvelteKit base)
			$app_base_slash = $app_base ? '/' . ltrim( $app_base, '/' ) : '';
			$html = preg_replace( '/base:\s*""/', 'base: "' . esc_js( $app_base_slash ) . '"', $html );
			
			// Inject WP API Settings
			$nonce = wp_create_nonce('wp_rest');
			$user_id = get_current_user_id();
			$wp_api_settings = "<script>window.wpApiSettings = { root: '" . esc_url_raw(rest_url()) . "', nonce: '" . $nonce . "', pluginUrl: '" . esc_url_raw(XOPHZ_NOOK_PHONE_URL) . "', version: '" . esc_js($this->version) . "', userId: " . $user_id . " };</script>";
			$html = str_replace('</head>', $wp_api_settings . "\n</head>", $html);

			echo $html;
			exit;
		} else {
			echo '<p>Nook OS production build not found.</p>';
			exit;
		}
	}

	private function render_island_designer_shell( $app_base ) {
		$index_file = XOPHZ_NOOK_PHONE_PATH . 'public/dist-designer/index.html';
		if ( file_exists( $index_file ) ) {
			$html = file_get_contents( $index_file );
			$dist_url = XOPHZ_NOOK_PHONE_URL . 'public/dist-designer/';
			
			// Inject base href for resolving relative assets correctly in JS
			$html = str_replace( '<head>', '<head><base href="' . esc_url( $dist_url ) . '">', $html );
			
			// Replace standard dist and static paths
			$html = str_replace( 'src="dist/', 'src="' . $dist_url . 'dist/', $html );
			$html = str_replace( 'href="dist/', 'href="' . $dist_url . 'dist/', $html );
			$html = str_replace( 'src="./static/', 'src="' . $dist_url . 'static/', $html );
			$html = str_replace( 'href="./static/', 'href="' . $dist_url . 'static/', $html );
			$html = str_replace( 'src="static/', 'src="' . $dist_url . 'static/', $html );
			$html = str_replace( 'href="static/', 'href="' . $dist_url . 'static/', $html );
			
			// Replace official URL references with our local dist URL
			$html = str_replace( 'https://eugeneration.github.io/HappyIslandDesigner/', $dist_url, $html );
			
			// Inject WP API Settings
			$nonce = wp_create_nonce('wp_rest');
			$user_id = get_current_user_id();
			$wp_api_settings = "<script>window.wpApiSettings = { root: '" . esc_url_raw(rest_url()) . "', nonce: '" . $nonce . "', pluginUrl: '" . esc_url_raw(XOPHZ_NOOK_PHONE_URL) . "', version: '" . esc_js($this->version) . "', userId: " . $user_id . " };</script>";
			$html = str_replace('</head>', $wp_api_settings . "\n</head>", $html);

			echo $html;
			exit;
		} else {
			echo '<p>Island Designer production build not found.</p>';
			exit;
		}
	}
}
