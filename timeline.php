<?php
/**
 * Plugin Name: Timeline
 * Plugin URI: http://mark.mcwilliams.me/wordpress/timeline-plugin/
 * Descritpion: Simple way to record and display events from the past, present, and future!
 * Author: Mark McWilliams
 * Author URI: http://mark.mcwilliams.me/
 * Version: 0.1-alpha
 *
 * Something pretty plain and simple ATM!
 * Learning WHY you do what, or attempting!
 */

class Timeline {

	/* Magic code in here! */

	public function __construct() {

		/* Register initial stuffs! */
		/* Apparently you need to do the array() in there? */

		add_action( 'init', array( &$this, 'register_timeline_post_type' ) );

	}

	public function register_timeline_post_type() {

		/* Probably lacking some code? */

		$labels = array(
			'name'               => __( 'Timeline',            'timeline' ),
			'menu_name'          => __( 'Timeline',            'timeline' ),
			'singular_name'      => __( 'Timeline',            'timeline' ),
			'all_items'          => __( 'All Timelines',       'timeline' ),
			'add_new'            => __( 'New Timeline',        'timeline' ),
			'add_new_item'       => __( 'Create New Timeline', 'timeline' ),
			'edit'               => __( 'Edit',                'timeline' ),
			'edit_item'          => __( 'Edit Timeline',       'timeline' ),
			'new_item'           => __( 'New Timeline',        'timeline' ),
			'search_items'       => __( 'Search Timelines',    'timeline' )
		);

		register_post_type( 'timeline', array(
			'labels'              => $labels,
			'rewrite'             => false,
			'supports'            => array( 'title', 'editor' ),
			'menu_position'       => 20,
			'has_archive'         => false, /* For present moment and time! */
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'public'              => true,
			'show_ui'             => true,
			'can_export'          => true,
			'query_var'           => false,
		) );

	}

}

?>