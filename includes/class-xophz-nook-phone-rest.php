<?php

class Xophz_Nook_Phone_REST {

	public function register_routes() {
		$namespace = 'xophz/v1';
		
		// Install an app
		register_rest_route( $namespace, '/nook-phone/install', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'record_install' ),
			'permission_callback' => '__return_true', // Open for now, or require auth if PRO
		) );

		// Get all apps
		register_rest_route( $namespace, '/nook-phone/apps', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_apps' ),
			'permission_callback' => '__return_true',
		) );

		// Rate an app
		register_rest_route( $namespace, '/nook-phone/rate', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'record_rating' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

        // Link passport
        register_rest_route( $namespace, '/nook-phone/passport/link', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'link_passport' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

        // Delete passport
        register_rest_route( $namespace, '/nook-phone/passport/delete', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'delete_passport' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		// Get user passports
		register_rest_route( $namespace, '/nook-phone/passports', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_passports' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		// Get resident chat threads
		register_rest_route( $namespace, '/nook-phone/threads', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_threads' ),
			'permission_callback' => '__return_true',
		) );

		// Create resident chat thread
		register_rest_route( $namespace, '/nook-phone/threads', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'create_thread' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		// Tip resident chat thread
		register_rest_route( $namespace, '/nook-phone/threads/tip', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'tip_thread' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		// Board Status and Donations
		register_rest_route( $namespace, '/nook-phone/boards/status', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_board_status' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/nook-phone/boards/donate', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'donate_to_board' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		// DMs
		register_rest_route( $namespace, '/nook-phone/dms/all', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_all_dms' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		register_rest_route( $namespace, '/nook-phone/dms/delete', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'delete_dms' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		register_rest_route( $namespace, '/nook-phone/dms/conversations', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_dm_conversations' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		register_rest_route( $namespace, '/nook-phone/dms/(?P<user_id>\d+)', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_dms_with_user' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		register_rest_route( $namespace, '/nook-phone/dms/send', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'send_dm' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		register_rest_route( $namespace, '/nook-phone/users', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nook_users' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		// NookOS State Sync
		register_rest_route( $namespace, '/nook-phone/state', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nook_state' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );

		register_rest_route( $namespace, '/nook-phone/state', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'save_nook_state' ),
			'permission_callback' => function () { return is_user_logged_in(); },
		) );
		// Nookipedia Recipes Proxy
		register_rest_route( $namespace, '/nookipedia/recipes', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nookipedia_recipes' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/nookipedia/materials', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nookipedia_materials' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/nookipedia/items', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nookipedia_items' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route(
			'xophz/v1',
			'/acnh/villagers',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_acnh_villagers' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route( $namespace, '/acnh/items', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_acnh_items' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/nookipedia/villagers', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nookipedia_villagers' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/auth/patreon/nook-phone/url', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_patreon_auth_url' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/auth/patreon/nook-phone/callback', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'handle_patreon_callback' ),
			'permission_callback' => '__return_true',
		) );
	}

	public function get_patreon_auth_url( WP_REST_Request $request ) {
		$client_id = get_option('xophz_nook_phone_patreon_client_id');
		if ( ! $client_id ) {
			return new WP_Error( 'missing_client_id', 'Patreon client ID is not configured', array( 'status' => 500 ) );
		}
		
		$return_url = $request->get_param( 'return_url' );
		if ( empty( $return_url ) ) {
			// default to nookphone if none provided
			$return_url = home_url( '/' . get_option( 'xophz_nook_phone_custom_slug', 'nookphone' ) );
		}
		
		$redirect_uri = site_url( '/wp-json/xophz/v1/auth/patreon/nook-phone/callback' );
		$url = 'https://www.patreon.com/oauth2/authorize?response_type=code&client_id=' . urlencode($client_id) . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=' . rawurlencode('identity identity[email]') . '&state=' . urlencode(base64_encode($return_url));
		
		return rest_ensure_response( array(
			'success' => true,
			'url'     => $url
		) );
	}

	public function handle_patreon_callback( WP_REST_Request $request ) {
		$code = $request->get_param( 'code' );
		$state = $request->get_param( 'state' );
		$error = $request->get_param( 'error' );
		
		$redirect_url = home_url( '/' . get_option( 'xophz_nook_phone_custom_slug', 'nookphone' ) );
		if ( ! empty( $state ) ) {
			$decoded_state = base64_decode( $state );
			if ( $decoded_state && filter_var( $decoded_state, FILTER_VALIDATE_URL ) ) {
				$redirect_url = $decoded_state;
			}
		}
		
		if ( $error || ! $code ) {
			$error_code = $error ? sanitize_text_field( $error ) : 'missing_code';
			wp_redirect( add_query_arg( 'error', $error_code, $redirect_url ) );
			exit;
		}

		$client_id = get_option('xophz_nook_phone_patreon_client_id');
		$client_secret = get_option('xophz_nook_phone_patreon_client_secret');
		$redirect_uri = site_url( '/wp-json/xophz/v1/auth/patreon/nook-phone/callback' );

		// Exchange code for token
		$token_url = 'https://www.patreon.com/api/oauth2/token';
		$body = array(
			'code'          => $code,
			'grant_type'    => 'authorization_code',
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'redirect_uri'  => $redirect_uri,
		);

		$response = wp_remote_post( $token_url, array(
			'body' => $body
		) );

		if ( is_wp_error( $response ) ) {
			wp_redirect( add_query_arg( 'error', 'token_error', $redirect_url ) );
			exit;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['access_token'] ) ) {
			wp_redirect( add_query_arg( 'error', 'invalid_token_response', $redirect_url ) );
			exit;
		}

		$access_token = $data['access_token'];
		
		// Fetch user identity with memberships
		$identity_url = 'https://www.patreon.com/api/oauth2/v2/identity?include=memberships.currently_entitled_tiers&fields[user]=email,full_name&fields[tier]=amount_cents,title';
		$identity_response = wp_remote_get( $identity_url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $access_token
			)
		) );

		if ( is_wp_error( $identity_response ) ) {
			wp_redirect( add_query_arg( 'error', 'identity_error', $redirect_url ) );
			exit;
		}

		$identity_body = wp_remote_retrieve_body( $identity_response );
		$identity_data = json_decode( $identity_body, true );

		$email = isset( $identity_data['data']['attributes']['email'] ) ? $identity_data['data']['attributes']['email'] : '';
		$patreon_id = isset( $identity_data['data']['id'] ) ? $identity_data['data']['id'] : '';

		if ( empty( $email ) ) {
			wp_redirect( add_query_arg( 'error', 'missing_email', $redirect_url ) );
			exit;
		}
		
		// Parse max tier amount
		$max_tier_cents = 0;
		if ( isset( $identity_data['included'] ) && is_array( $identity_data['included'] ) ) {
			foreach ( $identity_data['included'] as $included_item ) {
				if ( $included_item['type'] === 'tier' && isset( $included_item['attributes']['amount_cents'] ) ) {
					$amount = (int) $included_item['attributes']['amount_cents'];
					if ( $amount > $max_tier_cents ) {
						$max_tier_cents = $amount;
					}
				}
			}
		}

		// Find user by email or meta
		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			$users = get_users( array(
				'meta_key'   => '_patreon_id',
				'meta_value' => $patreon_id,
				'number'     => 1,
				'fields'     => 'all'
			) );
			if ( ! empty( $users ) ) {
				$user = $users[0];
			}
		}

		if ( ! $user ) {
			// Create user
			$password = wp_generate_password( 12, false );
			$username = sanitize_user( current( explode( '@', $email ) ), true );
			if ( username_exists( $username ) ) {
				$username .= '_' . wp_rand( 1000, 9999 );
			}
			$user_id = wp_create_user( $username, $password, $email );
			
			if ( is_wp_error( $user_id ) ) {
				wp_redirect( add_query_arg( 'error', 'user_creation_error', $redirect_url ) );
				exit;
			}
			$user = get_user_by( 'id', $user_id );
		}

		// Save Patreon ID
		update_user_meta( $user->ID, '_patreon_id', $patreon_id );
		update_user_meta( $user->ID, '_patreon_access_token', $access_token );
		update_user_meta( $user->ID, '_patreon_tier_cents', $max_tier_cents );
		if ( isset( $data['refresh_token'] ) ) {
			update_user_meta( $user->ID, '_patreon_refresh_token', $data['refresh_token'] );
		}

		if ( $max_tier_cents > 0 && ! in_array( 'pro_user', (array) $user->roles ) ) {
			$user->add_role( 'pro_user' );
		}

		// Log the user in
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID );

		// Redirect back
		$redirect_url = home_url( '/' . get_option( 'xophz_nook_phone_custom_slug', 'nookphone' ) );
		if ( ! empty( $state ) ) {
			$decoded_state = base64_decode( $state );
			if ( $decoded_state && filter_var( $decoded_state, FILTER_VALIDATE_URL ) ) {
				$redirect_url = $decoded_state;
			}
		}
		
		$redirect_url = add_query_arg( 'success', 'patreon', $redirect_url );
		wp_redirect( $redirect_url );
		exit;
	}

	private function get_or_create_app( $app_slug ) {
		$app_slug = sanitize_title( $app_slug );
		$args = array(
			'name'           => $app_slug,
			'post_type'      => 'nook_app',
			'post_status'    => 'publish',
			'posts_per_page' => 1
		);
		$apps = get_posts( $args );
		if ( $apps ) {
			return $apps[0]->ID;
		} else {
			return wp_insert_post( array(
				'post_title'  => ucwords( str_replace( '-', ' ', $app_slug ) ),
				'post_name'   => $app_slug,
				'post_type'   => 'nook_app',
				'post_status' => 'publish'
			) );
		}
	}

	public function record_install( WP_REST_Request $request ) {
		$app_slug = $request->get_param( 'app_slug' );
		$uuid = $request->get_param( 'uuid' );
		if ( empty( $app_slug ) ) {
			return new WP_Error( 'missing_app_slug', 'App slug is required', array( 'status' => 400 ) );
		}

		$app_id = $this->get_or_create_app( $app_slug );

		if ( ! empty( $uuid ) ) {
			$uuid = sanitize_text_field( $uuid );
			$meta_key = '_nook_installed_by_' . $uuid;
			$already_installed = get_post_meta( $app_id, $meta_key, true );
			if ( $already_installed ) {
				return rest_ensure_response( array(
					'success'  => true,
					'app_slug' => $app_slug,
					'installs' => (int) get_post_meta( $app_id, '_nook_app_installs', true ),
					'skipped'  => true
				) );
			}
			update_post_meta( $app_id, $meta_key, '1' );
		}

		$current_installs = (int) get_post_meta( $app_id, '_nook_app_installs', true );
		$current_installs++;
		update_post_meta( $app_id, '_nook_app_installs', $current_installs );

		return rest_ensure_response( array(
			'success'  => true,
			'app_slug' => $app_slug,
			'installs' => $current_installs
		) );
	}

	public function record_rating( WP_REST_Request $request ) {
		$app_slug = $request->get_param( 'app_slug' );
		$rating = (float) $request->get_param( 'rating' );
		$comment_content = sanitize_textarea_field( $request->get_param( 'comment' ) );
		
		if ( empty( $app_slug ) || empty( $rating ) ) {
			return new WP_Error( 'missing_params', 'App slug and Rating are required', array( 'status' => 400 ) );
		}

		if ( $rating < 1 || $rating > 5 ) {
			return new WP_Error( 'invalid_rating', 'Rating must be between 1 and 5', array( 'status' => 400 ) );
		}

		$app_id = $this->get_or_create_app( $app_slug );
		$user_id = get_current_user_id();
		$user = get_user_by( 'id', $user_id );

		// Check if user already rated (commented) on this app
		$existing_comments = get_comments( array(
			'post_id' => $app_id,
			'user_id' => $user_id,
			'count'   => false,
		) );

		if ( ! empty( $existing_comments ) ) {
			$comment = $existing_comments[0];
			
			// Update existing comment if content provided
			if ( $comment_content ) {
				$commentarr = array();
				$commentarr['comment_ID'] = $comment->comment_ID;
				$commentarr['comment_content'] = $comment_content;
				wp_update_comment( $commentarr );
			}
			
			// Update meta
			update_comment_meta( $comment->comment_ID, '_nook_app_rating', $rating );
		} else {
			// Insert new comment
			$commentdata = array(
				'comment_post_ID'      => $app_id,
				'comment_author'       => $user->display_name,
				'comment_author_email' => $user->user_email,
				'comment_content'      => $comment_content ? $comment_content : 'Rated ' . $rating . ' stars.',
				'comment_type'         => 'review',
				'user_id'              => $user_id,
				'comment_approved'     => 1, // Auto approve for now
			);
			
			$comment_id = wp_insert_comment( $commentdata );
			if ( $comment_id ) {
				add_comment_meta( $comment_id, '_nook_app_rating', $rating );
			}
		}

		// Recalculate average
		$all_comments = get_comments( array(
			'post_id' => $app_id,
			'meta_key' => '_nook_app_rating'
		) );

		$total_rating = 0;
		$rating_count = count( $all_comments );

		if ( $rating_count > 0 ) {
			foreach ( $all_comments as $c ) {
				$total_rating += (float) get_comment_meta( $c->comment_ID, '_nook_app_rating', true );
			}
			$average_rating = $total_rating / $rating_count;
		} else {
			$average_rating = 0;
		}

		update_post_meta( $app_id, '_nook_app_average_rating', round($average_rating, 1) );
		update_post_meta( $app_id, '_nook_app_rating_count', $rating_count );

		return rest_ensure_response( array(
			'success'        => true,
			'app_slug'       => $app_slug,
			'average_rating' => round($average_rating, 1),
			'rating_count'   => $rating_count
		) );
	}

    public function link_passport( WP_REST_Request $request ) {
        $passport_data = $request->get_param( 'passport' );
        if ( empty( $passport_data ) ) {
            return new WP_Error( 'missing_data', 'Passport data is required', array( 'status' => 400 ) );
        }

        $user_id = get_current_user_id();
        
        // Create or update a nook_passport CPT
        $post_data = array(
            'post_title'  => sanitize_text_field( $passport_data['name'] ) . "'s Passport",
            'post_type'   => 'nook_passport',
            'post_status' => 'publish',
            'post_author' => $user_id
        );

        if ( ! empty( $passport_data['id'] ) ) {
            $post_data['ID'] = intval( $passport_data['id'] );
        }

        $passport_id = wp_insert_post( $post_data );

        if ( is_wp_error( $passport_id ) ) {
            return $passport_id;
        }

        // Save meta data
        foreach( $passport_data as $key => $value ) {
            update_post_meta( $passport_id, '_nook_passport_' . sanitize_key( $key ), sanitize_text_field( $value ) );
        }

        return rest_ensure_response( array(
            'success'     => true,
            'passport_id' => $passport_id
        ) );
    }

    public function delete_passport( WP_REST_Request $request ) {
        $passport_id = intval( $request->get_param( 'id' ) );
        if ( empty( $passport_id ) ) {
            return new WP_Error( 'missing_id', 'Passport ID is required', array( 'status' => 400 ) );
        }

        $user_id = get_current_user_id();
        $post = get_post( $passport_id );

        if ( ! $post || $post->post_type !== 'nook_passport' || intval( $post->post_author ) !== $user_id ) {
            return new WP_Error( 'forbidden', 'You do not have permission to delete this passport', array( 'status' => 403 ) );
        }

        // Count remaining passports to ensure at least 1 remains
        $remaining = get_posts( array(
            'post_type'   => 'nook_passport',
            'author'      => $user_id,
            'post_status' => 'publish',
            'fields'      => 'ids',
            'posts_per_page' => -1
        ) );

        if ( count( $remaining ) <= 1 ) {
            return new WP_Error( 'minimum_reached', 'You must have at least one passport', array( 'status' => 400 ) );
        }

        // Force delete
        $deleted = wp_delete_post( $passport_id, true );

        if ( ! $deleted ) {
            return new WP_Error( 'delete_failed', 'Could not delete the passport', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array(
            'success' => true
        ) );
    }

	public function get_apps( WP_REST_Request $request ) {
		$args = array(
			'post_type'      => 'nook_app',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);
		$query = new WP_Query( $args );
		$apps = array();
		
		$user_id = get_current_user_id();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();
				
				$user_rating = null;
				$user_comment = '';
				if ( $user_id ) {
					$existing_comments = get_comments( array(
						'post_id' => $id,
						'user_id' => $user_id,
						'count'   => false,
					) );
					if ( ! empty( $existing_comments ) ) {
						$user_rating = (float) get_comment_meta( $existing_comments[0]->comment_ID, '_nook_app_rating', true );
						$user_comment = $existing_comments[0]->comment_content;
					}
				}

				$reviews = array();
				$comments = get_comments( array(
					'post_id' => $id,
					'status'  => 'approve',
					'order'   => 'DESC'
				) );
				foreach ( $comments as $c ) {
					$reviews[] = array(
						'author'  => $c->comment_author,
						'content' => $c->comment_content,
						'rating'  => (float) get_comment_meta( $c->comment_ID, '_nook_app_rating', true ),
						'date'    => mysql2date( 'F j, Y', $c->comment_date )
					);
				}

				$slug = $query->post->post_name;
				$installs = (int) get_post_meta( $id, '_nook_app_installs', true );
				$core_slugs = array( 'miles', 'happy-island-designer', 'pattern-tool', 'critterpedia', 'dodo-air' );
				
				if ( in_array( $slug, $core_slugs ) ) {
					$user_count = count_users();
					$installs = max( $installs, $user_count['total_users'] );
				}

				$apps[] = array(
					'id'             => $id,
					'slug'           => $slug,
					'title'          => get_the_title(),
					'description'    => get_the_content(),
					'installs'       => $installs,
					'average_rating' => (float) get_post_meta( $id, '_nook_app_average_rating', true ),
					'rating_count'   => (int) get_post_meta( $id, '_nook_app_rating_count', true ),
					'thumbnail'      => get_the_post_thumbnail_url( $id, 'full' ),
					'user_rating'    => $user_rating,
					'user_comment'   => $user_comment,
					'reviews'        => $reviews,
					'is_system'      => get_post_meta( $id, '_nook_app_is_system', true ) === 'yes',
					'app_id'         => get_post_meta( $id, '_nook_app_app_id', true ),
				);
			}
			wp_reset_postdata();
		}

		return rest_ensure_response( $apps );
	}

	public function get_passports( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$args = array(
			'post_type'      => 'nook_passport',
			'author'         => $user_id,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);
		$query = new WP_Query( $args );
		$passports = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();
				$meta = get_post_meta( $id );
				$clean_meta = array();
				foreach ( $meta as $k => $v ) {
					if ( strpos( $k, '_nook_passport_' ) === 0 ) {
						$clean_meta[ str_replace( '_nook_passport_', '', $k ) ] = $v[0];
					}
				}
				$passports[] = array(
					'id'        => $id,
					'title'     => get_the_title(),
					'data'      => $clean_meta,
					'thumbnail' => get_the_post_thumbnail_url( $id, 'full' ),
				);
			}
			wp_reset_postdata();
		}

		return rest_ensure_response( $passports );
	}

	public function get_threads( WP_REST_Request $request ) {
		$args = array(
			'post_type'      => 'nook_thread',
			'posts_per_page' => 20,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$query = new WP_Query( $args );
		$threads = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();
				$subnook = get_post_meta($id, '_subnook', true);
				$tips = (int) get_post_meta($id, '_tips', true);
				$threads[] = array(
					'id'            => $id,
					'title'         => get_the_title(),
					'content'       => get_the_content(),
					'subnook'       => $subnook,
					'tips'          => $tips,
					'author_id'     => get_the_author_meta('ID'),
					'author_name'   => get_the_author_meta('display_name'),
					'date'          => get_the_date('c'),
					'comment_count' => get_comments_number(),
				);
			}
			wp_reset_postdata();
		}

		return rest_ensure_response( $threads );
	}

	public function create_thread( WP_REST_Request $request ) {
		$title = sanitize_text_field( $request->get_param( 'title' ) );
		$content = wp_kses_post( $request->get_param( 'content' ) );
		$subnook = sanitize_text_field( $request->get_param( 'subnook' ) );

		if ( empty( $title ) || empty( $content ) ) {
			return new WP_Error( 'missing_fields', 'Title and content are required', array( 'status' => 400 ) );
		}

		$user_id = get_current_user_id();

		$post_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'nook_thread',
			'post_author'  => $user_id,
		) );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( ! empty( $subnook ) ) {
			update_post_meta( $post_id, '_subnook', $subnook );
		}

		return rest_ensure_response( array(
			'success'   => true,
			'thread_id' => $post_id
		) );
	}

	public function tip_thread( WP_REST_Request $request ) {
		$thread_id = (int) $request->get_param( 'thread_id' );
		$amount = (int) $request->get_param( 'amount' );
		if ( $amount <= 0 ) $amount = 25;

		if ( ! get_post( $thread_id ) ) {
			return new WP_Error( 'not_found', 'Thread not found', array( 'status' => 404 ) );
		}

		$current_tips = (int) get_post_meta( $thread_id, '_tips', true );
		update_post_meta( $thread_id, '_tips', $current_tips + $amount );

		$global_pool = (int) get_option( 'nook_global_raffle_pool', 0 );
		update_option( 'nook_global_raffle_pool', $global_pool + $amount );

		return rest_ensure_response( array(
			'success'      => true,
			'new_tips'     => $current_tips + $amount,
			'global_pool'  => $global_pool + $amount
		) );
	}

	public function get_board_status( WP_REST_Request $request ) {
		$board = sanitize_text_field( $request->get_param( 'board' ) );
		if ( empty( $board ) ) return rest_ensure_response( array() );

		// Core boards are permanently unlocked
		$core_boards = array('bb/All', 'bb/Isabelle', 'bb/TomNook', 'bb/Lottie', 'bb/KKSlider', 'bb/Blathers');
		if ( in_array( $board, $core_boards ) ) {
			return rest_ensure_response( array( 'board' => $board, 'funded' => true, 'raised' => 280000, 'goal' => 280000 ) );
		}

		$raised = (int) get_option( 'nook_board_funding_' . sanitize_key( $board ), 0 );
		return rest_ensure_response( array(
			'board'  => $board,
			'funded' => $raised >= 280000,
			'raised' => $raised,
			'goal'   => 280000
		) );
	}

	public function donate_to_board( WP_REST_Request $request ) {
		$board = sanitize_text_field( $request->get_param( 'board' ) );
		$amount = (int) $request->get_param( 'amount' );

		if ( empty( $board ) || $amount <= 0 ) {
			return new WP_Error( 'invalid_data', 'Board and amount are required', array( 'status' => 400 ) );
		}

		$raised = (int) get_option( 'nook_board_funding_' . sanitize_key( $board ), 0 );
		$new_total = $raised + $amount;
		update_option( 'nook_board_funding_' . sanitize_key( $board ), $new_total );

		return rest_ensure_response( array(
			'success' => true,
			'board'   => $board,
			'funded'  => $new_total >= 280000,
			'raised'  => $new_total
		) );
	}

	public function get_all_dms( WP_REST_Request $request ) {
		$user_id = get_current_user_id();

		$args = array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_nook_dm_recipient',
					'value'   => $user_id,
					'compare' => '='
				),
				array(
					'key'     => '_nook_dm_recipient',
					'value'   => -1,
					'compare' => '='
				)
			)
		);

		$sent_args = array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'author'         => $user_id
		);

		$received = get_posts( $args );
		$sent = get_posts( $sent_args );

		$all_dms = array_merge( $received, $sent );
		
		// Remove duplicates
		$seen_ids = array();
		$unique_dms = array();
		foreach ( $all_dms as $dm ) {
			if ( ! in_array( $dm->ID, $seen_ids ) ) {
				$seen_ids[] = $dm->ID;
				$unique_dms[] = $dm;
			}
		}
		$all_dms = $unique_dms;
		
		$deleted_dms = get_user_meta( $user_id, '_nook_deleted_dms', true );
		if ( ! is_array( $deleted_dms ) ) {
			$deleted_dms = array();
		}
		
		// Ensure welcome message exists
		$welcome_query = new WP_Query( array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => '_nook_dm_recipient',
					'value'   => -1,
					'compare' => '='
				)
			)
		) );

		if ( ! $welcome_query->have_posts() ) {
			$admin_user = get_user_by( 'id', 1 );
			$admin_id = $admin_user ? $admin_user->ID : 1;
			$post_id = wp_insert_post( array(
				'post_title'   => 'Welcome to COMPASS & NookPhone!',
				'post_content' => "Welcome to COMPASS!\n\nThis Messages app keeps you connected with other residents and island villagers.\n\nHere is how to get started:\n1. Choose category tabs to filter messages.\n2. Tap '+' in the top-right to start a new chat.\n3. Swipe or tap into letters to read them.\n4. Clean up old messages by tapping the trash icon.\n\nEnjoy! - Tom Nook",
				'post_status'  => 'publish',
				'post_type'    => 'nook_dm',
				'post_author'  => $admin_id,
				'comment_status' => 'open',
			) );

			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_nook_dm_recipient', -1 );
				update_post_meta( $post_id, '_nook_dm_read', false );
				// Refresh posts list to include this new DM
				$new_dm = get_post( $post_id );
				if ( $new_dm ) {
					$all_dms[] = $new_dm;
				}
			}
		}

		$letters = array();
		foreach ( $all_dms as $dm ) {
			if ( in_array( $dm->ID, $deleted_dms ) ) {
				continue;
			}

			$sender_id = (int) $dm->post_author;
			$recipient_id = (int) get_post_meta( $dm->ID, '_nook_dm_recipient', true );
			
			// Define read flag
			if ( $recipient_id === -1 ) {
				$read_dms = get_user_meta( $user_id, '_nook_read_global_dms', true );
				$is_read = is_array( $read_dms ) && in_array( $dm->ID, $read_dms );
			} else {
				$is_read = (bool) get_post_meta( $dm->ID, '_nook_dm_read', true );
			}

			$sender = get_user_by('id', $sender_id);
			$recipient = get_user_by('id', $recipient_id);

			$partner_id = ($sender_id === $user_id) ? $recipient_id : $sender_id;
			if ( $partner_id === -1 ) {
				$partner_id = $sender_id;
			}

			$letters[] = array(
				'id' => $dm->ID,
				'author' => $sender_id,
				'author_name' => $sender ? $sender->display_name : 'Unknown',
				'recipient_id' => $recipient_id,
				'recipient_name' => $recipient ? $recipient->display_name : ($recipient_id === -1 ? 'All Residents' : 'Unknown'),
				'title' => array('rendered' => $dm->post_title),
				'content' => array('rendered' => $dm->post_content),
				'date' => $dm->post_date,
				'unread_count' => ($recipient_id === $user_id && !$is_read) ? 1 : (($recipient_id === -1 && !$is_read) ? 1 : 0),
				'stationery_id' => get_post_meta( $dm->ID, '_nook_dm_stationery', true ) ?: 'airmail'
			);
		}

		usort($letters, function($a, $b) {
			return strtotime($b['date']) - strtotime($a['date']);
		});

		return rest_ensure_response( $letters );
	}

	public function get_dm_conversations( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		
		$args = array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_nook_dm_recipient',
					'value'   => $user_id,
					'compare' => '='
				)
			)
		);

		// Also get messages where current user is author
		$sent_args = array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'author'         => $user_id
		);

		$received = get_posts( $args );
		$sent = get_posts( $sent_args );

		$all_dms = array_merge( $received, $sent );
		
		// Group by conversation partner
		$conversations = array();
		
		foreach ( $all_dms as $dm ) {
			$sender_id = (int) $dm->post_author;
			$recipient_id = (int) get_post_meta( $dm->ID, '_nook_dm_recipient', true );
			
			$partner_id = ($sender_id === $user_id) ? $recipient_id : $sender_id;
			
			if ( ! isset( $conversations[ $partner_id ] ) ) {
				$partner = get_user_by( 'id', $partner_id );
				if ( ! $partner ) continue;

				$conversations[ $partner_id ] = array(
					'partner_id'   => $partner_id,
					'partner_name' => $partner->display_name,
					'last_message' => $dm->post_content,
					'date'         => $dm->post_date,
					'unread_count' => 0
				);
			} else {
				if ( strtotime( $dm->post_date ) > strtotime( $conversations[ $partner_id ]['date'] ) ) {
					$conversations[ $partner_id ]['last_message'] = $dm->post_content;
					$conversations[ $partner_id ]['date'] = $dm->post_date;
				}
			}

			// Unread count
			if ( $recipient_id === $user_id ) {
				$is_read = get_post_meta( $dm->ID, '_nook_dm_read', true );
				if ( ! $is_read ) {
					$conversations[ $partner_id ]['unread_count']++;
				}
			}
		}

		// Sort by date DESC
		usort($conversations, function($a, $b) {
			return strtotime($b['date']) - strtotime($a['date']);
		});

		return rest_ensure_response( array_values( $conversations ) );
	}

	public function get_dms_with_user( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$partner_id = (int) $request->get_param( 'user_id' );

		if ( ! $partner_id ) {
			return new WP_Error( 'missing_user', 'User ID is required', array( 'status' => 400 ) );
		}

		$args1 = array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'author'         => $user_id,
			'meta_key'       => '_nook_dm_recipient',
			'meta_value'     => $partner_id
		);

		$args2 = array(
			'post_type'      => 'nook_dm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'author'         => $partner_id,
			'meta_key'       => '_nook_dm_recipient',
			'meta_value'     => $user_id
		);

		$sent = get_posts( $args1 );
		$received = get_posts( $args2 );

		// If viewing thread with admin/ID 1, also pull welcome message (recipient -1)
		if ( $partner_id === 1 ) {
			$args3 = array(
				'post_type'      => 'nook_dm',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'author'         => 1,
				'meta_key'       => '_nook_dm_recipient',
				'meta_value'     => -1
			);
			$global_received = get_posts( $args3 );
			$received = array_merge( $received, $global_received );
		}

		$all_dms = array_merge( $sent, $received );
		
		// Remove duplicates
		$seen_ids = array();
		$unique_dms = array();
		foreach ( $all_dms as $dm ) {
			if ( ! in_array( $dm->ID, $seen_ids ) ) {
				$seen_ids[] = $dm->ID;
				$unique_dms[] = $dm;
			}
		}
		$all_dms = $unique_dms;
		usort( $all_dms, function($a, $b) {
			return strtotime( $a->post_date ) - strtotime( $b->post_date );
		});

		$deleted_dms = get_user_meta( $user_id, '_nook_deleted_dms', true );
		if ( ! is_array( $deleted_dms ) ) {
			$deleted_dms = array();
		}

		$messages = array();
		foreach ( $all_dms as $dm ) {
			if ( in_array( $dm->ID, $deleted_dms ) ) {
				continue;
			}

			// Define read flag
			$recipient_id = (int) get_post_meta( $dm->ID, '_nook_dm_recipient', true );
			if ( $recipient_id === -1 ) {
				$read_dms = get_user_meta( $user_id, '_nook_read_global_dms', true );
				if ( ! is_array( $read_dms ) ) {
					$read_dms = array();
				}
				if ( ! in_array( $dm->ID, $read_dms ) ) {
					$read_dms[] = $dm->ID;
					update_user_meta( $user_id, '_nook_read_global_dms', $read_dms );
				}
				$is_read = true;
			} else {
				$is_read = get_post_meta( $dm->ID, '_nook_dm_read', true );
				// Mark as read if we are the recipient
				if ( $recipient_id === $user_id && ! $is_read ) {
					update_post_meta( $dm->ID, '_nook_dm_read', true );
					$is_read = true;
				}
			}

			$messages[] = array(
				'id'        => $dm->ID,
				'sender_id' => (int) $dm->post_author,
				'content'   => $dm->post_content,
				'date'      => $dm->post_date,
				'is_read'   => (bool) $is_read
			);
		}

		return rest_ensure_response( $messages );
	}

	public function delete_dms( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$dm_id = (int) $request->get_param( 'dm_id' );
		$partner_id = (int) $request->get_param( 'partner_id' );

		$deleted_dms = get_user_meta( $user_id, '_nook_deleted_dms', true );
		if ( ! is_array( $deleted_dms ) ) {
			$deleted_dms = array();
		}

		if ( $dm_id ) {
			if ( ! in_array( $dm_id, $deleted_dms ) ) {
				$deleted_dms[] = $dm_id;
			}
		} elseif ( $partner_id ) {
			// Fetch all DMs between user and partner
			$args1 = array(
				'post_type'      => 'nook_dm',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'author'         => $user_id,
				'meta_key'       => '_nook_dm_recipient',
				'meta_value'     => $partner_id
			);
			$args2 = array(
				'post_type'      => 'nook_dm',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'author'         => $partner_id,
				'meta_key'       => '_nook_dm_recipient',
				'meta_value'     => $user_id
			);
			$sent = get_posts( $args1 );
			$received = get_posts( $args2 );
			$all_dms = array_merge( $sent, $received );
			
			// Also include global BCC messages if partner_id is 1 (Admin)
			if ( $partner_id === 1 ) {
				$args3 = array(
					'post_type'      => 'nook_dm',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'author'         => 1,
					'meta_key'       => '_nook_dm_recipient',
					'meta_value'     => -1
				);
				$global_dms = get_posts( $args3 );
				$all_dms = array_merge( $all_dms, $global_dms );
			}

			foreach ( $all_dms as $dm ) {
				if ( ! in_array( $dm->ID, $deleted_dms ) ) {
					$deleted_dms[] = $dm->ID;
				}
			}
		}

		update_user_meta( $user_id, '_nook_deleted_dms', $deleted_dms );

		return rest_ensure_response( array( 'success' => true ) );
	}

	public function send_dm( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$recipient_id = (int) $request->get_param( 'recipient_id' );
		$content = sanitize_textarea_field( $request->get_param( 'content' ) );
		$subject = sanitize_text_field( $request->get_param( 'subject' ) );
		$stationery_id = sanitize_text_field( $request->get_param( 'stationery_id' ) );

		if ( ! $recipient_id || ! $content ) {
			return new WP_Error( 'missing_fields', 'Recipient and content are required', array( 'status' => 400 ) );
		}

		if ( $user_id === $recipient_id ) {
			return new WP_Error( 'invalid_recipient', 'Cannot send message to yourself', array( 'status' => 400 ) );
		}

		$recipient = get_user_by( 'id', $recipient_id );
		if ( ! $recipient ) {
			return new WP_Error( 'invalid_recipient', 'Recipient does not exist', array( 'status' => 400 ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'   => ! empty( $subject ) ? $subject : 'DM from ' . $user_id . ' to ' . $recipient_id,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'nook_dm',
			'post_author'  => $user_id,
			'comment_status' => 'open',
		) );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		update_post_meta( $post_id, '_nook_dm_recipient', $recipient_id );
		update_post_meta( $post_id, '_nook_dm_read', false );

		if ( ! empty( $stationery_id ) ) {
			update_post_meta( $post_id, '_nook_dm_stationery', $stationery_id );
		}

		return rest_ensure_response( array(
			'success' => true,
			'dm_id'   => $post_id,
			'date'    => get_the_date('c', $post_id)
		) );
	}

	public function get_nook_state( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$state = get_user_meta( $user_id, '_nook_os_state', true );
		$state_data = array();
		if ( ! empty( $state ) ) {
			$decoded = json_decode( $state, true );
			if ( is_array( $decoded ) ) {
				$state_data = $decoded;
			}
		}
			// Dynamically populate bells from _xp_total_gp (Bells)
			$total_gp = (int) get_user_meta( $user_id, '_xp_total_gp', true ) ?: 0;
			if ( $total_gp > 0 ) {
				$state_data['bells'] = $total_gp;
			}
			
			// Attach player level and XP stats if the XP system is available
			if ( class_exists( 'Xophz_Compass_Xp_Players' ) ) {
				$stats = Xophz_Compass_Xp_Players::get_user_stats( $user_id );
				$state_data['xp_level'] = $stats['level'];
				$state_data['xp_current'] = $stats['current_xp'];
				$state_data['xp_target'] = $stats['target_xp'];
				$state_data['xp_total'] = $stats['total_xp'];
				$state_data['is_pro'] = Xophz_Compass_Xp_Players::is_pro_user( $user_id );
			} else {
				$state_data['is_pro'] = false;
			}
			
			$patreon_tier = get_user_meta( $user_id, '_patreon_tier_cents', true );
			if ( $patreon_tier ) {
				$state_data['patreonTierCents'] = (int) $patreon_tier;
			} else {
				$state_data['patreonTierCents'] = 0;
			}
		
		return rest_ensure_response( array(
			'state' => $state_data
		) );
	}

	public function save_nook_state( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$state_json = $request->get_param( 'state' );
		
		if ( empty( $state_json ) ) {
			return new WP_Error( 'missing_state', 'No state data provided.', array( 'status' => 400 ) );
		}
		
		$old_state_raw = get_user_meta( $user_id, '_nook_os_state', true );
		$old_state_data = !empty( $old_state_raw ) ? json_decode( $old_state_raw, true ) : array();
		
		$state_data = array();
		// Validate it's proper JSON by decoding and re-encoding
		if ( is_string( $state_json ) ) {
			$state_data = json_decode( $state_json, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return new WP_Error( 'invalid_json', 'Invalid JSON payload.', array( 'status' => 400 ) );
			}
			update_user_meta( $user_id, '_nook_os_state', wp_slash( wp_json_encode( $state_data ) ) );
		} else if ( is_array( $state_json ) ) {
			$state_data = $state_json;
			update_user_meta( $user_id, '_nook_os_state', wp_slash( wp_json_encode( $state_json ) ) );
		}
		
		// Do not blindly sync bells from local state to GP!
		// The XP system is the single source of truth for Bells/GP.
		// NookPhone must use the transaction API to spend/earn GP.
		
		// Fire action for the XP engine to process collections/activities
		do_action( 'xophz_nook_phone_state_saved', $user_id, $state_data, $old_state_data );
		
		return rest_ensure_response( array( 'success' => true ) );
	}

	public function get_nook_users( WP_REST_Request $request ) {
		$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
		return rest_ensure_response( $users );
	}

	public function get_nookipedia_recipes( WP_REST_Request $request ) {
		$cache_key = 'xophz_nook_recipes_cache';
		$cached_recipes = get_transient( $cache_key );

		if ( $cached_recipes !== false ) {
			return rest_ensure_response( $cached_recipes );
		}

		$all_recipes = array();
		$limit = 500;
		$offset = 0;
		$has_more = true;
		$mw_api_url = 'https://nookipedia.com/w/api.php';

		while ( $has_more ) {
			$params = array(
				'action' => 'cargoquery',
				'format' => 'json',
				'tables' => 'nh_recipe',
				'fields' => 'en_name=name, image_url, materials, type',
				'limit'  => $limit,
				'offset' => $offset,
			);

			$url = add_query_arg( $params, $mw_api_url );
			$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				break; // Stop on error
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( empty( $data['cargoquery'] ) ) {
				$has_more = false;
			} else {
				foreach ( $data['cargoquery'] as $row ) {
					$all_recipes[] = $row['title'];
				}
				$offset += $limit;
			}
		}

		if ( ! empty( $all_recipes ) ) {
			set_transient( $cache_key, $all_recipes, WEEK_IN_SECONDS );
		}

		return rest_ensure_response( $all_recipes );
	}

	public function get_nookipedia_materials( $request ) {
		$cache_key = 'nook_phone_nh_materials';
		$materials = get_transient( $cache_key );

		if ( false !== $materials ) {
			return rest_ensure_response( $materials );
		}

		$recipes = get_transient( 'nook_phone_nh_recipes' );
		if ( false === $recipes ) {
			$recipes_res = $this->get_nookipedia_recipes( $request );
			if ( is_wp_error( $recipes_res ) ) {
				return rest_ensure_response( array() );
			}
			$recipes = $recipes_res->get_data();
			if ( empty( $recipes ) ) {
				return rest_ensure_response( array() );
			}
		}

		$material_names = array();
		foreach ( $recipes as $r ) {
			if ( ! empty( $r['materials'] ) ) {
				$mats = json_decode( html_entity_decode( $r['materials'] ), true );
				if ( is_array( $mats ) ) {
					foreach ( array_keys( $mats ) as $m_name ) {
						$material_names[ $m_name ] = true;
					}
				}
			}
		}

		$unique_names = array_keys( $material_names );
		$materials_data = array();
		$chunks = array_chunk( $unique_names, 50 );
		$mw_api_url = 'https://nookipedia.com/w/api.php';

		foreach ( $chunks as $chunk ) {
			$quoted = array_map( function( $n ) {
				return "'" . esc_sql( str_replace( "'", "''", $n ) ) . "'";
			}, $chunk );
			
			$where = 'en_name IN (' . implode( ',', $quoted ) . ')';

			$params = array(
				'action' => 'cargoquery',
				'format' => 'json',
				'tables' => 'nh_item',
				'fields' => 'en_name=name, image_url',
				'where'  => $where,
				'limit'  => 500,
			);

			$url = add_query_arg( $params, $mw_api_url );
			$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );
				if ( ! empty( $data['cargoquery'] ) ) {
					foreach ( $data['cargoquery'] as $row ) {
						if ( ! empty( $row['title']['name'] ) && ! empty( $row['title']['image_url'] ) ) {
							$materials_data[ $row['title']['name'] ] = $row['title']['image_url'];
						}
					}
				}
			}
		}

		if ( ! empty( $materials_data ) ) {
			set_transient( $cache_key, $materials_data, WEEK_IN_SECONDS );
		}

		return rest_ensure_response( $materials_data );
	}

	private function query_nookipedia_table( $table, $fields, $where = '', $limit = 100 ) {
		$mw_api_url = 'https://nookipedia.com/w/api.php';
		$params = array(
			'action' => 'cargoquery',
			'format' => 'json',
			'tables' => $table,
			'fields' => $fields,
			'limit'  => $limit,
		);
		if ( ! empty( $where ) ) {
			$params['where'] = $where;
		}

		$url = add_query_arg( $params, $mw_api_url );
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body, true );
		}
		return null;
	}

	private function normalize_nookipedia_row( $row, $source_table ) {
		$name = isset( $row['name'] ) ? $row['name'] : '';
		$id = sanitize_title( $name );
		$buy = isset( $row['buy1_price'] ) ? intval( $row['buy1_price'] ) : 0;
		$sell = isset( $row['sell'] ) ? intval( $row['sell'] ) : 0;
		$image = isset( $row['image_url'] ) ? $row['image_url'] : '';

		// Resolve category mapping matching official ACNH storage categories
		$category = 'Other';
		if ( $source_table === 'nh_furniture' ) {
			$raw_cat = isset( $row['category'] ) ? strtolower( $row['category'] ) : '';
			if ( $raw_cat === 'housewares' ) {
				$category = 'Housewares';
			} elseif ( $raw_cat === 'miscellaneous' ) {
				$category = 'Miscellaneous';
			} elseif ( $raw_cat === 'wall-mounted' || $raw_cat === 'ceiling decor' ) {
				$category = 'Wall-mounted';
			} else {
				$category = 'Housewares';
			}
		} elseif ( $source_table === 'nh_interior' ) {
			$raw_cat = isset( $row['category'] ) ? strtolower( $row['category'] ) : '';
			if ( $raw_cat === 'wallpaper' ) {
				$category = 'Wallpaper';
			} elseif ( $raw_cat === 'flooring' ) {
				$category = 'Flooring';
			} elseif ( $raw_cat === 'rugs' ) {
				$category = 'Rugs';
			}
		} elseif ( $source_table === 'nh_clothing' || $source_table === 'nh_clothing_variation' ) {
			$category = 'Fashion';
		} elseif ( $source_table === 'nh_photo' ) {
			$category = 'Photos';
		} elseif ( $source_table === 'nh_bug' ) {
			$category = 'Bugs';
		} elseif ( $source_table === 'nh_fish' ) {
			$category = 'Fish';
		} elseif ( $source_table === 'nh_sea_creature' ) {
			$category = 'Sea Creatures';
		} elseif ( $source_table === 'nh_fossil' ) {
			$category = 'Fossils';
		} elseif ( $source_table === 'nh_art' ) {
			$category = 'Art';
		} elseif ( $source_table === 'nh_tool' ) {
			$category = 'Tools';
		} elseif ( $source_table === 'nh_item' ) {
			$mat_type = isset( $row['material_type'] ) ? strtolower( $row['material_type'] ) : '';
			if ( $mat_type === 'music' || strpos( strtolower( $name ), 'k.k.' ) !== false || strpos( strtolower( $name ), 'aircheck' ) !== false ) {
				$category = 'Music';
			} elseif ( $mat_type === 'tool' || preg_match( '/\b(axe|shovel|net|rod|watering can|slingshot|wand|ladder|pole)\b/i', $name ) ) {
				$category = 'Tools';
			} else {
				$category = 'Other';
			}
		}

		return array(
			'id'           => $id,
			'name'         => $name,
			'image'        => $image,
			'imageUrl'     => $image,
			'image_url'    => $image,
			'buy_price'    => $buy,
			'sell_price'   => $sell,
			'is_orderable' => $buy > 0,
			'category'     => $category,
		);
	}

	public function sync_nookipedia_catalog() {
		$cache_key = 'xophz_nook_shopping_items_cache_v10';
		$cached_items = array();
		$tables_to_query = array(
			array( 'name' => 'nh_furniture', 'fields' => 'en_name=name,buy1_price,sell,category,image_url' ),
			array( 'name' => 'nh_interior', 'fields' => 'en_name=name,buy1_price,sell,category,image_url' ),
			array( 'name' => 'nh_clothing_variation', 'fields' => 'en_name=name,image_url' ),
			array( 'name' => 'nh_photo', 'fields' => 'en_name=name,buy1_price,sell,category,image_url' ),
			array( 'name' => 'nh_bug', 'fields' => 'name,sell_nook=sell,image_url' ),
			array( 'name' => 'nh_fish', 'fields' => 'name,sell_nook=sell,image_url' ),
			array( 'name' => 'nh_sea_creature', 'fields' => 'name,sell_nook=sell,image_url' ),
			array( 'name' => 'nh_fossil', 'fields' => 'name,sell,image_url' ),
			array( 'name' => 'nh_art', 'fields' => 'name,buy,sell,image_url' ),
			array( 'name' => 'nh_tool', 'fields' => 'en_name=name,buy1_price,sell,image_url' ),
			array( 'name' => 'nh_item', 'fields' => 'en_name=name,buy1_price,sell,material_type,image_url' ),
		);
		$mw_api_url = 'https://nookipedia.com/w/api.php';

		foreach ( $tables_to_query as $t ) {
			$offset = 0;
			$limit = 500;
			$has_more = true;
			
			while ( $has_more ) {
				$params = array(
					'action' => 'cargoquery',
					'format' => 'json',
					'tables' => $t['name'],
					'fields' => $t['fields'],
					'limit'  => $limit,
					'offset' => $offset,
				);
				$url = add_query_arg( $params, $mw_api_url );
				$response = wp_remote_get( $url, array( 'timeout' => 8 ) );

				if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
					break;
				}

				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );

				if ( isset( $data['error'] ) ) {
					break;
				}

				if ( empty( $data['cargoquery'] ) ) {
					$has_more = false;
				} else {
					foreach ( $data['cargoquery'] as $row ) {
						if ( ! empty( $row['title']['name'] ) ) {
							$cached_items[] = $this->normalize_nookipedia_row( $row['title'], $t['name'] );
						}
					}
					if ( count( $data['cargoquery'] ) < $limit ) {
						$has_more = false;
					} else {
						$offset += $limit;
					}
				}
			}
		}

		if ( ! empty( $cached_items ) ) {
			set_transient( $cache_key, $cached_items, WEEK_IN_SECONDS );
		}
		return $cached_items;
	}

	public function get_acnh_items( WP_REST_Request $request ) {
		$search = $request->get_param( 'search' );
		$requested_cat = $request->get_param( 'category' );
		
		$compact = $this->get_unified_acnh_data();
		
		// If JSON processing fails, fallback to Nookipedia API
		if ( empty( $compact ) ) {
			return $this->get_nookipedia_items( $request );
		}

		$filtered = $compact;

		// Filter by category
		if ( ! empty( $requested_cat ) && strtolower( $requested_cat ) !== 'all' ) {
			$cat_lower = strtolower( $requested_cat );
			$temp = array();
			foreach ( $filtered as $item ) {
				$item_cat = isset( $item['category'] ) ? strtolower( $item['category'] ) : '';
				if ( $item_cat === $cat_lower ) {
					$temp[] = $item;
				}
			}
			$filtered = $temp;
		}

		// Filter by search
		if ( ! empty( $search ) ) {
			$search_lower = strtolower( $search );
			$temp = array();
			foreach ( $filtered as $item ) {
				if ( strpos( strtolower( $item['name'] ), $search_lower ) !== false ) {
					$temp[] = $item;
				}
			}
			$filtered = $temp;
		}

		return rest_ensure_response( array_values( $filtered ) );
	}

	private function get_unified_acnh_data() {
		$data_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'data/';
		$compact_file = $data_dir . 'compact_items.json';

		// Return cached compact file if exists
		if ( file_exists( $compact_file ) ) {
			return json_decode( file_get_contents( $compact_file ), true );
		}

		$items_file = $data_dir . 'Items.json';
		$creatures_file = $data_dir . 'Creatures.json';

		if ( ! file_exists( $items_file ) || ! file_exists( $creatures_file ) ) {
			return false;
		}

		$items = json_decode( file_get_contents( $items_file ), true );
		$creatures = json_decode( file_get_contents( $creatures_file ), true );
		$compact = array();
		$seen = array();

		$map_category = function( $sheet ) {
			$sheet = strtolower( $sheet );
			$fashion = array('tops', 'bottoms', 'dresses', 'headwear', 'accessories', 'socks', 'shoes', 'bags', 'umbrellas', 'clothing other');
			if ( in_array( $sheet, $fashion ) ) return 'Fashion';
			if ( $sheet === 'insects' ) return 'Bugs';
			if ( $sheet === 'fish' ) return 'Fish';
			if ( $sheet === 'sea creatures' ) return 'Sea Creatures';
			if ( $sheet === 'fossils' ) return 'Fossils';
			if ( $sheet === 'art' ) return 'Art';
			if ( $sheet === 'music' ) return 'Music';
			if ( $sheet === 'tools' ) return 'Tools';
			if ( $sheet === 'photos' || $sheet === 'posters' ) return 'Photos';
			if ( $sheet === 'housewares' ) return 'Housewares';
			if ( $sheet === 'miscellaneous' ) return 'Miscellaneous';
			if ( $sheet === 'wall-mounted' ) return 'Wall-mounted';
			if ( $sheet === 'wallpaper' ) return 'Wallpaper';
			if ( $sheet === 'floors' ) return 'Flooring';
			if ( $sheet === 'rugs' ) return 'Rugs';
			if ( $sheet === 'message cards' ) return 'Message Cards';
			return 'Other';
		};

		foreach ( $items as $item ) {
			if ( empty( $item['name'] ) ) continue;
			$name = $item['name'];
			if ( isset( $seen[ $name ] ) ) continue;
			$seen[ $name ] = true;

			$image = '';
			if ( ! empty( $item['image'] ) ) {
				$image = $item['image'];
			} elseif ( ! empty( $item['variations'] ) && is_array( $item['variations'] ) && ! empty( $item['variations'][0]['image'] ) ) {
				$image = $item['variations'][0]['image'];
			} elseif ( ! empty( $item['albumImage'] ) ) {
				$image = $item['albumImage'];
			} elseif ( ! empty( $item['storageImage'] ) ) {
				$image = $item['storageImage'];
			} elseif ( ! empty( $item['inventoryImage'] ) ) {
				$image = $item['inventoryImage'];
			}
			
			$buy = isset( $item['buy'] ) ? intval( $item['buy'] ) : 0;
			$sell = isset( $item['sell'] ) ? intval( $item['sell'] ) : 0;
			
			$entry = array(
				'id'           => sanitize_title( $name ),
				'name'         => $name,
				'imageUrl'     => $image,
				'image_url'    => $image,
				'buy_price'    => $buy,
				'sell_price'   => $sell,
				'is_orderable' => $buy > 0,
				'category'     => $map_category( $item['sourceSheet'] )
			);

			if ( $item['sourceSheet'] === 'Message Cards' ) {
				$entry['bodyColor'] = isset($item['bodyColor']) ? $item['bodyColor'] : '';
				$entry['penColor1'] = isset($item['penColor1']) ? $item['penColor1'] : '';
				$entry['penColor2'] = isset($item['penColor2']) ? $item['penColor2'] : '';
				$entry['penColor3'] = isset($item['penColor3']) ? $item['penColor3'] : '';
				$entry['penColor4'] = isset($item['penColor4']) ? $item['penColor4'] : '';
			}

			$compact[] = $entry;
		}

		foreach ( $creatures as $c ) {
			if ( empty( $c['name'] ) ) continue;
			$name = $c['name'];
			if ( isset( $seen[ $name ] ) ) continue;
			$seen[ $name ] = true;

			$image = isset( $c['iconImage'] ) ? $c['iconImage'] : '';
			$sell = isset( $c['sell'] ) ? intval( $c['sell'] ) : 0;
			
			$compact[] = array(
				'id' => sanitize_title( $name ),
				'name' => $name,
				'image' => $image,
				'imageUrl' => $image,
				'image_url' => $image,
				'buy_price' => 0,
				'sell_price' => $sell,
				'is_orderable' => false,
				'category' => $map_category( $c['sourceSheet'] )
			);
		}

		file_put_contents( $compact_file, json_encode( $compact ) );
		return $compact;
	}

	public function get_nookipedia_items( WP_REST_Request $request ) {
		$search = $request->get_param( 'search' );
		$requested_cat = $request->get_param( 'category' );

		$cache_key = 'xophz_nook_shopping_items_cache_v10';
		$cached_items = get_transient( $cache_key );

		if ( $cached_items === false ) {
			$cached_items = $this->sync_nookipedia_catalog();
		}

		$filtered = $cached_items ? $cached_items : array();

		// Filter by category
		if ( ! empty( $requested_cat ) && strtolower( $requested_cat ) !== 'all' ) {
			$cat_lower = strtolower( $requested_cat );
			$temp = array();
			foreach ( $filtered as $item ) {
				$item_cat = isset( $item['category'] ) ? strtolower( $item['category'] ) : '';
				if ( $cat_lower === 'wall-mounted' && ( $item_cat === 'wall-mounted' || $item_cat === 'ceiling decor' ) ) {
					$temp[] = $item;
				} elseif ( $item_cat === $cat_lower ) {
					$temp[] = $item;
				}
			}
			$filtered = $temp;
		}

		// Filter by search
		if ( ! empty( $search ) ) {
			$search_lower = strtolower( $search );
			$temp = array();
			foreach ( $filtered as $item ) {
				if ( strpos( strtolower( $item['name'] ), $search_lower ) !== false ) {
					$temp[] = $item;
				}
			}
			$filtered = $temp;
		}

		return rest_ensure_response( $filtered );
	}

	public function get_acnh_villagers( WP_REST_Request $request ) {
		$data_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'data/';
		$compact_file = $data_dir . 'compact_villagers.json';

		if ( file_exists( $compact_file ) ) {
			return rest_ensure_response( json_decode( file_get_contents( $compact_file ), true ) );
		}

		$villagers_file = $data_dir . 'Villagers.json';
		if ( ! file_exists( $villagers_file ) ) {
			return $this->get_nookipedia_villagers( $request );
		}

		$villagers = json_decode( file_get_contents( $villagers_file ), true );
		$compact = array();

		foreach ( $villagers as $v ) {
			if ( empty( $v['name'] ) ) continue;
			
			$birthday = isset( $v['birthday'] ) ? $v['birthday'] : '';
			$sign = 'Unknown';
			if ( ! empty( $birthday ) ) {
				$parts = explode( '/', $birthday );
				if ( count( $parts ) === 2 ) {
					$m = intval( $parts[0] );
					$d = intval( $parts[1] );
					if ( ( $m == 3 && $d >= 21 ) || ( $m == 4 && $d <= 19 ) ) $sign = 'Aries';
					elseif ( ( $m == 4 && $d >= 20 ) || ( $m == 5 && $d <= 20 ) ) $sign = 'Taurus';
					elseif ( ( $m == 5 && $d >= 21 ) || ( $m == 6 && $d <= 20 ) ) $sign = 'Gemini';
					elseif ( ( $m == 6 && $d >= 21 ) || ( $m == 7 && $d <= 22 ) ) $sign = 'Cancer';
					elseif ( ( $m == 7 && $d >= 23 ) || ( $m == 8 && $d <= 22 ) ) $sign = 'Leo';
					elseif ( ( $m == 8 && $d >= 23 ) || ( $m == 9 && $d <= 22 ) ) $sign = 'Virgo';
					elseif ( ( $m == 9 && $d >= 23 ) || ( $m == 10 && $d <= 22 ) ) $sign = 'Libra';
					elseif ( ( $m == 10 && $d >= 23 ) || ( $m == 11 && $d <= 21 ) ) $sign = 'Scorpio';
					elseif ( ( $m == 11 && $d >= 22 ) || ( $m == 12 && $d <= 21 ) ) $sign = 'Sagittarius';
					elseif ( ( $m == 12 && $d >= 22 ) || ( $m == 1 && $d <= 19 ) ) $sign = 'Capricorn';
					elseif ( ( $m == 1 && $d >= 20 ) || ( $m == 2 && $d <= 18 ) ) $sign = 'Aquarius';
					elseif ( ( $m == 2 && $d >= 19 ) || ( $m == 3 && $d <= 20 ) ) $sign = 'Pisces';
				}
			}

			$photoImage = isset( $v['photoImage'] ) ? $v['photoImage'] : '';
			$poster_url = '';
			if ( $photoImage ) {
				$poster_url = str_replace( 'NpcBromide/', 'FtrIcon/Poster', $photoImage );
			}

			// Generate Nookipedia / Dodo.ac full body render URL
			$render_filename = str_replace( ' ', '_', $v['name'] ) . '_NH.png';
			$render_hash = md5( $render_filename );
			$hash1 = substr( $render_hash, 0, 1 );
			$hash2 = substr( $render_hash, 0, 2 );
			$render_url = "https://dodo.ac/np/images/{$hash1}/{$hash2}/{$render_filename}";

			$compact[] = array(
				'id'          => sanitize_title( $v['name'] ),
				'name'        => $v['name'],
				'species'     => isset( $v['species'] ) ? $v['species'] : '',
				'gender'      => isset( $v['gender'] ) ? $v['gender'] : '',
				'personality' => isset( $v['personality'] ) ? $v['personality'] : '',
				'birthday'    => $birthday,
				'sign'        => $sign,
				'catchphrase' => isset( $v['catchphrase'] ) ? $v['catchphrase'] : '',
				'quote'       => isset( $v['favoriteSaying'] ) ? $v['favoriteSaying'] : '',
				'clothing'    => isset( $v['defaultClothing'] ) ? $v['defaultClothing'] : '',
				'icon'        => isset( $v['iconImage'] ) ? $v['iconImage'] : '',
				'icon_url'    => isset( $v['iconImage'] ) ? $v['iconImage'] : '',
				'image'       => $photoImage ? $photoImage : (isset( $v['iconImage'] ) ? $v['iconImage'] : ''),
				'image_url'   => isset( $v['iconImage'] ) ? $v['iconImage'] : ($photoImage ? $photoImage : ''),
				'poster_url'  => $poster_url,
				'render_url'  => $render_url,
				'favorite_colors' => isset( $v['colors'] ) ? $v['colors'] : array(),
				'favorite_styles' => isset( $v['styles'] ) ? $v['styles'] : array()
			);
		}

		file_put_contents( $compact_file, json_encode( $compact ) );
		return rest_ensure_response( $compact );
	}

	public function get_nookipedia_villagers( WP_REST_Request $request ) {
		$cache_key = 'xophz_nook_villagers_cache_v3';
		$cached_villagers = get_transient( $cache_key );

		if ( $cached_villagers !== false ) {
			return rest_ensure_response( $cached_villagers );
		}

		$all_villagers = array();
		$limit = 500;
		$offset = 0;
		$has_more = true;
		$mw_api_url = 'https://nookipedia.com/w/api.php';

		while ( $has_more ) {
			$params = array(
				'action' => 'cargoquery',
				'format' => 'json',
				'tables' => 'villager',
				'fields' => 'name,image_url,species,personality,birthday,sign,quote,phrase,gender,clothing,url',
				'limit'  => $limit,
				'offset' => $offset,
			);

			$url = add_query_arg( $params, $mw_api_url );
			$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				break; // Stop on error
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( empty( $data['cargoquery'] ) ) {
				$has_more = false;
			} else {
				foreach ( $data['cargoquery'] as $row ) {
					$all_villagers[] = array(
						'id'          => sanitize_title( $row['title']['name'] ),
						'name'        => $row['title']['name'],
						'image_url'   => $row['title']['image_url'],
						'species'     => $row['title']['species'],
						'personality' => $row['title']['personality'],
						'birthday'    => $row['title']['birthday'],
						'sign'        => $row['title']['sign'],
						'quote'       => $row['title']['quote'],
						'phrase'      => $row['title']['phrase'],
						'gender'      => $row['title']['gender'],
						'clothing'    => $row['title']['clothing'],
						'url'         => $row['title']['url'],
					);
				}
				$offset += $limit;
			}
		}

		if ( ! empty( $all_villagers ) ) {
			set_transient( $cache_key, $all_villagers, WEEK_IN_SECONDS );
		}

		return rest_ensure_response( $all_villagers );
	}
}
