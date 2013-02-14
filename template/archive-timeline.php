<?php
/**
 * Need to apply some CSS, but a hacky layout that does work!
 * Would like it to look more like WooThemes' Timeline template!
 * File built off Twenty Twelve!
 */
get_header(); ?>

		<section id="primary" class="content-area">
			<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php apply_filters( 'timeline_archive_page_title', 'Timeline' ); ?></h1>
				</header><!-- .page-header -->

				<div id="timeline">

					<ol id="timeline">

					<?php while ( have_posts() ) : the_post(); ?>

						<li id="timeline-<?php the_ID(); ?>">
							<h3 class="timeline-entry-title"><?php the_title(); ?></h3>
							<p class="timeline-entry-content"><?php the_content(); // Need to check how the get_* option works! ?></p>
							<p class="timeline-entry-meta"><time datetime="<?php the_date( 'c' ); ?>"><?php the_date( 'F j, Y' ); ?> &mdash; <?php the_time( 'g:i A' ); ?></time></p>
						</li>

					<?php endwhile; ?>

					</ol><!-- #timeline -->

				</div>

			<?php else : ?>

				<?php get_template_part( 'no-results', 'archive' ); ?>

			<?php endif; ?>

			</div><!-- #content .site-content -->
		</section><!-- #primary .content-area -->

<?php /** get_sidebar(); */ ?>
<?php get_footer(); ?>