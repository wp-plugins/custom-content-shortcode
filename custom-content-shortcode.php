<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: http://wordpress.org/plugins/custom-content-shortcode/
Description: Display posts, pages, custom post types, custom fields, files, images, comments, attachments, menus, or widget areas
Version: 0.3.8
Author: Eliot Akira
Author URI: eliotakira.com
License: GPL2
*/

$global_vars = array(
	'is_loop' => 'false',
	'is_gallery_loop' => 'false',
	'is_attachment_loop' => 'false',
	'is_repeater_loop' => 'false',
	'is_acf_gallery_loop' => 'false',
	'current_loop_id' => '',
	'current_row' => '',
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
	'current_attachment_id' => '',
	'current_attachment_ids' => '',
	'current_script' => '',
);

global $sort_posts; global $sort_key;

/*
 * Get a field or content from a post type
 */

function custom_content_shortcode($atts) {

	global $global_vars;

	extract(shortcode_atts(array(
		'type' => null,
		'name' => null,
		'field' => null,
		'id' => null,
		'menu' => null, 'ul' => null,
		'format' => null, 'shortcode' => null,
		'gallery' => 'false',
		'group' => null,
		'area' => null, 'sidebar' => null, 
		'align' => null, 'class' => null, 'height' => null,
		'num' => null, 'image' => null, 'in' => null,
		'row' => null, 'sub' => null,
		'acf_gallery' => null,
		'words' => null, 'len' => null, 'length' => null,
		'date_format' => null,
	), $atts));

	$custom_post_type = $type;
	$custom_post_name = $name;
	$custom_menu_name = $menu;
	$custom_field = $field;
	$custom_id = $id;
	$content_format = $format;
	$shortcode_option = $shortcode;
	$custom_gallery_type = $gallery;
	$custom_gallery_name = $group;
	$custom_area_name = $area;
	if($len!='') $length=$len;

	$out = null;
	if($image != null) {
		$custom_field = $image; // Search for the image field
	}

	if( $custom_post_type == '' ) { // If no post type is specified, then default is any
		$custom_post_type = 'any';
	}

	// If we're in a gallery field or attachments loop, return requested field

	if( ( $global_vars['is_gallery_loop'] == "true") || 
		( $global_vars['is_attachment_loop'] == "true" ) || 
		 ( $global_vars['is_acf_gallery_loop'] == "true" ) ) {
		switch($custom_field) {
			case "image": $out = $global_vars['current_image']; break;
			case "image-url": $out = $global_vars['current_image_url']; break;
			case "thumbnail": $out = $global_vars['current_image_thumb']; break;
			case "thumbnail-url": $out = $global_vars['current_image_thumb_url']; break;
			case "caption": $out = $global_vars['current_image_caption']; break;
			case "id": $out = $global_vars['current_attachment_id']; break;
			case "title": $out = $global_vars['current_image_title']; break;
			case "description": $out = $global_vars['current_image_description']; break;
			case "alt": $out = $global_vars['current_image_alt']; break;
			case "count": $out = $global_vars['current_row']; break;
		}
		if($class!='')
			return '<div class="' . $class . '">' . $out . '</div>';
		else return $out;
	}


	// Display sidebar/widget area

	if( $sidebar != '') {
		$custom_area_name = $sidebar;
	}
	if( $custom_area_name != '') {
		$back =  '<div id="' . str_replace( " ", "_", $custom_area_name ) . '" class="sidebar';
		if($class!='')
			$back .=  ' ' . $class;

		$back .= '">';

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
			'menu_class' => $ul,
		);

		$output = wp_nav_menu( $menu_args );

		if( $class == '') {
			return $output;
		} else {
			return '<div class="' . $class . '">' . $output . '</div>';
		}
	}


	// If post name/slug is defined, get its ID

	if($custom_post_name != '') {
		$args=array(
			'name' => $custom_post_name,
			'post_type' => $custom_post_type,
			'post_status' => 'publish',
			'posts_per_page' => '1',
  		);

		$my_posts = get_posts($args);
		if( $my_posts ) {
			$custom_id=$my_posts[0]->ID; }
		else { return null; // No posts found by that name
		}
	}
	else {

		// If no name or id, then current post

		if($custom_id == '') { $custom_id = get_the_ID(); }
	}

	// If repeater field loop then get sub field

	if($global_vars['is_repeater_loop'] != 'false') {

		$custom_id = $global_vars['current_loop_id'];

		if($custom_field=='row') {
			return $global_vars['current_row'];
		}
		if( function_exists('the_sub_field') ) {

			$out = get_sub_field($custom_field, $custom_id);
			switch($in) {
				case 'id' : $out = wp_get_attachment_image( $out, 'full' ); break;
				case 'url' : $out = '<img src="' . $out . '">'; break;
				default : if(is_array($out)) {
					$out = wp_get_attachment_image( $out['id'], 'full' );
				}
			}
			if($custom_field == 'id') {
				$out = $global_vars['current_loop_id'];
			}
		} else {
			$out = get_post_meta($custom_id, $custom_field, $single=true);
		}
		if(($class!='') || ($align!='')) {
			$pre = '<div';
			if($class!='')
				$pre .= ' class="' . $class . '"';
			if($align!='')
				$pre .= ' align="' . $align . '"';
			$pre .= '>' . $out . '</div>';
			return $pre;
		}
		else return $out;
	}
	
	// Repeater field subfield

	if($sub != '') {
		$out = null;
		if( function_exists('get_field') ) {
			$rows = get_field($custom_field, $custom_id); // Get all rows
			$row = $rows[$row-1]; // Get the specific row (first, second, ...)
			$out = $row[$sub]; // Get the subfield
			switch($in) {
				case 'id' : $out = wp_get_attachment_image( $out, 'full' ); break;
				case 'url' : $out = '<img src="' . $out . '">'; break;
				default : if(is_array($out)) {
					$out = wp_get_attachment_image( $out['id'], 'full' );
				}
			}
		}
		if(($class!='') || ($align!='')) {
			$pre = '<div';
			if($class!='')
				$pre .= ' class="' . $class . '"';
			if($align!='')
				$pre .= ' align="' . $align . '"';
			$pre .= '>' . $out . '</div>';
			return $pre;
		}
		else return $out;
	}


	// Gallery types - native or carousel

	if( $custom_gallery_type == "carousel") {
		$out = '[gallery type="carousel" ';
		if($custom_gallery_name != '') {
			$out .= 'name ="' . $custom_gallery_name . '" ';
		}
		if($height!='') {
			$out .= 'height ="' . $height . '" ';	
		}
		$out .= 'ids="';

		if($acf_gallery!='') {
			if( function_exists('get_field') ) {
				$out .= implode(',', get_field($acf_gallery, $custom_id, false));
			}
		} else {
			$out .= get_post_meta( $custom_id, '_custom_gallery', true );
		}
		$out .= '" ]';

		if($class!='')
			$out = '<div class="' . $class . '">' . $out . '</div>';
		
		return do_shortcode( $out );
	} else {

		if( $custom_gallery_type == "native") {
			$out = '[gallery " ';
			if($custom_gallery_name != '') {
				$out .= 'name ="' . $custom_gallery_name . '" ';
			}
			$out .= 'ids="';

			if($acf_gallery!='') {
				if( function_exists('get_field') ) {
					$out .= implode(',', get_field($acf_gallery, $custom_id, false));
				}
			} else {
				$out .= get_post_meta( $custom_id, '_custom_gallery', true );
			}
			$out .= '" ]';

			if($class!='')
				$out = '<div class="' . $class . '">' . $out . '</div>';
			return do_shortcode( $out );
		}	
	}

	// Image field

	if($image != null) {
		$image_id = get_post_meta( $custom_id, $image, true );
		$image_return = wp_get_attachment_image( $image_id, 'full' );
		if($class!='')
			$image_return = '<div class="' . $class . '">' . $image_return . '</div>';
		return $image_return;
	}

	// If no field is specified, return content

	if($custom_field == '') { 

		$out = get_post( $custom_id );
		$out = $out->post_content;
		if($content_format=='')
			$content_format = 'true';

	} else { // else return specified field


		// Predefined fields

		switch($custom_field) {
			case "id": $out = $custom_id; break;
			case "slug": $out = get_post($custom_id)->post_name; break;
			case "title": $out = get_post($custom_id)->post_title; break;
			case "author": $out = get_the_author($custom_id); break;
			case "date":

				if($date_format!='') {
					$out = mysql2date($date_format, get_post($custom_id)->post_date); break;
				}
				else { // Default date format under Settings -> General
					$out = mysql2date(get_option('date_format'), get_post($custom_id)->post_date); break;
				}
			case "url": $out = get_post_permalink($custom_id); break;
			case "image": $out = get_the_post_thumbnail($custom_id); break;
			case "image-url": $out = wp_get_attachment_url(get_post_thumbnail_id($custom_id)); break;
			case "thumbnail": $out = get_the_post_thumbnail( $custom_id, 'thumbnail' ); break;
			case "thumbnail-url": $res = wp_get_attachment_image_src( get_post_thumbnail_id($custom_id), 'thumbnail' ); $out = $res['0']; break;
			case "tags": $out = implode(' ', wp_get_post_tags( $custom_id, array( 'fields' => 'names' ) ) ); break;
			case 'gallery' :

				// Get specific image from gallery field

				$attachment_ids = get_post_meta( $custom_id, '_custom_gallery', true );
				$attachment_ids = array_filter( explode( ',', $attachment_ids ) );

				if($num == null) { $num = '1'; }
					$out = wp_get_attachment_image( $attachment_ids[$num-1], 'full' );
				break;

			case 'excerpt' :

				$out = get_post($custom_id);

				// Get excerpt
				$excerpt = get_post($custom_id)->post_excerpt;
				if( ($excerpt=='') || (is_wp_error($excerpt)) ) {
					$out = $out->post_content;
					if(($words=='') && ($length==''))
						$words='35';
				} else {
					$out = $excerpt; 
				}
				break;

			default :

				// Get other fields

				$out = get_post_meta($custom_id, $custom_field, $single=true);
				break;

		}

	}

	if($words!='') {
		$excerpt_length = $words;
		$the_excerpt = $out;

		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);

		if(count($words) > $excerpt_length) :
			array_pop($words);
//			array_push($words, '…');
			$the_excerpt = implode(' ', $words);
		endif;

		$out = $the_excerpt;
	}
	if($length!='') {

		$the_excerpt = $out;
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images

		$out = mb_substr($the_excerpt, 0, $length, 'UTF-8');
	}

	if($class!='')
		$out = '<div class="' . $class . '">' . $out . '</div>';


	if($content_format == 'true') { // Format?
		$out = wpautop( $out );
	}

	if($shortcode_option != 'false') { // Shortcode?
		$out = do_shortcode( $out );
	}

	return $out;
}

add_shortcode('content', 'custom_content_shortcode');

// For debugging purpose: list all taxonomies

function custom_taxonomies_terms_links($id){
  // get post by post id
  $post = get_post( $id );

  // get post type by post
  $post_type = $post->post_type;

  // get post type taxonomies
  $taxonomies = get_object_taxonomies( $post_type, 'objects' );

  $out = array();
  foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){

    // get the terms related to post
    $terms = get_the_terms( $post->ID, $taxonomy_slug );

    if ( !empty( $terms ) ) {
      $out[] = "<h2>" . $taxonomy->label . "</h2>\n<ul>";
      foreach ( $terms as $term ) {
        $out[] =
          '  <li><a href="'
        .    get_term_link( $term->slug, $taxonomy_slug ) .'">'
        .    $term->name
        . "</a></li>\n";
      }
      $out[] = "</ul>\n";
    }
  }

  return implode('', $out );
}


// Sort series helper function

	function series_orderby_key( $a, $b ) {
		global $sort_posts;global $sort_key;

		$apos = array_search( get_post_meta( $a->ID, $sort_key, $single=true ), $sort_posts );
		$bpos = array_search( get_post_meta( $b->ID, $sort_key, $single=true ), $sort_posts );

		return ( $apos < $bpos ) ? -1 : 1;
	}


/**********
 *
 * Query loop shortcode
 *
 */

class Loop_Shortcode {

	function __construct() {
		add_action( 'init', array( &$this, 'register' ) );
	}

	function register() {
		add_shortcode( 'loop', array( &$this, 'simple_query_shortcode' ) );
		add_shortcode( 'pass', array( &$this, 'simple_query_shortcode' ) );
	}

	function simple_query_shortcode( $atts, $template = null, $shortcode_name ) {

		global $global_vars;
		global $sort_posts;
		global $sort_key;

		$global_vars['is_loop'] = "true";
		$global_vars['current_gallery_name'] = '';
		$global_vars['current_gallery_id'] = '';
		$global_vars['is_gallery_loop'] = "false";
		$global_vars['is_attachment_loop'] = "false";
		$global_vars['is_repeater_loop'] = "false";

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
			'acf_gallery' => '',
			'id' => '',
			'name' => '',
			'field' => '',
			'repeater' => '',
			'x' => '',
			'taxonomy' => '', 'tax' => '', 'value' => '',
			'orderby' => '', 'keyname' => '', 'order' => '',
			'series' => '', 'key' => '',
			'post_offset' => '', 'offset' => ''
		);

		$all_args = shortcode_atts( $args , $atts, true );
		extract( $all_args );

		$custom_value = $value;
		if($key!='') $keyname=$key;
		if($offset!='') $post_offset=$offset;

		if($x != '') { // Simple loop without query

			$output = array();
			ob_start();

			while($x > 0) {
				echo do_shortcode($template);
				$x--;
			}
			$global_vars['is_loop'] = "false";
			return ob_get_clean();
		}


		$query = array_merge( $atts, $all_args );

		// filter out non-wpquery arguments
		foreach( $args as $key => $value ) {
			unset( $query[$key] );
		}

		$current_name = $name;
		$custom_field = $field;


		if( $category != '' ) {
			$query['category_name'] = $category;
		}
		if( $count != '' ) {
			$query['posts_per_page'] = $count;
		} else {

			if($post_offset!='')
				$query['posts_per_page'] = '9999'; // Show all posts (to make offset work)
			else
				$query['posts_per_page'] = '-1'; // Show all posts (normal method)

		}

		if($post_offset!='')
			$query['offset'] = $post_offset;

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

		if(( $custom_field == 'gallery' ) && ($shortcode_name != 'pass') ){
			$gallery = 'true';
		}
		if( $type == '' ) {
			$query['post_type'] = 'any';
		} else {
			$query['post_type'] = $type;
			if( $custom_field != 'gallery' ) {
				$query['p'] = '';
			}
		}

// Custom taxonomy query

		if($tax!='') $taxonomy=$tax;
		if($taxonomy!='') {

			$query['tax_query'] = array (
					array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => array($custom_value),
					)
				);
		}


		if($order!='')
			$query['order'] = $order;

// Orderby

		if( $orderby != '') {
				$query['orderby'] = $orderby;
				if(in_array($orderby, array('meta_value', 'meta_value_num') )) {
					$query['meta_key'] = $keyname;
				}
				if($order=='') {
					if($orderby=='meta_value_num')
						$query['order'] = 'ASC';	
					else
						$query['order'] = 'DESC';
				}				
		}

// Get posts in a series

		if($series!='') {

//			Expand range: 1-3 -> 1,2,3

		/* PHP 5.3+
			$series = preg_replace_callback('/(\d+)-(\d+)/', function($m) {
			    return implode(',', range($m[1], $m[2]));
			}, $series);
		*/

		/* Compatible with older versions of PHP */

			$callback = create_function('$m', 'return implode(\',\', range($m[1], $m[2]));');
			$series = preg_replace_callback('/(\d+)-(\d+)/', $callback, $series);

			$sort_posts = explode(',', $series);

			$sort_key = $keyname;

				$query['meta_query'] = array(
						array(
							'key' => $keyname,
							'value' => $sort_posts,
							'compare' => 'IN'
						)
					);

		}


	/*-----------------------
	 * Main loop
	 *-----------------------*/

	if( ( $gallery!="true" ) && ( $type != "attachment") ) {

		if( $custom_field == "gallery" ) {
			$custom_field = "_custom_gallery";
		}

		$output = array();
		ob_start();
		$posts = new WP_Query( $query );

// Re-order by series

		if($series!='') {

			usort($posts->posts, "series_orderby_key");

		}

		// For each post found

		if( $posts->have_posts() ) : while( $posts->have_posts() ) : $posts->the_post();

/*********
 * Repeater field
 */

			if($repeater != '') {
				$global_vars['is_repeater_loop'] = "true";
				$global_vars['current_loop_id'] = get_the_ID();

				if( function_exists('get_field') ) {

					if( get_field($repeater, $global_vars['current_loop_id']) ) { // If the field exists

						$count=1;

						while( has_sub_field($repeater) ) : // For each row

						// Pass details onto content shortcode

						$keywords = apply_filters( 'query_shortcode_keywords', array(
							'ROW' => $count,
						) );
						$global_vars['current_row'] = $count;
						$output[] = do_shortcode($this->get_block_template( $template, $keywords ));
						$count++;
						endwhile;
					}
				}

				$global_vars['is_repeater_loop'] = "false";
			} else {


/*********
 * ACF Gallery field
 */

			if($acf_gallery != '') {
				$global_vars['is_acf_gallery_loop'] = "true";
				$global_vars['current_loop_id'] = get_the_ID();

				if( function_exists('get_field') ) {

					$images = get_field($acf_gallery, get_the_ID());
					if( $images ) { // If images exist

						$count=1;

						$global_vars['current_image_ids'] = implode(',', get_field($acf_gallery, get_the_ID(), false));

						if($shortcode_name == 'pass') {

							// Pass details onto content shortcode

							$keywords = apply_filters( 'query_shortcode_keywords', array(
								'FIELD' => $global_vars['current_image_ids'],
							) );
							$output[] = do_shortcode($this->get_block_template( $template, $keywords ));
							
						} else { // For each image

							foreach( $images as $image ) :

							$global_vars['current_row'] = $count;
							$global_vars['current_image'] = '<img src="' . $image['sizes']['large'] . '">';
							$global_vars['current_image_id'] = $image['id'];
							$global_vars['current_attachment_id'] = $image['id'];
							$global_vars['current_image_url'] = $image['url'];
							$global_vars['current_image_title'] = $image['title'];
							$global_vars['current_image_caption'] = $image['caption'];
							$global_vars['current_image_description'] = $image['description'];
							$global_vars['current_image_thumb'] = '<img src="' . $image['sizes']['thumbnail'] . '">';
							$global_vars['current_image_thumb_url'] = $image['sizes']['thumbnail'];
							$global_vars['current_image_alt'] = $image['alt'];

							$output[] = do_shortcode($template);
							$count++;
							endforeach;
						} // End for each image
					}
				}

				$global_vars['is_acf_gallery_loop'] = "false";
			} else {

			// Not gallery field

			// Attachments?

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

			// Normal custom fields

				$custom_field_content = get_post_meta( get_the_ID(), $custom_field, $single=true );
				$attachment_ids = get_post_meta( get_the_ID(), '_custom_gallery', true );
			}

			$keywords = apply_filters( 'query_shortcode_keywords', array(
				'QUERY' => serialize($query), // DEBUG purpose
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

			} // End of not gallery field

		} // End of not repeater

		endwhile; endif; // End loop for each post

		wp_reset_query();
		wp_reset_postdata();

		echo implode( $posts_separator, $output );

		$global_vars['is_loop'] = "false";
		return ob_get_clean();

	} else {

// Loop for attachments

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

						$new_children =& get_children( array (
							'post_parent' => get_the_ID(),
							'post_type' => 'attachment',
							'post_status' => 'any'
						) );

						foreach( $new_children as $attachment_id => $attachment ) {
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
				$global_vars['is_loop'] = "false";
				return ob_get_clean();
			}
		} // End type="attachment"

		else {

			/*********************
			 *
			 * Gallery Loop
			 *
			 */

		if( function_exists('custom_gallery_get_image_ids') ) {

			$output = array();
			ob_start();

			if($global_vars['current_gallery_id'] == '') {
				$global_vars['current_gallery_id'] = get_the_ID();
			}
			$posts = new WP_Query( $query );
			$attachment_ids = custom_gallery_get_image_ids();

			if ( $attachment_ids ) { 
				$has_gallery_images = get_post_meta( $global_vars['current_gallery_id'], '_custom_gallery', true );
				if ( !$has_gallery_images ) {
					$global_vars['is_loop'] = "false";
					return;
				}
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
					$image_link	= wp_get_attachment_image_src( $attachment_id, 'full' );
					$image_link	= $image_link[0];	
										
					$global_vars['current_image']=wp_get_attachment_image( $attachment_id, 'full' );
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
				$global_vars['is_loop'] = "false";
				return ob_get_clean();
	    	} // End if attachment IDs exist
		} // End if function exists 
		$global_vars['current_gallery_id'] = '';
		$global_vars['is_loop'] = "false";
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


/************************
 *
 * Custom gallery field
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

	$args = array( 'public' => true	);

	$post_types = get_post_types( $args );

	// remove attachment
	unset( $post_types[ 'attachment' ] );

	return apply_filters( 'custom_gallery_get_post_types', $post_types );

}

/*
 * Retrieve the allowed post types from the option row
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
/*    if ( isset( $_POST[ 'post_type' ] ) && !array_key_exists( $_POST[ 'post_type' ], $post_types ) ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    }
    else { */
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
/*    } */

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
	add_options_page( __( 'Gallery Fields', 'custom-gallery' ), __( 'Gallery Fields', 'custom-gallery' ), 'manage_options', 'custom-gallery', 'custom_gallery_admin_page' );
}
add_action( 'admin_menu', 'custom_gallery_menu' );

/*
 * Admin page
 *
 */

function custom_gallery_admin_page() {
	?>
    <div class="wrap">
    	 <?php /* screen_icon( 'plugins' ); */ ?>
        <h2><?php _e( 'Gallery Fields', 'custom-gallery' ); ?></h2>

        <form action="options.php" method="POST">
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
	
	return apply_filters( 'validate_input_examples', $output, $input );
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

	if ( is_array( $images ) and !empty( $images ) ) : $posts = array();

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
	$output = '<div id="' . $name . '" class="carousel slide ' . $containerclass . '" ' . $container_style . ' align="center">';

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
			</button>';
		if($content!='') {
			$output .= '<a class="navbar-brand" href="' . get_site_url() . '">' . do_shortcode($content) .
			'</a>';
		}
		$output .= '</div>

		<div class="collapse navbar-collapse navbar-ex1-collapse">';

		$output .= wp_nav_menu( $menu_args ) . '</div></nav>';

    return $output;
}

add_shortcode('navbar', 'custom_bootstrap_navbar');


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
		'css' => null, 'js' => null, 'dir' => null,
		'file' => null,'format' => null, 'shortcode' => null,
		'gfonts' => null,
		), $atts ) );

	switch($dir) {
		case 'web' : $dir = "http://"; break;
        case 'site' : $dir = home_url() . '/'; break; /* Site address */
		case 'wordpress' : $dir = get_site_url() . '/'; break; /* WordPress directory */
		case 'content' : $dir = get_site_url() . '/wp-content/'; break;
		case 'layout' : $dir = get_site_url() . '/wp-content/layout/'; break;
		case 'child' : $dir = get_stylesheet_directory_uri() . '/'; break;
		default:

			if(($dir=='theme')||($dir=='template')) {
				$dir = get_template_directory_uri() . '/';
			} else {
				$dir = get_template_directory_uri() . '/';
				if($css != '') {
					$dir .= 'css/';
				}
				if($js != '') {
					$dir .= 'js/';
				}
			}
	}

	if($css != '') {
		echo '<link rel="stylesheet" type="text/css" href="';
		echo $dir . $css . '" />';
	}
	if($gfonts != '') {
		echo '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=';
		echo $gfonts . '" />';
	}
	if($js != '') {
		echo '<script type="text/javascript" src="' . $dir . $js . '"></script>';
	}

	if($file != '') {

		$output = @file_get_contents($dir . $file);

		if($output!='') {
			if(($format == 'on')||($format == 'true')) { // Format?
				$output = wpautop( $output );
			}
			if(($shortcode != 'false')||($shortcode != 'off')) { // Shortcode?
				$output = do_shortcode( $output );
			}
			return $output;
		}
	}
	return null;
}

add_shortcode('load', 'custom_load_script_file');


/** Load CSS field into header **/

add_action('wp_head', 'load_custom_css');
function load_custom_css() {
	global $wp_query;

	$custom_css = get_post_meta( $wp_query->post->ID, "css", $single=true );

/*	if($custom_css == '') { */
		$root_dir_soft = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
		$default_layout_dir = $root_dir_soft . 'wp-content/layout/';
		$default_css = $default_layout_dir . 'style.css';

		if(file_exists($default_css))
			$custom_css .= '[load css="style.css" dir="layout"]';
/*	} */

	$custom_css = do_shortcode( $custom_css );
	if( $custom_css != '' ) {
		echo $custom_css;
	}
}

/** Load JS field into footer **/

add_action('wp_footer', 'load_custom_js');
function load_custom_js() {
	global $wp_query;

	$custom_js = get_post_meta( $wp_query->post->ID, "js", $single=true );

/*	if($custom_js == '') { */

		$root_dir_soft = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
		$default_layout_dir = $root_dir_soft . 'wp-content/layout/';
		$default_js = $default_layout_dir . 'scripts.js';

		if(file_exists($default_js))
			$custom_js .= '[load js="scripts.js" dir="layout"]';
/*	} */

	$custom_js = do_shortcode( $custom_js );
	if( $custom_js != '' ) {
		echo $custom_js;
	}
}

/** Load HTML field instead of content **/

add_action('the_content', 'load_custom_html');
function load_custom_html($content) {
	global $wp_query;
	global $global_vars;

	if(( $global_vars['is_loop'] == "false" ) &&
		!is_admin() ) {

		$html_field = get_post_meta( $wp_query->post->ID, "html", $single=true );

		/* Set default layout filename */

		$root_dir_soft = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
		$default_layout_dir = $root_dir_soft . 'wp-content/layout/';
		$default_header = 'header.html';

		$current_post_type = $wp_query->post->post_type;
		$current_post_slug = $wp_query->post->post_name;

		$default_post_type_template = $current_post_type . '.html';
		$default_current_post_type_template = $current_post_type . '-' . $current_post_slug . '.html';

		$default_current_page_template = 'page-' . $current_post_slug . '.html';

		$default_page_template = 'page.html';

		$default_footer = 'footer.html';

		$output = '';

		// Load default header

		if( file_exists( $default_layout_dir . $default_header ) ) {
			$output .= '[load file="'. $default_header . '" dir="layout"]';
		}

		// Load default page template

		if ( $html_field == '' ) {
			if( file_exists( $default_layout_dir . $default_current_post_type_template ) ) {
				$output .= '[load file="'. $default_current_post_type_template . '" dir="layout"]';
			}
			elseif( file_exists( $default_layout_dir . $default_post_type_template ) ) {
				$output .= '[load file="'. $default_post_type_template . '" dir="layout"]';
			}
			elseif( ($current_post_type == 'page') &&
				( file_exists( $default_layout_dir . $default_current_page_template ) ) ) {
					$output .= '[load file="' . $default_current_page_template . '" dir="layout"]';
			}
			elseif( file_exists( $default_layout_dir . $default_page_template ) ) {
				$output .= '[load file="' . $default_page_template . '" dir="layout"]';
			}
		} else {
			$output .= $html_field;
		}

		// Load default footer

		if( file_exists( $default_layout_dir . $default_footer ) ) {
			$output .= '[load file="' . $default_footer . '" dir="layout"]';
		}

		$custom_html = do_shortcode( $output );
		if( $custom_html != '' ) {
			return $custom_html;
		} else {
			return $content;
		}
	}
	return $content;
}


/*
 *
 * Shortcode support for Live Edit
 *
 */

function sLiveEdit($atts, $inside_content = null) {
	extract(shortcode_atts(array(
		'field' => '',
		'admin' => '',
		'editor' => '',
		'edit' => '',
		'only' => '',
		'content' => '',
		'title' => '',
		'all' => '',
	), $atts));

	if( (function_exists('live_edit') && ( (current_user_can('edit_posts')) || ($all=="true") ) &&
		($edit!='off')) ){

		$edit_field = '';

		if(($title!='false')&&($title!='off')) {
			$edit_field .= 'post_title,';	
		}
		if(($content!='false')&&($content!='off')) {
			$edit_field .= 'post_content,';	
		}

		if($admin!=''){
			if ( current_user_can( 'manage_options' ) ) { // Admin user
				$edit_field .= $admin;
			} else { // Editor
				if(($editor=='') && ($only=='')) { // Edit only for admin
					return do_shortcode($inside_content);
				}
				if($editor!='') {
					$edit_field .= $editor;
				}
				if($only != '') {
					$edit_field = $only;
				}
			}
		} else {			if($field != '') {
				$edit_field .= $field;
			}
			if($only != '') {
				$edit_field = $only;
			}
		}
		$edit_field = trim($edit_field, ',');
		echo '<div ';
		$output = live_edit($edit_field);
		echo '>';
		$output .= do_shortcode($inside_content) . '</div>';

		return $output;
	} else {
		return do_shortcode($inside_content);
	}
}
add_shortcode('live-edit', 'sLiveEdit');


/*
 * Site URL shortcode [url site/theme/child/content/uploads]
 */

class urlShortcode
{
    public static function userSettings()
    {
        $blogurl_settings = array();

        $blogurl_settings['home'] = get_option( 'home' );
        $blogurl_settings['wordpress'] = get_option( 'siteurl' );
        $blogurl_settings['content'] = get_option( 'siteurl' ) . '/' . 'wp-content';
        $blogurl_settings['templateurl'] = get_bloginfo( 'template_directory' );
        $blogurl_settings['childtemplateurl'] = get_bloginfo( 'stylesheet_directory' );
        
        $blogurl_settings['insertslash'] = false;
        
        return $blogurl_settings;
    }
    
    public static function custom_url( $attributes )
    {
        $blogurl_settings = urlShortcode::getSettings();

		extract(shortcode_atts(array(
			'login' => '',
			'logout' => '',
			'go' => '',
		), $attributes));

        if( is_array( $attributes ) )
        {
            $attributes = array_flip( $attributes );
        }
        

		if($go!='') {
			if($go=='home')
				$go = $blogurl_settings['home'];
			elseif( (isset( $attributes['login'] )) || (isset( $attributes['logout'] )) )
				if( !strpos ($go,"." ) )
					$go = custom_content_shortcode(array('name'=>$go, 'field'=>'url'));
		}


        if( isset( $attributes['wordpress'] ) )
        {
            $return_blogurl = $blogurl_settings['wordpress'];
        }
        elseif( isset( $attributes['uploads'] ) )
        {
            $return_blogurl = $blogurl_settings['uploads'];
        }
        elseif( isset( $attributes['content'] ) )
        {
            $return_blogurl = $blogurl_settings['content'];
        }
        elseif( isset( $attributes['theme'] ) )
        {
            $return_blogurl = $blogurl_settings['templateurl'];
        }
        elseif( isset( $attributes['child'] ) )
        {
            $return_blogurl = $blogurl_settings['childtemplateurl'];
        }
        elseif( isset( $attributes['login'] ) )
        {
        	$return_blogurl = wp_login_url( $go );
        }
        elseif( isset( $attributes['logout'] ) )
        {
			$return_blogurl = wp_logout_url( $go );
        }
        else
        {
            $return_blogurl = $blogurl_settings['home'];
        }

        if( isset( $attributes['slash'] ) || ( $blogurl_settings['insertslash'] && !isset( $attributes['noslash'] ) ) )
        {
            $return_blogurl .= '/';
        }

        return $return_blogurl;
    }
    
    public static function getSettings()
    {
        $blogurl_settings = urlShortcode::userSettings();
        $upload_dir = wp_upload_dir();
        
        if( !$upload_dir['error'] )
        {
            $blogurl_settings['uploads'] = $upload_dir['baseurl'];
        }
        elseif( '' != get_option( 'upload_url_path' ) )
        {
            // Prior to WordPress 3.5, this was set in Settings > Media > Full URL path to files
            // In WordPress 3.5+ this is now hidden
            $blogurl_settings['uploads'] = get_option( 'upload_url_path' );
        }
        else
        {
            $blogurl_settings['uploads'] = $blogurl_settings['wordpress'] . '/' . get_option( 'upload_path' );
        }

        return $blogurl_settings;
    }
}

add_shortcode( 'url', array( 'urlShortcode', 'custom_url' ) );

/*
 * Comment form/template/count
 *
 */

function return_comment_form() {
	ob_start();
	comment_form( $args = array(
		'id_form'           => 'commentform',  // that's the wordpress default value! delete it or edit it ;)
		'id_submit'         => 'commentsubmit',
		'title_reply'       => __( '' ),  // Leave a Reply - that's the wordpress default value! delete it or edit it ;)
		'title_reply_to'    => __( '' ),  // Leave a Reply to %s - that's the wordpress default value! delete it or edit it ;)
		'cancel_reply_link' => __( 'Cancel Reply' ),  // that's the wordpress default value! delete it or edit it ;)
		'label_submit'      => __( 'Post Comment' ),  // that's the wordpress default value! delete it or edit it ;)
			
		'comment_field' =>  '<p><textarea placeholder="" id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>', 
			
		'comment_notes_after' => ''
	));
	$form = ob_get_contents();
    ob_end_clean();
    return $form;
}

function return_comments_template($file) {
	ob_start();
	comments_template($file);
	$form = ob_get_contents();
    ob_end_clean();
    return $form;
}

function custom_comment_shortcode( $atts, $content, $tag ) {

	if( is_array( $atts ) ) {
		$atts = array_flip( $atts );
	}

	if( ($tag=='comments') || isset( $atts['template'] ) ) {
		$content = return_comments_template($atts['template']);
		return $content;
	}

	if( isset( $atts['form'] ) ) {
		$content = return_comment_form();
		return $content;
	}
	if( isset( $atts['count'] ) ) {
		return get_comments_number();
	}
}
add_shortcode('comment', 'custom_comment_shortcode');
add_shortcode('comments', 'custom_comment_shortcode');

function custom_is_shortcode( $atts, $content, $tag ) {
	global $current_user;

	extract(shortcode_atts(array(
		'user' => '',
		'format' => '',
		'shortcode' => '',
	), $atts));

	if($format == 'true') { // Format?
		$content = wpautop( $content );
	}
	if($shortcode != 'false') { // Shortcode?
		$content = do_shortcode( $content );
	}

	if($user!='') {
		get_currentuserinfo();
		$is_it = false;

		if ( $user == ($current_user->user_login) )
			$is_it = true;
		if ( ( $user == ($current_user->ID) ) &&
			ctype_digit($user) ) // $user is a number?
				$is_it = true;
		if($tag=="isnt")
			$is_it = !$is_it;
		if($is_it)
			return $content;
		return null;
	}

	if( is_array( $atts ) ) {
		$atts = array_flip( $atts );
	}

	if 	( ($tag=='is') &&
		( 
		( isset( $atts['admin'] ) && current_user_can( 'manage_options' ) ) ||
		( isset( $atts['login'] ) && is_user_logged_in() ) ||
		( isset( $atts['logout'] ) && !is_user_logged_in() )
		) ) {
			return $content;
	}
	if 	( ($tag=='isnt') &&
		( 
		( isset( $atts['admin'] ) && !current_user_can( 'manage_options' ) ) ||
		( isset( $atts['login'] ) && !is_user_logged_in() ) ||
		( isset( $atts['logout'] ) && is_user_logged_in() )
		) ) {
			return $content;
	}

	return null;
}
add_shortcode('is', 'custom_is_shortcode');
add_shortcode('isnt', 'custom_is_shortcode');

function custom_user_shortcode( $atts, $content ) {

	global $current_user;
		get_currentuserinfo();

	if( is_array( $atts ) )
		$atts = array_flip( $atts );

	if( isset( $atts['name'] ) )
		return $current_user->user_login;

	if( isset( $atts['id'] ) )
		return $current_user->ID;

}
add_shortcode('user', 'custom_user_shortcode');


?>
