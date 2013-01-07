<?php
/**
 * Maybe another hacky layout, but just a general idea?
 * Ideally we don't want to need this file, but there are
 * probably a few people who'll want to make use of them?
 * File built off Twenty Twelve!
 */
get_header(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>