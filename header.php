<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php
	global $page, $paged;

	wp_title( '|', true, 'right' );

	bloginfo( 'name' );

	$site_description = get_bloginfo('description', 'display');
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . __('Page', 'jeo') . max($paged, $page);

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php // by mohjak: fixed openearth#215 ico files in themes ?>

<?php wp_head(); ?>
</head>
<body <?php body_class(get_bloginfo('language')); ?>>
	<header id="masthead">
		<div class="container">
			<div class="four columns">
				<div class="site-meta">
					<h1>
						<a href="<?php echo home_url('/'); ?>" title="<?php bloginfo('name'); ?>">
							<?php bloginfo('name'); ?>
						</a>
					</h1>
					<h2><?php bloginfo('description'); ?></h2>
				</div>
                <!-- Comentado por josego. Creo que yo coloque para la traduccion de los idiomas. No recuerdo bien
                <?php
				 //    $lang = '';
				 //     if(function_exists('qtrans_getLanguage'))
					// $lang = qtrans_getLanguage();
				?>
                                //-->
			</div>
			<div class="eight columns">
				<div class="social">

                                        <span class="fa-stack fa-lg header-sh">
                                                <a href="https://twitter.com/cartochaco" class="tw" title="Twitter" target="_blank">
                                                        <i class="fa fa-circle fa-stack-2x icon-background-header"></i>
                                                        <i class="fab fa-twitter fa-stack-1x"></i>
                                                </a>
                                        </span>

					<span class="fa-stack fa-lg header-sh">
						<a href="https://www.facebook.com/cartochaco" class="fb" title="Facebook" target="_blank">
							<i class="fa fa-circle fa-stack-2x icon-background-header"></i>
							<i class="fab fa-facebook-f fa-stack-1x"></i>
						</a>
					</span>

				</div>

				<?php get_search_form(); ?>

				<div id="masthead-nav">
					<div class="clearfix">
						<nav id="main-nav">
							<?php wp_nav_menu(array('theme_location' => 'header_menu')); ?>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</header>
