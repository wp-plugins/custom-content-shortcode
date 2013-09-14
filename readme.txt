=== Custom Content Shortcode ===
Tags: custom post type, custom field, shortcode, query, loop
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a shortcode to get content or field from any post type

== Description ==

This plugin adds a shortcode to get the content or a field from any post type.

There is also a shortcode to perform query loops, with which you can create layouts for displaying the content and fields of a specific post type, category, etc.

= Basic examples =  
<br />
*Display post content by name*

	[content type="post" name="hello-world"]

*Display the featured image of a page*

	[content type="page" name="about-me" field="image"]

*Display a custom field from a custom post type*

	[content type="apartment" name="lux-suite-22" field="rent-per-day"]

*Display fields from the current post*

	The Article <b>[content field="title"]</b> by [content field="author"] was written on [content field="date"].


= Available parameters =  
<br />
Here are the available parameters for the *content* shortcode.

* **type** - which post type to target (post / page / custom post type) - if empty, the default is "page"
 
* **name** or **id** - which entry to target by its ID or name (slug not post title) - if empty, the default is the current post

* **field** - which field to get - if empty, default is the main content of the post

You can display custom fields you created, as well as predefined fields: *title*, *id*, *author, date*, *url*, *image*, *image-url*, *thumbnail*, and *excerpt*.


= Query loops =  
<br />
There is also a shortcode to perform query loops.

*Display all posts*

	[loop type="post"]
	  [content]
	[/loop]

Notice that inside a query loop, the *content* shortcode does not need *type* and *name* parameters, because it is targeting the current post in the loop.

*Display fields from all posts of a custom post type by category*

	[loop type="apartment" category="suite"]
	  Apartment: [content field="title"]
	  Rent per day: [content field="rent-per-day"]
	[/loop]

Available parameters for the *loop* shortcode are:

 * **type** - which post type to query (post / page / custom post type) - if empty, the default is "page"
 
 * **category** - display posts from a category

 * **count** - number of posts to show

 * **tag** - display posts with a specific tag (for multiple tags: *tags="apples, green"*)

In addition, you can use parameters of the WP_Query class, such as *author_name* and *order*. Custom field and taxonomy parameters are not yet supported.


= Custom content layout =  
<br />
Here is an example of how the *loop* and *content* shortcodes can be used to create layout templates.

1. Let's imagine a bicycle shop.  We create a custom post type called *bicycle*, and add custom fields such as *model*, *price*, and *description*.
1. Add all bicycles as new entries, with featured image and other info in the fields.
1. Create a new page to display the bicycles. A basic layout could be:

		[loop type="bicycle"]
			[content field="image"]
			Model: [content field="model"]
		    Price: [content field="price"]
		    Description: [content field="description"]
		[/loop]
1. Another page could display a list of bicycles of the *freestyle* category:

		Freestyle bikes available:

		<ul>
			[query post_type="bicycle" category="freestyle"]
				<li>[content field="model"] - [content field="price"]</li>
			[/query]
		</ul>


= Custom content management =  
<br />
Here are some plugins that work well together for custom content management.

 * **Custom Post Type UI** - easily create and manage custom post types and taxonomies
 * **Advanced Custom Fields** - create and manage all kinds of useful custom field types. *Note: Some advanced fields types (such as **gallery**) are not yet supported by the Custom Content Shortcode.*
 * **Admin Menu Editor** - essential for customizing the admin menu, especially for client use. For example, you can move the edit menu for the Product post type near the top of the menu for easier access; hide menu items for unnecessary or sensitive settings; arrange and simplify the admin menu; and so on.
 * **Intuitive Custom Post Order** - change the order of post/page/custom post types by drag-and-drop
 * **Post Type Converter** - convert a post from one post type to another
 * **Codepress Admin Columns** - customize the overview pages for post/page/custom post types, by showing/hiding custom fields as columns. I wish it could do sortable columns so custom post types are easier to organize. Perhaps another plugin is more fully featured?
 * **Duplicate Post** - useful for making similar post items, backup posts, etc.


= Features to be implemented =  
<br />
Additional query parameters for the *loop* shortcode:

* custom field and taxonomy parameters
* advanced fields, such as *gallery* and other arrays
* galleries in the media library

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

= 0.1.4 =

* Added shortcode for query loops
* Format post content using the_content filter

= 0.1.3 =

* Changed shortcode to [content]
* Added banner image to Wordpress plugin page

= 0.1.2 =

* Better documentation

= 0.1.1 =
* Simplified code, added a few parameters

= 0.1 =
* First release

== Upgrade Notice ==


