<?php
/**
 * Plugin Name: Timeline
 * Plugin URI: http://mark.mcwilliams.me/wordpress/plugin/timeline/
 * Description: Simple way to record and display events from the past, the present, and the future!
 * Author: Mark McWilliams
 * Author URI: http://mark.mcwilliams.me/
 * Version: 0.2.2
 * Text Domain: timeline
 *
 * Copyright 2013 - Mark McWilliams (mark@mcwilliams.me)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Main mcwTimeline Class
 */
class mcwTimeline {

	/**
	 * Adds hooks and initiates the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'timeline_i18n' ), 5 );
		add_action( 'init', array( $this, 'timeline_cpt_init' ) );

		remove_action( 'future_timeline', array( $this, '_future_post_hook' ) );
		add_action( 'wp_insert_post_data', array( $this, 'publish_future_timeline' ) );

		add_action( 'pre_get_posts', array( $this, 'timeline_default_order' ) );

		add_filter( 'template_include', array( $this, 'include_timeline_template' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'include_timeline_template_css' ) );

		add_shortcode( 'timeline', array( $this, 'timeline_shortcode_setup' ) );

	}

	/**
	 * Initiate the i18n files (early).
	 *
	 * @since 1.0.0
	 *
	 * @uses load_plugin_textdomain()
	 */
	public function timeline_i18n() {

		load_plugin_textdomain( 'timeline', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Registers the Timeline Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @uses register_post_type()
	 * @uses apply_filters() Calls 'timeline_cpt_rewrite' on 'rewrite' argument.
	 * @uses apply_filters() Calls 'timeline_cpt_supports' on 'supports' argument.
	 * @uses apply_filters() Calls 'timeline_cpt_archive' on 'has_archive' argument.
	 */
	public function timeline_cpt_init() {

		$labels = array(
			'name'         => __( 'Timeline', 'timeline' ),
			'menu_name'    => __( 'Timeline', 'timeline' ),
			'all_items'    => __( 'All Timelines', 'timeline' ),
			'add_new'      => __( 'New Timeline', 'timeline' ),
			'add_new_item' => __( 'Create New Timeline', 'timeline' ),
			'edit'         => __( 'Edit', 'timeline' ),
			'edit_item'    => __( 'Edit Timeline', 'timeline' ),
			'new_item'     => __( 'New Timeline', 'timeline' ),
			'search_items' => __( 'Search Timelines', 'timeline' )
		);

		$args = array(
			'labels'              => $labels,
			'rewrite'             => apply_filters( 'timeline_cpt_rewrite', true ),
			'supports'            => apply_filters( 'timeline_cpt_supports', array( 'title', 'editor' ) ),
			'menu_position'       => 20,
			'has_archive'         => apply_filters( 'timeline_cpt_archive', true ),
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'public'              => true,
			'show_ui'             => true,
			'can_export'          => true,
			'query_var'           => false
		);

		register_post_type( 'timeline', $args );

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

		if ( $query->get( 'post_type' ) == 'timeline' ) {

			if ( $query->get( 'orderby' ) == '' )

				$query->set( 'orderby', 'date' );

			if ( $query->get( 'order' ) == '' )

				$query->set( 'order', 'ASC' );

		}

	}

	/**
	 * Need to document what happens here!
	 */
	public function include_timeline_template( $template ) {

		if ( is_post_type_archive( 'timeline' ) ):

			$template_name = 'archive-timeline.php';

		elseif ( is_singular( 'timeline' ) ):

			$template_name = 'single-timeline.php';

		else:

			return $template; // return early if it's not a template we care about

		endif;

		$template = locate_template( array( $template_name ) );

		if ( empty( $template ) )

			$template = dirname( __FILE__ ) . '/template/' . $template_name;

		return $template;

	}

	/**
	 * Includes the custom timeline.css file.
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_enqueue_style()
	 *
	 * TODO: Check that you can use your own style(s) if wanted?
	 */
	public function include_timeline_template_css() {

		wp_enqueue_style( 'timeline', plugins_url( '/template/css/timeline.css', __FILE__ ) );

	}

	/**
	 * Need to document what happens here!
	 */
	public function timeline_shortcode_setup( $atts ) {

		extract( shortcode_atts( array(
			'foo' => 'bar',
			'bar' => 'foo',
			/**
			 * Figure out proper attributes!
			 */
		), $atts ) );

		$start = '<ol id="timeline">';

		$timeline = new WP_Query( array(
			'post_type' => 'timeline',
			'order' => 'ASC',
			'orderby' => 'date',
			/**
			 * Still more to add. These will be the [timeline] attributes I think?
			 */
		) );

		ob_start();

		while ( $timeline->have_posts() ) : $timeline->the_post(); ?>

			<li id="timeline-<?php the_ID(); ?>">
				<h3 class="entry-title"><?php the_title(); ?></h3>
				<span class="entry-meta-date">When: <time datetime="0000-00-00"><?php the_time( 'F j, Y' ); ?></time></span>
				<span class="entry-meta-time">Time: <time datetime="0000-00-00"><?php the_time( 'g:i A' ); ?></time></span>
				<?php the_content(); ?>
			</li><!-- #timeline-<?php the_ID(); ?> -->

		<?php endwhile; wp_reset_postdata();

		$finish .= '</ol><!-- #timeline -->';

		return $start . ob_get_clean() . $finish;

	}

}

new mcwTimeline();

?>