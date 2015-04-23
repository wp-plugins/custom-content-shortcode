<?php

/*---------------------------------------------
 *
 * Attached shortcode
 *
 */

new CCS_Attached;

class CCS_Attached {

	public static $state;

	function __construct() {

		add_shortcode( 'attached', array( $this, 'attached_shortcode' ) );
		self::$state['is_attachment_loop'] = false;
	}

	function attached_shortcode($atts, $content) {

		$args = array(
			'orderby' => '',
			'order' => '',
			'category' => '',
			'count' => '',
			'offset' => '',
			'trim' => '',
			'columns' => '', 'pad' => '', 'between' => ''
		);
		extract( shortcode_atts( $args , $atts, true ) );		

		/*---------------------------------------------
		 *
		 * Get attachments
		 *
		 */

		$attachment_ids = array();
		$current_id = get_the_ID();

		if ( isset($atts[0]) && ($atts[0]=='gallery') ){

			// Get attachment IDs from gallery field
			$attachment_ids = CCS_Gallery_Field::get_image_ids( $current_id );

      // Support for orderby title
      if ( $orderby=='title' ) {
        usort($attachment_ids, array($this, 'sort_gallery_by_title'));
      }
		} else {

			$attach_args = array (
				'post_parent' => $current_id,
				'post_type' => 'attachment',
				'post_status' => 'any',
				'posts_per_page' => '-1' // Get all attachments
			);

			// default orderby
			$attach_args['orderby'] = empty($orderby) ? 'date' : $orderby;

			// default for titles
			if ( $orderby == 'title' ) $order = empty($order) ? 'ASC' : $order;

			if (!empty($order)) $attach_args['order'] = $order;
			if (!empty($category)) $attach_args['category'] = $category;
			if (!empty($count)) $attach_args['posts_per_page'] = $count;
			if (!empty($offset)) $attach_args['offset'] = $offset;

			// Get attachments for current post

			$posts = get_posts($attach_args);

			$index = 0;
			foreach( $posts as $post ) {
				$attachment_ids[$index] = $post->ID; // Keep it in order
				$index++;
			}
		}

		// If no images in gallery field
		if (count($attachment_ids)==0) return null; 


		/*---------------------------------------------
		 *
		 * Compile template
		 *
		 */

		$out = array();

		self::$state['is_attachment_loop'] = true;

		foreach ( $attachment_ids as $index => $attachment_id ) {

			self::$state['current_attachment_id'] = $attachment_id;
			$out[] = do_shortcode( $content );
		}

		self::$state['is_attachment_loop'] = false;

		/*---------------------------------------------
		 *
		 * Post-process
		 *
		 */

		if (!empty($columns)) {
			$out = CCS_Loop::render_columns( $out, $columns, $pad, $between );
		} else {
			$out = implode('', $out);

			if ( $trim == 'true' ) {
				$out = trim($out, " \t\n\r\0\x0B,");
			}
		}

		return $out;
	}


  public static function sort_gallery_by_title( $a, $b ) {

    $a_title = CCS_Content::wp_get_attachment_field( $a, 'title' );
    $b_title = CCS_Content::wp_get_attachment_field( $b, 'title' );

    return ( $a_title < $b_title ) ? -1 : 1;
  }

}
