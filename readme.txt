=== Custom Content Shortcode ===
Contributors: miyarakira
Author: Eliot Akira
Author URI: eliotakira.com
Plugin URI: wordpress.org/plugins/custom-content-shortcode/
Tags: custom post type, custom field, shortcode, query, loop
Requires at least: 3.0.1
Tested up to: 3.7.1
Stable tag: 0.3.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display posts, pages, custom post types, custom fields, files, images, comments, attachments, menus, or widget areas

== Description ==

From a single field to entire pages, Custom Content Shortcode is a set of commands to display content where you need.

The **[content]** shortcode displays any of the following: posts, pages, custom post types, custom fields, files, images, attachments, menus, or widget areas.

The **[loop]** shortcode performs query loops. It can display, for example, available products in a category, or excerpts from the 5 most recent posts.

*Please visit the documentation page for a full description: [Custom Content Shortcode](http://eliotakira.com/wordpress/custom-content-shortcode/)*

= Additional features =  
<br />
In addition, you can choose to:

* Enable a simple **gallery field**
* Use **relative URLs** for links and images
* Display a **comments** list, input form or comment count
* Display content based on **user status**: admin, non-admin, logged in/out, or user name
* Display user name, ID, **login/logout links** with user redirect
* Include **HTML/CSS/JavaScript**: stylesheets, jQuery libraries, Google Fonts, etc.

With other libraries:

* Display [Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/) - **image**, **gallery**, **repeater**
* Display content based on **device type** using [WP Mobile Detect](http://wordpress.org/plugins/wp-mobile-detect/)
* Display [Bootstrap](http://getbootstrap.com/) **carousel**, **navbar menu**, **pills**, **stacked**
* Easily include [Live Edit](http://wordpress.org/plugins/live-edit/) - **front-end editing** of content and fields


= Basic examples =  
<br />
*Display post content by name*

	[content name="hello-world"]

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

* **format** - add paragraph tags `<p>` and preserve line breaks `<br>` - set to *true* or *false* - by default, post content is formatted and for all other fields, it's set to *false*
* **class** - add a `<div>` class to the output for styling purpose
* **words** or **length**/**len** - limit number of words or characters
* **date_format** - use a custom format when displaying *field="date"* - for example, "*d.m.Y*" will display as *18.11.2013*. Use double backslashes to escape characters - "*Y/m/d \\\a\\\t g:i A*" will show as *2013/11/17 at 11:06 PM*. For more info, see the Codex: [Formatting Date and Time](http://codex.wordpress.org/Formatting_Date_and_Time).

Other types of content:

* **area** or **sidebar** - display a widget area/sidebar by *title*
* **menu** - display a menu list by *name/slug*, *ID* or *title*. Use an additional **ul** parameter to make Bootstrap nav-tabs, nav-pills and nav-pills-stacked. For a Bootstrap navbar-style menu, please see the section for the **[navbar]** shortcode.
* **image** - display an image field by *name/slug*

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

Category, tags, and taxonomies:

 * **category** - display posts from a category
 * **tag** - display posts with a specific tag - for multiple tags: *tag="apples, green"*
 * **taxonomy**, **value** - display posts whose *taxonomy* equals *value*
 
Custom field query:
 
 * **field, compare, value** - display posts according to custom field value; where *compare* is *EQUAL* (default) or *NOT EQUAL*
 * **relation, field_2, compare_2, value_2** - Additional query for custom field value, where *relation* is *AND* (default) or *OR*

For sorting and series:
 
 * **orderby** - order by *ID*, *author*, *title*, *name*, *date*, *parent*, *meta_value*, *meta_value_num*
 * **key** - when ordering by *meta_value* (string) or *meta_value_num* (number), you need to specify **key** as the name of the custom field to order by
 * **order** - ASC (ascending/alphabetical) or DESC (descending/from most recent date) when using the *orderby* parameter
 * **series**, **key** - order posts by a series of custom field values, where *key* is the name of the field - the series can include ranges, for example: *1-15,30-40,42,44*.

Other parameters:

 * **count** - number of posts to show - default is *all*
 * **offset** - offset the loop by a number of posts, for example: skip the first 3 posts in the query
* **strip_tags** - set to *true* to remove `<p>` and `<br>` tags inside the loop; use **[p]** and **[br]** shortcodes to manually create paragraphs and break lines
 * **x** - repeat the loop *x* times - no query



= Content views =  
<br />
Here is an example of how to create content views.

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


= Gallery fields =  
<br />
In the admin menu, under *Settings -> Gallery Fields*, there is an option to enable a simple gallery field for any post type. Images can be added, ordered and removed.

The **[loop]** shortcode handles the gallery field just like attachments. For each image, the **[content]** shortcode can display these fields: *id*, *title*, *image*, *image-url*, *caption*, *description*, *thumbnail*, and *thumbnail-url*.

This could be useful if you want to show captions, link the thumbnails to their full-size images, add a lightbox, etc.

*Display image details from the gallery field of a specific post*

	[loop field="gallery" name="hello-world"]
		Title: [content field="title"]
		Full-size image: [content field="image"]
		Caption: [content field="caption"]
	[/loop]

The *content* shortcode can display individual images of the gallery field, or all images in a gallery layout.

*Display the 3rd image in the gallery field*

	[content field="gallery" num="3"]

*Display the images in a native gallery or a Bootstrap carousel*

	[content gallery="native"]
	[content gallery="carousel"]


= Attachments =  
<br />
For attachments such as images attached to a post, you can query the post type *attachment* and display the necessary fields.

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

*Display attachment thumbnails of all posts in the category **tree**, linked to full-sized images in a lightbox*

	[loop type="attachment" category="tree"]
		<a href='[content field="image-url"]' rel="lightbox">
			<img src='[content field="thumbnail-url"]'>
		</a>
	[/loop]

= Comments =  
<br />
For displaying comments of the current post, or in a query loop, use the **[comment]** shortcode.

*Display comment count, input form, or comments list*

	Comment count: [comment count]
	Comment input form: [comment form]
	Comments list: [comment template] or [comments]

By default, the comments list is displayed by *comments.php* in the theme directory. If you want to specify a different template:

	[comment template="/short-comments.php"]

Here is an example of using Bootstrap accordion for a comments list that shows when clicked.

	<a class="accordion-toggle" data-toggle="collapse" data-parent="accordion" href="#comments-area">
		Comments ([comment count])
	</a>
	<div id="comments-area" class="panel-collapse collapse">
		[comments]
	</div>


= Use a field as parameter to another shortcode =  
<br />
To use the content of a field as a parameter to another shortcode, use the **[pass]** shortcode.

*Display a map according to the address entered in a custom field*

	[pass field="address"][google_map address="{FIELD}"][/pass]

*Display images in the gallery field using another shortcode*

	[pass field="gallery"][isotope_gallery ids="{FIELD}"][/pass]

This is necessary because a shortcode cannot be used as a parameter for another shortcode.

= Loading CSS or JavaScript =  
<br />
Create a custom field called *css*, and the content of the field will be automatically added to the header on page load. This could be useful for loading page-specific style changes, Google Fonts, etc.

Create a custom field called *js*, and the content of the field will be automatically added to the footer. This could be useful for loading page-specific JavaScript files, jQuery libraries or scripts.

*Load a CSS stylesheet*

	[load css="bootstrap.min.css"]

*Load a JavaScript file or jQuery plugin*

	[load js="bootstrap.min.js"]

By default, the **[load]** shortcode gets the specified file under the *css* or *js* folder in the theme directory.  You can choose a different directory using the *dir* parameter:

* *web* - http://
* *site* - site address
* *wordpress* - Wordpress directory
* *content* - /wp-content/
* *theme* - theme directory - /wp-content/theme
* *child* - child theme directory - /wp-content/child_theme

*Load a CSS file from a different location*

	[load dir="theme" css="include/custom.css"]

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

= Include fonts from Google Fonts =  
<br />
Use the *gfonts* parameter of the **[load]** shortcode to include fonts from Google Fonts.

*Include fonts and apply them to page elements*

	[load gfonts="Lato|Lora:400,700"]
	[css]
		h1, h2 { font-family: Lora, serif; }
		p { font-family: Lato, sans-serif; }
	[/css]

It should be placed in the *css* field, so the fonts are included in the header.

= Display any file =  
<br />
Use the *file* parameter of the **[load]** shortcode to include any file into the post.  The file can include HTML and shortcodes.

	[load dir="content" file="docs/readme.txt"]

If you don't set the *dir* parameter, it gets the file from the theme directory. To format the output with line breaks and paragraph tags, set the parameter *format* to *true*. If you want to disable shortcodes, set the parameter *shortcode* to *false*.

= Content override =  
<br />
Create a custom field called *html*, the content of the field will be displayed *instead of* the post content. This could be useful for wrapping the post content in a different layout. More documentation later.

= Relative URLs =  
<br />
Use relative URLs for links, images, etc. with the **[url]** shortcode.

*Display an image from a relative location*

	<img src='[url uploads]/assets/logo.png'>

This could be useful when you're migrating sites, for example, from local server to public. When inserting an image, replace the specific web address with the **[url]** shortcode. Then the image location doesn't depend on where the site is located.

Available parameters:

* *site* - site address
* *wordpress* - WordPress directory
* *content* - /wp-content/
* *uploads* - /wp-content/uploads/
* *theme* - /wp-content/theme - theme directory
* *child* - /wp-content/child_theme - child theme directory

= Login and logout links =  
<br />
The **[url]** shortcode is also used to display login/logout links.

*Display a login link*

	<a href="[url login]">User login</a>

*Display a logout link with redirect to home*

	<a href="[url logout go='home']">	Logout</a>

Available parameters:

* login - login link
* logout - logout link
* go - redirect user after login/logout

For the redirect link, you can either use a full URL (*www.example.com*) or the name/slug of any post type. The slug *home* will return the user to the home page of your site.

= Display content based on user status =  
<br />
The [is] shortcode is used to display content if the user is a specific user, an admin, non-admin, or logged in/out.

*Display user status*

	[is admin]You are an administrator.[/is]
	[is login]You are logged in.[/is][is logout]You are logged out.[/is]
	[is user="john"]You are John.[/is]

Available parameters:

* admin - administrator
* login - logged in
* logout - logged out
* user - user name or ID

You can also use the **[isnt]** shortcode, to see if the user is not admin, etc.

Here is an example using both [is] and [url] to show a login/logout link based on user status.

	[is logout]
		<a href="[url login go='home']">
			Login
		</a>
	[/is]
	[is login]
		<a href="[url logout go='somewhere']">
			Logout
		</a>
	[/is]

= Display user name or ID =  
<br />
Like this:

	User name: [user name]
	User ID: [user id]

= Bootstrap navbar menu =  
<br />
To display a menu in a Bootstrap navbar with dropdown, use the following shortcode:

	[navbar menu="Main Menu"]
		Brand
	[/navbar]

The *menu* parameter is the title of the menu to be displayed. You can put text or image for the brand/logo area.

Optionally, you can add the *navclass* parameter: *top-nav*, *navbar-fixed-top*, *navbar-fixed-bottom*, *navbar-static-top*. The default is *top-nav*. Please read the [Bootstrap documentation](http://getbootstrap.com/components/#navbar) for the description of these navbar types.

= Advanced Custom Fields: Gallery =  
<br />
For gallery fields made in Advanced Custom Fields, use the *acf_gallery* parameter of the *loop* shortcode. For each image, the *content* shortcode can display these fields: *id*, *title*, *image*, *image-url*, *caption*, *description*, *thumbnail*, *thumbnail-url*, *count*.

*Display images with title*

	[loop acf_gallery="images"]
		[content field="title"]
		[content field="image"]
	[/loop]

You can also pass the image IDs to another shortcode to display.

*Pass the images to another shortcode*

	[pass acf_gallery="images"]
		[isotope_gallery ids="{FIELD}"]
	[/pass]

= Advanced Custom Fields: Repeater =  
<br />
For repeater fields made in Advanced Custom Fields, use the *repeater* parameter of the *loop* shortcode. You can display subfields using the *content* shortcode.

	[loop repeater="boxes"]
		[content field="row"]
		[content field="title"]
		[content field="image"]
	[/loop]

For an image field, Advanced Custom Fields gives you an option to return its value as: image ID, image URL, or image object. Tell the *content* shortcode how to handle the image by setting the *in* parameter as: *id*, *url*, or *image object*. The default is *image object*.

	[content field="image" in="url"]

To display a specific subfield only, use the *content* shortcode by itself like this:

	[content field="boxes" row="1" sub="title"]

= Live Edit for content and fields =  
<br />
To enable front-end editing, install and activate [Live Edit](http://wordpress.org/plugins/live-edit/), and wrap the content you'd like to edit with the **[live-edit]** shortcode.

If Live Edit is active, and the user is logged in and has *post edit* capability, there will be an Edit button on the top right corner of the container. Otherwise the shortcode just displays the content normally.

*Enable live edit of post title and content*

	[live-edit]
	Here is an article you can edit from the front-end.
	[/live-edit]

When you press the *update* button, the changes will be saved to the post. Changes made to any ccontent not wrapped inside the shortcode will be visible after reloading the page.

Here are the available parameters:

 * **field** - enable Live Edit for title, content and fields
 * **admin** - enable title, content and fields for administrator
 * **editor** - enable title, content and fields for editor
 * **only** - enable specific fields only, e.g., *post_title*, *post_content*, *images*, etc. - applies only to the editor if the *admin* parameter is set
 * **title** - set to *false* to disable *post_title* edit
 * **content** - set to *false* to disable *post_content* edit

*Admin can edit post content and images; editor can only edit content*

	[live-edit admin="images" only="post_content"]	
	...
	[/live-edit]


= Custom content management =  
<br />
Here are some plugins that work well together for custom content management.

*Custom post types and fields*

 * **[Custom Post Type UI](http://wordpress.org/plugins/custom-post-type-ui/)** - Create and manage custom post types and taxonomies
 * **[Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/)** - Create and manage all kinds of useful custom field types.
 
*Admin Extensions*

 * **[Admin Menu Editor](http://wordpress.org/plugins/admin-menu-editor/)** - Essential for customizing the admin menu, especially for client use. For example, you can move the edit menu for the Product post type near the top of the menu for easier access; hide menu items for unnecessary or sensitive settings; arrange and simplify the admin menu; and so on.
 * **[Intuitive Custom Post Order](http://wordpress.org/plugins/intuitive-custom-post-order/)** - Change the order of post/page/custom post types by drag-and-drop
 * **[CMS Tree Page View](http://wordpress.org/plugins/cms-tree-page-view/)** - Add a tree overview of all your pages and custom posts on the dashboard; drag-and-drop post order and post types
 * **[Post Type Converter](http://wordpress.org/plugins/post-type-converter/)** - Convert a post from one post type to another
 * **[Codepress Admin Columns](http://wordpress.org/plugins/codepress-admin-columns/)** - Customize the overview pages for post/page/custom post types, by showing/hiding custom fields as columns. I wish it could do sortable columns so custom post types are easier to organize. Perhaps another plugin is more fully featured?
 * **[Duplicate Post](http://wordpress.org/plugins/duplicate-post/)** - Useful for quickly creating similar post items, backup posts, etc.
 * **[Admin Menu Post List](http://wordpress.org/plugins/admin-menu-post-list/)** - Display a post list in the admin menu for easy access

= Features to be implemented =  
<br />
Additional parameters:

* Handle image sizes: *thumbnail, medium, large, full*

== Installation ==

1. Upload `custom-content-shortcode.zip` through Plugins->Add New, or extract and upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the shortcode in a post, page, etc.

== Coming soon.. ==

 <br />
= WP Custom Toolbox =  
<br />
A set of plugins under development, to take Custom Content Shortcode to another level: for building whole sites and even web applications.

== Screenshots ==

None.

== Changelog ==

= 0.3.9 =

* **[loop]** - Added meta query parameters: field, compare, value, relation, field_2, compare_2, value_2
* **[loop]** - Added *strip_tags* parameter to remove `<p>` and `<br>` tags inside the loop
* Added **[p]** and **[br]** shortcodes to manually create paragraphs and break lines

= 0.3.8 =

* Added *offset* parameter to offset the query loop by a number of posts, for example: start from the 3rd most recent post

= 0.3.7 =

* Added *date_format* parameter to display post dates in a custom format

= 0.3.6 =

* Fixed one line to be compatible with older versions (<5.3) of PHP

= 0.3.5 =

* Added *series* parameter to order posts by a series of custom field values

= 0.3.4 =

* Added *taxonomy*, *value*, *orderby*, *order*, *meta_key*
* Added *align* parameter - left, center, right
* Fixed fetching repeater subfield from post other than current

= 0.3.3 =

* Changed *format* parameter - only post content is formatted (paragraph tags and line breaks) by default

= 0.3.2 =

* Added *words* and *length* parameters to limit number of words/characters

= 0.3.1 =

* Changed *class* parameter to work on all fields
* Added *ul* parameter to **[content menu]** - ul class to allow Bootstrap or other customization
* Moved **gallery field** settings from Plugins to Settings
* Added ability to override post content with the *html* field

= 0.2.8 =

* Created documentation page
* No change in code

= 0.2.7 =

* Added **[is]** shortcode - display content when user is administrator, non-administrator, logged in, or logged out
* Added *login* and *logout* parameter to **[url]** shortcode - display login/logout link url, also possible to redirect
* Improved the way *css* and *js* fields are loaded when outside the loop

= 0.2.6 =

* Added **[comment]** shortcode for displaying comment count, input form and template

= 0.2.5 =

* Added *gfonts* parameter for loading Google Fonts

= 0.2.4 =

* Added **[url]** shortcode
* Added a few parameters to **[load]** and **[live-edit]** shortcodes
* Added Bootstrap carousel support for *acf_gallery*
* Fixed live-edit when not logged in
* Support for older version of PHP

= 0.2.3 =

* Added support for Advanced Custom Fields: *acf_gallery*
* Added *admin* and *editor* parameters for Live Edit

= 0.2.2 =

* Added **[live-edit]**

= 0.2.1 =

* **[loop]** - Added *x* parameter - repeat content x times
* Added support for Advanced Custom Fields: *repeater*

= 0.2.0 =

* **[load]** - Added *dir* parameter to choose directory
* **[content]** - Added *image* parameter for image fields
* **[content]** - Get specific image from gallery field


= 0.1.9 =

* Added **[navbar]** - Bootstrap navbar menu

= 0.1.8 =

* Cleaned code
* Load *css* and *js* fields into the header/footer
* Added shortcodes: **[css]**, **[js]**, and **[load]**
* Fixed attachment image showing only thumbnail size

= 0.1.7 =

* Better documentation

= 0.1.6 =

* **[content]** - Added menu and sidebar content
* **[loop]** - Pass a field content as parameter to another shortcode

= 0.1.5 =

* Added simple gallery fields
* Added attachment type and fields

= 0.1.4 =

* Added **[loop]** shortcode for query loops
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


