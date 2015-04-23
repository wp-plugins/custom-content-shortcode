<?php

/*---------------------------------------------
 *
 * Pass shortcode - pass field values
 *
 */

new CCS_Pass;

class CCS_Pass {

  function __construct() {
    add_shortcode( 'pass', array($this, 'pass_shortcode') );
    add_shortcode( '-pass', array($this, 'pass_shortcode') );
    add_shortcode( '--pass', array($this, 'pass_shortcode') );
  }

  public static function pass_shortcode( $atts, $content, $shortcode_name ) {

    $args = array(
      'field' => '',
      'fields' => '',
      'field_loop' => '',     // Field is array or comma-separated list
      'taxonomy_loop' => '',    // Loop through each term in taxonomy
      'list' => '',         // Loop through an arbitrary list of items
      'acf_gallery' => '',    // Pass image IDs from ACF gallery field

      'current' => '',
      'orderby' => '',      // Default: order by taxonomy term name
      'order' => '',
      'hide_empty' => 'false',

      'pre_render' => 'false',  // do_shortcode before replacing tags?
      'post_render' => 'true',  // do_shortcode at the end

      'trim' => 'false',
      'count' => '9999',      // Max number of taxonomy terms

      'user_field' => '',
      'user_fields' => '', // Multiple

      'global' => '',
      'sub' => ''
    );

    extract( shortcode_atts( $args , $atts, true ) );

    if ( $pre_render == 'true' ) $content = do_shortcode($content);

    $post_id = get_the_ID();

    // Support nested

    $prefix = '';
    if (substr($shortcode_name,0,2)=='--') {
      $prefix = '--';
    } elseif (substr($shortcode_name,0,1)=='-') {
      $prefix = '-';
    }


    /*---------------------------------------------
     *
     * Pass single field to {FIELD}
     *
     */

    if ( !empty($field) ) {

      if ($field=='gallery') $field = '_custom_gallery'; // Support CCS gallery field

      if ( !empty($global) ) {

        $field_value = '';
        if ( $field == 'this' ) {
          $field_value = $GLOBALS[$global];
        } elseif ( !empty($sub) && isset($GLOBALS[$global][$field][$sub]) ) {
          $field_value = $GLOBALS[$global][$field][$sub];
        } elseif (isset($GLOBALS[$global][$field])) {
          $field_value = $GLOBALS[$global][$field];
        }

      } elseif (class_exists('CCS_To_ACF') && CCS_To_ACF::$state['is_repeater_or_flex_loop']=='true') {
        // Repeater or flexible content field: then get sub field
        if (function_exists('get_sub_field')) {
          $field_value = get_sub_field( $field );
        } else $field_value = null;

      } else {
        // Get normal field
        $field_value = get_post_meta( $post_id, $field, true );
      }

      if (is_array($field_value)) {

        $field_value = implode(",", $field_value);

      } else {

        // Clean extra spaces if it's a list
        $field_value = CCS_Loop::clean_list($field_value);
      }

      // Replace it

      $content = str_replace('{'.$prefix.'FIELD}', $field_value, $content);


    /*---------------------------------------------
     *
     * Pass each item in a list stored in a field
     *
     */

    } elseif (!empty($field_loop)) {

      if ( $field_loop=='gallery' && class_exists('CCS_Gallery_Field')) {

        // Support gallery field

        $field_values = CCS_Gallery_Field::get_image_ids(); 

      } else {

        $field_values = get_post_meta( $post_id, $field_loop, true );
      }


      if (!empty($field_values)) {

        if (!is_array($field_values))
          $field_values = CCS_Loop::explode_list($field_values); // Get comma-separated list of values

        $contents = null;

        // Loop for the number of field values

        foreach ($field_values as $field_value) {

          $contents[] = str_replace('{'.$prefix.'FIELD}', $field_value, $content);
        }

        $content = implode('', $contents);
      }

    /*---------------------------------------------
     *
     * Pass image IDs from ACF gallery
     *
     */

    } elseif (!empty($acf_gallery)) {

      if ( function_exists('get_field') && function_exists('get_sub_field') ) {
        $field = $acf_gallery;
        $images = get_field($acf_gallery, $post_id, false);
        if (empty($field_value)) {
          // Try sub field
          $images = get_sub_field($acf_gallery, $post_id, false);
        }
        if (!empty($images)) {

          $ids = array();
          foreach ($images as $image) {
            $ids[] = $image['id'];
          }
          if (is_array($ids))
            $replace = implode(',', $ids);
          else $replace = $ids;
          $content = str_replace('{'.$prefix.'FIELD}', $replace, $content);
        }
      }


    /*---------------------------------------------
     *
     * Pass each taxonomy term
     *
     */

    } elseif (!empty($taxonomy_loop)) {

      if ( $current=='true' ) {

        if ( empty($orderby) && empty($order) ) {

          // Doesn't accept order/orderby parameters - but it's cached
          $terms = get_the_terms( $post_id, $taxonomy_loop );
        } else {

          $terms = wp_get_object_terms( $post_id, $taxonomy_loop, array(
            'orderby' => empty($orderby) ? 'name' : $orderby,
            'order' => empty($order) ? 'ASC' : strtoupper($order),
          ));

        }

      } else {

        // Get all terms: not by post ID
        $terms = get_terms( $taxonomy_loop, array(
          'orderby' => empty($orderby) ? 'name' : $orderby,
          'order' => empty($order) ? 'ASC' : strtoupper($order),
          'hide_empty' => ($hide_empty=='true') // Boolean
        ));
      }

      $contents = '';

      // Loop through each term

      if ( !empty( $terms ) ) {

        $i = 0;

        foreach ($terms as $term) {

          if ($i++ >= $count) break;

          $slug = $term->slug;
          $id = $term->term_id;
          $name = $term->name;

          $replaced_content = str_replace('{'.$prefix.'TERM}',
            $slug, $content);
          $replaced_content = str_replace('{'.$prefix.'TERM_ID}',
            $id, $replaced_content);
          $replaced_content = str_replace('{'.$prefix.'TERM_NAME}',
            $name, $replaced_content);

          $contents .= $replaced_content;
        }
      }

      $content = $contents;


    /*---------------------------------------------
     *
     * Pass an arbitrary list of items
     *
     */
    
    } elseif (!empty($list)) {

      $items = CCS_Loop::explode_list($list); // Comma-separated list -> array

      $contents = '';

      foreach ($items as $item) {

        $replaced_content = $content;

        // Multiple items per loop
        if ( strpos($item, ':') !== false ) {

          $parts = explode(':', $item);
          $count = count($parts);
          for ($i=0; $i < $count; $i++) { 

            $this_item = trim($parts[$i]);

            // Index starts at ITEM_1
            $replaced_content = str_replace(
              '{'.$prefix.'ITEM_'.($i+1).'}', $this_item, $replaced_content);

            // Would this be useful?
            // $replaced_content = str_replace('{Item_'.$i.'}', ucfirst($this_item),
            //  $replaced_content);
          }

        } else {
          $replaced_content = str_replace('{'.$prefix.'ITEM}',
            $item, $replaced_content);
          $replaced_content = str_replace('{'.$prefix.'Item}',
            ucfirst($item), $replaced_content );
        }

        $contents .= $replaced_content;
      }

      $content = $contents;

    }


    /*---------------------------------------------
     *
     * Pass user field(s)
     *
     */
    
    if (!empty($user_field)) {
      $user_field_value = do_shortcode('[user '.$user_field.' out="slug"]');
      // Replace it
      $content = str_replace('{'.$prefix.'USER_FIELD}', $user_field_value, $content);
    }

    if (!empty($user_fields)) {
      $user_fields_array = CCS_Loop::explode_list($user_fields);

      foreach ($user_fields_array as $this_field) {
        $user_field_value = do_shortcode('[user '.$this_field.' out="slug"]');
        // Replace {FIELD_NAME}
        $content = str_replace('{'.$prefix.strtoupper($this_field).'}', $user_field_value, $content);
      }
    }


    if ( !empty($fields) ) {

      if ( !empty($global) ) {

        $fields = CCS_Loop::explode_list($fields);

        foreach ($fields as $this_field) {
          $tag = '{'.$prefix.strtoupper($this_field).'}';
          $value = '';
          if (isset($GLOBALS[$global][$this_field])) {
            $value = $GLOBALS[$global][$this_field];
          }
          $content = str_replace($tag, $value, $content);
        }

      } else {

        // Replace these fields (+default)
        $content = CCS_Loop::render_field_tags( $content, array('fields' => $fields) );
      }
    } else {
      $content = CCS_Loop::render_default_field_tags( $content );
    }

    if ( $post_render == 'true' ) $content = do_shortcode($content);

    // Trim trailing white space and comma
    if ( $trim != 'false' ) {

      if ($trim=='true') $trim = null;
      $content = rtrim($content, " \t\n\r\0\x0B,".$trim);
    }

    return $content;

  } // End pass shortcode
  
} // End CCS_Pass
