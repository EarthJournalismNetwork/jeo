<?php

/*
 * Map global vars and query settings
 */

global $map, $mapgroup_id;

function mappress_map_global_setup($post) {
	if(mappress_is_map($post->ID)) {
		$GLOBALS['map'] = $post;
		if(get_post_type($post->ID) == 'map-group')
			mappress_setup_mapgroupdata($post);
		do_action_ref_array('mappress_the_map', array(&$map));
	}
}
add_action('the_post', 'mappress_map_global_setup');

function mappress_reset_mapdata() {
	global $wp_query;
	if(!empty($wp_query->post) && mappress_is_map_query($wp_query)) {
		$GLOBALS['post'] = $GLOBALS['map'] = $wp_query->post;
		setup_postdata($wp_query->post);
	}
}

function mappress_is_map($map_id = false) {
	global $post;
	$map_id = $map_id ? $map_id : $post->ID;
	if(get_post_type($post->ID) == 'map' || get_post_type($post->ID) == 'map-group')
		return true;

	return false;
}

function mappress_is_map_query($query = false) {
	global $wp_query;
	$query = $query ? $query : $wp_query;
	$post_type = $query->vars['post_type'];
	if((is_array($post_type) && (in_array('map', $post_type) || in_array('map-group', $post_type))) || ($post_type == 'map' || $post_type == 'map-group')) 
		return true;

	return false;
}

function mappress_setup_mapgroupdata($mapgroup) {
	global $mapgroup_id;
	$mapgroup_id = $mapgroup->ID;
	do_action_ref_array('mappress_the_mapgroup', array(&$mapgroup));
	return true;
}

/*
 * Theme setup
 */
function mappress_setup() {
	// register map and map group post types
	include(TEMPLATEPATH . '/inc/mappress-post-types.php');
}
add_action('after_setup_theme', 'mappress_setup');

/*
 * Register/enqueue scripts & styles
 */
function mappress_scripts() {
	wp_register_script('underscore', get_template_directory_uri() . '/lib/underscore-min.js', array(), '1.4.3');
	wp_register_script('mapbox-js', get_template_directory_uri() . '/lib/mapbox.js', array(), '0.6.7');
	wp_enqueue_style('mapbox', get_template_directory_uri() . '/lib/mapbox.css', array(), '0.6.7');

	wp_register_script('d3js', get_template_directory_uri() . '/lib/d3.v2.min.js', array('jquery'), '3.0.5');

	wp_enqueue_script('mappress', get_template_directory_uri() . '/js/mappress.js', array('mapbox-js', 'underscore', 'jquery'), '0.0.9.3');
	wp_enqueue_script('mappress.hash', get_template_directory_uri() . '/js/mappress.hash.js', array('mappress', 'underscore'), '0.0.1.12');
	wp_enqueue_script('mappress.geocode', get_template_directory_uri() . '/js/mappress.geocode.js', array('mappress', 'd3js', 'underscore'), '0.0.2.7');
	wp_enqueue_script('mappress.filterLayers', get_template_directory_uri() . '/js/mappress.filterLayers.js', array('mappress', 'underscore'), '0.0.7');
	wp_enqueue_script('mappress.groups', get_template_directory_uri() . '/js/mappress.groups.js', array('mappress', 'underscore'), '0.0.5.2');
	wp_enqueue_script('mappress.markers', get_template_directory_uri() . '/js/mappress.markers.js', array('mappress', 'underscore'), '0.0.5');

	wp_enqueue_style('mappress', get_template_directory_uri() . '/css/mappress.css', array(), '0.0.1.2');

	wp_localize_script('mappress', 'mappress_localization', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'more_label' => __('More', 'mappress')
	));

	wp_localize_script('mappress.geocode', 'mappress_labels', array(
		'search_placeholder' => __('Find a location', 'mappress'),
		'results_title' => __('Results', 'mappress'),
		'clear_search' => __('Clear search', 'mappress'),
		'not_found' => __('Nothing found, try something else.', 'mappress')
	));

	wp_localize_script('mappress.groups', 'mappress_groups', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'more_label' => __('More', 'mappress')
	));

	wp_localize_script('mappress.markers', 'mappress_markers', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'query' => mappress_get_marker_query_args()
	));

	/* geocode scripts */
	$geocode_service = mappress_geocode_service();
	$gmaps_key = mappress_gmaps_api_key();
	if($geocode_service == 'gmaps' && $gmaps_key) {
		wp_register_script('google-maps-api', 'http://maps.googleapis.com/maps/api/js?key=' . $gmaps_key . '&sensor=true');
		wp_register_script('mappress.geocode.box', get_template_directory_uri() . '/metaboxes/geocode/geocode-gmaps.js', array('jquery', 'google-maps-api'), '0.0.1');
	} else {
		wp_register_script('mappress.geocode.box', get_template_directory_uri() . '/metaboxes/geocode/geocode-osm.js', array('jquery', 'mapbox-js'), '0.0.3.3');
	}
	wp_localize_script('mappress.geocode.box', 'geocode_labels', array(
		'not_found' => __('We couldn\'t find what you are looking for, please try again.', 'mappress'),
		'results_found' => __('results found', 'mappress')
	));
}
add_action('wp_enqueue_scripts', 'mappress_scripts');

// geocode service choice
function mappress_geocode_service() {
	// osm or gmaps (gmaps requires api key)
	return apply_filters('mappress_geocode_service', 'osm');
}

// gmaps api
function mappress_gmaps_api_key() {
	return apply_filters('mappress_gmaps_api_key', false);
}

// get data

add_action('wp_ajax_nopriv_mapgroup_data', 'mappress_get_mapgroup_json_data');
add_action('wp_ajax_mapgroup_data', 'mappress_get_mapgroup_json_data');
function mappress_get_mapgroup_json_data($group_id = false) {
	$group_id = $group_id ? $group_id : $_REQUEST['group_id'];
	$data = json_encode(mappress_get_mapgroup_data($group_id));
	header('Content Type: application/json');
	echo $data;
	exit;
}

function mappress_get_mapgroup_data($group_id) {
	global $map;
	$group_id = $group_id ? $group_id : $map->ID;
	$data = array();
	if(get_post_type($group_id) != 'map-group')
		return;
	$group_data = get_post_meta($group_id, 'mapgroup_data', true);
	foreach($group_data['maps'] as $map) {
		$map_id = 'map_' . $map['id'];
		$data['maps'][$map_id] = mappress_get_map_data($map['id']);
	}
	return apply_filters('mappress_mapgroup_data', $data, $post);
}

add_action('wp_ajax_nopriv_map_data', 'mappress_get_map_json_data');
add_action('wp_ajax_map_data', 'mappress_get_map_json_data');
function mappress_get_map_json_data($map_id = false) {
	$map_id = $map_id ? $map_id : $_REQUEST['map_id'];
	$data = json_encode(mappress_get_map_data($map_id));
	header('Content Type: application/json');
	echo $data;
	exit;
}

function mappress_get_map_data($map_id = false) {
	global $map;
	$map_id = $map_id ? $map_id : $map->ID;
	if(get_post_type($map_id) != 'map')
		return;
	$post = get_post($map_id);
	setup_postdata($post);
	$data = get_post_meta($map_id, 'map_data', true);
	$data['postID'] = $map_id;
	$data['title'] = get_the_title($map_id);
	$data['legend'] = mappress_get_map_legend();
	if(get_the_content())
		$data['legend_full'] = '<h2>' . $data['title'] . '</h2>' .  apply_filters('the_content', get_the_content());
	wp_reset_postdata();
	return apply_filters('mappress_map_data', $data, $post);
}

function mappress_get_map_legend($map_id = false) {
	global $map;
	$map_id = $map_id ? $map_id : $map->ID;
	return apply_filters('mappress_map_legend', get_post_meta($map_id, 'legend', true), $post);
}

// disable canonical redirect on map/map-group post type for stories pagination
add_filter('redirect_canonical', 'mappress_disable_canonical');
function mappress_disable_canonical($redirect_url) {
	if(is_singular('map') || is_singular('map-group'))
		return false;
}

?>