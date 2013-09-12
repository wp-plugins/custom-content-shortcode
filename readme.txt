=== Custom content shortcode ===
Tags: custom post type, custom field, shortcode
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.122
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a shortcode to get content or field from any post type

== Description ==

This plugin adds a shortcode to get the content or a field from any post type.

= Basic examples =  
<br />
*Display post content by name*

	[custom type="post" name="hello-world"]

*Display the featured image of a page*

	[custom type="page" name="about-me" field="image"]

*Display a custom field from a custom post type*

	[custom type="apartment" name="lux-suite-22" field="rent-per-day"]

*Display fields from the current post*

	The Article <b>[custom field="title"]</b> by [custom field="author"] was written on [custom field="date"].


= Available parameters =  
<br />
Here are the available parameters.

* **type** - define which post type to target (post / page / custom post type) - if empty, the default is "page"
 
* **name** or **id** - define which entry to target by its ID or name (slug not post title) - if empty, the default is the current post

* **field** - define which field to get - if empty, default is the main content of the post

You can display any custom field you create, as well as predefined fields: *title*, *id*, *author, date*, *url*, *image*, *image-url*, *thumbnail*, and *excerpt*.


= Custom content layout =  
<br />
Here is an example of how this shortcode can be used to create a layout template.

1. Let's imagine a bicycle shop.  We create a custom post type called **bicycle**, and add custom fields such as **model**, **price**, and **description**.
1. A bicycle is added as a new entry, with a featured image and other info fields.
1. For the content, we create a basic template to display the custom fields:

		<div class="item-wrap">
			Model: [custom field="model"]
			<div class="image-wrap">
				[custom field="image"]
			</div>
			Price: [custom field="price"]
			Description: [custom field="description"]
		</div>

1. The same template can be used for all **bicycle** entries. We can copy & paste, or use a handy plugin called Duplicate Post for all new entries, and just edit the info fields.
1. We can show each product on its own page (www.example-site.com/bicycle/product-name) or several on a page:

		[custom type="bicycle" name="bmx-super-22"]
		[custom type="bicycle" name="mongoose-rad-fx"]
		[custom type="bicycle" name="freestyle-B5"]


= Custom content admin =  
<br />
Here are some plugins that work well together for custom content management.

 * **Custom Post Type UI** - easily create and manage custom post types and taxonomies
 * **Advanced Custom Fields** - create and manage all kinds of useful custom field types. *Actually, I need to work on my shortcode to be able to display these advanced field types also.*
 * **Admin Menu Editor** - essential for customizing the admin menu, especially for client use. For example, you can move the edit menu for the Product post type near the top of the menu for easier access; hide menu items for unnecessary or sensitive settings; arrange and simplify the admin menu; and so on.
 * **Intuitive Custom Post Order** - change the order of post/page/custom post types by drag-and-drop
 * **Post Type Converter** - convert a post from one post type to another
 * **Codepress Admin Columns** - customize the overview pages for post/page/custom post types, by showing/hiding custom fields as columns. I wish it could do sortable columns so custom post types are easier to organize. Perhaps another plugin is more fully featured?
 * **Duplicate Post** - useful for making similar post items, backup posts, etc.

= Custom content query loops =  
<br />
This feature is not yet integrated into the shortcode, but I'm working on it.

There is an apparently little-known but very powerful plugin called **Query Shortcodes**, that lets you easily create query loops inside a post / page /custom post type.  To make it work with the Custom Content Shortcode, I had to change it a bit to allow shortcodes inside the query loop, as well as pass each post ID.

To come back to the example of the bicycle shop, this would display all bicycles of the category **freestyle**, according to the layout we created for each entry:

	[query post_type="bicycle" category="freestyle"]
		[custom id="{ID}"] /* display the content */
	[/query]

This way you can run query loops for any custom post type, and display the content and fields in any layout.

Possible future applications could include:

 * Display the next five up-coming events
 * Display product types in excerpts or individual pages
 * Display the same group of images in different layouts - carousel, albums, thumbnails, etc.


== Installation ==

1. Upload `custom-content-shortcode.zip` through Plugins->Add New, or extract and upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the shortcode in a post, page, etc.

== Frequently Asked Questions ==

= Any Questions? =

Not yet.

== Screenshots ==

None.

== Changelog ==

= 0.12 =

* Better documentation - longer than the plugin code itself

= 0.11 =
* Simplified code, added a few parameters

= 0.1 =
* First release

== Upgrade Notice ==


