<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: 
Description: Adds a shortcode to get content or field from any post type by name. For example: [custom type="room" name="room-name" field="rent"]
Version: 0.1
Author: Lio Eters
Author URI: eliotakira.com
License: GPL2
*/

/**
 * Add shortcode for getting the content or custom field from custom post type by slug name.
 */

function page_func($atts) {
	extract(shortcode_atts(array(
		'name' => null, 'field' => null, 'id' => null,
	), $atts));

	$custom_post_type = 'page';
	$custom_post_name = $name;
	$custom_field = $field;
	$custom_id = $id;

	$excerpt_out = null;

	if($custom_post_name != '') { // Post name specified
		// Get post ID from post slug
		$args=array(
		  'name' => $custom_post_name,
		  'post_type' => $custom_post_type,
		  'post_status' => 'publish',
		  'showposts' => 1,
		  'caller_get_posts'=> 1
		);

		$my_posts = get_posts($args);
		if( $my_posts ) { // Get post ID, then custom field from that ID
			$id=$my_posts[0]->ID;
			if($custom_field == '') { // If no field is specified, return content
				return $res=do_shortcode($my_posts[0]->post_content);
			} else { // else return field
				$excerpt_out = get_post_meta($id, $custom_field, $single=true);
			}
		}
	} else {
		if($custom_id=='') { // if no name and id, then current post
			$custom_id = get_the_ID();
		}
		if($custom_field == '') { // If no field is specified, return content
			$custom_content = get_post_field('post_content', $custom_id);
			return $res=do_shortcode($custom_content);
		} else {
			$excerpt_out = get_post_meta($custom_id, $custom_field, $single=true);
		}
	}
	switch($custom_field) {
		case "title": return $res=get_the_title($id); break;
		case "link": return $res=get_post_permalink($id); break;
		case "thumb": return $res=wp_get_attachment_url(get_post_thumbnail_id($id)); break;
		default: return $res=$excerpt_out;
	}
}

function custom_func($atts) {
	extract(shortcode_atts(array(
		'type' => null, 'name' => null, 'field' => null, 'id' => null,
	), $atts));

	$custom_post_type = $type;
	$custom_post_name = $name;
	$custom_field = $field;
	$custom_id = $id;

	$excerpt_out = null;

	if($custom_post_name != '') { // Specific post name/slug
		$args=array( // Get post ID from post slug
		  'name' => $custom_post_name,
		  'post_type' => $custom_post_type,
		  'post_status' => 'publish',
		  'showposts' => 1,
		  'caller_get_posts'=> 1
		);

		$my_posts = get_posts($args);
		if( $my_posts ) { // Get post ID, then custom field from that ID
			$id=$my_posts[0]->ID;
			if($custom_field == '') { // If no field is specified, return content
				return $res=do_shortcode($my_posts[0]->post_content);
			} else { // else return field
				$excerpt_out = get_post_meta($id, $custom_field, $single=true);
			}
		}
	} else {
		if($custom_id=='') { // if no name and id, then current post
			$custom_id = get_the_ID();
		}
		if($custom_field == '') { // If no field is specified, return content
			$custom_content = get_post_field('post_content', $custom_id);
			return $res=do_shortcode($custom_content);
		} else {
			$excerpt_out = get_post_meta($custom_id, $custom_field, $single=true);
		}
	}
	switch($custom_field) {
		case "title": return $res=get_the_title($id); break;
		case "link": return $res=get_post_permalink($id); break;
		case "thumb": return $res=wp_get_attachment_url(get_post_thumbnail_id($id)); break;
		default: return $res=$excerpt_out;
	}
}

add_shortcode('custom', 'custom_func');
add_shortcode('page', 'page_func');
