<?php
/**
 * File which registers the Custom Post Type.
 *
 * @package    Timeline
 * @subpackage Includes
 * @since      0.1.0
 * @author     Mark McWilliams <mark@mcwilliams.me>
 * @copyright  Copyright (c) 2013, Mark McWilliams
 * @link       http://mark.mcwilliams.me/plugins/timeline/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

add_action( 'init', 'setup_timeline_post_type' );

/**
 * Registers the required Custom Post Type for the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function setup_timeline_post_type() {

	$labels = array(
		'name'           => __( 'Timeline Entries',           'timeline' ),
		'menu_name'      => __( 'Timeline',                   'timeline' ),
		'singular_name'  => __( 'Timeline Entry',             'timeline' ),
		'name_admin_bar' => __( 'Timeline Entry',             'timeline' ),
		'add_new'        => __( 'Add New',                    'timeline' ),
		'add_new_item'   => __( 'Add New Timeline Entry',     'timeline' ),
		'edit_item'      => __( 'Edit Timeline Entry',        'timeline' ),
		'new_item'       => __( 'New Timeline Entry',         'timeline' ),
		'all_items'      => __( 'All Timelines',              'timeline' ),
		'view_item'      => __( 'View Timeline Entry',        'timeline' ),
		'items_archive'  => __( 'Timeline Archive',           'timelime' ),
		'search_items'   => __( 'Search Timeline Entries',    'timeline' ),
		'not_found'      => __( 'Timeline Entries Not Found', 'timeline' )
	);

	$supports = array(
		'title',
		'editor'
	);

	$args = array(
		'labels'              => $labels,
		'supports'            => $supports,
		'description'         => __( 'Timeline', 'timeline' ),
		'public'              => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 22,
		'menu_icon'           => '',
		'has_archive'         => apply_filters( 'timeline_archive_variable', false ),
		'rewrite'             => apply_filters( 'timeline_rewrite_variable', false ),
		'query_var'           => false
	);

	register_post_type( 'timeline', $args );

}

?>