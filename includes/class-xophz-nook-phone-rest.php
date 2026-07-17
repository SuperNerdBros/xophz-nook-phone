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

		// DMs
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

		register_rest_route( $namespace, '/nookipedia/villagers', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_nookipedia_villagers' ),
			'permission_callback' => '__return_true',
		) );
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
		if ( empty( $app_slug ) ) {
			return new WP_Error( 'missing_app_slug', 'App slug is required', array( 'status' => 400 ) );
		}

		$app_id = $this->get_or_create_app( $app_slug );

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
        $passport_id = wp_insert_post( array(
            'post_title'  => sanitize_text_field( $passport_data['name'] ) . "'s Passport",
            'post_type'   => 'nook_passport',
            'post_status' => 'publish',
            'post_author' => $user_id
        ) );

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
				if ( $user_id ) {
					$existing_comments = get_comments( array(
						'post_id' => $id,
						'user_id' => $user_id,
						'count'   => false,
					) );
					if ( ! empty( $existing_comments ) ) {
						$user_rating = (float) get_comment_meta( $existing_comments[0]->comment_ID, '_nook_app_rating', true );
					}
				}

				$apps[] = array(
					'id'             => $id,
					'slug'           => $query->post->post_name,
					'title'          => get_the_title(),
					'description'    => get_the_content(),
					'installs'       => (int) get_post_meta( $id, '_nook_app_installs', true ),
					'average_rating' => (float) get_post_meta( $id, '_nook_app_average_rating', true ),
					'rating_count'   => (int) get_post_meta( $id, '_nook_app_rating_count', true ),
					'thumbnail'      => get_the_post_thumbnail_url( $id, 'full' ),
					'user_rating'    => $user_rating
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
				$threads[] = array(
					'id'            => $id,
					'title'         => get_the_title(),
					'content'       => get_the_content(),
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

		return rest_ensure_response( array(
			'success'   => true,
			'thread_id' => $post_id
		) );
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

		$all_dms = array_merge( $sent, $received );
		usort( $all_dms, function($a, $b) {
			return strtotime( $a->post_date ) - strtotime( $b->post_date );
		});

		$messages = array();
		foreach ( $all_dms as $dm ) {
			$is_read = get_post_meta( $dm->ID, '_nook_dm_read', true );
			
			// Mark as read if we are the recipient
			$recipient_id = (int) get_post_meta( $dm->ID, '_nook_dm_recipient', true );
			if ( $recipient_id === $user_id && ! $is_read ) {
				update_post_meta( $dm->ID, '_nook_dm_read', true );
				$is_read = true;
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

	public function send_dm( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$recipient_id = (int) $request->get_param( 'recipient_id' );
		$content = sanitize_textarea_field( $request->get_param( 'content' ) );

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
			'post_title'   => 'DM from ' . $user_id . ' to ' . $recipient_id,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'nook_dm',
			'post_author'  => $user_id,
		) );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		update_post_meta( $post_id, '_nook_dm_recipient', $recipient_id );
		update_post_meta( $post_id, '_nook_dm_read', false );

		return rest_ensure_response( array(
			'success' => true,
			'dm_id'   => $post_id,
			'date'    => get_the_date('c', $post_id)
		) );
	}

	public function get_nook_state( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$state = get_user_meta( $user_id, '_nook_os_state', true );
		if ( empty( $state ) ) {
			return rest_ensure_response( array( 'state' => null ) );
		}
		
		return rest_ensure_response( array(
			'state' => json_decode( $state, true )
		) );
	}

	public function save_nook_state( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$state_json = $request->get_param( 'state' );
		
		if ( empty( $state_json ) ) {
			return new WP_Error( 'missing_state', 'No state data provided.', array( 'status' => 400 ) );
		}
		
		// Validate it's proper JSON by decoding and re-encoding
		if ( is_string( $state_json ) ) {
			$state_data = json_decode( $state_json, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return new WP_Error( 'invalid_json', 'Invalid JSON payload.', array( 'status' => 400 ) );
			}
			update_user_meta( $user_id, '_nook_os_state', wp_json_encode( $state_data ) );
		} else if ( is_array( $state_json ) ) {
			update_user_meta( $user_id, '_nook_os_state', wp_json_encode( $state_json ) );
		}
		
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

	public function get_nookipedia_items( WP_REST_Request $request ) {
		$cache_key = 'xophz_nook_shopping_items_cache_v6';
		$cached_items = get_transient( $cache_key );

		if ( $cached_items !== false ) {
			return rest_ensure_response( $cached_items );
		}

		$all_items = array();
		$mw_api_url = 'https://nookipedia.com/w/api.php';
		
		// Use a dynamic offset based on the day of the year so the stock rotates daily
		$params = array(
			'action' => 'cargoquery',
			'format' => 'json',
			'tables' => 'nh_item',
			'fields' => 'en_name=name,image_url,buy1_price,sell',
			'limit'  => 500,
		);

		$url = add_query_arg( $params, $mw_api_url );
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( ! empty( $data['cargoquery'] ) ) {
				$categories = array('Daily Selection', 'Promotion', 'Seasonal', 'Furniture', 'Fashion', 'Wallpapers & Rugs', 'Other');
				$seen_ids = array();
				foreach ( $data['cargoquery'] as $row ) {
					$name = $row['title']['name'];
					$id = sanitize_title($name);
					
					if ( isset( $seen_ids[$id] ) ) {
						continue;
					}
					$seen_ids[$id] = true;
					
					// Get real prices from the Cargo API
					$buy_price_raw = $row['title']['buy1_price'];
					$sell_price_raw = $row['title']['sell'];
					
					$buy = $buy_price_raw ? intval($buy_price_raw) : 0;
					$sell = $sell_price_raw ? intval($sell_price_raw) : 0;
					
					// Weigh 'Furniture' and 'Fashion' heavily, others less so
					$hash = md5($name);
					$cat_num = hexdec(substr($hash, 4, 2)) % 100;
					if ($cat_num < 15) {
						$category = 'Daily Selection';
					} elseif ($cat_num < 20) {
						$category = 'Promotion';
					} elseif ($cat_num < 25) {
						$category = 'Seasonal';
					} elseif ($cat_num < 50) {
						$category = 'Furniture';
					} elseif ($cat_num < 75) {
						$category = 'Fashion';
					} elseif ($cat_num < 90) {
						$category = 'Wallpapers & Rugs';
					} else {
						$category = 'Other';
					}

					$all_items[] = array(
						'id'           => $id,
						'name'         => $name,
						'image'        => $row['title']['image_url'],
						'imageUrl'     => $row['title']['image_url'],
						'image_url'    => $row['title']['image_url'],
						'buy_price'    => $buy,
						'sell_price'   => $sell,
						'is_orderable' => $buy > 0,
						'category'     => $category,
					);
				}
			}
		}

		if ( ! empty( $all_items ) ) {
			set_transient( $cache_key, $all_items, WEEK_IN_SECONDS );
		}

		return rest_ensure_response( $all_items );
	}

	public function get_nookipedia_villagers( WP_REST_Request $request ) {
		$cache_key = 'xophz_nook_villagers_cache_v2';
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
				'fields' => 'name,image_url,species,personality,birthday,sign',
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
