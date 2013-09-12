<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: 
Description: Add a shortcode to get content or field from any post type
Version: 0.13
Author: miyarakira
Author URI: eliotakira.com
License: GPL2
*/

/*
 * Get a field or content from a post type
 */

function custom_func($atts) {
	extract(shortcode_atts(array(
		'type' => null, 'name' => null, 'field' => null, 'id' => null,
	), $atts));

	$custom_post_type = $type;
	$custom_post_name = $name;
	$custom_field = $field;
	$custom_id = $id;

	$excerpt_out = null;

	if($custom_post_type == '') { // If no post type is specified, then default is page
		$custom_post_type = 'page';
	}

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
			$custom_id=$my_posts[0]->ID;
			if($custom_field == '') { // If no field is specified, return content
				return $res=do_shortcode($my_posts[0]->post_content);
			} else { // else return specified field
				if($custom_field == 'excerpt') {
					$excerpt_out = $res=$my_posts[0]->post_excerpt;
				} else {
					$excerpt_out = get_post_meta($custom_id, $custom_field, $single=true);
				}				
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
		case "title": return $res=get_the_title($custom_id); break;
		case "author": return $res=get_the_author($custom_id); break;
		case "date": return $res=mysql2date(get_option('date_format'), get_post($custom_id)->post_date); break;
		case "url": return $res=get_post_permalink($custom_id); break;
		case "image": return $res=get_the_post_thumbnail($custom_id); break;
		case "image-url": return $res=wp_get_attachment_url(get_post_thumbnail_id($custom_id)); break;
		case "thumbnail": return $res=get_the_post_thumbnail( $custom_id, 'thumbnail' ); break;
		case "excerpt": return $res=get_post($custom_id)->post_excerpt; break;
		case "id": return $res=get_the_ID($custom_id); break;

		default: return $res=$excerpt_out;
	}
}

add_shortcode('content', 'custom_func');
add_shortcode('custom', 'custom_func');
