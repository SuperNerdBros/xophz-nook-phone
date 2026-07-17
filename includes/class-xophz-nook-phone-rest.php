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
			'permission_callback' => '__return_true',
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
		
		if ( empty( $app_slug ) || empty( $rating ) ) {
			return new WP_Error( 'missing_params', 'App slug and Rating are required', array( 'status' => 400 ) );
		}

		if ( $rating < 1 || $rating > 5 ) {
			return new WP_Error( 'invalid_rating', 'Rating must be between 1 and 5', array( 'status' => 400 ) );
		}

		$app_id = $this->get_or_create_app( $app_slug );

		$total_rating = (float) get_post_meta( $app_id, '_nook_app_total_rating', true );
		$rating_count = (int) get_post_meta( $app_id, '_nook_app_rating_count', true );

		$total_rating += $rating;
		$rating_count++;

		$average_rating = $total_rating / $rating_count;

		update_post_meta( $app_id, '_nook_app_total_rating', $total_rating );
		update_post_meta( $app_id, '_nook_app_rating_count', $rating_count );
		update_post_meta( $app_id, '_nook_app_average_rating', round($average_rating, 1) );

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

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();
				$apps[] = array(
					'id'             => $id,
					'slug'           => $query->post->post_name,
					'title'          => get_the_title(),
					'description'    => get_the_content(),
					'installs'       => (int) get_post_meta( $id, '_nook_app_installs', true ),
					'average_rating' => (float) get_post_meta( $id, '_nook_app_average_rating', true ),
					'rating_count'   => (int) get_post_meta( $id, '_nook_app_rating_count', true ),
					'thumbnail'      => get_the_post_thumbnail_url( $id, 'full' ),
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
}
