=== Custom Content Shortcode ===
Tags: custom post type, custom field, shortcode, query, loop
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A shortcode to display content from posts, pages, custom post types, custom fields, images, attachments, menus, or widget areas

== Description ==

The shortcode **[content]** displays any of the following content types: posts, pages, custom post types, custom fields, images, attachments, menus, and widget areas.

In addition, you can:

* Use the shortcode **[loop]** to perform query loops -- for example, display available products in a category, or excerpts of the 5 most recent posts.

* Enable **gallery fields** for chosen post types, where images can be added, removed and ordered. The images can be displayed individually or in a gallery, as well as with the *loop* shortcode for a custom layout.

* Load page-specific **CSS/JavaScript** file or script from custom fields.

If you're using the Bootstrap library (v3.x) you can also:

* Display the gallery field in a carousel
* Display a menu in a navigation bar

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
Here are the main parameters for the **[content]** shortcode:

* **type** - which post type to target: *post*, *page*, or *custom post type* - if empty, the default is *any post type*
* **name**, **id**, or **title** - which entry to target: *name/slug*, *ID* or *title* - if empty, default is the current post
* **field** - which field to display - if empty, default is the main content of the post. You can display custom fields you created, as well as predefined fields: *title*, *id*, *author, date*, *url*, *image*, *image-url*, *thumbnail*, and *excerpt*.

In addition:

* **image** - display image fields by *name/slug*
* **area** or **sidebar** - display a widget area/sidebar by *title*
* **menu** - display a menu list by *name/slug*, *ID* or *title*. Use the **class** parameter to add a `<div>` class to the menu for styling purpose. For a Bootstrap navbar menu, please see the section for the **[navbar]** shortcode.


= Query examples =  
<br />
Here are examples of query loops using the **[content]** and **[loop]** shortcodes.

*Display all posts*

	[loop type="post"]
	  [content]
	[/loop]

Note that inside a query loop, the **[content]** shortcode does not need *type* or *name* parameters, because it is targeting each post in the loop.

*Display fields from all posts of a custom post type, filtered by category*

	[loop type="apartment" category="suite"]
	  Apartment: [content field="title"]
	  Rent per day: [content field="rent-per-day"]
	[/loop]

Available parameters for the **[loop]** shortcode are:

 * **type** - which post type to query: *post*, *page*, *custom post type*, or *attachment* - if empty, the default is *any post type*
 * **category** - display posts from a category
 * **count** - number of posts to show - default is *all*
 * **tag** - display posts with a specific tag - for multiple tags: *tag="apples, green"*

You can use other parameters of the [WP_Query class](http://codex.wordpress.org/Class_Reference/WP_Query), such as *author_name* and *order*.


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
For attachments such as images attached to a post, query the post type *attachment*, and display necessary fields.

*Display details on all the attachments of the current post*

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

*Display the attachment thumbnails of all posts in the category **tree**, linked to their full-sized images in a lightbox*

	[loop type="attachment" category="tree"]
		<a href='[content field="image-url"]' rel="lightbox">
			<img src='[content field="thumbnail-url"]'>
		</a>
	[/loop]


= Gallery fields =  
<br />
In the admin menu, under *Plugins -> Gallery Fields*, there is an option to enable gallery fields for selected post types, where images can be added, removed and ordered.

The *content* shortcode can display individual images, or all images in a gallery.

*Display the 3rd image in the gallery field*

	[content field="gallery" num="3"]

*Display images in a native gallery or a Bootstrap carousel*

	[content gallery="native"]
	[content gallery="carousel"]

A basic responsive gallery layout will be included soon.

For a custom layout, images from the gallery field can be displayed with the **[loop]** shortcode just like attachments, using the same fields: *id*, *title*, *image*, *image-url*, *caption*, *description*, *thumbnail*, and *thumbnail-url*.

This could be useful if you want to present the images in different ways, for example, add borders to each image, link to lightbox, show captions, etc.

*Display image details from the gallery field of a specific post*

	[loop type="gallery" name="hello-world"]
		Title: [content field="title"]
		Full-size image: [content field="image"]
		Caption: [content field="caption"]
	[/loop]


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
Create a custom field called *css*, and the content of the field will be automatically added to the header on page load. This could be useful for loading page-specific styles or theme variations.

Create a custom field called *js*, and the content of the field will be automatically added to the footer. This could be useful for loading page-specific JavaScript files, jQuery libraries or scripts.

*Load a CSS stylesheet*

	[load css="bootstrap.min.css"]

*Load a JavaScript file or jQuery plugin*

	[load js="bootstrap.min.js"]

By default, the **[load]** shortcode gets the specified file under the *css* or *js* folder in your template directory.  You can choose a different directory using the *dir* parameter with *site*, *template*, or *child*. 

*Load a CSS file from a different location*

	[load dir="template" css="include/custom.css"]

For shorter scripts, the following shortcodes can be used.

*Wrapping CSS script*

	[css]
	.entry-content {
		background-color: black;
	}
	[/css]

*Wrapping JS script*

	[js]
	jQuery(window).load(function() {
		jQuery('.isotope_container').isotope('reLayout');
	});
	[/js]


= Bootstrap navbar menu =  
<br />
To display a menu in a Bootstrap navbar with dropdown, use the following shortcode:

	[navbar menu="Main Menu"]
		Brand
	[/navbar]

The *menu* parameter is the title of the menu to be displayed. You can put text or image for the brand/logo area.

Optionally, you can add the *navclass* parameter: *top-nav*, *navbar-fixed-top*, *navbar-fixed-bottom*, *navbar-static-top*. The default is *top-nav*. Please read the [Bootstrap documentation](http://getbootstrap.com/components/#navbar) for the description of these navbar types.

= Custom content management =  
<br />
Here are some plugins that work well together for custom content management.

*Custom post types and fields*

 * **[Custom Post Type UI](http://wordpress.org/plugins/custom-post-type-ui/)** - Create and manage custom post types and taxonomies
 * **[Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/)** - Create and manage all kinds of useful custom field types.
 * **[Simple Fields](http://wordpress.org/plugins/simple-fields/)** - Add any kind of custom fields to your pages, posts and attachments
 * **[Meta Boxes](http://wordpress.org/plugins/meta-box/)** - Easily implement multiple meta boxes in editing pages, with custom post types and vairous field types
 
*Admin Extensions*

 * **[Admin Menu Editor](http://wordpress.org/plugins/admin-menu-editor/)** - Essential for customizing the admin menu, especially for client use. For example, you can move the edit menu for the Product post type near the top of the menu for easier access; hide menu items for unnecessary or sensitive settings; arrange and simplify the admin menu; and so on.
 * **[Intuitive Custom Post Order](http://wordpress.org/plugins/intuitive-custom-post-order/)** - Change the order of post/page/custom post types by drag-and-drop
 * **[CMS Tree Page View](http://wordpress.org/plugins/cms-tree-page-view/)** - Add a tree overview of all your pages and custom posts on the dashboard; drag-and-drop post order and post types
 * **[Post Type Converter](http://wordpress.org/plugins/post-type-converter/)** - Convert a post from one post type to another
 * **[Codepress Admin Columns](http://wordpress.org/plugins/codepress-admin-columns/)** - Customize the overview pages for post/page/custom post types, by showing/hiding custom fields as columns. I wish it could do sortable columns so custom post types are easier to organize. Perhaps another plugin is more fully featured?
 * **[Duplicate Post](http://wordpress.org/plugins/duplicate-post/)** - Useful for quickly creating similar post items, backup posts, etc.


= Features to be implemented =  
<br />
Additional parameters:

* basic gallery layout with responsive columns
* option to load JavaScript library in the header
* Bootstrap navigation for menus: *tabs*, *pills*, *dropdowns*
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


= 0.2.0 =

* **[load]** - Added *dir* parameter to choose directory
* **[content]** - Added *image* parameter for image fields
* **[content]** - Get specific image from gallery field


= 0.1.9 =

* Added **[navbar]** - Bootstrap v3.x menu

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


