<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: 
Description: Add a shortcode to get content or field from any post type
Version: 0.1.9
Author: miyarakira
Author URI: eliotakira.com
License: GPL2
*/

$global_vars = array(
	'is_loop' => 'false',
	'is_gallery_loop' => 'false',
	'current_image' => '',
	'current_image_url' => '',
	'current_image_thumb' => '',
	'current_image_thumb_url' => '',
	'current_image_caption' => '',
	'current_image_title' => '',
	'current_image_description' => '',
	'current_image_alt' => '',
	'current_image_ids' => '',
	'current_gallery_name' => '',
	'current_gallery_id' => '',
	'is_attachment_loop' => 'false',
	'current_attachment_id' => '',
	'current_attachment_ids' => '',
	'current_script' => '',
);


/*
 * Get a field or content from a post type
 */

function custom_content_shortcode($atts) {

	global $global_vars;

	extract(shortcode_atts(array(
		'type' => null, 'name' => null, 'field' => null, 'id' => null,
		'menu' => null, 'format' => null, 'shortcode' => null, 'gallery' => 'false',
		'group' => null, 'class' => null, 'area' => null, 'sidebar' => null, 
		), $atts));

	$custom_post_type = $type;
	$custom_post_name = $name;
	$custom_menu_name = $menu;
	$custom_menu_class = $class;
	$custom_field = $field;
	$custom_id = $id;
	$content_format = $format;
	$shortcode_option = $shortcode;
	$custom_gallery_type = $gallery;
	$custom_gallery_name = $group;
	$custom_area_name = $area;

	$excerpt_out = null;

	if( $custom_post_type == '' ) { // If no post type is specified, then default is any
		$custom_post_type = 'any';
	}


	// If we're in a gallery field or attachments loop, return requested field

	if( ( $global_vars['is_gallery_loop'] == "true") ||
		( $global_vars['is_attachment_loop'] == "true" ) ) {
		switch($custom_field) {
			case "image": return $global_vars['current_image']; break;
			case "image-url": return $global_vars['current_image_url']; break;
			case "thumbnail": return $global_vars['current_image_thumb']; break;
			case "thumbnail-url": return $global_vars['current_image_thumb_url']; break;
			case "caption": return $global_vars['current_image_caption']; break;
			case "id": return $global_vars['current_attachment_id']; break;
			case "title": return $global_vars['current_image_title']; break;
			case "description": return $global_vars['current_image_description']; break;
			case "alt": return $global_vars['current_image_alt']; break;
		}
	}


	// Display sidebar/widget area

	if( $sidebar != '') {
		$custom_area_name = $sidebar;
	}
	if( $custom_area_name != '') {
		$back =  "<div id='" . str_replace( " ", "_", $name ) . "' class='sidebar_shortcode'>";
		ob_start();
		if ( ! function_exists('dynamic_sidebar') || ! dynamic_sidebar($custom_area_name) ) {}
		$back .= ob_get_contents();
		ob_end_clean();
		$back .= "</div>";
		return $back;
	}


	// Display menu

	if( $custom_menu_name != '' ) {

		// Simple menu list

		$menu_args = array (
			'menu' => $custom_menu_name,
			'echo' => false,
		);

		$output = wp_nav_menu( $menu_args );

		if( $custom_menu_class == '') {
			return $output;
		} else {
			return '<div class="' . $custom_menu_class . '">' . $output . '</div>';
		}
	}


	// If post name/slug is defined, get its ID

	if($custom_post_name != '') {
		$args=array(
		  'name' => $custom_post_name,
		  'post_type' => $custom_post_type,
		  'post_status' => 'publish',
		  'showposts' => 1,
		  'caller_get_posts'=> 1,
		);

		$my_posts = get_posts($args);

		if( $my_posts ) { $custom_id=$my_posts[0]->ID; }
		else { return null; // No posts found by that name
		}
	}
	else {

		// If no name or id, then current post

		if($custom_id == '') { $custom_id = get_the_ID(); }
	}

	
	// Gallery types - carousel, native

	if( $custom_gallery_type == "carousel") {
		$excerpt_out = '[gallery type="carousel" ';
		if($custom_gallery_name != '') {
			$excerpt_out .= 'name ="' . $custom_gallery_name . '" ';
		}
		$excerpt_out .= 'ids="';
		$excerpt_out .= get_post_meta( $custom_id, '_custom_gallery', true );
		$excerpt_out .= '" ]';
		return $res=do_shortcode( $excerpt_out );
	} else {
		if( $custom_gallery_type == "native") {
			$excerpt_out = '[gallery " ';
			if($custom_gallery_name != '') {
				$excerpt_out .= 'name ="' . $custom_gallery_name . '" ';
			}
			$excerpt_out .= 'ids="';
			$excerpt_out .= get_post_meta( $custom_id, '_custom_gallery', true );
			$excerpt_out .= '" ]';
			return $res=do_shortcode( $excerpt_out );
		}	
	}


	// If no field is specified, return content

	if($custom_field == '') { 

		$excerpt_out = get_post_field('post_content', $custom_id);

	} else { // else return specified field

		// Predefined fields

		switch($custom_field) {
			case "id": return $custom_id; break;
			case "title": return get_the_title($custom_id); break;
			case "author": return get_the_author($custom_id); break;
			case "date": return mysql2date(get_option('date_format'), get_post($custom_id)->post_date); break;
			case "url": return get_post_permalink($custom_id); break;
			case "image": return get_the_post_thumbnail($custom_id); break;
			case "image-url": return wp_get_attachment_url(get_post_thumbnail_id($custom_id)); break;
			case "thumbnail": return get_the_post_thumbnail( $custom_id, 'thumbnail' ); break;
			case "thumbnail-url": wp_get_attachment_image_src( get_post_thumbnail_id($custom_id), 'thumbnail' ); return $res['0']; break;
			case "excerpt": return get_post($custom_id)->post_excerpt; break;
			case "tags": return implode(' ', wp_get_post_tags( $custom_id, array( 'fields' => 'names' ) ) ); break;
			case "gallery-ids": return get_post_meta( $custom_id, '_custom_gallery', true ); break;
		}

		if($custom_field == 'excerpt') {

			// Get excerpt

			$excerpt_out = get_post_field('post_excerpt', $custom_id);
		} else {

			// Get other fields

			$excerpt_out = get_post_meta($custom_id, $custom_field, $single=true);
		}				
	}

	if($content_format != 'false') { // Format?
		$excerpt_out = apply_filters('the_content', $excerpt_out );
	}

	if($shortcode_option != 'false') { // Shortcode?
		$excerpt_out = do_shortcode( $excerpt_out );
	}

	return $excerpt_out;
}

add_shortcode('content', 'custom_content_shortcode');



/*
 * Simple query loop shortcode
 *
 * Based on the original Query Shortcode by Hassan Derakhshandeh
 *
 */

class Loop_Shortcode {

	function __construct() {
		add_action( 'init', array( &$this, 'register' ) );
	}

	function register() {
		add_shortcode( 'loop', array( &$this, 'simple_query_shortcode' ) );
	}

	function simple_query_shortcode( $atts, $template = null ) {

		global $global_vars;

		$global_vars['is_loop'] = "true";
		$global_vars['current_gallery_name'] = '';
		$global_vars['current_gallery_id'] = '';
		$global_vars['is_attachment_loop'] = "false";

		if( ! is_array( $atts ) ) return;

		// non-wp_query arguments
		$args = array(
			'type' => '',
			'category' => '',
			'count' => '',
			'content_limit' => 0,
			'thumbnail_size' => 'thumbnail',
			'posts_separator' => '',
			'gallery' => '',
			'id' => '',
			'name' => '',
			'field' => '',
		);

		$all_args = shortcode_atts( $args , $atts, true );
		extract( $all_args );

		$query = array_merge( $atts, $all_args );

		// filter out non-wpquery arguments
		foreach( $args as $key => $value ) {
			unset( $query[$key] );
		}

		$current_name = $name;
		$custom_field = $field;
		if( $field == "gallery" ) {
			$custom_field = "_custom_gallery";
		}

		if( $category != '' ) {
			$query['category_name'] = $category;
		}
		if( $count != '' ) {
			$query['posts_per_page'] = $count;
		} else {
			$query['posts_per_page'] = '-1'; // Show all posts
		}
		if( $id != '' ) {
			$query['p'] = $id; $query['post_type'] = "any";
		} else {
			if( $current_name != '') {
				$query['name']=$current_name; $query['post_type'] = "any";
				$global_vars['current_gallery_name'] = $current_name;
				$posts = get_posts( $query );
				if( $posts ) { $global_vars['current_gallery_id'] = $posts[0]->ID;
			}
			} else {
				$query['p'] = get_the_ID(); $query['post_type'] = "any";
			}
		}

		if( $type == '' ) {
			$query['post_type'] = 'any';
		} else {
			if( $type == 'gallery' ) {
				$gallery = "true"; $query['post_type'] = 'any';
			} else {
					$query['post_type'] = $type; $query['p'] = '';
			}
		}

	if( ( $gallery!="true" ) && ( $type != "attachment") ) {

		$global_vars['is_gallery_loop'] = "false";
		$output = array();
		ob_start();
		$posts = new WP_Query( $query );

		if( $posts->have_posts() ) : while( $posts->have_posts() ) : $posts->the_post();

			if($custom_field == "attachment") {
				$attachments =& get_children( array(
					'post_parent' => get_the_ID(),
					'post_type' => 'attachment',
				) );
				if( empty($attachments) ) {
					$custom_field_content = null; $attachment_ids = null;
				} else {
					$attachment_ids = '';
					foreach( $attachments as $attachment_id => $attachment) {
						$attachment_ids .= $attachment_id . ",";
					}
					$attachment_ids = trim($attachment_ids, ",");
					$custom_field_content = $attachment_ids;
				}
			} else {
				$custom_field_content = get_post_meta( get_the_ID(), $custom_field, $single=true );
				$attachment_ids = get_post_meta( get_the_ID(), '_custom_gallery', true );
			}

			$keywords = apply_filters( 'query_shortcode_keywords', array(
				'QUERY' => serialize($query), // Debug purpose
				'URL' => get_permalink(),
				'ID' => get_the_ID(),
				'TITLE' => get_the_title(),
				'AUTHOR' => get_the_author(),
				'AUTHOR_URL' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
				'DATE' => get_the_date(),
				'THUMBNAIL' => get_the_post_thumbnail( null, $thumbnail_size ),
				'THUMBNAIL_URL' => wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())),
				'CONTENT' => ( $content_limit ) ? wp_trim_words( get_the_content(), $content_limit ) : get_the_content(),
				'EXCERPT' => get_the_excerpt(),
				'COMMENT_COUNT' => get_comments_number( '0', '1' ),
				'TAGS' => strip_tags( get_the_tag_list('',', ','') ),
				'IMAGE' => get_the_post_thumbnail(),
				'IMAGE_URL' => wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())),
				'FIELD' => $custom_field_content,
				'IDS' => $attachment_ids,
			) );

			$output[] = do_shortcode($this->get_block_template( $template, $keywords ));
		endwhile; endif;

		wp_reset_query();
		wp_reset_postdata();

		echo implode( $posts_separator, $output );

		return ob_get_clean();
	} else {

		if( $type == 'attachment' ) {

			$output = array();
			ob_start();

			if($category == '') {
				$posts =& get_children( array (
				'post_parent' => get_the_ID(),
				'post_type' => 'attachment',
				'post_status' => 'any'
				) );

				foreach( $posts as $attachment_id => $attachment ) {
					$attachment_ids .= $attachment_id . " ";
				}

			} else { // Fetch posts by category, then attachments

				$my_query = new WP_Query( array(
			    	'cat' => get_category_by_slug($category)->term_id, 
					'post_type' => 'any',
				));
				if( $my_query->have_posts() ) {
					$posts = array('');
					while ( $my_query->have_posts() ) {
						$my_query->the_post();

// DEBUG					echo "Found post ID:" . get_the_ID() . "<br>";

						$new_children =& get_children( array (
							'post_parent' => get_the_ID(),
							'post_type' => 'attachment',
							'post_status' => 'any'
						) );

						foreach( $new_children as $attachment_id => $attachment ) {
// DEBUG					echo "&nbsp;&nbsp;Attached: " . $attachment_id . "<br>";
							$attachment_ids .= $attachment_id . " ";
						}
					}
				}
			} // End fetch attachments by category

			if( empty($posts) ) {
				$output = null;
			} else {

				$attachment_ids = explode(" ", trim( $attachment_ids ) );

				if ( $attachment_ids ) { 

					$global_vars['is_attachment_loop'] = "true";

					foreach ( $attachment_ids as $attachment_id ) {
					// get original image

						$global_vars['current_attachment_id'] = $attachment_id;

						$image_link	= wp_get_attachment_image_src( $attachment_id, "full" );
						$image_link	= $image_link[0];	
										
						$global_vars['current_image'] = wp_get_attachment_image( $attachment_id, "full" );
						$global_vars['current_image_url'] = $image_link;
						$global_vars['current_image_thumb'] = wp_get_attachment_image( $attachment_id, 'thumbnail', '', array( 'alt' => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) ) );
						$global_vars['current_image_thumb_url'] = wp_get_attachment_thumb_url( $attachment_id, 'thumbnail' ) ;
						$global_vars['current_image_caption'] = get_post( $attachment_id )->post_excerpt ? get_post( $attachment_id )->post_excerpt : '';
						$global_vars['current_image_title'] = get_post( $attachment_id )->post_title;
						$global_vars['current_image_description'] = get_post( $attachment_id )->post_content;
						$global_vars['current_image_alt'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

						$global_vars['current_image_ids'] = implode(" ", $attachment_ids);
						$global_vars['current_attachment_ids'] = $global_vars['current_image_ids'];

			$keywords = apply_filters( 'query_shortcode_keywords', array(
				'URL' => get_permalink( $attachment_id ),
				'ID' => $attachment_id,
				'TITLE' => get_post( $attachment_id )->post_title,
				'CONTENT' => get_post( $attachment_id )->post_content,
				'CAPTION' => get_post( $attachment_id )->post_excerpt,
				'DESCRIPTION' => get_post( $attachment_id )->post_content,
				'IMAGE' => $global_vars['current_image'],
				'IMAGE_URL' => $global_vars['current_image_url'],
				'ALT' => $global_vars['current_image_alt'],
				'THUMBNAIL' => $global_vars['current_image_thumb'],
				'THUMBNAIL_URL' => $global_vars['current_image_thumb_url'],
				'TAGS' => strip_tags( get_the_tag_list('',', ','') ),
				'FIELD' => get_post_meta( get_the_ID(), $custom_field, $single=true ),
				'IDS' => get_post_meta( get_the_ID(), '_custom_gallery', true ),
			) );

						$output[] = do_shortcode(custom_clean_shortcodes($this->get_block_template( $template, $keywords ) ) );
					} /** End for each attachment **/
				}
				$global_vars['is_attachment_loop'] = "false";
				wp_reset_query();
				wp_reset_postdata();

				echo implode( $posts_separator, $output );
				return ob_get_clean();
			}
		} // End type="attachment"

		else {

			/** Gallery Loop **/

		if( function_exists('custom_gallery_get_image_ids') ) {

			$output = array();
			ob_start();

			if($global_vars['current_gallery_id'] == '') {
				$global_vars['current_gallery_id'] = get_the_ID();
			}
			$posts = new WP_Query( $query );
			$attachment_ids = custom_gallery_get_image_ids();
/** DEBUG
echo "Current Gallery ID: " . $global_vars['current_gallery_id'] . "<br>";
echo "Query: " . implode(" ", $query) . "<br>";
echo "Attachments ID: " . implode(" ", $attachment_ids) . "<br>";
**/
			if ( $attachment_ids ) { 
				$has_gallery_images = get_post_meta( $global_vars['current_gallery_id'], '_custom_gallery', true );
				if ( !$has_gallery_images )
					return;
				// convert string into array
				$has_gallery_images = explode( ',', get_post_meta( $global_vars['current_gallery_id'], '_custom_gallery', true ) );

				// clean the array (remove empty values)
				$has_gallery_images = array_filter( $has_gallery_images );

				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $global_vars['current_gallery_id'] ), 'feature' );
				$image_title = esc_attr( get_the_title( get_post_thumbnail_id( $global_vars['current_gallery_id'] ) ) );

				$global_vars['is_gallery_loop'] = "true";

				foreach ( $attachment_ids as $attachment_id ) {

					$global_vars['current_attachment_id'] = $attachment_id;

					// get original image
					$image_link	= wp_get_attachment_image_src( $attachment_id, apply_filters( 'linked_image_size', 'large' ) );
					$image_link	= $image_link[0];	
										
					$global_vars['current_image']=wp_get_attachment_image( $attachment_id, apply_filters( 'linked_image_size', 'large' ) );
					$global_vars['current_image_url']=$image_link;
					$global_vars['current_image_thumb']=wp_get_attachment_image( $attachment_id, apply_filters( 'thumbnail_image_size', 'thumbnail' ), '', array( 'alt' => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) ) );
					$global_vars['current_image_thumb_url']= wp_get_attachment_thumb_url( $attachment_id ) ;
					$global_vars['current_image_caption']=get_post( $attachment_id )->post_excerpt ? get_post( $attachment_id )->post_excerpt : '';
					$global_vars['current_image_title'] = get_post( $attachment_id )->post_title;
					$global_vars['current_image_description'] = get_post( $attachment_id )->post_content;
					$global_vars['current_image_alt'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

					$global_vars['current_image_ids'] = implode(" ", $attachment_ids);
					$global_vars['current_attachment_ids'] = $global_vars['current_image_ids'];

			$keywords = apply_filters( 'query_shortcode_keywords', array(
				'URL' => get_permalink( $attachment_id ),
				'ID' => $attachment_id,
				'TITLE' => get_post( $attachment_id )->post_title,
				'CONTENT' => get_post( $attachment_id )->post_content,
				'CAPTION' => get_post( $attachment_id )->post_excerpt,
				'DESCRIPTION' => get_post( $attachment_id )->post_content,
				'IMAGE' => $global_vars['current_image'],
				'IMAGE_URL' => $global_vars['current_image_url'],
				'ALT' => $global_vars['current_image_alt'],
				'THUMBNAIL' => $global_vars['current_image_thumb'],
				'THUMBNAIL_URL' => $global_vars['current_image_thumb_url'],
				'TAGS' => strip_tags( get_the_tag_list('',', ','') ),
				'IMAGE' => get_the_post_thumbnail(),
				'IMAGE_URL' => wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())),

				'FIELD' => get_post_meta( get_the_ID(), $custom_field, $single=true ),
				'IDS' => get_post_meta( get_the_ID(), '_custom_gallery', true ),
			) );
				
					$output[] = do_shortcode(custom_clean_shortcodes($this->get_block_template( $template, $keywords ) ) );
				} /** End for each attachment **/

				$global_vars['is_gallery_loop'] = "false";
				wp_reset_query();
				wp_reset_postdata();

				echo implode( $posts_separator, $output );
				return ob_get_clean();
	    	} // End if attachment IDs exist
		} // End if function exists 
		$global_vars['current_gallery_id'] = '';
		return;
	} /* End of gallery loop */
	}

	} /* End of function simple_query_shortcode */ 

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

/*--------------------------------------*/
/*    Clean up Shortcodes
/*--------------------------------------*/
function custom_clean_shortcodes($content){   
/*    $array = array (
        '<p>[' => '[', 
        ']</p>' => ']', 
        ']<br />' => ']'
    );
    $content = strtr($content, $array); */
    return $content;
}


/*
 *
 * Custom Gallery Fields
 *
 *
 */


/**** Functions ****/

/*
 * Is gallery
 */

function custom_is_gallery() {
	$attachment_ids = get_post_meta( get_the_ID(), '_custom_gallery', true );
	if ( $attachment_ids )
		return true;
}

/*
 * Check the current post for the existence of a short code
 */

function custom_gallery_has_shortcode( $shortcode = '' ) {
	global $post;
	$found = false;

	if ( !$shortcode ) {
		return $found;
	}
	if (  is_object( $post ) && stripos( $post->post_content, '[' . $shortcode ) !== false ) {
		$found = true; // we have found the short code
	}
	return $found;
}

/*
 * Has linked images
 */

function custom_gallery_has_linked_images() {
	$link_images = get_post_meta( get_the_ID(), '_custom_gallery_link_images', true );

	if ( 'on' == $link_images ) return true;
}


/*
 * Get list of post types for populating the checkboxes on the admin page
 */

function custom_gallery_get_post_types() {

	$args = array(
		'public' => true
	);

	$post_types = array_map( 'ucfirst', get_post_types( $args ) );

	// remove attachment
	unset( $post_types[ 'attachment' ] );

	return apply_filters( 'custom_gallery_get_post_types', $post_types );

}

/*
 * Retrieve the allowed post types from the option row
 * Defaults to post and page when the settings have not been saved
 *
 */
function custom_gallery_allowed_post_types() {
	
/*	$defaults['post_types']['post'] = '';
	$defaults['post_types']['page'] = '';
*/
	// get the allowed post type from the DB
	$settings = ( array ) get_option( 'custom-gallery', $defaults );
	$post_types = isset( $settings['post_types'] ) ? $settings['post_types'] : '';

	// post types don't exist, bail
	if ( ! $post_types )
		return;

	return $post_types;
}


/*
 * Is the currently viewed post type allowed?
 * For use on the front-end when loading scripts etc
 */

function custom_gallery_allowed_post_type() {

	// post and page defaults
/*	$defaults['post_types']['post'] = '';
	$defaults['post_types']['page'] = '';
*/
	// get currently viewed post type
	$post_type = ( string ) get_post_type();

	//echo $post_type; exit; // download

	// get the allowed post type from the DB
	$settings = ( array ) get_option( 'custom-gallery', $defaults );
	$post_types = isset( $settings['post_types'] ) ? $settings['post_types'] : '';

	// post types don't exist, bail
	if ( ! $post_types )
		return;

	// check the two against each other
	if ( array_key_exists( $post_type, $post_types ) )
		return true;
}


/**
 * Retrieve attachment IDs
 */

function custom_gallery_get_image_ids() {

	global $global_vars;

	if($global_vars['current_gallery_id'] == '') {
		global $post;
		if( ! isset( $post->ID) )
			return;
		$attachment_ids = get_post_meta( $post->ID, '_custom_gallery', true );
	} else {
		$attachment_ids = get_post_meta( $global_vars['current_gallery_id'], '_custom_gallery', true );
	}

	$attachment_ids = explode( ',', $attachment_ids );

	return array_filter( $attachment_ids );
}


/*
 * Shortcode
 */

function custom_gallery_shortcode() {

	// return early if the post type is not allowed to have a gallery
	if ( ! custom_gallery_allowed_post_type() )
		return;

	return custom_gallery();
}
add_shortcode( 'custom_gallery', 'custom_gallery_shortcode' );


/*
 * Count number of images in array
 */

function custom_gallery_count_images() {

	$images = get_post_meta( get_the_ID(), '_custom_gallery', true );
	$images = explode( ',', $images );

	$number = count( $images );

	return $number;
}

/*
 * Output gallery
 *
 */
function custom_gallery() { // No output without shortcode
}


/*
 * CSS for admin
 */

function custom_gallery_admin_css() { ?>

	<style>
		.attachment.details .check div {
			background-position: -60px 0;
		}

		.attachment.details .check:hover div {
			background-position: -60px 0;
		}

		.gallery_images .details.attachment {
			box-shadow: none;
		}

		.eig-metabox-sortable-placeholder {
			background: #DFDFDF;
		}

		.gallery_images .attachment.details > div {
			width: 150px;
			height: 150px;
			box-shadow: none;
		}

		.gallery_images .attachment-preview .thumbnail {
			 cursor: move;
		}

		.attachment.details div:hover .check {
			display:block;
		}

        .gallery_images:after,
        #gallery_images_container:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }

        .gallery_images > li {
            float: left;
            cursor: move;
            margin: 0 20px 20px 0;
        }

        .gallery_images li.image img {
            width: 150px;
            height: auto;
        }

        .add_gallery_images { margin-top: -15px; }

    </style>

<?php }
add_action( 'admin_head', 'custom_gallery_admin_css' );


/***** Metabox *****/


/*
 * Add meta boxes to selected post types
 */

function custom_gallery_add_meta_box() {

    $post_types = custom_gallery_allowed_post_types();

    if ( ! $post_types )
        return;

    foreach ( $post_types as $post_type => $status ) {
        add_meta_box( 'custom_gallery', apply_filters( 'custom_gallery_meta_box_title', __( 'Gallery', 'custom-gallery' ) ), 'custom_gallery_metabox', $post_type, apply_filters( 'custom_gallery_meta_box_context', 'normal' ), apply_filters( 'custom_gallery_meta_box_priority', 'low' ) );
    }

}
add_action( 'add_meta_boxes', 'custom_gallery_add_meta_box' );


/*
 * Render gallery metabox
 */

function custom_gallery_metabox() {

    global $post;
?>

    <div id="gallery_images_container">
        <ul class="gallery_images">
    	<?php
    		$image_gallery = get_post_meta( $post->ID, '_custom_gallery', true );
		    $attachments = array_filter( explode( ',', $image_gallery ) );

		    if ( $attachments )
		        foreach ( $attachments as $attachment_id ) {
		            echo '<li class="image attachment details" data-attachment_id="'
		            	. $attachment_id
		            	. '"><div class="attachment-preview"><div class="thumbnail">'
		            	. wp_get_attachment_image( $attachment_id, 'thumbnail' )
		            	. '</div><a href="#" class="delete check" title="'
		            	. __( 'Remove image', 'custom-gallery' )
		            	. '"><div class="media-modal-icon"></div></a></div></li>';
        		}
		?>
        </ul>

        <input type="hidden" id="image_gallery" name="image_gallery"
        	value="<?php echo esc_attr( $image_gallery ); ?>" />
        <?php wp_nonce_field( 'custom_gallery', 'custom_gallery' ); ?>

    </div>

    <p class="add_gallery_images hide-if-no-js">
        <a href="#"><?php _e( 'Add images', 'custom-gallery' ); ?></a>
    </p>

    <?php 	// options don't exist yet, set to checked by default
    	if ( ! get_post_meta( get_the_ID(), '_custom_gallery_link_images', true ) )
	        $checked = ' checked="checked"';
    	else
        	$checked = custom_gallery_has_linked_images() ? checked( get_post_meta( get_the_ID(), '_custom_gallery_link_images', true ), 'on', false ) : '';
	?>

    <?php
    /*
     * Image ordering and removing - Javascript
     */
	?>
    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Uploading files
            var image_gallery_frame;
            var $image_gallery_ids = $('#image_gallery');
            var $gallery_images = $('#gallery_images_container ul.gallery_images');

            jQuery('.add_gallery_images').on( 'click', 'a', function( event ) {

                var $el = $(this);
                var attachment_ids = $image_gallery_ids.val();

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if ( image_gallery_frame ) {
                    image_gallery_frame.open();
                    return;
                }

                // Create the media frame.
                image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: '<?php _e( 'Add Images to Gallery', 'custom-gallery' ); ?>',
                    button: {
                        text: '<?php _e( 'Add to gallery', 'custom-gallery' ); ?>',
                    },
                    multiple: true
                });

                // When an image is selected, run a callback.
                image_gallery_frame.on( 'select', function() {

                    var selection = image_gallery_frame.state().get('selection');

                    selection.map( function( attachment ) {

                        attachment = attachment.toJSON();

                        if ( attachment.id ) {
                            attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

                             $gallery_images.append('\
                                <li class="image attachment details" data-attachment_id="' + attachment.id + '">\
                                    <div class="attachment-preview">\
                                        <div class="thumbnail">\
                                            <img src="' + attachment.url + '" />\
                                        </div>\
                                       <a href="#" class="delete check" title="<?php _e( 'Remove image', 'custom-gallery' ); ?>"><div class="media-modal-icon"></div></a>\
                                    </div>\
                                </li>');

                        }

                    } );

                    $image_gallery_ids.val( attachment_ids );
                });

                // Finally, open the modal.
                image_gallery_frame.open();
            });

            // Image ordering
            $gallery_images.sortable({
                items: 'li.image',
                cursor: 'move',
                scrollSensitivity:40,
                forcePlaceholderSize: true,
                forceHelperSize: false,
                helper: 'clone',
                opacity: 0.65,
                placeholder: 'eig-metabox-sortable-placeholder',
                start:function(event,ui){
                    ui.item.css('background-color','#f6f6f6');
                },
                stop:function(event,ui){
                    ui.item.removeAttr('style');
                },
                update: function(event, ui) {
                    var attachment_ids = '';

                    $('#gallery_images_container ul li.image').css('cursor','default').each(function() {
                        var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                        attachment_ids = attachment_ids + attachment_id + ',';
                    });

                    $image_gallery_ids.val( attachment_ids );
                }
            });

            // Remove images
            $('#gallery_images_container').on( 'click', 'a.delete', function() {

                $(this).closest('li.image').remove();

                var attachment_ids = '';

                $('#gallery_images_container ul li.image').css('cursor','default').each(function() {
                    var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                    attachment_ids = attachment_ids + attachment_id + ',';
                });

                $image_gallery_ids.val( attachment_ids );

                return false;
            } );

        });
    </script>
    <?php
}


/*
 * Save function
 *
 */

function custom_gallery_save_post( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    $post_types = custom_gallery_allowed_post_types();

    // check user permissions
    if ( isset( $_POST[ 'post_type' ] ) && !array_key_exists( $_POST[ 'post_type' ], $post_types ) ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    }
    else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }

    if ( ! isset( $_POST[ 'custom_gallery' ] ) || ! wp_verify_nonce( $_POST[ 'custom_gallery' ], 'custom_gallery' ) )
        return;

    if ( isset( $_POST[ 'image_gallery' ] ) && !empty( $_POST[ 'image_gallery' ] ) ) {
        $attachment_ids = sanitize_text_field( $_POST['image_gallery'] );
        $attachment_ids = explode( ',', $attachment_ids ); // turn comma separated values into array
        $attachment_ids = array_filter( $attachment_ids  ); // clean the array
        $attachment_ids =  implode( ',', $attachment_ids ); // return back to comma separated list with no trailing comma. This is common when deleting the images
        update_post_meta( $post_id, '_custom_gallery', $attachment_ids );
    } else {
        delete_post_meta( $post_id, '_custom_gallery' );
    }

    // link to larger images
    if ( isset( $_POST[ 'custom_gallery_link_images' ] ) )
        update_post_meta( $post_id, '_custom_gallery_link_images', $_POST[ 'custom_gallery_link_images' ] );
    else
        update_post_meta( $post_id, '_custom_gallery_link_images', 'off' );

    do_action( 'custom_gallery_save_post', $post_id );
}
add_action( 'save_post', 'custom_gallery_save_post' );


/***** Admin page *****/

function custom_gallery_menu() {
	add_plugins_page( __( 'Gallery Fields', 'custom-gallery' ), __( 'Gallery Fields', 'custom-gallery' ), 'manage_options', 'custom-gallery', 'custom_gallery_admin_page' );
}
add_action( 'admin_menu', 'custom_gallery_menu' );


/*
 * Admin page
 *
 */

function custom_gallery_admin_page() { ?>
    <div class="wrap">
    	 <?php /* screen_icon( 'plugins' ); */ ?>
        <h2><?php _e( 'Gallery Fields', 'custom-gallery' ); ?></h2>

        <form action="options.php" method="POST">
        	<?php settings_errors(); ?>
            <?php settings_fields( 'my-settings-group' ); ?>
            <?php do_settings_sections( 'custom-gallery-settings' ); ?>
            <?php submit_button(); ?>
        </form>

    </div>
<?php
}


/*
 * Admin init
 */

function custom_gallery_admin_init() {
	register_setting( 'my-settings-group', 'custom-gallery', 'custom_gallery_settings_sanitize' );
	// sections
	add_settings_section( 'general', __( '', 'custom-gallery' ), '', 'custom-gallery-settings' );
	// settings
	add_settings_field( 'post-types', __( '<b>Select post types</b>', 'custom-gallery' ), 'post_types_callback', 'custom-gallery-settings', 'general' );
}
add_action( 'admin_init', 'custom_gallery_admin_init' );

/*
 * Post Types callback
 */

function post_types_callback() {

	// post and page defaults
/*	$defaults['post_types']['post'] = '';
	$defaults['post_types']['page'] = '';
*/
	$settings = (array) get_option( 'custom-gallery', $defaults );

	 foreach ( custom_gallery_get_post_types() as $key => $label ) {
		$post_types = isset( $settings['post_types'][ $key ] ) ? esc_attr( $settings['post_types'][ $key ] ) : '';

		?><p>
			<input type="checkbox" id="<?php echo $key; ?>" name="custom-gallery[post_types][<?php echo $key; ?>]" <?php checked( $post_types, 'on' ); ?>/><label for="<?php echo $key; ?>"> <?php echo $label; ?></label>
		</p><?php
	} 
}


/**
 * Sanitization
 *
 */

function custom_gallery_settings_sanitize( $input ) {

	// Create our array for storing the validated options
	$output = array();

	// post types
	$post_types = isset( $input['post_types'] ) ? $input['post_types'] : '';

	// only loop through if there are post types in the array
	if ( $post_types ) {
		foreach ( $post_types as $post_type => $value )
			$output[ 'post_types' ][ $post_type ] = isset( $input[ 'post_types' ][ $post_type ] ) ? 'on' : '';	
	}
	
	return apply_filters( 'sandbox_theme_validate_input_examples', $output, $input );
}


/**
 * Action Links
 */

function custom_gallery_plugin_action_links( $links ) {

	$settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/plugins.php?page=custom-gallery">'. __( 'Settings', 'custom-gallery' ) .'</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

/****************************
 *
 * Bootstrap Carousel Gallery
 *
 ****************************/


add_filter( 'post_gallery', 'custom_carousel_gallery_shortcode', 10, 4 );

function custom_carousel_gallery_shortcode( $output = '', $atts, $content = false, $tag = false ) {

	/* Define data by given attributes. */
	$shortcode_atts = shortcode_atts( array(
		'ids' => false,
		'type' => '',
		'name' => 'custom-carousel', /* Any name. String will be sanitize to be used as HTML ID. Recomended when you want to have more than one carousel in the same page. Default: custom-carousel. */
		'width' => '',  /* Carousel container width, in px or % */
		'height' => '', /* Carousel item height, in px or % */
		'indicators' => 'before-inner',  /* Accepted values: before-inner, after-inner, after-control, false. Default: before-inner. */
		'control' => 'true', /* Accepted values: true, false. Default: true. */
		'interval' => 5000,  /* The amount of time to delay between automatically cycling an item. If false, carousel will not automatically cycle. */
		'pause' => 'hover', /* Pauses the cycling of the carousel on mouseenter and resumes the cycling of the carousel on mouseleave. */
		'titletag' => 'h4', /* Define tag for image title. Default: h4. */
		'title' => 'false', /* Show or hide image title. Set false to hide. Default: true. */
		'text' => 'false', /* Show or hide image text. Set false to hide. Default: true. */
		'wpautop' => 'true', /* Auto-format text. Default: true. */
		'containerclass' => '', /* Extra class for container. */
		'itemclass' => '', /* Extra class for item. */
		'captionclass' => '' /* Extra class for caption. */
	), $atts );

	extract( $shortcode_atts );

	$name = sanitize_title( $name );

	/* Validate for necessary data */
	if ( isset( $ids ) 
		and ( ( isset( $type ) and 'carousel' == $type ) 
			or ( 'carousel-gallery' == $tag ) 
		) 
	) :

		/* Obtain HTML. */
		$output = custom_carousel_get_html_from( $shortcode_atts );

	/* If attributes could not be validated, execute default gallery shortcode function */
	else : $output = '';

	endif;

	return $output;

}



function custom_carousel_get_html_from( $shortcode_atts ) {

	/* Obtain posts array by given ids. Then construct HTML. */

	extract( $shortcode_atts );

	$images = custom_carousel_make_array( $ids );

	$output = '';

	if ( is_array( $images ) and !empty( $images ) ) :

		$posts = array();

		foreach ( $images as $image_id ) :

			$posts[] = get_post( intval( $image_id ) , ARRAY_A );

		endforeach;

		if ( is_array( $posts ) and !empty( $posts ) ) :

			$output = custom_carousel_make_html_from( $shortcode_atts , $posts );

		endif;

	endif;

	return $output;

}



function custom_carousel_make_html_from( $shortcode_atts , $posts ) {

	/* The important stuff happens here! */

	extract( $shortcode_atts );

	/* Define width of carousel container */
	$container_style = '';
	if ( $width ) :
		$container_style = 'style="';
		if ( $width ) : $container_style .= 'width:' . $width . ';' ; endif;
		$container_style .= '"';
	endif;

	/* Define height of carousel item */
	$item_style = '';
	if ( $height ) :
		$item_style = 'style="';
		if ( $height ) : $item_style .= 'height:' . $height . ';' ; endif;
		$item_style .= '"';
	endif;

	/* Initialize carousel HTML. */
	$output = '<div id="' . $name . '" class="carousel slide ' . $containerclass . '" ' . $container_style . '>';

	/* Try to obtain indicators before inner. */
	$output .= ( $indicators == 'before-inner' ) ? custom_carousel_make_indicators_html_from( $posts , $name ) : '' ;

	/* Initialize inner. */
	$output .= '<div class="carousel-inner">';

	/* Start counter. */
	$i = 0;

	/* Process each item into $posts array and obtain HTML. */
	foreach ( $posts as $post ) :

		if ( $post['post_type'] == 'attachment' ) : /* Make sure to include only attachments into the carousel */

			$image = wp_get_attachment_image_src( $post['ID'] , 'full' );

			$class = ( $i == 0 ) ? 'active ' : '';

			$output .= '<div class="' . $class . 'item ' . $itemclass . '" data-slide-no="' . $i . '" ' . $item_style . '>';

			$output .= '<img alt="' . $post['post_title'] . '" src="' . $image[0] . '" />';

			if ( $title != 'false' or $text != 'false' ) :

				$output .= '<div class="carousel-caption ' . $captionclass . '">';

				if ( $title != 'false' ) : $output .= '<'. $titletag .'>' . $post['post_title'] . '</' . $titletag . '>'; endif;

				if ( $text != 'false' ) : $output .= ( $wpautop != 'false' ) ? wpautop( $post['post_excerpt'] ) : $post['post_excerpt'] ; endif;

				$output .= '</div>';

			endif;

			$output .= '</div>';

			$i++;

		endif;

	endforeach;

	/* End inner. */
	$output .= '</div>';

	/* Try to obtain indicators after inner. */
	$output .= ( $indicators == 'after-inner' ) ? custom_carousel_make_indicators_html_from( $posts , $name ) : '' ;

	$output .= ( $control != 'false' ) ? custom_carousel_make_control_html_with( $name ) : '' ;

	/* Try to obtain indicators after control. */
	$output .= ( $indicators == 'after-control' ) ? custom_carousel_make_indicators_html_from( $posts , $name ) : '' ;

	/* End carousel HTML. */
	$output .= '</div>';

	/* Obtain javascript for carousel. */
	$output .= '<script type="text/javascript">// <![CDATA[
jQuery(document).ready( function() { jQuery(\'#' . $name . '\').carousel( { interval : ' . $interval . ' , pause : "' . $pause . '" } ); } );
// ]]></script>';

	return $output;

}


/* Obtain indicators from $posts array. */
function custom_carousel_make_indicators_html_from( $posts , $name ) {

	$output = '<ol class="carousel-indicators">';

	$i = 0;

	foreach ( $posts as $post ) :

		if ( $post['post_type'] == 'attachment' ) : /* Make sure to include only attachments into the carousel */

			$class = ( $i == 0 ) ? 'active' : '';

			$output .= '<li data-target="#' . $name . '" data-slide-to="' . $i . '" class="' . $class . '"></li>';

			$i++;

		endif;

	endforeach;

	$output .= '</ol>';

	return $output;

}


/* Obtain control links. */
function custom_carousel_make_control_html_with( $name ) {

	$output = '<div class="carousel-controls"><a class="carousel-control left" href="#' . $name . '" data-slide="prev">&lsaquo;</a>';
	$output .= '<a class="carousel-control right" href="#' . $name . '" data-slide="next">&rsaquo;</a></div>';

	return $output;

}



/* Obtain array of id given comma-separated values in a string. */
function custom_carousel_make_array( $string ) {

	$array = explode( ',' , $string );
	return $array;

}


/*************************************
 *
 * Shortcodes for CSS and JS fields
 *
 */


function custom_css_wrap($atts, $content = null) {
    $result = '<style type="text/css">';
    $result .= do_shortcode($content);
    $result .= '</style>';
    return $result;
}

add_shortcode('css', 'custom_css_wrap');

function custom_js_wrap( $atts, $content = null ) {
    $result = '<script type="text/javascript">';
    $result .= do_shortcode( $content );
    $result .= '</script>';
    return $result;
}

add_shortcode('js', 'custom_js_wrap');


function custom_load_script_file($atts) {

	extract( shortcode_atts( array(
		'css' => null, 'js' => null, 
		), $atts ) );

	if($css != '') {
		echo '<link rel="stylesheet" type="text/css" href="';
		echo get_template_directory_uri() . "/css/" . $css . '" />';
	}
	if($js != '') {
		echo '<script src="' . get_template_directory_uri() . "/js/" . $js . '"></script>';
	}
	return null;
}

add_shortcode('load', 'custom_load_script_file');




/** Load CSS field into header **/

add_action('wp_head', 'load_custom_css');
function load_custom_css() {
	$custom_css = do_shortcode( get_post_meta( get_the_ID(), "css", $single=true ) );
	if( $custom_css != '' ) {
		echo $custom_css;
	}
}

/** Load JS field into footer **/

add_action('wp_footer', 'load_custom_js');
function load_custom_js() {
	$custom_js = do_shortcode( get_post_meta( get_the_ID(), "js", $single=true ) );
	if( $custom_js != '' ) {
		echo $custom_js;
	}
}



/**********************************
 *
 * Bootstrap nav walker
 *
 */


class custom_bootstrap_navwalker extends Walker_Nav_Menu {
	
	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul role=\"menu\" class=\" dropdown-menu\">\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		/**
		 * Dividers, Headers or Disabled
	         * =============================
		 * Determine whether the item is a Divider, Header, Disabled or regular
		 * menu item. To prevent errors we use the strcasecmp() function to so a
		 * comparison that is not case sensitive. The strcasecmp() function returns
		 * a 0 if the strings are equal.
		 */
		if (strcasecmp($item->attr_title, 'divider') == 0 && $depth === 1) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if (strcasecmp($item->title, 'divider') == 0 && $depth === 1) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if (strcasecmp($item->attr_title, 'dropdown-header') == 0 && $depth === 1) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} else if (strcasecmp($item->attr_title, 'disabled') == 0) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			
			if($args->has_children) {	$class_names .= ' dropdown'; }
			if(in_array('current-menu-item', $classes)) { $class_names .= ' active'; }

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->title ) 	   ? $item->title 	   : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';

			//If item has_children add atts to a
			if($args->has_children && $depth === 0) {
				$atts['href']   		= '#';
				$atts['data-toggle']	= 'dropdown';
				$atts['class']			= 'dropdown-toggle';
			} else {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			/*
			 * Glyphicons
			 * ===========
			 * Since the the menu item is NOT a Divider or Header we check the see
			 * if there is a value in the attr_title property. If the attr_title
			 * property is NOT null we apply it as the class name for the glyphicon.

			if(! empty( $item->attr_title )){
				$item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
			} else {
				$item_output .= '<a'. $attributes .'>';
			}
			 */

			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= ($args->has_children && $depth === 0) ? ' <span class="caret"></span></a>' : '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. 
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @see Walker::start_el()
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */

	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( !$element ) {
            return;
        }

        $id_field = $this->db_fields['id'];

        //display this element
        if ( is_object( $args[0] ) ) {
           $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
}

/*
 * Bootstrap navwalker shortcode
 *
 */

function custom_bootstrap_navbar( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'menu' => null, 'navclass' => null, 
		), $atts ) );

	$menu_args = array (
			'menu' => $menu,
			'echo' => false,
			'depth' => 2,
			'container' => false,
			'menu_class' => 'nav navbar-nav',
			'fallback_cb' => 'custom_bootstrap_navwalker::fallback',
			'walker' => new custom_bootstrap_navwalker(),
		);

		if( $navclass=='' ) {
			$navclass = "top-nav";
		}

		$output = '<nav class="navbar navbar-default '
				. $navclass . '" role="navigation">';

		// Brand and toggle get grouped for better mobile display -->
		$output .= '
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="' . get_site_url() . '">' . $content .
			'</a>
		</div>

		<div class="collapse navbar-collapse navbar-ex1-collapse">';

		$output .= wp_nav_menu( $menu_args ) . '</div></nav>';

    return $output;
}

add_shortcode('navbar', 'custom_bootstrap_navbar');
