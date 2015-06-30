<?php

/*---------------------------------------------
 *
 * [content] - Display field or post content
 *
 * @todo Add and list available filters
 * @todo Separate code areas by functionality for better management
 *
 */

new CCS_Content;

class CCS_Content {

  public static $original_parameters; // Before merge with defaults
  public static $parameters; // with defaults
  public static $state;

  function __construct() {

    add_shortcode( 'content', array($this, 'content_shortcode') );
    add_shortcode( 'field', array($this, 'field_shortcode') );
    add_shortcode( 'taxonomy', array($this, 'taxonomy_shortcode') );
    add_shortcode( 'array', array($this, 'array_field_shortcode') );

    self::$state = array();
    self::$state['is_array_field'] = false;
  }


  /*---------------------------------------------
   *
   * Main function
   *
   */

  function content_shortcode( $parameters ) {

    $result = $this->before_anything( $parameters );
    if ( $result != false ) return $result;

    $parameters = $this->merge_with_defaults( $parameters );
    self::$parameters = $parameters;

    $result = $this->before_query( $parameters );

    if ( empty($result) ) {

      $result = $this->run_query( $parameters );
    }

    $result = $this->process_result( $result, self::$parameters );

    return $result;
  }

  /**
   *
   * Before anything, check for result
   *
   * @param   array   $parameters All shortcode parameters
   *
   * @return  false   Continue processing shortcode
   * @return  null    Exit shortcode with empty result
   * @return  string  Exit shortcode with result
   *
   */

  function before_anything( $parameters ) {

    $out = false;

    //
    // @todo Put a filter here and move below to optional/wck.php
    //
    if ( CCS_To_WCK::$state['is_wck_loaded'] == 'true' ) {

      if (
        ( CCS_To_WCK::$state['is_wck_metabox_loop'] == 'true' )
        ||  ( CCS_To_WCK::$state['is_wck_repeater'] == 'true' )
        ||  (
            // Backward compatibility for WCK metabox parameter
            ( !empty($parameters['meta']) || !empty($parameters['metabox']) )
            && !empty($parameters['field'])
            && ($parameters['field'] !== 'author')
          )
      ) {

        // For post field, get normal
        if ( CCS_To_WCK::$state['is_wck_post_field'] != 'true' ) {

          // Get WCK field
          $out = CCS_To_WCK::wck_field_shortcode( $parameters );
          if ( $out == false ) {
            $out = null; // Force empty content
          }
        }
      }
    }
    return $out;
  }


  /*---------------------------------------------
   *
   * Merge parameters with defaults
   *
   */

  function merge_with_defaults( $parameters ) {

    self::$original_parameters = $parameters;

    $defaults = array(

      'type' => 'any',
      'status' => 'publish',
      'name' => '',
      'id' => '',

      // Field value
      'field' => '',

      'page' => '',

      // Taxonomy value

      'taxonomy' => '',
      'term' => '', 'term_name' => '',
      'out' => '', // out="slug" taxonomy slug

      // Image field
      'image' => '',
      'size' => 'full', // Default
      'in' => '', // ID, url or object
      'return' => '',
      'alt' => '', 'title' => '',
      'height' => '', 'width' => '',
      'image_class' => '',
      'nopin' => '',
      'url' => '', // Option for image-link

      // Author meta
      'meta' => '',

      // Checkbox value
      'checkbox' => '',

      // Sidebar/widget area
      'area' => '', 'sidebar' => '',

      // Menu
      'menu' => '', 'ul' => '', 'cb' => '', 'menu_slug' => '',

      // Gallery
      'gallery' => 'false', 'group' => '',

      // Native gallery options

      'orderby' => '', 'order' => '', 'columns' => '',
       'include' => '', 'exclude' => '',

      // ACF gallery
      'row' => '', 'sub' => '',
      'acf_gallery' => '', 'num' => '',

      // ACF date field
      'acf_date' => '',

      // Read more
      'more' => '', 'link' => '', 'dots' => 'false',
      'between' => 'false',

      // Get property from field object
      'property' => '',

      // Formatting

      'format' => '', 'shortcode' => '', 'escape' => '',
      'filter' => '',
      'embed' => '', 'http' => '',
      'nl' => '', // Remove \r and \n
      'align' => '', 'class' => '', 'height' => '',
      'words' => '', 'len' => '', 'length' => '',
      'date_format' => '', 'timestamp' => '',
      'new' => '', // Set true to open link in new tab - currently only for download-link

      'currency' => '',
      'decimals' => '',
      'point' => '',
      'thousands' => ''
    );


    /*---------------------------------------------
     *
     * Pre-process parameters
     *
     */

    if ( isset($parameters['type']) && ($parameters['type']=='attachment') ) {
      if (!isset($parameters['status'])) {
        $parameters['status'] = 'any'; // Default for attachment
      }
    }

    // Default size for featured image thumbnail

    $image_fields = array('thumbnail','thumbnail-link');

    if ( isset($parameters['field']) && in_array($parameters['field'],$image_fields)) {
      $parameters['size'] = isset($parameters['size']) ? $parameters['size'] : 'thumbnail';
    }

    if (!empty($parameters['acf_date'])) {
      $parameters['field'] = $parameters['acf_date'];
    }



    // Merge with defaults

    $parameters = shortcode_atts($defaults, $parameters);




    /*---------------------------------------------
     *
     * Post-process parameters
     *
     */

    // Get page by name
    if (!empty($parameters['page'])) {

      $parameters['type'] = 'page';
      $parameters['name'] = $parameters['page'];
    }

    // Post status

    if (!empty($parameters['status'])) {
      $parameters['status'] = CCS_Loop::explode_list($parameters['status']); // multiple values
    }

    // ACF page link
    if (!empty($parameters['link']) && empty($parameters['more'])) {
      $parameters['field'] = $parameters['link'];
      $parameters['return'] = 'page-link';
    }

    // Image field

    if (!empty($parameters['image'])) {
      $parameters['field'] = $parameters['image'];
    }

    // Image size alias
    if ($parameters['size']=='middle') {
      $parameters['size'] = 'medium';
    }

    // Checkbox
    if (!empty($parameters['checkbox'])) {
      $parameters['field'] = $parameters['checkbox'];
    }


    if (class_exists('CCS_To_ACF') && CCS_To_ACF::$state['is_relationship_loop']=='true') {

      // Inside ACF Relationship field
      $parameters['id'] = CCS_To_ACF::$state['relationship_id'];

    } else if ( CCS_To_WCK::$state['is_wck_post_field'] == 'true' ) {

      // Inside WCK post field
      $parameters['id'] = CCS_To_WCK::$state['current_wck_post_id'];
    }

    // HTML escape
    if ( $parameters['escape'] == 'true' && empty($parameters['shortcode']) ) {
      $parameters['shortcode'] = 'false';
    }

    // Date format: allow escape via "//" because "\" disappears in shortcode parameters
    if ( !empty($parameters['date_format']) ) {
      $parameters['date_format'] = str_replace("//", "\\", $parameters['date_format']);
    }

    return $parameters;
  }



  /*---------------------------------------------
   *
   * Before query: if return is not null, there is result already
   *
   */

  function before_query( $parameters ) {

    if ( ! CCS_Loop::$state['is_loop'] ) {
      $orig_post = get_the_ID();
    } else {
      $orig_post = '';
    }

    if (empty($parameters['id'])) {

      if ( CCS_Related::$state['is_related_posts_loop'] == 'true' ) {

        // Inside [related]
        $post_id = CCS_Related::$state['current_related_post_id'];

      }  elseif ( CCS_Loop::$state['is_loop'] ) {

        $post_id = CCS_Loop::$state['current_post_id']; // Current post in loop

      } else {
        $post_id = get_the_ID(); // Current post
      }

    } else {
      $post_id = $parameters['id'];
    }

    self::$state['current_post_id'] = $post_id;

    $result = '';


    /*---------------------------------------------
     *
     * Menu
     *
     */

    if ( !empty($parameters['menu']) || !empty($parameters['menu_slug']) ) {

      $args = array (
        'echo' => false,
        'menu_class' => $parameters['ul'],
        'container' => false, // 'div' container will not be added
        // 'fallback_cb' => $parameters['cb'], // name of default function
      );

      if ( !empty($parameters['menu']) ) {
        $args['menu'] = $parameters['menu'];
        $menu = $args['menu'];
      } elseif ( !empty($parameters['menu_slug']) ) {
        $args['theme_location'] = $parameters['menu_slug'];
        $menu = $args['theme_location'];
      }

      $result = wp_nav_menu( $args );

      if (empty($result)) {
        return '<ul class="nav"><li>'.$menu.'</li></ul>'; // Default menu
      }
      if( empty($parameters['class']) && empty($parameters['id']) ) {
        return $result;
      } else {
        $out = '<div';
        if (!empty($parameters['id'])) $out .= ' id="'.$parameters['id'].'"';
        if (!empty($parameters['class'])) $out .= ' class="'.$parameters['class'].'"';
        $out .= '>' . $result . '</div>';

        return $out;
      }

    } elseif ( !empty($parameters['sidebar']) || !empty($parameters['area']) ) {


    /*---------------------------------------------
     *
     * Sidebar or widget area
     *
     */

      if (!empty($parameters['sidebar']))
        $sidebar = $parameters['sidebar'];
      else $sidebar = $parameters['area'];

      $result =  '<div id="sidebar-' . str_replace( " ", "_", strtolower($sidebar)) . '"';


      if(!empty($parameters['class']))
        $result .=  ' class="' . $parameters['class'].'"';

      $result .= '>';

      ob_start();
      if ( function_exists('dynamic_sidebar') )
        dynamic_sidebar($parameters['sidebar']);
      $result .= ob_get_clean();
      $result .= "</div>";

      return $result;
    }


    /*---------------------------------------------
     *
     * Native gallery
     *
     */

    elseif ( $parameters['gallery'] == 'native' ) {

      $result = '[gallery " ';

      if(!empty($parameters['name'])) {
        $result .= 'name="' . $parameters['name'] . '" ';
      }

      $result .= 'ids="';

      if (!empty($parameters['acf_gallery'])) {
        if( function_exists('get_field') ) {
          $result .= implode(',', get_field($parameters['acf_gallery'], $post_id, false));
        }
      } else {
        $result .= get_post_meta( $post_id, '_custom_gallery', true );
      }
      $result .= '"';

      /* Additional parameters */

      $native_gallery_options = array(
        'orderby' => $parameters['orderby'],
        'order' => $parameters['order'],
        'columns' => $parameters['columns'],
        'size' => $parameters['size'],
        'link' => $parameters['link'],
        'include' => $parameters['include'],
        'exclude' => $parameters['exclude']
      );

      if (!empty($parameters['columns']))
        $parameters['columns'] = ''; // prevent CCS columns

      foreach ($native_gallery_options as $option => $value) {

        if (!empty($value)) {
          $result .= ' ' . $option . '="' . $value . '"';
        }
      }

      $result .= ']';

      if(!empty($parameters['class']))
        $result = '<div class="' . $parameters['class'] . '">' . $result . '</div>';

      return do_shortcode( $result );

    } elseif ( $parameters['gallery'] == 'carousel' ) {


      /*---------------------------------------------
       *
       * Gallery Bootstrap carousel
       *
       */

      $result = '[gallery type="carousel" ';

      if (!empty($parameters['name'])) {
        $result .= 'name="' . $parameters['name'] . '" ';
      }
      if (!empty($parameters['height'])!='') {
        $result .= 'height="' . $parameters['height'] . '" ';
      }
      $result .= 'ids="';

      if(!empty($parameters['acf_gallery'])) {
        if( function_exists('get_field') ) {
          $result .= implode(',', get_field($parameters['acf_gallery'], $post_id, false));
        }
      } else {
        $result .= get_post_meta( $post_id, '_custom_gallery', true );
      }
      $result .= '" ]';

      if (!empty($parameters['class']))
        $result = '<div class="' . $class . '">' . $result . '</div>';

      return do_shortcode( $result );
    }


    return $result;
  }

  /*---------------------------------------------
   *
   * Get the post
   *
   */

  function prepare_post( $parameters = array() ) {

    // Get post from ID

    if (!empty($parameters['id'])) {

      $this_post = get_post( $parameters['id'] );

      if (empty($this_post)) return false; // No post by that ID

      self::$state['current_post'] = $this_post;
      self::$state['current_post_id'] = $parameters['id'];

    } elseif (!empty($parameters['name'])) {

      // Get post from name

      $args=array(
        'name' => $parameters['name'],
        'post_type' => $parameters['type'],
        'post_status' => $parameters['status'], // Default is publish, or any for attachment
        'posts_per_page' => '1',
        );

      $posts = get_posts($args);

      if ( $posts ) {

        self::$state['current_post'] = $posts[0];
        self::$state['current_post_id'] = $posts[0]->ID; // ID of the post

      } else {

        return false; // No post by that name
      }

    } else {

      // Current post

      self::$state['current_post'] = get_post(self::$state['current_post_id']);

    }

    if ( !empty($parameters['exclude']) && ($parameters['exclude']=='this') ) {

      // Exclude current post ID
      if (self::$state['current_post_id'] == get_the_ID())
        return false;

    }

    return true;
  }


  /*---------------------------------------------
   *
   * Main query
   *
   */

  function run_query( $parameters ) {

    $result = '';

    if (self::prepare_post( $parameters ) == false) {

      return null; // No post by those parameters
    }


    /*---------------------------------------------
     *
     * Taxonomy
     *
     */

    elseif (!empty($parameters['taxonomy'])) {

      $results = array();

      if ($parameters['taxonomy'] == 'tag') {
        $taxonomy='post_tag'; // Alias
      } else {
        $taxonomy = $parameters['taxonomy'];
      }

      // Get taxonomy term by ID, slug or name

      if (!empty($parameters['term'])) {
        if (is_numeric($parameters['term'])) {
          // By term ID
          $terms = get_term_by('id', $parameters['term'], $taxonomy);
        } else {
          // By term slug
          $terms = get_term_by('slug', $parameters['term'], $taxonomy);
        }
        $terms = array($terms); // Single term
      } elseif (!empty($parameters['term_name'])) {
          // By term name
          $terms = get_term_by('name', $parameters['term_name'], $taxonomy);
          $terms = array($terms); // Single term
      } else {

        // Default: get all taxonomy terms of current post

        $terms = get_the_terms( self::$state['current_post_id'], $taxonomy );
      }

      if ( !empty( $terms ) ) {

        $slugs = array();
        if (!empty($parameters['image'])) {
          $parameters['field'] = $parameters['image'];
        }

        $tax_field = !empty($parameters['field']) ? $parameters['field'] : 'name';
        // Backward compatibility
        if ( !empty($parameters['out']) ) $tax_field = $parameters['out'];

        foreach ($terms as $term) {

          if (!is_object($term)) continue; // Invalid taxonomy

          $slugs[] = $term->slug;

          // Get taxonomy field

          switch ( $tax_field ) {
            case 'id': $results[] = $term->term_id; break;
            case 'slug': $results[] = $term->slug; break;
            case 'name': $results[] = $term->name; break;
            case 'description': $results[] = $term->description; break;
            case 'url':
              $results[] = get_term_link( $term );
            break;
            case 'link':
              $url = get_term_link( $term );
              $results[] = '<a href="'.$url.'">'.$term->name.'</a>';
            break;
            default:

              // Support custom taxonomy fields

              $field_value = self::get_the_taxonomy_field(
                $taxonomy, $term->term_id, $parameters['field'], $parameters
              );

              if (!empty($field_value)) {
                $results[] = $field_value;
              }

            break;
          }

        } // End for each term

        if ( $tax_field=='slug' ) {
          $result = implode(' ', $slugs);
          $result = trim($result);
        } else {
          $result = implode(', ', $results);
          $result = trim($result, " \t\n\r\0\x0B,");
        }
      } else {
        return null; // No terms found
      }

    }


    /*---------------------------------------------
     *
     * Image field
     *
     * @note Must be after taxonomy, to allow custom taxonomy image field
     *
     */

    elseif (!empty($parameters['image'])) {

      $result = self::get_image_field( $parameters );

    }


    /*---------------------------------------------
     *
     * ACF label for checkbox/select
     *
     */

    elseif ( !empty($parameters['field']) && ($parameters['out']=='label') ) {

      if (function_exists('get_field_object')) {

        $out = '';

        $all_selected = self::get_the_field( $parameters );

        if (!empty($all_selected)) {

          $field = get_field_object( $parameters['field'], self::$state['current_post_id'] );

          if ( isset($field['choices']) ) {

            if ( is_array($all_selected) ) {
              // Multiple selections
              foreach( $all_selected as $selected ){
                $out[] = $field['choices'][ $selected ];
              }
              $out = implode(', ', $out);
            } else {
              // Single selection
              $out = isset($field['choices'][$all_selected]) ?
                $field['choices'][$all_selected] : null;
            }

          } // End: if choices

        } // End: field not empty

        $result = $out;
      }

    }


    /*---------------------------------------------
     *
     * Field
     *
     * @note Must be after taxonomy, to allow custom taxonomy field
     *
     */

    elseif (!empty($parameters['field'])) {

      $result = self::get_the_field( $parameters );

    } elseif ( !empty(self::$state['current_post']) ) {

      /*---------------------------------------------
       *
       * Show post content - [content]
       *
       * TODO: How to detect and avoid infinite loop
       *
       */

      $result = self::$state['current_post']->post_content;

      // Format post content by default - except when trimmed
      if ( empty($parameters['words']) && empty($parameters['length']) ) {

        self::$parameters['format'] = empty(self::$parameters['format']) ?
          'true' : self::$parameters['format'];
      }
    }

    return $result;
  }


  function process_result( $result, $parameters ) {

    // If it's an array, make it a list

    if ( is_array($result) ) {
      $result = implode(', ', $result);
    }


    // Support qTranslate Plus

    $result = self::check_translation( $result );


    /*---------------------------------------------
     *
     * Time/date
     *
     */

    // Format ACF date field

    if (!empty($parameters['acf_date'])) {
      if ( function_exists('get_field') ) {
        $result = get_field( $parameters['field'], $post_id = false, $format_value = false );
      }
    }

    if (!empty($parameters['timestamp']) && ($parameters['timestamp']=='ms') ) {
      $result = $result / 1000;
    }

    if ( !empty($parameters['date_format']) && !empty($parameters['field'])
      && ($parameters['field']!='date') && ($parameters['field']!='modified') ) {

      // Date format for custom field

      if ( !empty($parameters['in']) && ($parameters['in']=="timestamp") ) {
        // Check if it's really a timestamp
        if (is_numeric($result)) {
          $result = gmdate("Y-m-d H:i:s", $result);
        }
      }

      if ($parameters['date_format']=='true')
        $parameters['date_format'] = get_option('date_format');


      $result = mysql2date($parameters['date_format'], $result);

    }


    /*---------------------------------------------
     *
     * Trim by words or characters
     *
     */

    if (!empty($parameters['words'])) {

      if ($parameters['dots']=='false') {
        $parameters['dots'] = false;
      } elseif ($parameters['dots']=='true') {
        $parameters['dots'] = '&hellip;';
      }

      if (intval($parameters['words']) < 0) {

        // Remove X words from beginning and return the rest

        // If format, do it before content gets trimmed
        if ($parameters['format'] == 'true') {
          $whole_result = self::wp_trim_words_retain_formatting( $result, 9999, '' );
          $result = self::wp_trim_words_retain_formatting(
            wpautop( $result ), 0 - $parameters['words'], ''
          );
        } else {
          $whole_result = wp_trim_words( $result, 9999, '' );
          $result = wp_trim_words( $result, 0 - $parameters['words'], '' );
        }
        // Offset and get the rest
        $result = substr($whole_result, strlen($result));

      } else {
        // If format, do it before content gets trimmed
        if ($parameters['format'] == 'true') {
          $result = self::wp_trim_words_retain_formatting(
            wpautop( $result ), $parameters['words'], $parameters['dots']
          );
        } else {
          $result = wp_trim_words( $result, $parameters['words'], $parameters['dots'] );
        }
      }

    }

    if (!empty($parameters['length'])) {

      $result = strip_tags(strip_shortcodes($result)); //Strips tags and images

      // Support multi-byte character code
      $result = mb_substr($result, 0, $parameters['length'], 'UTF-8');
    }


    /*---------------------------------------------
     *
     * Escape HTML and shortcodes
     *
     */

    if ( $parameters['escape'] == 'true' ) {
      $result = str_replace(array('[',']'), array('&#91;','&#93;'), esc_html($result));
    }


    /*---------------------------------------------
     *
     * Wrap in link
     *
     */

    $post_id = isset(self::$state['current_post_id']) ? self::$state['current_post_id'] : get_the_ID();

    switch ($parameters['field']) {

      case "edit-link":

        $url = isset(self::$state['current_link_url']) ?
          self::$state['current_link_url'] : get_edit_post_link( $post_id );

        $result = '<a target="_blank" href="' . $url . '">' . $result . '</a>';

      break;

      case "edit-link-self":

        $url = isset(self::$state['current_link_url']) ?
          self::$state['current_link_url'] : get_edit_post_link( $post_id );

        $result = '<a href="' . $url . '">' . $result . '</a>';

      break;

      case "image-link":        // Link image to post
      case "thumbnail-link":      // Link thumbnail to post
      case "title-link":        // Link title to post

        $url = isset(self::$state['current_link_url']) ?
          self::$state['current_link_url'] : post_permalink( $post_id );

        $result = '<a href="' . $url . '">' . $result . '</a>';

      break;

      case "image-post-link-out":   // Link image to post
      case "thumbnail-post-link-out": // Link thumbnail to post
      case "title-link-out":      // Open link in new tab

        $url = isset(self::$state['current_link_url']) ?
          self::$state['current_link_url'] : post_permalink( $post_id );

        $result = '<a target="_blank" href="' . $url . '">' . $result . '</a>';

      break;

      case "image-link-self":
      case "thumbnail-link-self": // Link to image attachment page

        $url = isset(self::$state['current_link_url']) ?
          self::$state['current_link_url'] :
          get_attachment_link( get_post_thumbnail_id( $post_id ) );

        $result = '<a href="' . $url . '">' . $result . '</a>';

      break;

    }

    // Class

    if (!empty($parameters['class']))
      $result = '<div class="' . $parameters['class'] . '">' . $result . '</div>';

    // Shortcode

    if ( $parameters['field'] != 'debug' && $parameters['shortcode'] != 'false' ) {    // Shortcode
      $result = do_shortcode( $result );
    }

    if ($parameters['http'] == 'true') {         // Add "http://" for links

      if ( substr($result, 0, 4) !== 'http' )
        $result = 'http://'.$result;
    }


    // Auto-embed links

    if ($parameters['embed'] == 'true') {         // Then auto-embed

      if (isset($GLOBALS['wp_embed'])) {
        $wp_embed = $GLOBALS['wp_embed'];
        $result = $wp_embed->autoembed($result);

        // Run [audio], [video] in embed
        $result = do_shortcode( $result );
      }
    }

    // Then the_content filter or format

    if ($parameters['filter']=='true') {

      // Attempt to support SiteOrigin Page Builder
      add_filter( 'siteorigin_panels_filter_content_enabled',
        array($this, 'siteorigin_support') );

      $result = apply_filters( 'the_content', $result );

      // And clean up
      remove_filter( 'siteorigin_panels_filter_content_enabled',
        array($this, 'siteorigin_support') );

    } elseif ($parameters['format'] == 'true' && empty($parameters['words'])) {
      $result = wpautop( $result );
    }

    if ($parameters['nl']=='true') {
      $result = trim(preg_replace('/\s+/', ' ', $result));
    }

    /*---------------------------------------------
     *
     * Read more tag
     *
     */

    if (!empty($parameters['more'])) {

      $until_pos = strpos($result, '<!--more-->');
      if ($until_pos!==false) {
        $result = substr($result, 0, $until_pos); // Get content until tag
      } elseif (empty($parameters['field'])) {

        // If post content has no read-more tag, trim it

        if (empty($parameters['words']) && empty($parameters['length'])) {
          // It hasn't been trimmed yet
          if (!empty($parameters['dots'])) {
            if ($parameters['dots']=='false')
              $parameters['dots'] = false;
            elseif ($parameters['dots']=='true')
              $parameters['dots'] = '&hellip;'; // default

            $result = wp_trim_words( $result, 25, $parameters['dots'] );
          }
          else
            $result = wp_trim_words( $result, 25 );
        }
      }

      if ($parameters['more']=='true') {
        $more = 'Read more';
      } else {
        $more = $parameters['more'];
      }

      if ($more!='none') {

        if ($parameters['link'] == 'false') {

          $result .= $more;

        } else {
          if (empty($parameters['between']))
            $result .= '<br>';
          elseif ($parameters['between']!='false')
            $result .= $parameters['between'];

          $result .= '<a class="more-tag" href="'. get_permalink($post_id) . '">'
            . $more . '</a>';
        }
      }
    }

    return $result;
  }


  /*---------------------------------------------
   *
   * Field
   *
   */

  public static function get_the_field( $parameters, $id = null ) {

    $field = $parameters['field'];
    $result = '';

    /*---------------------------------------------
     *
     * Attachment field
     *
     */

    if ( (!empty($parameters['type']) && $parameters['type']=='attachment') ||
      CCS_Loop::$state['is_attachment_loop'] ||
      CCS_Attached::$state['is_attachment_loop'] ) {

      return self::get_the_attachment_field( $parameters );

    } elseif ( self::$state['is_array_field'] ) {

      // Array field

      $array = self::$state['current_field_value'];

      if (isset( $array[$field] ) ) {
        return $array[$field];
      }

    } elseif ( class_exists('CCS_To_ACF') &&
        CCS_To_ACF::$state['is_repeater_or_flex_loop']=='true' ) {

      /*---------------------------------------------
       *
       * Repeater or flexible content loop
       *
       */

      // If not inside relationship loop
      if ( CCS_To_ACF::$state['is_relationship_loop']!='true' ) {

        // Get sub field
        if (function_exists('get_sub_field')) {
          return get_sub_field( $field );
        } else return null;
      }

    }

    if ( !empty($id) ) {

      // Get the post

      $post_id = $id;
      $post = get_post($post_id);

    } else {

      // In a loop

      $post = self::$state['current_post'];
      $post_id = self::$state['current_post_id'];
    }

    if (empty($post)) return null; // No post

    /*---------------------------------------------
     *
     * Prepare image attributes
     *
     */

    $image_fields = array('image','image-full','image-link','image-link-self',
      'thumbnail','thumbnail-link','thumbnail-link-self','gallery');

    if ( $field=='thumbnail' && empty($parameters['size']) ) {
      $parameters['size'] = 'thumbnail'; // Default thumbnail
    }

    $attr = array();

    if (in_array($field, $image_fields)) {

      if (!empty($parameters['width']) || !empty($parameters['height']))
        $parameters['size'] = array((int)$parameters['width'], (int)$parameters['height']);
      if (!empty($parameters['image_class']))
        $attr['class'] = $parameters['image_class'];
      if (!empty($parameters['nopin']))
        $attr['nopin'] = $parameters['nopin'];
      if (!empty($parameters['alt']))
        $attr['alt'] = $parameters['alt'];
      if (!empty($parameters['title']))
        $attr['title'] = $parameters['title'];
    }


    /*---------------------------------------------
     *
     * Pre-defined fields
     *
     */

    switch ($field) {

      case 'id': $result = $post_id; break;
      case 'url': $result = post_permalink( $post_id ); break;
      case 'edit-url': $result = get_edit_post_link( $post_id ); break;
      case 'edit-link':
        $result = $post->post_title; break;
      case 'edit-link-self':
        $result = $post->post_title; break;
      case 'slug': $result = $post->post_name; break;
      case 'post-type': $result = $post->post_type; break;
      case 'post-type-name': $post_type = $post->post_type;
                             $obj = get_post_type_object( $post_type );
                             $result = $obj->labels->singular_name; break;
      case 'post-type-plural': $post_type = $post->post_type;
                         $obj = get_post_type_object( $post_type );
                         $result = $obj->labels->name; break;
      case 'post-status':
        $result = $post->post_status;
        if ($parameters['out'] !== 'slug') {
          $result = ucwords($result);
        }
        break;

      case 'title-link':
      case 'title-link-out':
      case 'title': $result = $post->post_title; break;

      case 'author':

        $author_id = $post->post_author;
        $user = get_user_by( 'id', $author_id);

        if ( !empty($parameters['meta']) )
          $result = get_the_author_meta( $parameters['meta'], $author_id );
        else
          $result = $user->display_name;
        break;

      case 'author-id':

        $result = $post->post_author; break;

      case 'author-url':

        $result = get_author_posts_url($post->post_author); break;

      case 'avatar':
        if( !empty($parameters['size']) )
          $result = get_avatar($post->post_author, $parameters['size']);
        else
          $result = get_avatar($post->post_author);
      break;

      case 'date':

        if (!empty($parameters['date_format'])) {
          $result = mysql2date($parameters['date_format'], $post->post_date);
        }
        else { // Default date format under Settings -> General
          $result = mysql2date(get_option('date_format'), $post->post_date);
        }
      break;

      case 'modified':

        if (!empty($parameters['date_format'])) {
          $result = get_post_modified_time( $parameters['date_format'], $gmt=false, $post_id, $translate=true );
        }
        else { // Default date format under Settings -> General
          $result = get_post_modified_time( get_option('date_format'), $gmt=false, $post_id, $translate=true );
        }
      break;

      case 'image-full':
        $parameters['size'] = 'full';
      case 'image':       // image
      case 'image-link':      // image with link to post
      case 'image-link-self':   // image with link to attachment page
        $parameters['size'] = (isset($parameters['size']) && !empty($parameters['size'])) ?
          $parameters['size'] : 'full';
        $result = get_the_post_thumbnail( $post_id, $parameters['size'], $attr );
        break;

      case 'image-url':
        $parameters['size'] = (isset($parameters['size']) && !empty($parameters['size'])) ?
          $parameters['size'] : 'full';
        $src = wp_get_attachment_image_src(
          get_post_thumbnail_id($post_id),
          $parameters['size']
        );
        $result = $src['0'];
        // $result = wp_get_attachment_url(get_post_thumbnail_id($post_id));
      break;

      case 'image-title':
      case 'image-caption':
      case 'image-alt':
      case 'image-description':
        $image_field_name = substr($field, 6); // Remove "image-"
        $result = self::wp_get_featured_image_field( $post_id, $image_field_name );
      break;

      case 'thumbnail':     // thumbnail
      case 'thumbnail-link':    // thumbnail with link to post
      case 'thumbnail-link-self': // thumbnail with link to attachment page
        $parameters['size'] = (isset($parameters['size']) && !empty($parameters['size'])) ?
          $parameters['size'] : 'thumbnail';

        $result = get_the_post_thumbnail( $post_id, $parameters['size'], $attr );
        break;

      case 'thumbnail-url':
        $src = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'thumbnail' );
        $result = $src['0'];
        break;

      case 'tags':
        $result = implode(' ', wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ) );
        break;

      case 'gallery' :

        // Get specific image from gallery field

        if (class_exists('CCS_Gallery_Field')) { // Check if gallery field is enabled

          $attachment_ids = CCS_Gallery_Field::get_image_ids( $post_id );

          if (empty($parameters['num']))
            $parameters['num'] = 1;
          if (empty($parameters['size']))
            $parameters['size'] = 'full';

          $result = wp_get_attachment_image( $attachment_ids[$parameters['num']-1], $parameters['size'], $icon=false, $attr );
        }

        break;

      case 'excerpt' :

        // Get excerpt

//        $result = get_the_excerpt();
        $result = $post->post_excerpt;

        if( empty($result) ) { // If empty, get it from post content
          $result = $post->post_content;
          if (empty($parameters['words']) && empty($parameters['length'])) {
            self::$parameters['words'] = 25;
          }
        }
        break;

      case 'debug' :
        ob_start();
        echo '<pre>'; print_r( get_post_custom($post_id) ); echo '</pre>';
        if (function_exists('acf_get_fields_by_id')) {
          echo '<pre>'; print_r( acf_get_fields_by_id($post_id) ); echo '</pre>';
        }
        $result = ob_get_clean();
        break;

      default :

        /*---------------------------------------------
         *
         * Custom field
         *
         */

        $result = get_post_meta($post_id, $field, true);

        if ( is_numeric($result) && !empty($parameters['return']) ) {

          if ($parameters['return']=='page-link') {

            // ACF page link: get URL from post ID
            $result = get_permalink( $result );

          } else {

            // Get attachment field

            $parameters['id'] = $result;
            $parameters['field'] = $parameters['return'];

            $result = self::get_the_attachment_field($parameters);
          }

        } elseif (!empty($parameters['property']) && is_object($result) ) {

          $result = self::get_object_property($result, $parameters['property']);

        } elseif (
          !empty($parameters['currency']) ||
          !empty($parameters['decimals']) ||
          !empty($parameters['point']) ||
          !empty($parameters['thousands'])) {

          $currency = !empty($parameters['currency']) ? $parameters['currency'] : '';
          $decimals = !empty($parameters['decimals']) ? $parameters['decimals'] : 2;
          $point = !empty($parameters['point']) ? $parameters['point'] : '.';
          $thousands = !empty($parameters['thousands']) ? $parameters['thousands'] : ',';

          $result = CCS_Format::getCurrency($result,
            $currency, $decimals, $point, $thousands);
        }


        break;
    }

    return $result;

  } // End get_the_field



  /*---------------------------------------------
   *
   * Attachment field
   *
   */

  public static function get_the_attachment_field( $parameters ) {

    if (!empty($parameters['id'])) {
      $post_id = $parameters['id'];
    } elseif (CCS_Loop::$state['is_attachment_loop']) {
      $post_id = CCS_Loop::$state['current_post_id'];
    } elseif (CCS_Attached::$state['is_attachment_loop']) {
      $post_id = CCS_Attached::$state['current_attachment_id'];
    }

    if (empty($post_id)) return; // Needs attachment ID

    $post = get_post($post_id);

    if (empty($parameters['size'])) {
      $parameters['size'] = 'full';
    }

    $field = $parameters['field'];
    $result = '';


    /*---------------------------------------------
     *
     * Prepare image attributes
     *
     * @todo *** Refactor ***
     *
     */

    $image_fields = array('image','thumbnail');

    $attr = array();

    if (in_array($field, $image_fields)) {
      if (!empty($parameters['width']) && !empty($parameters['height']))
        $parameters['size'] = array($parameters['width'], $parameters['height']);
      if (!empty($parameters['image_class']))
        $attr['class'] = $parameters['image_class'];
      if (!empty($parameters['nopin']))
        $attr['nopin'] = $parameters['nopin'];
      if (!empty($parameters['alt']))
        $attr['alt'] = $parameters['alt'];
      if (!empty($parameters['title']))
        $attr['title'] = $parameters['title'];
    }

    switch ($field) {
      case 'id':
        $result = $post_id;
        break;
      case 'alt':
        $result = get_post_meta( $post_id, '_wp_attachment_image_alt', true );
        break;
      case 'caption' :
        $result = $post->post_excerpt;
        break;
      case 'description' :
        $result = $post->post_content;
        break;
      case 'url' :
      case 'download-url' :
        $src = wp_get_attachment_image_src( $post_id, $parameters['size'] );
        if (isset($src[0]) && !empty($src[0])) {
          $result = $src[0];
        } else {
          $result = wp_get_attachment_url( $post_id );
        }
        break;
      case 'download-link' :
        $target = '';
        if ( $parameters['new'] == 'true' ) {
          $target = 'target="_blank" ';
        }
        $result = '<a '.$target.'href="'.wp_get_attachment_url( $post_id ).'" download>'.$post->post_title.'</a>';
        break;
      case 'page-url' :
      case 'href' : $result = get_permalink( $post_id );
        break;
      case 'src' : $result = $post->guid;
        break;
      case 'title' : $result = $post->post_title;
        break;
      case 'title-link' :
      case 'title-link-out' :
        $src = wp_get_attachment_image_src( $post_id, $parameters['size'] );
        if (isset($src[0]) && !empty($src[0])) {
          $result = $src[0];
        } else {
          $result = wp_get_attachment_url( $post_id );
        }
        self::$state['current_link_url'] = $result;
        $result = $post->post_title;
      break;
      case 'image' :
        $result = wp_get_attachment_image(
          $post_id, $parameters['size'], $icon = false, $attr
        );
        break;
      case 'image-url' :
        $src = wp_get_attachment_image_src( $post_id, $parameters['size'] );
        if (isset($src[0]) && !empty($src[0])) {
          $result = $src[0];
        } else {
          $result = wp_get_attachment_url( $post_id );
        }
        break;
      case 'thumbnail' :
        $result = wp_get_attachment_image(
          $post_id, 'thumbnail', $icon = false, $attr
        );
        break;
      case 'thumbnail-url' : $result = wp_get_attachment_thumb_url( $post_id ) ;
        break;
      default:
        break;
    }

    return $result;
  }




  /*---------------------------------------------
   *
   * Image field
   *
   */

  function get_image_field( $parameters ) {

    $result = '';

    $post_id = self::$state['current_post_id'];

    if (class_exists('CCS_To_ACF') && CCS_To_ACF::$state['is_repeater_or_flex_loop']=='true') {

      // Repeater or flexible content field: then get sub field

      if (function_exists('get_sub_field')) {
        $field = get_sub_field( $parameters['image'] );
      } else return null;
    } else {
      $field = get_post_meta( $post_id, $parameters['image'], true );
    }

    /*---------------------------------------------
     *
     * Prepare image attributes
     *
     * @todo Refactor
     *
     */

    $attr = array();
    if (!empty($parameters['width']) || !empty($parameters['height']))
      $parameters['size'] = array($parameters['width'], $parameters['height']);
    if (!empty($parameters['image_class']))
      $attr['class'] = $parameters['image_class'];
    if (!empty($parameters['nopin']))
      $attr['nopin'] = $parameters['nopin'];
    if (!empty($parameters['alt']))
      $attr['alt'] = $parameters['alt'];
    if (!empty($parameters['title']))
      $attr['title'] = $parameters['title'];

    switch($parameters['in']) {

      case 'array' :
      case 'object' : // ACF image object

        if (is_array( $field )) {
          $image_id = $field['id'];
        } else {
          $image_id = $field; // Assume it's ID
        }

        $result = wp_get_attachment_image( $image_id , $parameters['size'], $icon=false, $attr );

        break;

      case 'url' :

        if ( $parameters['return']=='url' ) {

          $result = $field;

        } else {

          $result = '<img src="' . $field . '"';
          if (!empty($parameters['image_class']))
            $result .= ' class="' . $parameters['image_class'] . '"';
          if (!empty($parameters['alt']))
            $result .= ' alt="' . $parameters['alt'] . '"';
          if (!empty($parameters['height']))
            $result .= ' height="' . $parameters['height'] . '"';
          if (!empty($parameters['width']))
            $result .= ' width="' . $parameters['width'] . '"';
          if (!empty($parameters['nopin']))
            $result .= ' nopin="' . $parameters['nopin'] . '"';
          $result .= '>';
        }
        break;
      case 'id' : // Default is attachment ID for the image
      default :

        if (is_array($field)) {
          $image_id = $field['id']; // If it's an array, assume image object
        } else {
          $image_id = $field;
        }
        $result = wp_get_attachment_image( $image_id, $parameters['size'], $icon=false, $attr );
        break;
    }

    if ($parameters['return']=='url') {

      $image_info = wp_get_attachment_image_src( $image_id, 'full' );
      return isset($image_info) ? $image_info[0] : null;

    } else {

      if (!empty($parameters['class'])) {
        $result = '<div class="' . $parameters['class'] . '">' . $result . '</div>';
      }

      return $result;
    }

  }


  /*---------------------------------------------
   *
   * Taxonomy field
   *
   */

  public static function get_the_taxonomy_field(
    $taxonomy, $term_id, $field, $parameters = array() ) {

    $value = '';

    // ACF
    if (function_exists('get_field')) {

      $value = get_field( $field, $taxonomy.'_'.$term_id );

      if (!isset($parameters['in'])) $parameters['in']='object';
      if (is_array($value)) {
        // Assume image..?
        $parameters['image'] = $field;
      }


    // Which plugin defines get_tax_meta..?
    } elseif (function_exists('get_tax_meta')) {

      $value = get_tax_meta( $term_id, $field );

      if (!isset($parameters['in'])) $parameters['in']='id';
    }

    // Image field
    if ( !empty($parameters['image']) ) {

      if ( empty($parameters['size']) ) $parameters['size']='full';

      switch($parameters['in']) {
        case 'id' :
          $parameters['id'] = $value;
          $value = wp_get_attachment_image( $value, $parameters['size'] ); break;
        case 'url' : $value = '<img src="' . $value . '">'; break;
        case 'object' : /* image object */
        default :
          if (is_array($value)) {

            $parameters['id'] = $value['id'];
            $value = wp_get_attachment_image( $value['id'], $parameters['size'] );
          } else {
            $value = wp_get_attachment_image( $value, $parameters['size'] ); // Assume it's ID
          }
      }

      if ( !empty($parameters['out']) && !empty($parameters['id'])) {

        $parameters['field'] = $parameters['out'];
        $value = self::get_the_attachment_field( $parameters );
      }
    }

    return $value;
  }





/*---------------------------------------------
 *
 * Other shortcodes
 *
 */


  /*---------------------------------------------
   *
   * [field]
   *
   */

  public static function field_shortcode($atts) {

    $out = null; $rest='';

    if (!isset($atts)) return;

    if (!empty($atts['image'])) {
      $field_param = 'image="'.$atts['image'].'"';
    } elseif (!empty($atts['link'])) {
      $field_param = 'link="'.$atts['link'].'"';
    } elseif (!empty($atts['acf_date'])) {
      $field_param = 'acf_date="'.$atts['acf_date'].'"';
    } elseif (!empty($atts[0])) {
      $field_param = 'field="'.$atts[0].'"';
    } else return;

    if (count($atts)>1) { // Combine additional parameters
      $i=0;
      foreach ($atts as $key => $value) {
        $rest .= ' ';
        if ($i>0) $rest .= $key.'="'.$value.'"'; // Skip the first parameter
        $i++;
      }
    }

    // Pass it to [content]
    $out = do_shortcode('[content '.$field_param.$rest.']');

    return $out;
  }


  /*---------------------------------------------
   *
   * [taxonomy]
   *
   */

  public static function taxonomy_shortcode($atts) {
    $out = null; $rest='';
    if (isset($atts) && !empty($atts[0])) {

      if (count($atts)>1) {
        $i=0; $rest='';
        foreach ($atts as $key => $value) {
          $rest .= ' ';
          if ($i>0) $rest .= $key.'="'.$value.'"';
          $i++;
        }
      }
      $out = do_shortcode('[content taxonomy="'.$atts[0].'"'.$rest.']');
    }
    return $out;
  }


  /*---------------------------------------------
   *
   * [array]
   *
   */

  public static function array_field_shortcode( $atts, $content ) {

    $out = null;
    $array = null;

    extract( shortcode_atts( array(
      'each'  => 'false', // Loop through each array
      'debug' => 'false', // Print array for debug purpose
      'global' => ''
    ), $atts ) );

    if (!empty($global)) $atts[0] = 'GLOBAL';

    if ( isset($atts) && !empty($atts[0]) ) {

      $field = $atts[0];

      if ( class_exists('CCS_To_ACF') &&
        CCS_To_ACF::$state['is_repeater_or_flex_loop']=='true' &&
        $field != 'GLOBAL'
        ) {
//        && CCS_To_ACF::$state['is_relationship_loop']!='true' ) {

        // Inside ACF repeater/flex

        // Get sub field
        if (function_exists('get_sub_field'))
          $array = get_sub_field( $field );

      } else {

        if ( $field == 'GLOBAL' ) {
          $array = $GLOBALS[$global];
          if (!is_array($array)) {
            $array = array('value'=>$array);
          }

        } else {
          // Normal field
          $array = get_post_meta( get_the_ID(), $field, true );
        }

        // IF value is not array
        if ( !empty($array) && !is_array($array)) {
          // See if it's an ACF field
          if (function_exists('get_field')) {
            $array = get_field( $field );
          }
        }
      }

      if ( $debug!='false') {
        $out = self::print_array($array,false);
      }

      if ( !empty($array) && is_array($array) ) {

        self::$state['is_array_field'] = true;

        if ( $each != 'true' ) {
          $array = array($array); // Create a single array
        }

        foreach ( $array as $each_array ) {

          self::$state['current_field_value'] = $each_array;
          $out .= do_shortcode( $content );
        }

        self::$state['is_array_field'] = false;

      } else {

        $out = $array; // Empty or not array
      }

    }
    return $out;
  }


  /*---------------------------------------------
   *
   * Utilities
   *
   */

  public static function wp_get_attachment_array( $attachment_id ) {

    $attachment = get_post( $attachment_id );
    return array(
      'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
      'caption' => $attachment->post_excerpt,
      'description' => $attachment->post_content,
      'href' => get_permalink( $attachment->ID ),
      'src' => $attachment->guid,
      'title' => $attachment->post_title
    );
  }

  public static function wp_get_attachment_field( $attachment_id, $field_name ) {

    if (empty($attachment_id)) return null;

    $attachment = get_post( $attachment_id );
    $attachment_array = self::wp_get_attachment_array( $attachment_id );

    if (isset($attachment_array[$field_name])) {
      return $attachment_array[$field_name];
    } else {
      return null;
    }
  }

  public static function wp_get_featured_image_field( $post_id, $field_name ) {

    // Get featured image ID from post ID
    $attachment_id = get_post_thumbnail_id( $post_id );
    return self::wp_get_attachment_field( $attachment_id, $field_name );
  }




  // Helper for getting property from field object
  public static function get_object_property($object, $prop_string, $delimiter = '->') {
    $prop_array = explode($delimiter, $prop_string);
    foreach ($prop_array as $property) {
      if (isset($object->{$property}))
        $object = $object->{$property};
      else
        return;
    }
    return $object;
  }

  // Helper for getting field including predefined
  public static function get_prepared_field( $field, $id = null ) {

    if (empty($id)) $id = get_the_ID();
    return self::get_the_field( array('field' => $field), $id );
  }


  // For debug purpose: Print an array in a human-readable format
  public static function print_array( $array, $echo = true ) {

    if ( !$echo ) ob_start();
    echo '<pre>';
      print_r( $array );
    echo '</pre>';
    if ( !$echo ) return ob_get_clean();
  }


  /*---------------------------------------------
   *
   * Get all shortcode attributes including empty
   *
   * [shortcode param]
   * [shortcode param="value"]
   *
   */

  public static function get_all_atts( $atts ) {
    $new_atts = array();
    if (is_array($atts) && count($atts)>0) {
      foreach ($atts as $key => $value) {
        if (is_numeric($key)) {
          $new_atts[$value] = true;
        } else {
          $new_atts[$key] = $value;
        }
      }
    }
    return $new_atts;
  }



  /*---------------------------------------------
   *
   * Support qTranslate Plus
   *
   */

  public static function check_translation( $text ) {

    if ( !isset(self::$state['ppqtrans_exists']) ) {
      // Check only once and store result
      self::$state['ppqtrans_exists'] = function_exists('ppqtrans_use');
    }

    if ( self::$state['ppqtrans_exists'] ) {
      global $q_config;
      return ppqtrans_use($q_config['language'], $text, false);
    }

    return $text;
  }


  public static function siteorigin_support() { return true; }


  public static function wp_trim_words_retain_formatting(
    $text, $num_words = 55, $more = null ) {

    if ( null === $more )
        $more = __( '&hellip;' );
    $original_text = $text;
    /* translators: If your word count is based on single characters (East Asian characters),
       enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
    if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
        $text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
        preg_match_all( '/./u', $text, $words_array );
        $words_array = array_slice( $words_array[0], 0, $num_words + 1 );
        $sep = '';
    } else {
        $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
        $sep = ' ';
    }
    if ( count( $words_array ) > $num_words ) {
        array_pop( $words_array );
        $text = implode( $sep, $words_array );
        $text = $text . $more;
    } else {
        $text = implode( $sep, $words_array );
    }
    /**
     * Filter the text content after words have been trimmed.
     *
     * @since 3.3.0
     *
     * @param string $text          The trimmed text.
     * @param int    $num_words     The number of words to trim the text to. Default 5.
     * @param string $more          An optional string to append to the end of the trimmed text, e.g. &hellip;.
     * @param string $original_text The text before it was trimmed.
     */
    return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
  }

} // End CCS_Content
