<?php
/**
 * Plugin Name: Timeline
 * Plugin URI: http://mark.mcwilliams.me/wordpress/plugin/timeline/
 * Descritpion: Simple way to record and display events from the past, present, and future!
 * Author: Mark McWilliams
 * Author URI: http://mark.mcwilliams.me/
 * Version: 0.1.1
 * Text Domain: timeline
 */

class mcwTimeline {

	/**
	 * Need to document what happens here!
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

		remove_action( 'future_timeline', array( $this, '_future_post_hook' ) );
		add_action( 'wp_insert_post_data', array( $this, 'publish_future_timeline' ) );

		add_action( 'pre_get_posts', array( $this, 'timeline_default_order' ) );

	}

	/**
	 * Need to document what happens here!
	 */
	public function init() {

		load_plugin_textdomain( 'timeline', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		register_post_type( 'timeline', array(
			'labels' => array(
				'name' => __( 'Timeline', 'timeline' ),
				'menu_name' => __( 'Timeline', 'timeline' ),
				'all_items' => __( 'All Timelines', 'timeline' ),
				'add_new' => __( 'New Timeline', 'timeline' ),
				'add_new_item' => __( 'Create New Timeline', 'timeline' ),
				'edit' => __( 'Edit', 'timeline' ),
				'edit_item' => __( 'Edit Timeline', 'timeline' ),
				'new_item' => __( 'New Timeline', 'timeline' ),
				'search_items' => __( 'Search Timelines', 'timeline' ) ),
			'rewrite' => true,
			'supports' => array( 'title', 'editor' ),
			'menu_position' => 20,
			'has_archive' => true,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => true,
			'public' => true,
			'show_ui' => true,
			'can_export' => true,
			'query_var' => false
		) );

	}

	/**
	 * Need to document what happens here!
	 */
	public function publish_future_timeline( $data ) {

		if ( $data['post_status'] == 'future' && $data['post_type'] == 'timeline' )

			$data['post_status'] = 'publish';

		return $data;

	}

	/**
	 * Need to document what happens here!
	 */
	public function timeline_default_order( $query ) {

		if( 'timeline' == $query->get( 'post_type' ) ) {

			if( $query->get( 'orderby' ) == '' )

				$query->set( 'orderby', 'date' );

			if( $query->get( 'order' ) == '' )

				$query->set( 'order', 'ASC' );

		}

	}

}

new mcwTimeline();

?>