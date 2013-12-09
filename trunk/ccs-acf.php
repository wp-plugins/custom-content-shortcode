<?php

/*====================================================================================================
 *
 * Shortcodes for Advanced Custom Fields: gallery, repeater, flexible content
 *
 *====================================================================================================*/



function custom_sub_field( $atts ) {

	extract(shortcode_atts(array(
		'field' => '',
		'format' => '',
		'image' => '',
		'in' => '',
	), $atts));


	if ($image!='') {

		$output = get_sub_field($image);

		if ( $output != '' ) {
			switch($in) {
				case 'id' : $output = wp_get_attachment_image( $output, 'full' ); break;
				case 'url' : $output = '<img src="' . $output . '">'; break;
				default : /* image object */
					if(is_array($output)) {
						$output = wp_get_attachment_image( $output['id'], 'full' );
					}
			}
		}
	} else {

		$output = get_sub_field($field);

		if ( ($format=='true') && ($output!='') ) {
			$output = wpautop($output);
		}
	}
	return $output;

}
add_shortcode('sub', 'custom_sub_field');


function loop_through_acf_field( $atts, $content ) {

	/* For flex and repeater fields */

	extract( shortcode_atts( array(
		'field' => '',
		'count' => '',
		'start' => '',
	), $atts ));

	if ( get_field( $field ) ) {

		$index_now = 0;
		if ( $start == '' ) $start="1";


		while ( has_sub_field( $field ) ) {

			$index_now++;

			if ( $index_now >= $start ) { /* Start loop */

				if ( ( $count!= '' ) && ( $index_now >= ($start+$count) ) ) {
						/* If over count, continue empty looping for has_sub_field */
				} else {
					$output[] = do_shortcode( $content );
				}
			}
		}
	}
	if( $output != null)
		$output = implode( '', $output );
	return $output;
}
add_shortcode('flex', 'loop_through_acf_field');
add_shortcode('repeat', 'loop_through_acf_field');



function loop_through_acf_gallery_field( $atts, $content ) {

	global $ccs_global_variable;

	extract( shortcode_atts( array(
		'field' => '',
		'count' => '',
		'start' => '',
	), $atts ));

	$images = get_field( $field );

	if ( $images ) {

		$index_now = 0;
		if ( $start == '' ) $start="1";

		foreach ( $images as $image ) {

			$ccs_global_variable['current_image'] = $image;

			$index_now++;

			if ( $index_now >= $start ) { /* Start loop */

				if ( ( $count!= '' ) && ( $index_now >= ($start+$count) ) ) {
						break;				/* If over count, break the loop */
				}

				$output[] = do_shortcode( $content );
			}
		}
	}
	if( $output != null)
		$output = implode( '', $output );

	$ccs_global_variable['current_image'] = '';
	return $output;
}
add_shortcode('acf_gallery', 'loop_through_acf_gallery_field');


function get_image_details_from_acf_gallery( $atts ) {

	global $ccs_global_variable;

	extract(shortcode_atts(array(
		'field' => '',
		'sizes' => '',
	), $atts));

	if ( $field!='' ) {
		if ( $sizes=='' ) {
			$output = $ccs_global_variable['current_image'][$field];
		} else {
			$output = $ccs_global_variable['current_image']['sizes'][$field];
		}
	} else {
		$output = '<img src="' . $ccs_global_variable['current_image']['url'] . '">';
	}
	return $output;
}
add_shortcode('sub_image', 'get_image_details_from_acf_gallery');


function if_get_row_layout( $atts, $content ) {

	extract(shortcode_atts(array(
		'field' => '',
	), $atts));

	if( get_row_layout() == $field ) {
		return do_shortcode( $content );
	} else {
		return null;
	}

}
add_shortcode('row_layout', 'if_get_row_layout');














/*====================================================================================================
 *
 * Shortcode support for Live Edit
 *
 *====================================================================================================*/


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

