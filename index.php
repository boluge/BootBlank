<?php get_header(); ?>
<?php get_sidebar(); ?>

	<main class="<?php bootblank_main_class(); ?>" role="main">
		<!-- section -->
		<section>

			<h1><?php _e( 'Latest Posts', 'bootblank' ); ?></h1>

			<?php get_template_part('loop'); ?>

			<?php get_template_part('pagination'); ?>

		</section>
		<!-- /section -->
	</main>

<?php get_sidebar('right'); ?>

<?php get_footer(); ?>
