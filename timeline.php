<?php
/**
 * Plugin Name: Timeline
 * Plugin URI: http://mark.mcwilliams.me/wordpress/timeline/
 * Description: Manage Timeline Events Through WordPress
 * Author: Mark McWilliams
 * Author URI: http://mark.mcwilliams.me/
 * Version: 0.1.0
 * Text Domain: timeline
 * Domain Path: languages
 *
 * Timeline is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Timeline is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Timeline. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Timeline
 * @author Mark McWilliams
 * @version 0.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Timeline' ) ) :

/**
 * Main Timeline Class
 *
 * @since 0.1.0
 */
final class Timeline {

	/**
	 * Plugin Version
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $version = '0.1.0';

	/**
	 * Unique Identifier
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $plugin_slug = 'timeline';

	/**
	 * Single Instance Of Timeline
	 *
	 * @since 0.1.0
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initiate The Plugin
	 *
	 * @since 0.1.0
	 */
	private function __construct() {

		// Load Plugins Text Domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Register If Theme Doesn't Support Timeline
		if ( ! current_theme_supports( 'timeline' ) ) {

			// Default Styles
			add_action( 'wp_enqueue_scripts', array( $this, 'setup_default_styles' ) );

			// Default Templates
			add_action( 'template_include', array( $this, 'setup_default_templates' ) );

		}

		// Remove _future_post_hook In Favour Of A Custom Option
		remove_action( 'future_timeline', array( $this, '_future_post_hook' ) );
		add_action( 'wp_insert_post_data', array( $this, 'setup_future_entries' ) );

		// Register Default Query Alterations
		add_action( 'pre_get_posts', array( $this, 'setup_default_query' ) );

		// Add Option Page To The Admin (Maybe Just Filter Everything?)
		// add_action( 'admin_menu', array( $this, 'setup_admin_options' ) );

		// Register [timeline] Shortcode
		add_shortcode( 'timeline', array( $this, 'setup_shortcode_details' ) );

	}

	/**
	 * Return An Instance Of Timeline
	 *
	 * @since 0.1.0
	 * @return object Single Instance Of Timeline
	 */
	public static function instance() {

		// Set The Single Instance If It Hasn't Been Set Already
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Fired On Plugin Activation
	 *
	 * @since 0.1.0
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		//Flush Rewrite Rules
		flush_rewrite_rules();

	}


	/**
	 * Fired On Plugin Deactivation
	 *
	 * @since 0.1.0
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		// Flush Rewrite Rules
		flush_rewrite_rules();

	}

	/**
	 * Load the translation file for current language. Checks the languages
	 * folder inside the Timeline plugin first, and then the default WordPress
	 * languages folder.
	 *
	 * Note that custom translation files inside the Timeline plugin folder
	 * will be removed on Timeline updates. If you're creating custom
	 * translation files, please use the global language folder.
	 *
	 * @since 0.1.0
	 * @uses apply_filters() Calls 'timeline_locale' with the
	 *                        {@link get_locale()} value
	 * @uses load_textdomain() To load the textdomain
	 * @return bool True on success, false on failure
	 */
	public function load_textdomain() {

		// Traditional WordPress Plugin Locale Filter
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'timeline_locale',  get_locale(), $domain );
		$mofile = sprintf( '%1$s-%2$s.mo', $domain, $locale );

		// Setup Paths To Current Locale File
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/' . $domain . '/' . $mofile;

		// Look In Global /wp-content/languages/timeline/ Folder
		if ( file_exists( $mofile_global ) ) {

			return load_textdomain( $domain, $mofile_global );

		// Look In Local /wp-content/plugins/timeline/languages/ Folder
		} elseif ( file_exists( $mofile_local ) ) {

			return load_textdomain( $domain, $mofile_local );

		}

		// If Nothing Found
		return false;

	}

	/**
	 * Enqueues Default Styles
	 *
	 * Might never be registered if users theme supports Timeline?
	 *
	 * @since 0.1.0
	 * @uses wp_enqueue_style()
	 */
	public function setup_default_styles() {

		wp_enqueue_style( 'timeline', plugins_url( '/assets/css/default.css', __FILE__ ) );

	}

	/**
	 * Include Specific Templates
	 *
	 * If you're using the built-in archive for Timeline then
	 * we need to include our specific templates. We first do a
	 * check in the Parent and Child theme directories before
	 * including the relevant template supplied.
	 *
	 * @since 0.1.0
	 * @uses locate_template()
	 */
	public function setup_default_templates( $template ) {

		if ( is_post_type_archive( 'timeline' ) ) :

			$template_name = 'archive-timeline.php';

		elseif ( is_singular( 'timeline' ) ) :

			$template_name = 'single-timeline.php';

		else :

			return $template; // Return early if it's not a template we care about.

		endif;

		$template = locate_template( array( $template_name ) );

		if ( empty( $template ) )

			$template = dirname( __FILE__ ) . '/templates/' . $template_name;

		return $template;

	}

	/**
	 * Alter Future Entries
	 *
	 * Sets all of the posts added to Timeline with a future
	 * timestamp to 'publish' when you publish them. Thanks
	 * to Andrew Nacin for the snippet of code.
	 *
	 * @since 0.1.0
	 * @link http://wordpress.org/support/topic/publish-scheduled-posts-in-35#post-3561466
	 * @link http://plugins.trac.wordpress.org/changeset/639040
	 */
	public function setup_future_entries( $data ) {

		if ( $data['post_status'] == 'future' && $data['post_type'] == 'timeline' )

			$data['post_status'] = 'publish';

		return $data;

	}

	/**
	 * Changes the default order in which Timeline Entries are
	 * displayed. We want to show the closest post/event first,
	 * based on the date, and in an ascending order.
	 *
	 * @since 0.1.0
	 */
	public function setup_default_query( $query ) {

		if ( $query->get( 'post_type' ) == 'timeline' ) {

			if ( $query->get( 'orderby' ) == '' )

				$query->set( 'orderby', 'date' );

			if ( $query->get( 'order' ) == '' )

				$query->set( 'order', 'ASC' );

		}

		if ( is_post_type_archive( 'timeline' ) ) {

			$query->set( 'posts_per_page', -1 );

		}

	}

	/**
	 * Register [timeline] Shortcode
	 *
	 * @since 0.1.0
	 */
	public function setup_shortcode_details( $atts ) {

		// Plenty More Attributes To Add
		extract( shortcode_atts( array(
			'type'  => 'timeline',
			'show'  => -1,
			'year'  => null,
			'month' => null,
			'day'   => null,
			'week'  => null,
		), $atts ) );

		// See How Else The Query Can Be Altered
		$timeline = new WP_Query( array(
			'post_type'      => $type,
			'posts_per_page' => $show,
			'year'           => $year,
			'monthnum'       => $month,
			'day'            => $day,
			'w'              => $week,
			'order'          => 'ASC',
			'orderby'        => 'date',
		) );

		$output = '<div class="timeline">';

			$output .= '<ol>';

			while ( $timeline->have_posts() ) : $timeline->the_post();

				$output .= '<li id="timeline-' . esc_attr( get_the_ID() ) . '">';

					$output .= '<h3 class="timeline-entry-title">' . esc_html( get_the_title() ) . '</h3>';
					$output .= '<p class="timeline-entry-content">' . esc_html( get_the_content() ) . '</p>';
					$output .= '<p class="timeline-entry-meta">' . sprintf( '<time datetime="%1$s">%2$s &mdash; %3$s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date( 'F j, Y' ) ), esc_html( get_the_time( 'g:i A' ) ) ) . '</p>';

				$output .= '</li>';

			endwhile;

			wp_reset_postdata();

			$output .= '</ol>';

		$output .= '</div>';

		return $output;

	}

}

// Activation & Deactivation Hooks
register_activation_hook( __FILE__, array( 'Timeline', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Timeline', 'deactivate' ) );

Timeline::instance();

endif; // The class_exists Check

?>