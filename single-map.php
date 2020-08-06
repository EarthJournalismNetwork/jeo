<?php get_header(); ?>

<?php jeo_map(); ?>

<section id="content" class="bblanco">
	<?php
	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	$query = array(
		'paged' => $paged,
		's' => isset($_GET['s']) ? $_GET['s'] : null
	);
	query_posts($query);
	if(have_posts()) : ?>

        <section id="last-stories" class="loop-section">
            <div class="section-title">
                <div class="container">
                    <div class="twelve columns">
                        <h3 style="text-align: center;"><?php _e('Stories on', 'jeo-blank'); ?> &ldquo;<?php the_title(); ?>&ldquo;</h3>
                        <div class="query-actions">
                            <?php
                            global $wp_query;
                            $args = $wp_query->query;
                            $args = array_merge($args, $_GET);
                            $geojson = jeo_get_api_url($args);
                            $download = jeo_get_api_download_url($args);
                            $rss = add_query_arg(array('feed' => 'rss'));
                            ?>
<!--
                            <a class="rss" href="<?php /*echo $rss;*/ ?>"><?php /*_e('RSS Feed', 'jeo');*/ ?></a>
                            <a class="geojson" href="<?php /*echo $geojson;*/ ?>"><?php /*_e('Get GeoJSON', 'jeo');*/ ?></a>
                            <a class="download" href="<?php /*echo $download;*/ ?>"><?php /*_e('Download', 'jeo');*/ ?></a>
//-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <?php get_template_part('loop'); ?>
            </div>
        </section>

	<?php
	endif;
	wp_reset_query(); ?>
</section>

<?php get_footer(); ?>
