<?php

class Xophz_Nook_Phone_CPT {

	public function register_post_types() {
		$this->register_nook_app();
		$this->register_nook_passport();
		$this->register_nook_thread();
		$this->register_nook_dm();
	}

	private function register_nook_app() {
		$labels = array(
			'name'                  => 'Apps',
			'singular_name'         => 'App',
			'menu_name'             => 'Nook Apps',
			'name_admin_bar'        => 'App',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New App',
			'new_item'              => 'New App',
			'edit_item'             => 'Edit App',
			'view_item'             => 'View App',
			'all_items'             => 'All Apps',
			'search_items'          => 'Search Apps',
			'not_found'             => 'No apps found.',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'xophz-nook-phone',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'nook-app' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'nook-apps',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
		);

		register_post_type( 'nook_app', $args );
	}

	private function register_nook_passport() {
		$labels = array(
			'name'                  => 'Passports',
			'singular_name'         => 'Passport',
			'menu_name'             => 'Passports',
			'name_admin_bar'        => 'Passport',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New Passport',
			'new_item'              => 'New Passport',
			'edit_item'             => 'Edit Passport',
			'view_item'             => 'View Passport',
			'all_items'             => 'All Passports',
			'search_items'          => 'Search Passports',
			'not_found'             => 'No passports found.',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'xophz-nook-phone',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'nook-passport' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'nook-passports',
			'supports'           => array( 'title', 'author', 'thumbnail', 'custom-fields' ),
		);

		register_post_type( 'nook_passport', $args );
	}

	private function register_nook_thread() {
		$labels = array(
			'name'                  => 'Threads',
			'singular_name'         => 'Thread',
			'menu_name'             => 'Resident Chat',
			'name_admin_bar'        => 'Thread',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New Thread',
			'new_item'              => 'New Thread',
			'edit_item'             => 'Edit Thread',
			'view_item'             => 'View Thread',
			'all_items'             => 'All Threads',
			'search_items'          => 'Search Threads',
			'not_found'             => 'No threads found.',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'xophz-nook-phone',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'nook-thread' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'nook-threads',
			'supports'           => array( 'title', 'editor', 'author', 'comments', 'thumbnail', 'custom-fields' ),
		);

		register_post_type( 'nook_thread', $args );
	}

	private function register_nook_dm() {
		$labels = array(
			'name'                  => 'Direct Messages',
			'singular_name'         => 'Direct Message',
			'menu_name'             => 'DMs',
			'all_items'             => 'All DMs',
			'search_items'          => 'Search DMs',
			'not_found'             => 'No DMs found.',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'xophz-nook-phone',
			'query_var'          => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true, // Required to allow WP REST comments to be posted
			'supports'           => array( 'title', 'editor', 'author', 'custom-fields', 'comments' ),
		);

		register_post_type( 'nook_dm', $args );

		// Ensure comments are always open for nook_dm CPT so REST comments post succeeds
		add_filter( 'comments_open', function( $open, $post_id ) {
			$post = get_post( $post_id );
			if ( $post && $post->post_type === 'nook_dm' ) {
				return true;
			}
			return $open;
		}, 10, 2 );
	}

	public function populate_default_apps() {
		$system_apps = array(
			array(
				'title'       => 'Camera',
				'slug'        => 'camera',
				'description' => 'Take beautiful photos around your island with custom filters and frames.',
				'app_id'      => 'camera'
			),
			array(
				'title'       => 'AC Miles',
				'slug'        => 'ac-miles',
				'description' => 'Track your active Nook Miles achievements and milestones.',
				'app_id'      => 'miles'
			),
			array(
				'title'       => 'ACNH Critterpedia',
				'slug'        => 'acnh-critterpedia',
				'description' => 'Keep track of all caught and donated bugs, fish, and sea creatures.',
				'app_id'      => 'critter'
			),
			array(
				'title'       => 'DIY Recipes',
				'slug'        => 'diy-recipes',
				'description' => 'View recipes and required materials to craft tools and furniture.',
				'app_id'      => 'diy'
			),
			array(
				'title'       => 'Nook Shopping',
				'slug'        => 'nook-shopping',
				'description' => 'Order furniture, clothing, and utility items directly using your Bells.',
				'app_id'      => 'shopping'
			),
			array(
				'title'       => 'Happy Island Designer',
				'slug'        => 'happy-island-designer',
				'description' => 'Draw custom terrain paths and plan placing buildings on your island.',
				'app_id'      => 'designer'
			),
			array(
				'title'       => 'Animal Crossing Pattern Tool',
				'slug'        => 'animal-crossing-pattern-tool',
				'description' => 'Create and edit custom pixel patterns for clothing, path tiles, and signs.',
				'app_id'      => 'designs'
			),
			array(
				'title'       => 'Map',
				'slug'        => 'map',
				'description' => 'Interactive map showcasing your resident villagers and key buildings.',
				'app_id'      => 'map'
			),
			array(
				'title'       => 'Passport',
				'slug'        => 'passport',
				'description' => 'Showcase your player info, title, island name, and status updates.',
				'app_id'      => 'passport'
			),
			array(
				'title'       => 'Chat Log',
				'slug'        => 'chat-log',
				'description' => 'Review recent dialogue and system logs from residents and notifications.',
				'app_id'      => 'chat'
			),
			array(
				'title'       => 'Settings',
				'slug'        => 'settings',
				'description' => 'Configure ringtones, sound effects, theme wallpapers, and clock display modes.',
				'app_id'      => 'settings'
			),
			array(
				'title'       => 'Residential Recycle Box',
				'slug'        => 'residential-recycle-box',
				'description' => 'Access the residential catalog to search, install, and rate community web apps.',
				'app_id'      => 'directory'
			),
			array(
				'title'       => 'Messages',
				'slug'        => 'messages',
				'description' => 'Send and receive direct text messages with other players.',
				'app_id'      => 'messages'
			),
			array(
				'title'       => 'Contacts',
				'slug'        => 'contacts',
				'description' => 'Manage your villager directory, residency status, and friendship points.',
				'app_id'      => 'contacts'
			),
			array(
				'title'       => 'Best Friends',
				'slug'        => 'best-friends',
				'description' => 'Connect, chat, and keep track of your closest friends online.',
				'app_id'      => 'best_friends'
			),
			array(
				'title'       => 'Rescue Service',
				'slug'        => 'rescue-service',
				'description' => 'Help service to transport you back to safety when you get stuck.',
				'app_id'      => 'rescue'
			),
			array(
				'title'       => 'Changelog',
				'slug'        => 'changelog',
				'description' => 'Keep track of the latest NookOS updates, features, and improvements.',
				'app_id'      => 'changelog'
			),
		);

		foreach ( $system_apps as $app ) {
			$existing = get_posts( array(
				'post_type'   => 'nook_app',
				'name'        => $app['slug'],
				'post_status' => 'any',
				'numberposts' => 1
			) );

			if ( empty( $existing ) ) {
				$post_id = wp_insert_post( array(
					'post_title'   => $app['title'],
					'post_content' => $app['description'],
					'post_name'    => $app['slug'],
					'post_status'  => 'publish',
					'post_type'    => 'nook_app',
				) );

				if ( ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_nook_app_is_system', 'yes' );
					update_post_meta( $post_id, '_nook_app_app_id', $app['app_id'] );
					update_post_meta( $post_id, '_nook_app_installs', 2026 );
					update_post_meta( $post_id, '_nook_app_average_rating', 5.0 );
					update_post_meta( $post_id, '_nook_app_rating_count', 1 );
				}
			}
		}
	}
}
