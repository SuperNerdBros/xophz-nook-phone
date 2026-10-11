<?php

class Xophz_Nook_Phone_Admin {

	private $plugin_name;
	private $version;

	const OPTION_LOAD_MODE = 'xophz_nook_phone_load_mode';
	const OPTION_LOAD_PAGE = 'xophz_nook_phone_load_page_id';
	const OPTION_CUSTOM_SLUG = 'xophz_nook_phone_custom_slug';
	const OPTION_DESIGNER_SLUG = 'xophz_nook_phone_designer_slug';

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function add_plugin_admin_menu() {
		add_menu_page(
			'Nook OS Settings',
			'Nook OS',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' ),
			'dashicons-smartphone',
			30
		);
	}

	public function register_settings() {
		register_setting( 'xophz_nook_phone_settings', self::OPTION_LOAD_MODE, [
			'type' => 'string',
			'default' => 'routes_only',
			'sanitize_callback' => array( $this, 'sanitize_load_mode' )
		] );

		register_setting( 'xophz_nook_phone_settings', self::OPTION_LOAD_PAGE, [
			'type' => 'integer',
			'default' => 0,
			'sanitize_callback' => 'absint'
		] );

		register_setting( 'xophz_nook_phone_settings', self::OPTION_CUSTOM_SLUG, [
			'type' => 'string',
			'default' => 'nookphone',
			'sanitize_callback' => 'sanitize_title'
		] );

		register_setting( 'xophz_nook_phone_settings', self::OPTION_DESIGNER_SLUG, [
			'type' => 'string',
			'default' => 'island-designer',
			'sanitize_callback' => 'sanitize_title'
		] );

		// The Patreon client ID and secret are the patreon_client_id / patreon_client_secret
		// connectors (Settings > Connectors). Core registers their settings; none is registered here.

		add_settings_section(
			'xophz_nook_phone_main_section',
			'App Routing Configuration',
			array( $this, 'render_section_description' ),
			'xophz-nook-phone-settings'
		);

		add_settings_field(
			'xophz_nook_phone_load_mode_field',
			'Load App On',
			array( $this, 'render_load_mode_field' ),
			'xophz-nook-phone-settings',
			'xophz_nook_phone_main_section'
		);

		add_settings_field(
			'xophz_nook_phone_load_page_field',
			'Target Page',
			array( $this, 'render_load_page_field' ),
			'xophz-nook-phone-settings',
			'xophz_nook_phone_main_section'
		);

		add_settings_field(
			'xophz_nook_phone_designer_slug_field',
			'Island Designer Slug',
			array( $this, 'render_designer_slug_field' ),
			'xophz-nook-phone-settings',
			'xophz_nook_phone_main_section'
		);

		add_settings_section(
			'xophz_nook_phone_patreon_section',
			'Patreon',
			array( $this, 'render_patreon_section_description' ),
			'xophz-nook-phone-settings'
		);

		add_settings_field(
			'xophz_nook_phone_patreon_credentials_field',
			'Client Credentials',
			array( $this, 'render_patreon_credentials_field' ),
			'xophz-nook-phone-settings',
			'xophz_nook_phone_patreon_section'
		);
	}

	public function sanitize_load_mode( $value ) {
		$validModes = [ 'routes_only', 'homepage', 'specific_page', 'custom_slug' ];
		return in_array( $value, $validModes, true ) ? $value : 'routes_only';
	}

	public function flush_rewrites_on_save( $old_value, $new_value ) {
		if ( $old_value !== $new_value ) {
			delete_option( 'rewrite_rules' );
		}
	}

	public function render_section_description() {
		echo '<p>Configure how and where the Nook OS (NookPhone) application is loaded on your WordPress site.</p>';
	}

	public function render_load_mode_field() {
		$currentMode = get_option( self::OPTION_LOAD_MODE, 'routes_only' );
		$customSlug = get_option( self::OPTION_CUSTOM_SLUG, 'nookphone' );
		?>
		<fieldset>
			<label>
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="routes_only" <?php checked( $currentMode, 'routes_only' ); ?>>
				<strong>Routes Only</strong> - No default slug. Uses core API endpoints.
			</label><br>
			<label style="display: flex; align-items: center; gap: 8px; margin: 8px 0;">
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="custom_slug" <?php checked( $currentMode, 'custom_slug' ); ?>>
				<strong>Custom Slug</strong> - 
				<code>/</code> <input type="text" id="xophz_nook_phone_custom_slug_input" name="<?php echo self::OPTION_CUSTOM_SLUG; ?>" value="<?php echo esc_attr( $customSlug ); ?>" class="regular-text" placeholder="e.g. nookphone" style="width: 150px;" /> <code>/</code>
			</label>
			<label>
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="homepage" <?php checked( $currentMode, 'homepage' ); ?>>
				<strong>Homepage</strong> - Replace the site's front page with Nook OS.
			</label><br>
			<label>
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="specific_page" <?php checked( $currentMode, 'specific_page' ); ?>>
				<strong>Specific Page</strong> - Load on a chosen WordPress page.
			</label>
		</fieldset>
		<?php
	}

	public function render_load_page_field() {
		$selectedPageId = get_option( self::OPTION_LOAD_PAGE, 0 );
		
		wp_dropdown_pages( [
			'name' => self::OPTION_LOAD_PAGE,
			'selected' => $selectedPageId,
			'show_option_none' => '- Select a Page -',
			'option_none_value' => '0',
		] );
		?>
		<p class="description">Only used when "Specific Page" is selected above.</p>
		<script>
		(function() {
			const modeRadios = document.querySelectorAll('input[name="<?php echo self::OPTION_LOAD_MODE; ?>"]');
			const pageDropdown = document.querySelector('select[name="<?php echo self::OPTION_LOAD_PAGE; ?>"]');
			const customSlugInput = document.getElementById('xophz_nook_phone_custom_slug_input');

			function updateVisibility() {
				const selectedMode = document.querySelector('input[name="<?php echo self::OPTION_LOAD_MODE; ?>"]:checked').value;
				pageDropdown.disabled = (selectedMode !== 'specific_page');
				customSlugInput.disabled = (selectedMode !== 'custom_slug');
			}

			modeRadios.forEach(radio => radio.addEventListener('change', updateVisibility));
			updateVisibility();
		})();
		</script>
		<?php
	}

	public function render_designer_slug_field() {
		$designerSlug = get_option( self::OPTION_DESIGNER_SLUG, 'island-designer' );
		?>
		<code>/</code> <input type="text" name="<?php echo self::OPTION_DESIGNER_SLUG; ?>" value="<?php echo esc_attr( $designerSlug ); ?>" class="regular-text" placeholder="e.g. island-designer" style="width: 250px;" /> <code>/</code>
		<p class="description">The URL slug to use for the separate Island Designer application.</p>
		<?php
	}

	public function render_patreon_section_description() {
		echo '<p>Patreon API credentials enable the Patreon connection for users.</p>';
	}

	public function render_patreon_credentials_field() {
		$hasClientId     = Xophz_Nook_Phone_Connectors::is_configured( Xophz_Nook_Phone_Connectors::PATREON_CLIENT_ID );
		$hasClientSecret = Xophz_Nook_Phone_Connectors::is_configured( Xophz_Nook_Phone_Connectors::PATREON_CLIENT_SECRET );
		?>
		<p>Client ID: <strong><?php echo $hasClientId ? 'Configured' : 'Not configured'; ?></strong></p>
		<p>Client Secret: <strong><?php echo $hasClientSecret ? 'Configured' : 'Not configured'; ?></strong></p>
		<p class="description">
			Manage the <strong>Patreon Client ID</strong> and <strong>Patreon Client Secret</strong> connectors in
			<a href="<?php echo esc_url( Xophz_Nook_Phone_Connectors::connectors_url() ); ?>">Settings &gt; Connectors</a>.
			The secret is never shown on this page.
		</p>
		<?php
	}

	public function display_plugin_setup_page() {
		?>
		<div class="wrap">
			<h1>Nook OS Settings</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'xophz_nook_phone_settings' );
				do_settings_sections( 'xophz-nook-phone-settings' );
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}
}
