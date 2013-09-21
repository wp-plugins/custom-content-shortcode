=== Custom Content Shortcode ===
Tags: custom post type, custom field, shortcode, query, loop
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A shortcode to display content from posts, pages, custom post types, custom fields, images, menus, widget areas, and attachment files

== Description ==

This plugin adds the shortcode **[content]** to display any of the following content types: posts, pages, custom post types, custom fields, images, menus, widget areas/sidebars, and attachment files.

There is also the shortcode **[loop]** to perform query loops, with which you can create layout templates for displaying the content, for example, the five most recent posts, or products in a category.

In addition, there is an option to enable gallery fields for selected post types, which can be displayed using the *content* and *loop* shortcodes.

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
Here are the basic parameters for the *content* shortcode.

* **type** - which post type to target: *post*, *page*, or *custom post type* - if empty, the default is *any post type*
* **name**, **id**, or **title** - which entry to target: *name/slug*, *ID* or *title* - if empty, default is the current post
* **field** - which field to display - if empty, default is the main content of the post

	You can display custom fields you created, as well as predefined fields: *title*, *id*, *author, date*, *url*, *image*, *image-url*, *thumbnail*, and *excerpt*.

Advanced parameters:

* **menu** - display a menu by *name/slug*, *ID* or *title*
* **area** or **sidebar** - display a widget area/sidebar by *title*
* **class** - add a `<div>` class to the output, for styling purpose
* **header**, **footer** - include a field in the header or footer - useful for loading css or javascript

= Query examples =  
<br />
Here are examples of query loops using the *content* and *loop* shortcodes.

*Display all posts*

	[loop type="post"]
	  [content]
	[/loop]

Notice that inside a query loop, the *content* shortcode does not need *type* and *name* parameters, because it is targeting the current post in the loop.

*Display fields from all posts of a custom post type, filtered by category*

	[loop type="apartment" category="suite"]
	  Apartment: [content field="title"]
	  Rent per day: [content field="rent-per-day"]
	[/loop]

Available parameters for the *loop* shortcode are:

 * **type** - which post type to query: *post*, *page*, *custom post type*, or *attachment* - if empty, the default is *any post type*
 * **category** - display posts from a category
 * **count** - number of posts to show
 * **tag** - display posts with a specific tag - for multiple tags: *tag="apples, green"*

You can use other parameters of the [WP_Query class](http://codex.wordpress.org/Class_Reference/WP_Query), such as *author_name* and *order*. Custom field and taxonomy parameters are not yet supported.


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
			[loop type="bicycle" category="freestyle"]
				<li>[content field="model"] - [content field="price"]</li>
			[/loop]
		</ul>

= Attachments =

Get the attachments of the current post:

	[loop type="attachment"]
		Attachment ID: [content field="id"]
		Title: [content field="title"]
		Full-size image: [content field="image"]
		Full-size image URL: [content field="image-url"]
		Caption: [content field="caption"]
		Description: [content field="description"]
		Thumbnail: [content field="thumbnail"]
		Thumbnail URL: [content field="thumbnail-url"]
	[/loop]

Get the attachments of all posts in the category *tree*, and display them using Bootstrap v3 columns:

	<div class="row">
		[loop type="attachment" category="tree"]
			<div class="col-md-4" align="center">
				<a href='[content field="image-url"]' rel="lightbox">
					<img src='[content field="thumbnail-url"]'>
				</a>
			</div>  
		[/loop]
	</div> 


= Gallery fields =  
<br />
In the admin menu, under *Plugins -> Gallery Fields*, there is an option to enable gallery fields for selected post types, where images can be added and ordered.

Gallery fields are displayed in a similar way to attachments, using the same field names.

*Display individual thumbnails linked to its full-size images*

	[loop type="gallery"]
		<a href='[content field="image-url"]'>
			<img src='[content field="thumbnail-url"]'>
		</a>
	[/loop]

Currently there are two gallery types included in the *content* shortcode: the native gallery and Bootstrap v3 carousel.

	[content gallery="native"]
	[content gallery="carousel"]

Finally, if you need to pass the image IDs to another shortcode, here is the workaround solution. This is necessary because a shortcode cannot be used as a parameter for another shortcode.

*Display using another gallery shortcode*

	[loop field="gallery"][another_gallery ids="{IDS}"][/loop]



= Custom content management =  
<br />
Here are some plugins that work well together for custom content management.

*Custom post types and fields*

 * **[Custom Post Type UI](http://wordpress.org/plugins/custom-post-type-ui/)** - easily create and manage custom post types and taxonomies
 * **[Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/)** - create and manage all kinds of useful custom field types. *Note: Some advanced fields types such as arrays and repeaters are not yet supported by the Custom Content Shortcode.*
 * **[Simple Fields](http://)**
 * **[Meta Boxes](http://)**
 
*Admin Extensions*

 * **[Admin Menu Editor](http://wordpress.org/plugins/admin-menu-editor/)** - essential for customizing the admin menu, especially for client use. For example, you can move the edit menu for the Product post type near the top of the menu for easier access; hide menu items for unnecessary or sensitive settings; arrange and simplify the admin menu; and so on.
 * **[Intuitive Custom Post Order](http://wordpress.org/plugins/intuitive-custom-post-order/)** - change the order of post/page/custom post types by drag-and-drop
 * **[Post Type Converter](http://wordpress.org/plugins/post-type-converter/)** - convert a post from one post type to another
 * **[Codepress Admin Columns](http://wordpress.org/plugins/codepress-admin-columns/)** - customize the overview pages for post/page/custom post types, by showing/hiding custom fields as columns. I wish it could do sortable columns so custom post types are easier to organize. Perhaps another plugin is more fully featured?
 * **[Duplicate Post](http://wordpress.org/plugins/duplicate-post/)** - useful for quickly creating similar post items, backup posts, templates, etc.

*Front-end framework*

 * **[Easy Bootstrap Shortcode](http://wordpress.org/plugins/easy-bootstrap-shortcodes/)**


= Features to be implemented =  
<br />
Additional parameters:


* Style parameters for menus (navs, tabs, pills, dropdowns) and gallery (carousel, responsive columns)
* query for custom fields and taxonomies
* advanced fields such as arrays


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

= 0.1.5 =

* Added gallery fields
* Added attachment type and fields

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


