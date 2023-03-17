<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;


class PostType {

	protected static $FLUSH = false;
	public static $POST_TYPE = 'stimulsoft_report';


	public static function init() {

		// Register Custom Post Type
		add_action( 'init', [ PostType::class, 'register_post_type' ] );

		// Post Type Rewriterule
		add_action( 'init', [ PostType::class, 'register_post_type_rewriterule' ] );

	}


	/**
	 * Register Report Post Type
	 */
	public static function register_post_type() {

		$labels = [
//			'name'                     => esc_html__( 'گزارشات', 'your-textdomain' ),
//			'singular_name'            => esc_html__( 'گزارش', 'your-textdomain' ),
//			'add_new'                  => esc_html__( 'افزودن جدید', 'your-textdomain' ),
//			'add_new_item'             => esc_html__( 'افزودن گزارش جدید', 'your-textdomain' ),
//			'edit_item'                => esc_html__( 'ویرایش گزارش', 'your-textdomain' ),
//			'new_item'                 => esc_html__( 'گزارش جدید', 'your-textdomain' ),
//			'view_item'                => esc_html__( 'نمایش گزارش', 'your-textdomain' ),
//			'view_items'               => esc_html__( 'نمایش گزارشات', 'your-textdomain' ),
//			'search_items'             => esc_html__( 'جستجوی گزارشات', 'your-textdomain' ),
//			'not_found'                => esc_html__( 'هیچ گزارش پیدا نشد.', 'your-textdomain' ),
//			'not_found_in_trash'       => esc_html__( 'هیچ گزارشی در زباله دان پیدا نشد.', 'your-textdomain' ),
//			'parent_item_colon'        => esc_html__( 'گزارش پدر:', 'your-textdomain' ),
//			'all_items'                => esc_html__( 'همه گزارشات', 'your-textdomain' ),
//			'archives'                 => esc_html__( 'گزارش Archives', 'your-textdomain' ),
//			'attributes'               => esc_html__( 'گزارش Attributes', 'your-textdomain' ),
//			'insert_into_item'         => esc_html__( 'Insert into گزارش', 'your-textdomain' ),
//			'uploaded_to_this_item'    => esc_html__( 'Uploaded to this گزارش', 'your-textdomain' ),
//			'featured_image'           => esc_html__( 'Featured image', 'your-textdomain' ),
//			'set_featured_image'       => esc_html__( 'Set featured image', 'your-textdomain' ),
//			'remove_featured_image'    => esc_html__( 'Remove featured image', 'your-textdomain' ),
//			'use_featured_image'       => esc_html__( 'Use as featured image', 'your-textdomain' ),
//			'menu_name'                => esc_html__( 'گزارشات', 'your-textdomain' ),
//			'filter_items_list'        => esc_html__( 'Filter گزارشات list', 'your-textdomain' ),
//			'filter_by_date'           => esc_html__( '', 'your-textdomain' ),
//			'items_list_navigation'    => esc_html__( 'گزارشات list navigation', 'your-textdomain' ),
//			'items_list'               => esc_html__( 'لیست گزارشات', 'your-textdomain' ),
//			'item_published'           => esc_html__( 'گزارش منتشر شده', 'your-textdomain' ),
//			'item_published_privately' => esc_html__( 'گزارش published privately', 'your-textdomain' ),
//			'item_reverted_to_draft'   => esc_html__( 'گزارش reverted to draft', 'your-textdomain' ),
//			'item_scheduled'           => esc_html__( 'گزارش scheduled', 'your-textdomain' ),
//			'item_updated'             => esc_html__( 'گزارش بروزرسانی شد', 'your-textdomain' ),
		];

		$args = [
			'label'       => esc_html__( 'گزارشات', 'your-textdomain' ),
			'labels'      => $labels,
			'description' => '',

			'public'             => true,
			'publicly_queryable' => true,
			'query_var'          => 'report',
//			'rewrite'            => false,
//			'_builtin'           => true,

			'hierarchical'        => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => false,
			'can_export'          => true,
			'delete_with_user'    => false,
			'has_archive'         => false,
			'rest_base'           => '',
			'show_in_menu'        => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-analytics',
			'capability_type'     => 'post',
			'supports'            => [ 'title' ],
			'taxonomies'          => [],
			'rewrite'             => [
				'ep_mask'             => EP_NONE,
				'slug'                => 'report',
				'permalink_structure' => '/%post_id%/',
				'with_front'          => false,
				'paged'               => false,
				'feed'                => false,
				'forcomments'         => false,
				'walk_dirs'           => false,
				'endpoints'           => false,
			],
		];

		register_post_type( self::$POST_TYPE, $args );

	}


	public static function register_post_type_rewriterule() {

//		add_rewrite_tag( "%post_id%", '([^/]+)', "p=" );

		$post_type     = self::$POST_TYPE;
		$post_type_obj = get_post_type_object( $post_type );

		$slug_placeholder = "%{$post_type}_slug%";
		$permalink        = $slug_placeholder . $post_type_obj->rewrite['permalink_structure'];

		add_rewrite_tag( $slug_placeholder, '(' . $post_type_obj->rewrite['slug'] . ')', 'post_type=' . $post_type . '&slug=' );
		add_permastruct( self::$POST_TYPE, $permalink, $post_type_obj->rewrite );

		if ( defined( 'WP_SANDBOX_SCRAPING') && WP_SANDBOX_SCRAPING ) {
			flush_rewrite_rules();
		}

		/**
		 * Handle the '%post_id%' URL placeholder
		 *
		 * @param  string    $link  The link to the post
		 * @param  \WP_Post  $post  object $post The post object
		 *
		 * @return string
		 */
		add_filter( 'post_type_link', function ( $permalink, $post ) {

			if ( $post->post_type == self::$POST_TYPE ) {

				$post_type     = self::$POST_TYPE;
				$post_type_obj = get_post_type_object( $post_type );

				$permalink = str_replace( [
					'%post_id%',
					"%{$post_type}_slug%",
				], [
					$post->ID,
					$post_type_obj->rewrite['slug'],
				], $permalink );
			}

			return $permalink;
		}, 999, 2 );

//		add_filter( 'rewrite_rules_array', function ( $rules ) {
//
////			var_dump( $rules);
////			die();
//			$rules['report/([0-9]+)/?$'] = 'index.php?p=$matches[1]&report=' . self::$POST_TYPE;
//
//			return $rules;
//		}, 999 );


	}

}

PostType::init();