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
					<h1 class="page-title">Timeline</h1>
				</header><!-- .page-header -->

				<ul id="timeline">

				<?php while ( have_posts() ) : the_post(); ?>

					<li id="timeline-<?php the_ID(); ?>" <?php post_class(); ?>>
						<span class="entry-title"><?php the_title(); ?></span>
						<span class="entry-meta-date">When: <?php the_time( 'F j, Y' ); ?></span>
						<span class="entry-meta-time">Time: <?php the_time( 'g:i A' ); ?></span>
						<span class="entry-content"><?php the_content(); ?></span>
					</li><!-- #timeline-<?php the_ID(); ?> -->

				<?php endwhile; ?>

				</ul><!-- #timeline -->

			<?php else : ?>

				<?php get_template_part( 'no-results', 'archive' ); ?>

			<?php endif; ?>

			</div><!-- #content .site-content -->
		</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>