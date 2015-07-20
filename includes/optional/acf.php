<?php

/*---------------------------------------------
 *
 * Shortcodes for Advanced Custom Fields
 *
 * Gallery, repeater, flexible content, relationship/post object..
 *
 */

new CCS_To_ACF;

class CCS_To_ACF {

	public static $state;

	function __construct() {

		self::$state['is_relationship_loop'] = 'false';
		self::$state['is_repeater_or_flex_loop'] = 'false';

//    add_action( 'init', array($this, 'init') ); // Wait until plugins and theme loaded

		// Available to themes

    add_shortcode('acf_sub', array($this, 'acf_sub_field'));
    add_shortcode('flex', array($this, 'loop_through_acf_field'));
    add_shortcode('-flex', array($this, 'loop_through_acf_field'));
    add_shortcode('--flex', array($this, 'loop_through_acf_field'));

    // This will be called by [repeater] if not inside WCK metabox
    // add_shortcode('repeater', array($this, 'loop_through_acf_field'));
    add_shortcode('-repeater', array($this, 'loop_through_acf_field')); // Nested repeater

    add_shortcode('acf_gallery', array($this, 'loop_through_acf_gallery_field'));
    add_shortcode('acf_image', array($this, 'get_image_details_from_acf_gallery'));
    // add_shortcode('sub_image', array($this, 'get_image_details_from_acf_gallery')); // Alias
    add_shortcode('layout', array($this, 'if_get_row_layout'));
    add_shortcode('-layout', array($this, 'if_get_row_layout'));
    add_shortcode('--layout', array($this, 'if_get_row_layout'));

    // This will be called by [related] when relationship field is specified
    // add_shortcode('related', array($this, 'loop_relationship_field'));

    add_filter( 'ccs_loop_parameters', array($this, 'acf_date_parameters_for_loop') );

	}

  function init() {

    if (!class_exists('acf')) return; // If ACF is not installed

  }

	public static function acf_sub_field( $atts ) {

		extract(shortcode_atts(array(
			'field' => '',
			'format' => '',
			'image' => '',
			'in' => '',
			'size' => '',
		), $atts));

    if (empty($field) && isset($atts[0])) $field = $atts[0];

		if ($image!='') {

			$output = get_sub_field($image);

			if ( $output != '' ) {

				if ($size=='') $size='full';

				switch($in) {
					case 'id' : $output = wp_get_attachment_image( $output, $size ); break;
					case 'url' : $output = '<img src="' . $output . '">'; break;
					default : /* image object */
						if (is_array($output)) {
							$output = wp_get_attachment_image( $output['id'], $size );
						} else {
							$output = wp_get_attachment_image( $output, $size ); // Assume it's ID
						}
				}
			}

		} else {

			$output = do_shortcode(get_sub_field($field));

			if ( ($format=='true') && ($output!='') ) {
				$output = wpautop($output);
			}
		}
		// if (is_array($output)) $output=implode(', ', $output);
		return $output;
	}

	public static function loop_through_acf_field( $atts, $content ) {

		/* For repeater and flexible content fields */

		extract( shortcode_atts( array(
			'field' => '',
			'count' => '',
			'start' => '',
			'num' => '',
			'row' => '',
			'sub' => '',
			'sub_image' => '',
			'size' => '',
			'format' => '',
			'columns' => '', 'pad' => '', 'between' => '',
		), $atts ));

		if ( !empty($row) ) $num = $row; // Alias
		if ( !empty($num) && $num != 'rand' ) {
			$start = $num;
			$count = 1;
		}

    if (empty($field) && isset($atts[0])) $field = $atts[0];

		if ( empty($content) && (!empty($sub) || !empty($sub_image))) {

			if (!empty($sub_image))
				$content = '[acf_sub image="'.$sub_image.'"';
			else
				$content = '[acf_sub field="'.$sub.'"'; // Display sub field

			if (!empty($size))
				$content .= ' size= "'.$size.'"';
			if (!empty($format))
				$content .= ' format= "'.$format.'"';

			$content .= ']';
		}

		if ( have_rows( $field ) ) {

			$index_now = 0;
			$outputs = array();

			if ( $start == '' ) $start='1';

			while ( have_rows( $field ) ) {

				// Keep true for each row in case nested
				self::$state['is_repeater_or_flex_loop'] = 'true';

				the_row(); // Move index forward

				$index_now++;

				if ( $index_now >= $start ) { /* Start loop */

					if ( ( !empty($count) ) && ( $index_now >= ($start+$count) ) ) {
							/* If over count, continue empty looping for has_sub_field */
					} else {
						$outputs[] = str_replace( '{COUNT}', $index_now, do_shortcode($content) );
					}
				}
			}

			self::$state['is_repeater_or_flex_loop'] = 'false';

		} else {
			return null;
		}

		if ( $num == 'rand' ) {
			shuffle( $outputs );
			$item = array_pop($outputs);
			$outputs = array($item);
		}

		if( !empty($outputs) && is_array($outputs)) {

			if (!empty($columns)) {

				$output = CCS_Loop::render_columns( $outputs, $columns, $pad, $between );

			} else {

				$output = implode( '', $outputs );
			}
		}



		return $output;
	} //

	public static function loop_through_acf_gallery_field( $atts, $content ) {

		extract( shortcode_atts( array(
			'field' => '',
			'count' => '',
			'start' => '',
			'subfield' => '',
			'sub' => '',
			'columns' => '', 'pad' => '', 'between' => '',
		), $atts ));


    if (empty($field) && isset($atts[0])) $field = $atts[0];

		// If in repeater or flexible content, get subfield by default
		if ( self::$state['is_repeater_or_flex_loop']=='true' ) {
			$sub = 'true';
		}

		// Backward compatibility
		if (!empty($subfield)) {
			$field = $subfield;
			$sub = 'true';
		}

		global $post;
		$prev_post = $post;
		if (CCS_Loop::$state['is_loop']) {
			$post = get_post(CCS_Loop::$state['current_post_id']);
		}

		if (empty($sub)) {
			$images = get_field( $field );
		} else {
			$images = get_sub_field( $field );
		}

		if (CCS_Loop::$state['is_loop']) {
			$post = $prev_post;
		}

		$outputs = array();

		if ( $images ) {

			$index_now = 0;
			if ( $start == '' ) $start='1';

			foreach ( $images as $image ) {

				self::$state['current_image'] = $image;
				$index_now++;

				if ( $index_now >= $start ) { /* Start loop */

					if ( ( $count!= '' ) && ( $index_now >= ($start+$count) ) ) {
							break;				/* If over count, break the loop */
					}

					$outputs[] = str_replace( '{COUNT}', $index_now, do_shortcode($content) );
				}
			}
		}
		if( is_array($outputs)) {

			if (!empty($columns))
				$output = CCS_Loop::render_columns( $outputs, $columns, $pad, $between );
			else
				$output = implode( '', $outputs );
		} else {
			$output = $outputs;
		}

		self::$state['current_image'] = '';


		return $output;
	}

	public static function get_image_details_from_acf_gallery( $atts ) {

		extract(shortcode_atts(array(
			'field' => '',
			'size' => '',
			'class' => ''
		), $atts));

    if (empty($field) && isset($atts[0])) $field = $atts[0];

    if ( empty($size) ||
      (!empty($size) && !isset(self::$state['current_image']['sizes'][$size]))) {

      $image_url = self::$state['current_image']['url'];
    } else {
      $image_url = self::$state['current_image']['sizes'][$size];
    }

		if ( !empty($field) ) {

        if ($field == 'url') {
          $output = $image_url;
        } else {
          $output = self::$state['current_image'][$field];
        }

		} else {

			$output = '<img ';
			if (!empty($class)) $output .= ' class="'.$class.'"';
			$output .= 'src="' . $image_url . '">';

		}
		return $output;
	}

	public static function if_get_row_layout( $atts, $content ) {

		extract(shortcode_atts(array(
			'name' => '',
		), $atts));

    if (empty($name) && isset($atts[0])) $name = $atts[0];

		$names = CCS_Loop::explode_list($name);
		$layout = get_row_layout();

		if ( in_array($layout, $names) ) {
			return do_shortcode( $content );
		} else {
			return null;
		}
	}

	public static function loop_relationship_field( $atts, $content ) {

		extract( shortcode_atts( array(
			'field' => '',
			'subfield' => '',
      'trim' => ''
		), $atts ) );

		$output = array();

    if (empty($field) && isset($atts[0])) $field = $atts[0];

		// If in repeater or flexible content, get subfield by default
		if ( self::$state['is_repeater_or_flex_loop']=='true' ) {
			if (empty($subfield)) {
				$subfield = $field;
				$field = null;
			}
		}

		if (!empty($field)) {
			$posts = get_field($field);
		} elseif (!empty($subfield)) {
			$posts = get_sub_field($subfield);
		} else return null;


		if ($posts) {

			self::$state['is_relationship_loop'] = 'true';

			$index_now = 0;

			if ( ! is_array($posts) ) {
				$posts = array( $posts ); // Single post
			}

			foreach ($posts as $post) { // must be named $post

				$index_now++;

				self::$state['relationship_id'] = $post->ID;

				$output[] = str_replace('{COUNT}', $index_now, do_shortcode($content));
			}

		}

		self::$state['is_relationship_loop'] = 'false';

		if (is_array($output)) {
			$output = implode('', $output);
    }
    if (!empty($trim)) {
      $output = CCS_Format::trim($output, $trim);
    }

		return $output;
	}


	function acf_date_parameters_for_loop( $parameters ) {

		// ACF date field query
		if ( !empty($parameters['acf_date']) && !empty($parameters['value'])) {
			$parameters['field'] = $parameters['acf_date'];
			if ( empty($parameters['date_format']) )
				$parameters['date_format'] = 'Ymd';
			if ( empty($parameters['in']) )
				$parameters['in'] = 'string';
			unset($parameters['acf_date']);
		}
		return $parameters;
	}

}
