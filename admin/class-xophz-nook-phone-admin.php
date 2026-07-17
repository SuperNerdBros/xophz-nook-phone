<?php

class Xophz_Nook_Phone_Admin {

	private $plugin_name;
	private $version;

	const OPTION_LOAD_MODE = 'xophz_nook_phone_load_mode';
	const OPTION_LOAD_PAGE = 'xophz_nook_phone_load_page_id';
	const OPTION_CUSTOM_SLUG = 'xophz_nook_phone_custom_slug';

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
				<strong>Routes Only</strong> — No default slug. Uses core API endpoints.
			</label><br>
			<label style="display: flex; align-items: center; gap: 8px; margin: 8px 0;">
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="custom_slug" <?php checked( $currentMode, 'custom_slug' ); ?>>
				<strong>Custom Slug</strong> — 
				<code>/</code> <input type="text" id="xophz_nook_phone_custom_slug_input" name="<?php echo self::OPTION_CUSTOM_SLUG; ?>" value="<?php echo esc_attr( $customSlug ); ?>" class="regular-text" placeholder="e.g. nookphone" style="width: 150px;" /> <code>/</code>
			</label>
			<label>
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="homepage" <?php checked( $currentMode, 'homepage' ); ?>>
				<strong>Homepage</strong> — Replace the site's front page with Nook OS.
			</label><br>
			<label>
				<input type="radio" name="<?php echo self::OPTION_LOAD_MODE; ?>" value="specific_page" <?php checked( $currentMode, 'specific_page' ); ?>>
				<strong>Specific Page</strong> — Load on a chosen WordPress page.
			</label>
		</fieldset>
		<?php
	}

	public function render_load_page_field() {
		$selectedPageId = get_option( self::OPTION_LOAD_PAGE, 0 );
		
		wp_dropdown_pages( [
			'name' => self::OPTION_LOAD_PAGE,
			'selected' => $selectedPageId,
			'show_option_none' => '— Select a Page —',
			'option_none_value' => '0',
		] );
		?>
		<p class="description">Only used when "Specific Page" is selected above.</p>
		<script>
		(function() {
			const radios = document.querySelectorAll('input[name="<?php echo self::OPTION_LOAD_MODE; ?>"]');
			const pageRow = document.getElementById('<?php echo self::OPTION_LOAD_PAGE; ?>').closest('tr');
			const slugInput = document.getElementById('xophz_nook_phone_custom_slug_input');

			function toggleDropdowns() {
				const selected = document.querySelector('input[name="<?php echo self::OPTION_LOAD_MODE; ?>"]:checked');
				const isSpecificPage = selected && selected.value === 'specific_page';
				const isCustomSlug = selected && selected.value === 'custom_slug';
				
				if (pageRow) pageRow.style.display = isSpecificPage ? '' : 'none';
				if (slugInput) {
					slugInput.disabled = !isCustomSlug;
					if (isCustomSlug) slugInput.focus();
				}
			}

			radios.forEach(function(radio) { radio.addEventListener('change', toggleDropdowns); });
			toggleDropdowns();
		})();
		</script>
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
