<?php

/**
 * Plugin Name: Timeline
 * Plugin URI:  http://mark.mcwilliams.me/wordpress/timeline-plugin/
 * Description: Record and display events from the past, present, and future!
 * Author:      Mark McWilliams
 * Author URI:  http://mark.mcwilliams.me/
 * Version:     0.1.0
 * Text Domain: timeline
 * Domain Path: /languages/
 */

class Timeline {

	/**
	 * Sets up actions/filters apparently?
	 *
	 * @since 0.1.0
	 */
	public function __construct() {}

	/**
	 * Registers a [timeline] shortcode I believe?
	 * Still need more code to go along with this?
	 *
	 * @since  0.1.0
	 */
	public function register_shortcode() {
		add_shortcode( 'timeline', array( &$this, 'do_shortcode' ) );
	}

	/**
	 * Setup of the Timeline Events Post Type
	 *
	 * @since 0.1.0
	 * @uses register_post_type() To register the post types
	 * @uses apply_filters() Calls various filters to modify the arguments
	 *                        sent to register_post_type()
	 */
	public static function register_post_types() {

		// Define local variable(s)
		$post_type = array(); // subtle inspiration from bbPress

		// Event labels
		$post_type['labels'] = array(
			'name'               => __( 'Events',                   'timeline' ),
			'menu_name'          => __( 'Timeline',                 'timeline' ),
			'singular_name'      => __( 'Event',                    'timeline' ),
			'all_items'          => __( 'All Events',               'timeline' ),
			'add_new'            => __( 'New Event',                'timeline' ),
			'add_new_item'       => __( 'Create New Event',         'timeline' ),
			'edit'               => __( 'Edit',                     'timeline' ),
			'edit_item'          => __( 'Edit Event',               'timeline' ),
			'new_item'           => __( 'New Event',                'timeline' ),
			'view'               => __( 'View Event',               'timeline' ),
			'view_item'          => __( 'View Event',               'timeline' ),
			'search_items'       => __( 'Search Events',            'timeline' ),
			'not_found'          => __( 'No Events found',          'timeline' ),
			'not_found_in_trash' => __( 'No Events found in Trash', 'timeline' ),
			'parent_item_colon'  => __( 'Parent Event:',            'timeline' )
		);

		// Event supports
		$post_type['supports'] = array(
			'title',
			'editor',
			'revisions'
		);

		// Register Event content type
		register_post_type(
			'timeline',
			apply_filters( 'timeline_post_type_registration', array(
				'labels'              => $post_type['labels'],
				'supports'            => $post_type['supports'],
				'rewrite'             => false,
				'description'         => __( 'Timeline Events', 'timeline' ),
				'menu_position'       => 20,
				'has_archive'         => bbp_get_root_slug(),
				'exclude_from_search' => true,
				'show_in_nav_menus'   => true,
				'public'              => true,
				'can_export'          => true,
				'hierarchical'        => false,
				'query_var'           => true,
				'menu_icon'           => ''
			) )
		);

	/**
	 * Register the topic tag taxonomy
	 *
	 * @since 0.1.0
	 * @uses register_taxonomy() To register the taxonomy
	 */
	public static function register_taxonomies() {

		// Define local variable(s)
		$topic_tag = array();

		// Topic tag labels
		$topic_tag['labels'] = array(
			'name'          => __( 'Topic Tags',     'timeline' ),
			'singular_name' => __( 'Topic Tag',      'timeline' ),
			'search_items'  => __( 'Search Tags',    'timeline' ),
			'popular_items' => __( 'Popular Tags',   'timeline' ),
			'all_items'     => __( 'All Tags',       'timeline' ),
			'edit_item'     => __( 'Edit Tag',       'timeline' ),
			'update_item'   => __( 'Update Tag',     'timeline' ),
			'add_new_item'  => __( 'Add New Tag',    'timeline' ),
			'new_item_name' => __( 'New Tag Name',   'timeline' ),
			'view_item'     => __( 'View Topic Tag', 'timeline' )
		);

		// Register the topic tag taxonomy
		register_taxonomy(
			'timeline_type',
			'timeline',
			apply_filters( 'timeline_type_taxonomy_registration', array(
				'labels'                => $topic_tag['labels'],
				'rewrite'               => false,
				'query_var'             => true,
				'show_tagcloud'         => true,
				'hierarchical'          => false,
				'public'                => true
			) )
		) );
	}
}

new Timeline();

?>