<?php

require_once(get_template_directory() . '/inc/admin/settings.php');
require_once(get_template_directory() . '/inc/core.php');
require_once(get_template_directory() . '/inc/share-widget.php');
require_once(STYLESHEETPATH . '/inc/advanced-navigation.php');

/*
 * Clears JEO default front-end styles and scripts
 */
function jeo_blank_scripts() {

	// deregister jeo styles
	wp_deregister_style('jeo-main');

  // deregister jeo site frontend scripts
  wp_deregister_script('jeo-site');

    // by mohjak
    wp_register_script('d3.v4.min.js', 'https://ajax.googleapis.com/ajax/libs/d3js/4.13.0/d3.min.js', array(), '4.13.0', true);
    wp_enqueue_script('d3.v4.min.js');
}
add_action('wp_enqueue_scripts', 'jeo_blank_scripts', 10);

/*
 * JEO Hooks examples
 * Most common hooks
 */



// Action right after JEO functionality inits
function jeo_blank_init() {
  // Action goes here
}
add_action('jeo_init', 'jeo_blank_init');

// Hook scripts after JEO scripts has been initialized
function jeo_blank_jeo_scripts() {

  // Register and enqueue scripts here

  // Enqueue child theme JEO related scripts
  //wp_enqueue_script('jeo-blank-jeo-scripts', get_stylesheet_directory_uri() . '/js/jeo-scripts.js', array('jquery') , '0.0.1');

  // Enqueue child theme main CSS
  wp_enqueue_style('jeo-blank-styles', get_stylesheet_directory_uri() . '/css/main.css');
}
add_action('jeo_enqueue_scripts', 'jeo_blank_jeo_scripts', 20);

// Hook scripts after JEO Marker scripts has been initialized
function jeo_blank_markers_scripts() {

  // Register and enqueue scripts here
  //wp_enqueue_script('jeo-blank-jeo-markers-scripts', get_stylesheet_directory_uri() . '/js/jeo-markers-scripts.js', array('jquery') , '0.0.1');

}
add_action('jeo_markers_enqueue_scripts', 'jeo_blank_markers_scripts', 20);

// Filter to change posts GeoJSON data (also changes the GeoJSON API output)
function jeo_blank_marker_data($data, $post) {

  // Change $data here

  return $data;
}
add_filter('jeo_marker_data', 'jeo_blank_marker_data', 10, 2);

// Filter to change GeoJSON response
function jeo_blank_markers_data($data, $query) {

  // Change $data here

  return $data;
}
add_filter('jeo_markers_data', 'jeo_blank_markers_data', 10, 2);

// Filter to programatically change map data
function jeo_blank_map_data($data, $map) {

  // Change $data here

  return $data;
}
add_filter('jeo_map_data', 'jeo_blank_map_data', 10, 2);

function jeo_themeblank_scripts() {

        /* Chosen */
	wp_register_script('chosen', get_stylesheet_directory_uri() . '/lib/chosen.jquery.min.js', array('jquery'), '1.0.0');

	wp_register_script('jquery-isotope', get_stylesheet_directory_uri() . '/lib/jquery.isotope.min.js', array('jquery'), '1.5.25');

	wp_register_script('jeo-site', get_stylesheet_directory_uri() . '/js/site.js', array('jquery', 'jquery-isotope'));

	// styles
        // Submit CSS.
 //       wp_register_style('submit_story_css', get_template_directory_uri() . '/css/submit_story.css', array('jeo-skeleton', 'jeo-lsf', 'font-opensans'), '0.0.3')
        // Parche para el boton de enviar del dataset.
        wp_register_style('parche_jose_css', get_stylesheet_directory_uri() . '/css/parche_jose.css');
        // Submit JS.
        wp_register_script('submit-story', get_stylesheet_directory_uri() . '/js/submit-story.js', array('jquery'), '0.1.1');

        wp_localize_script('submit-story', 'cartochaco_submit', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'success_label' => __('Success! Thank you, your story will be reviewed by one of our editors and soon will be online.', 'jeo'),
		'redirect_label' => __('You\'re being redirect to the home page in 4 seconds.', 'jeo'),
		'home' => home_url('/'),
		'error_label' => __('Oops, please try again in a few minutes.', 'jeo')
	));

}
add_action('wp_enqueue_scripts', 'jeo_themeblank_scripts', 5);

function jeo_enqueue_themeblank_scripts() {

//        if(wp_style_is('submit_story_css', 'registered'))
//		wp_enqueue_style('submit_story_css');

        // Parche para el boton de enviar del dataset.
        if(wp_style_is('parche_jose_css', 'registered'))
		wp_enqueue_style('parche_jose_css');

        if(wp_script_is('submit-story', 'registered'))
		wp_enqueue_script('submit-story');

}
add_action('wp_enqueue_scripts', 'jeo_enqueue_themeblank_scripts', 12);


// adding google material-desing icons set
function md_icons_link() {
    echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">' . "\n";

	// text domain
	//load_child_theme_textdomain('jeo-blank', get_stylesheet_directory() . '/languages');

}
add_action( 'wp_head', 'md_icons_link' );





// Geocode box.
include(STYLESHEETPATH . '/inc/geocode-box.php');

// Submit story.
include(STYLESHEETPATH . '/inc/submit-story.php');



// Remove page from search result.
function cartochaco_remove_page_from_search($query) {
	if($query->is_search) {
		$query->set('post_type', 'post');
	}
	return $query;
}
add_filter('pre_get_posts', 'cartochaco_remove_page_from_search');

// Metaboxes.
include(STYLESHEETPATH . '/inc/metaboxes/metaboxes.php');

// Register taxonomies.
include(STYLESHEETPATH . '/inc/taxonomies.php');

// Taxonomy meta.
include(STYLESHEETPATH . '/inc/taxonomies-meta.php');


function cartochaco_before_embed() {
	remove_action('wp_footer', 'cartochaco_submit');
	remove_action('wp_footer', 'cartochaco_geocode_box');
}
add_action('jeo_before_embed', 'cartochaco_before_embed');

function cartochaco_embed_type($post_types) {
	if(get_query_var('embed')) {
		$post_types = 'map';
	}
	return $post_types;
}
add_filter('jeo_featured_map_type', 'cartochaco_embed_type');


/*
 * Advanced Custom Fields.
 */
if(!class_exists('Acf')) {
	function cartochaco_acf_dir() {
		return ABSPATH . 'wp-content/plugins/advanced-custom-fields';
	}
	add_filter('acf/helpers/get_dir', 'cartochaco_acf_dir');

	function cartochaco_acf_date_time_picker_dir() {
		return cartochaco_acf_dir() . '/add-ons/acf-field-date-time-picker/';
	}
	add_filter('acf/add-ons/date-time-picker/get_dir', 'cartochaco_acf_date_time_picker_dir');

	function cartochaco_acf_repeater_dir() {
		return cartochaco_acf_dir() . '/add-ons/acf-repeater/';
	}
	add_filter('acf/add-ons/repeater/get_dir', 'cartochaco_acf_repeater_dir');

	define('ACF_LITE', true);
	require_once(ABSPATH . 'wp-content/plugins/advanced-custom-fields/acf.php');
}


/*
 * Datasets.
 */
include(STYLESHEETPATH . '/inc/datasets.php');

/*
 * Reports.
 */
include(STYLESHEETPATH . '/inc/reports.php');

function jeoblank_setup() {
	add_theme_support('post-thumbnails');
	add_image_size('post-thumb', 360, 121, true);
	add_image_size('map-thumb', 200, 200, true);

	// text domain

	load_theme_textdomain('jeo', get_stylesheet_directory() . '/languages');

	load_child_theme_textdomain('jeo-blank', get_stylesheet_directory() . '/languages');

	//sidebars
	register_sidebar(array(
		'name' => __('Main widgets', 'jeo'),
		'id' => 'main-sidebar',
		'description' => __('Widgets used on front and inside pages.', 'jeo'),
		'before_widget' => '<div class="four columns row">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
    ));
}
add_action('after_setup_theme', 'jeoblank_setup');

/*
 * Para mostrar una cantidad determinada de dataset por pagina.
 */
/*
function dataset_posts_per_page($query) {
    if($query->query_vars['post_type'] == 'dataset'){
        $query->query_vars['posts_per_page'] = 1;
    }
    return $query;
}
if (!is_admin() ) add_filter('pre_get_posts', 'dataset_posts_per_page');*/

/*
 * Limpeza KSES para ACF - https://github.com/Hube2/acf-filters-and-functions/blob/master/acf-form-kses.php
 */

function acf_wp_kses_post($data, $post_id=0, $field=array()) {
		if (isset($field['type']) &&
		    ($field['type'] == 'repeater' || $field['type'] == 'flexible_content' || $field['type'] == 'clone' || $field['type'] == 'group')) {
			// no need to run it on repeaters
			// will be called agaian for each subfield
			return $value;
		}
		if (!is_array($data)) {
			return wp_kses_post($data);
		}
		$return = array();
		if (count($data)) {
			foreach ($data as $index => $value) {
				$return[$index] = acf_wp_kses_post($value);
			}
		}
		return $return;
	}
	add_filter('acf/update_value', 'acf_wp_kses_post', 10, 3);
