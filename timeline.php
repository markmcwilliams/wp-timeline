<?php
/**
 * Plugin Name: Timeline
 * Plugin URI: http://mark.mcwilliams.me/wordpress/plugin/timeline/
 * Description: Simple way to record and display events from the past, the present, and the future!
 * Author: Mark McWilliams
 * Author URI: http://mark.mcwilliams.me/
 * Version: Beta 0.3.0
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

		/* Load the plugins Text Domain */
		add_action( 'init', array( $this, 'timeline_i18n' ) );

		/* Register the 'timeline' Custom Post Type */
		add_action( 'init', array( $this, 'timeline_cpt_init' ) );

		/* Registers activation and deactivation hooks. */
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		/* Register the site style. */
		add_action( 'wp_enqueue_scripts', array( $this, 'register_timeline_css' ) );

		/* Removes the '_future_post_hook' in favour of a custom action. */
		remove_action( 'future_timeline', array( $this, '_future_post_hook' ) );
		add_action( 'wp_insert_post_data', array( $this, 'publish_future_timeline' ) );

		/* Registers the alterations to default 'timeline' query. */
		add_action( 'pre_get_posts', array( $this, 'timeline_default_order' ) );

		/* Registers the location of included templates. */
		add_filter( 'template_include', array( $this, 'include_timeline_template' ) );

		/* Registers the [timeline] shortcode. */
		add_shortcode( 'timeline', array( $this, 'timeline_shortcode_setup' ) );

	}

	/**
	 * Fired when the plugin gets activated.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		/* Input functionality here. */
	}

	/**
	 * Fired when the plugin gets deactivated.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		/* Input functionality here. */
	}

	/**
	 * Initiate the i18n files.
	 *
	 * @since 1.0.0
	 *
	 * @uses load_plugin_textdomain()
	 */
	public function timeline_i18n() {

		load_plugin_textdomain( 'timeline', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Registers the 'timeline' Custom Post Type.
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
	 * Registers and enqueues the custom timeline.css style.
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_enqueue_style()
	 *
	 * TODO: Check that you can use your own style(s) if wanted?
	 */
	public function register_timeline_css() {

		wp_enqueue_style( 'timeline', plugins_url( '/template/css/timeline.css', __FILE__ ) );

	}

	/**
	 * Sets all of the posts added to the 'timeline' CPT with
	 * a future timestamp to 'publish' when you publish them.
	 *
	 * Thanks to Andrew Nacin for the snippet of code.
	 *
	 * @link http://wordpress.org/support/topic/publish-scheduled-posts-in-35#post-3561466
	 * @link http://plugins.trac.wordpress.org/changeset/639040
	 *
	 * @since 1.0.0
	 */
	public function publish_future_timeline( $data ) {

		if ( $data['post_status'] == 'future' && $data['post_type'] == 'timeline' )

			$data['post_status'] = 'publish';

		return $data;

	}

	/**
	 * Changes the default order in which 'timeline' posts are
	 * displayed. We want to show the closest post/event first,
	 * based on the date, and in an ascending order.
	 *
	 * @since 1.0.0
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
	 * If you're using the built-in archive for 'timeline' then
	 * we need to include our specific templates. We first do a
	 * check in the Parent and Child theme directories before
	 * including the relevant template supplied.
	 *
	 * @since 1.0.0
	 *
	 * @uses locate_template()
	 */
	public function include_timeline_template( $template ) {

		if ( is_post_type_archive( 'timeline' ) ):

			$template_name = 'archive-timeline.php';

		elseif ( is_singular( 'timeline' ) ):

			$template_name = 'single-timeline.php';

		else:

			return $template; // Return early if it's not a template we care about.

		endif;

		$template = locate_template( array( $template_name ) );

		if ( empty( $template ) )

			$template = dirname( __FILE__ ) . '/template/' . $template_name;

		return $template;

	}

	/**
	 * Registers the main functions of the [timeline] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function timeline_shortcode_setup( $atts ) {

		extract( shortcode_atts( array(
			'foo' => 'bar',
			'bar' => 'foo',
			/**
			 * Still to figure our proper attributes.
			 * Any suggestions are more than welcome.
			 */
		), $atts ) );

		$start = '<ol id="timeline">';

		$timeline = new WP_Query( array(
			'post_type' => 'timeline',
			'order' => 'ASC',
			'orderby' => 'date',
			/**
			 * Still to get these added. Will more than likely
			 * just be the [timeline] attributes I would think?
			 */
		) );

		/* TODO: Apparently ob isn't recommended. So do it a clean(er) way! */
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