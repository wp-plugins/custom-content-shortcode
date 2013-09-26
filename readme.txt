=== Custom Content Shortcode ===
Tags: custom post type, custom field, shortcode, query, loop
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.1.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A shortcode to display content from posts, pages, custom post types, custom fields, images, attachment files, menus and widget areas.

== Description ==

The shortcode **[content]** displays any of the following content types: posts, pages, custom post types, custom fields, images, attachment files, menus and widget areas (also called sidebars).

The shortcode **[loop]** performs query loops, with which you can create layout templates for displaying the content -- for example,  available products in a category, or excerpts from the 5 most recent posts with thumbnails.

Additional features:

* Enable gallery fields for chosen post types, where images can be added, removed and ordered.  They can be displayed using the **[content]** and **[loop]** shortcodes, the native gallery, a Bootstrap v3 carousel, or a gallery shortcode of your choice.

* Load CSS/JavaScript files or scripts from custom fields

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
Here are the available parameters for the **[content]** shortcode.

* **type** - which post type to target: *post*, *page*, or *custom post type* - if empty, the default is *any post type*
* **name**, **id**, or **title** - which entry to target: *name/slug*, *ID* or *title* - if empty, default is the current post
* **field** - which field to display - if empty, default is the main content of the post. You can display custom fields you created, as well as predefined fields: *title*, *id*, *author, date*, *url*, *image*, *image-url*, *thumbnail*, and *excerpt*.
* **menu** - display a menu by *name/slug*, *ID* or *title*. Currently, it's just a simple list.
* **class** - add a `<div>` class to the menu for styling purpose
* **area** or **sidebar** - display a widget area/sidebar by *title*


= Query examples =  
<br />
Here are examples of query loops using the **[content]** and **[loop]** shortcodes.

*Display all posts*

	[loop type="post"]
	  [content]
	[/loop]

Notice that inside a query loop, the **[content]** shortcode does not need *type* and *name* parameters, because it is targeting the current post in the loop.

*Display fields from all posts of a custom post type, filtered by category*

	[loop type="apartment" category="suite"]
	  Apartment: [content field="title"]
	  Rent per day: [content field="rent-per-day"]
	[/loop]

Available parameters for the **[loop]** shortcode are:

 * **type** - which post type to query: *post*, *page*, *custom post type*, or *attachment* - if empty, the default is *any post type*
 * **category** - display posts from a category
 * **count** - number of posts to show
 * **tag** - display posts with a specific tag - for multiple tags: *tag="apples, green"*

You can use other parameters of the [WP_Query class](http://codex.wordpress.org/Class_Reference/WP_Query), such as *author_name* and *order*. Custom field and taxonomy parameters are not yet supported.


= Custom content layout =  
<br />
Here is an example of how the two shortcodes can be used to create layout templates.

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
<br />
Display details on all the attachments of the current post:

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
In the admin menu, under *Plugins -> Gallery Fields*, there is an option to enable gallery fields for selected post types, where images can be added, removed and ordered.

Gallery fields are displayed with the **[loop]** shortcode in a similar way to attachments, using the same content fields: *id*, *title*, *image*, *image-url*, *caption*, *description*, *thumbnail*, and *thumbnail-url*.

*Display images from the gallery field of a specific post*

	[loop type="gallery" name="hello-world"]
		Title: [content field="title"]
		Full-size image: [content field="image"]
		Caption: [content field="caption"]
	[/loop]

Currently there are two gallery types included in the *content* shortcode: the native gallery and Bootstrap v3 carousel. These are used *without* the **[loop]** shortcode.

	[content gallery="native"]
	[content gallery="carousel"]

= Use a field content as parameter to another shortcode =  
<br />
If you need to pass the content of a field as a parameter to another shortcode, here is the workaround solution.

*Display a map according to the address entered in a custom field*

	[loop field="address"][google_map address="{FIELD}"][/loop]

*Display images in the gallery field using another shortcode*

	[loop field="gallery"][isotope_gallery ids="{IDS}"][/loop]

This is necessary because a shortcode cannot be used as a parameter for another shortcode. Note that when you're passing a field, the loop goes through only once.

= Loading CSS or JavaScript =  
<br />
Create a custom field called *css*, and the content of the field will be automatically added to the header on page load. This could be useful for loading page-specific styles, or making variations quickly.

Create a custom field called *js*, and the content of the field will be automatically added to the footer. This could be useful for loading page-specific JavaScript files or scripts.

There are shortcodes to simplify the loading of scripts.

The **[load]** shortcode gets the specified file under the *css* and *js* folder in your template directory.

*To load a CSS file, include this in the **css** custom field*

	[load css="bootstrap.min.css"]

*To load a JavaScript file, include this in the **js** custom field*

	[load js="bootstrap.min.js"]

For short scripts, you can use the following shortcodes.

*Wrapping CSS script*

	[css]
	.entry-content {
		background-color: black;
	}
	[/css]

*Wrapping JS script*

	[js]
	jQuery( window ).load(function() {
		jQuery('.isotope_container').isotope('reLayout');
	});
	[/js]


= Custom content management =  
<br />
Here are some plugins that work well together for custom content management.

*Custom post types and fields*

 * **[Custom Post Type UI](http://wordpress.org/plugins/custom-post-type-ui/)** - Create and manage custom post types and taxonomies
 * **[Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/)** - Create and manage all kinds of useful custom field types. *Note: Some advanced fields types such as arrays and repeaters are not yet supported by the Custom Content Shortcode.*
 * **[Simple Fields](http://wordpress.org/plugins/simple-fields/)** - Add any kind of custom fields to your pages, posts and attachments
 * **[Meta Boxes](http://wordpress.org/plugins/meta-box/)** - Easily implement multiple meta boxes in editing pages, with custom post types and vairous field types
 
*Admin Extensions*

 * **[Admin Menu Editor](http://wordpress.org/plugins/admin-menu-editor/)** - Essential for customizing the admin menu, especially for client use. For example, you can move the edit menu for the Product post type near the top of the menu for easier access; hide menu items for unnecessary or sensitive settings; arrange and simplify the admin menu; and so on.
 * **[Intuitive Custom Post Order](http://wordpress.org/plugins/intuitive-custom-post-order/)** - Change the order of post/page/custom post types by drag-and-drop
 * **[Post Type Converter](http://wordpress.org/plugins/post-type-converter/)** - Convert a post from one post type to another
 * **[Codepress Admin Columns](http://wordpress.org/plugins/codepress-admin-columns/)** - Customize the overview pages for post/page/custom post types, by showing/hiding custom fields as columns. I wish it could do sortable columns so custom post types are easier to organize. Perhaps another plugin is more fully featured?
 * **[Duplicate Post](http://wordpress.org/plugins/duplicate-post/)** - Useful for quickly creating similar post items, backup posts, etc.


= Features to be implemented =  
<br />
Additional parameters:

* Bootstrap style parameters for menus: *navs*, *tabs*, *pills*, *dropdowns*
* Simple gallery layout with responsive columns
* query for custom fields and taxonomies



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

= 0.1.8 =

* Cleaned code
* Added function to load *css* and *js* fields into the header/footer
* Added shortcodes: **[css]**, **[js]**, and **[load]**
* Fixed attachment image showing only thumbnail size

= 0.1.7 =

* Better documentation

= 0.1.6 =

* **[content]** - Added menu and sidebar content
* **[loop]** - Pass a field content as parameter to another shortcode

= 0.1.5 =

* Added gallery fields
* Added attachment type and fields

= 0.1.4 =

* Added shortcode for query loops
* Format post content using the_content filter

= 0.1.3 =

* Changed shortcode to **[content]**
* Added banner image to Wordpress plugin page

= 0.1.2 =

* Better documentation

= 0.1.1 =
* Simplified code, added a few parameters

= 0.1 =
* First release

== Upgrade Notice ==


