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
			'show_in_rest'       => false, // We use custom endpoints
			'supports'           => array( 'title', 'editor', 'author', 'custom-fields' ),
		);

		register_post_type( 'nook_dm', $args );
	}
}
