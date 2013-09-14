<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: 
Description: Add a shortcode to get content or field from any post type
Version: 0.1.4
Author: miyarakira
Author URI: eliotakira.com
License: GPL2
*/

/*
 * Get a field or content from a post type
 */

function custom_content_shortcode($atts) {

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
		if($custom_id == '') { // if no name and id, then current post
			$custom_id = get_the_ID();
		}
		if($custom_field == '') { // If no field is specified, return content
			$custom_content = apply_filters('the_content', get_post_field('post_content', $custom_id));
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
		case "id": return $res=$custom_id; break;

		default: return $res=$excerpt_out;
	}
}

add_shortcode('content', 'custom_content_shortcode');
add_shortcode('custom', 'custom_content_shortcode');

/*
 * Simple query loop shortcode
 * Based on the original Query Shortcode by Hassan Derakhshandeh
 */

class Loop_Shortcode {

	function __construct() {
		add_action( 'init', array( &$this, 'register' ) );
	}

	function register() {
		add_shortcode( 'loop', array( &$this, 'simple_query_shortcode' ) );
	}

	function simple_query_shortcode( $atts, $template = null ) {

		if( ! is_array( $atts ) ) return;

		// non-wp_query arguments
		$args = array(
			'type' => '',
			'category' => '',
			'count' => '',
			'content_limit' => 0,
			'thumbnail_size' => 'thumbnail',
			'posts_separator' => '',
		);

		$all_args = shortcode_atts( $args , $atts, true );
		extract( $all_args );

		$query = array_merge( $atts, $all_args );

		// filter out non-wpquery arguments
		foreach( $args as $key => $value ) {
			unset( $query[$key] );
		}

		if( $type == '' ) {
			$query['post_type'] = "page";
		} else {
			$query['post_type'] = $type;
		}
		if( $category != '' ) {
			$query['category_name'] = $category;
		}
		if( $count != '' ) {
			$query['posts_per_page'] = $count;
		}


		$output = array();
		ob_start();

		$posts = new WP_Query( $query );

		if( $posts->have_posts() ) : while( $posts->have_posts() ) : $posts->the_post();
			$keywords = apply_filters( 'query_shortcode_keywords', array(
				'URL' => get_permalink(),
				'ID' => get_the_ID(),
				'TITLE' => get_the_title(),
				'AUTHOR' => get_the_author(),
				'AUTHOR_URL' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
				'DATE' => get_the_date(),
				'THUMBNAIL' => get_the_post_thumbnail( null, $thumbnail_size ),
				'CONTENT' => ( $content_limit ) ? wp_trim_words( get_the_content(), $content_limit ) : get_the_content(),
				'EXCERPT' => get_the_excerpt(),
				'COMMENT_COUNT' => get_comments_number( '0', '1' ),
				'TAGS' => strip_tags( get_the_tag_list('',', ','') ),
				'IMAGE' => get_the_post_thumbnail(),
				'IMAGE_URL' => wp_get_attachment_url(get_post_thumbnail_id($post->ID)),
			) );

			$output[] = do_shortcode($this->get_block_template( $template, $keywords ));
		endwhile; endif;

		wp_reset_query();
		wp_reset_postdata();

		echo implode( $posts_separator, $output );

		return ob_get_clean();
	}

	/*
	 * Replaces {VAR} with $parameters['var'];
	 */

	function get_block_template( $string, $parameters = array() ) {
		$searches = $replacements = array();

		// replace {KEYWORDS} with variable values
		foreach( $parameters as $find => $replace ) {
			$searches[] = '{'.$find.'}';
			$replacements[] = $replace;
		}

		return str_replace( $searches, $replacements, $string );
	}

}

$loop_shortcode = new Loop_Shortcode;
