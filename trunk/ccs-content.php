<?php

/*====================================================================================================
 *
 * Content shortcode
 *
 *====================================================================================================*/

/*
 * Get a field or content from a post type
 */

function custom_content_shortcode($atts) {

	global $ccs_global_variable;

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

	if( ( $ccs_global_variable['is_gallery_loop'] == "true") || 
		( $ccs_global_variable['is_attachment_loop'] == "true" ) || 
		 ( $ccs_global_variable['is_acf_gallery_loop'] == "true" ) ) {
		switch($custom_field) {
			case "image": $out = $ccs_global_variable['current_image']; break;
			case "image-url": $out = $ccs_global_variable['current_image_url']; break;
			case "thumbnail": $out = $ccs_global_variable['current_image_thumb']; break;
			case "thumbnail-url": $out = $ccs_global_variable['current_image_thumb_url']; break;
			case "caption": $out = $ccs_global_variable['current_image_caption']; break;
			case "id": $out = $ccs_global_variable['current_attachment_id']; break;
			case "title": $out = $ccs_global_variable['current_image_title']; break;
			case "description": $out = $ccs_global_variable['current_image_description']; break;
			case "alt": $out = $ccs_global_variable['current_image_alt']; break;
			case "count": $out = $ccs_global_variable['current_row']; break;
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

	if($ccs_global_variable['is_repeater_loop'] != 'false') {

		$custom_id = $ccs_global_variable['current_loop_id'];

		if($custom_field=='row') {
			return $ccs_global_variable['current_row'];
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
				$out = $ccs_global_variable['current_loop_id'];
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

function custom_custom_taxonomies_terms_links($id){
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


