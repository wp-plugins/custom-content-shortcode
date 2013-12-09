<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: http://wordpress.org/plugins/custom-content-shortcode/
Description: Display posts, pages, custom post types, custom fields, files, images, comments, attachments, menus, or widget areas
Version: 0.4.3
Author: Eliot Akira
Author URI: eliotakira.com
License: GPL2
*/



$ccs_global_variable = array(
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


include (dirname(__FILE__).'/ccs-content.php');		// Content shortcode
include (dirname(__FILE__).'/ccs-loop.php');			// Loop shortcode
include (dirname(__FILE__).'/ccs-gallery.php');		// Simple gallery
include (dirname(__FILE__).'/ccs-bootstrap.php');		// Bootstrap support
include (dirname(__FILE__).'/ccs-field-loader.php');	// Load HTML, CSS, JS fields
include (dirname(__FILE__).'/ccs-acf.php');			// Advanced Custom Fields support
include (dirname(__FILE__).'/ccs-user.php');			// Miscellaneous user shortcodes
include (dirname(__FILE__).'/ccs-docs.php');			// Documentation under Settings -> Content Shortcodes


