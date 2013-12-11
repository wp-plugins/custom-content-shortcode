=== Custom Content Shortcode ===
Contributors: miyarakira
Author: Eliot Akira
Author URI: eliotakira.com
Plugin URI: wordpress.org/plugins/custom-content-shortcode/
Tags: custom post type, custom field, shortcode, query, loop
Requires at least: 3.0.1
Tested up to: 3.7.1
Stable tag: 0.4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display posts, pages, custom post types, custom fields, files, images, comments, attachments, menus, or widget areas

== Description ==

= Overview =  
<br />
From a single field to entire pages, Custom Content Shortcode is a set of commands to display content where you need.

The **[content]** shortcode displays any of the following: posts, pages, custom post types, custom fields, files, images, attachments, menus, or widget areas.

The **[loop]** shortcode performs query loops. It can display, for example, available products in a category, or excerpts from the 5 most recent posts.

There is an overview of the available shortcodes and parameters, under *Settings -> Custom Content*. Please visit the documentation page for a full description: [Custom Content Shortcode](http://eliotakira.com/wordpress/custom-content-shortcode/)


= Included =  
<br />
You'll find useful related features:

* Add a simple **gallery field** to any post type
* Use **relative URLs** for links and images
* Display a **comments** list, input form or comment count
* Display content based on **user status**: admin, non-admin, logged in/out, or user name
* Display user name, ID, **login/logout links** with user redirect
* Include **HTML/CSS/JavaScript**: stylesheets, jQuery libraries, Google Fonts, etc.

It plays well with others:


* Display [Bootstrap v3](http://getbootstrap.com/) **carousel**, **navbar menu**, **pills**, **stacked**
* Display content based on **device type** using [WP Mobile Detect](http://wordpress.org/plugins/wp-mobile-detect/)
* Display [Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/) - **image**, **gallery**, **repeater**, **flexible content**
* Include [Live Edit](http://wordpress.org/plugins/live-edit/) with a shortcode - **front-end editing** of content and fields


== Installation ==

1. Upload `custom-content-shortcode.zip` through Plugins->Add New, or extract and upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the shortcode in a post, page, etc.

== Coming soon.. ==
 
= Custom Toolbox =  
<br />
A set of plugins under development, to take Custom Content Shortcode to another level: for building page layouts, websites and applications.

== Screenshots ==

None.

== Changelog ==

= 0.4.7 =

* Better support for Advanced Custom Fields - *gallery*, *repeater* and *flexible content* fields: flex, repeat, layout, sub, sub_image, and acf_gallery
* Added new pages to reference section
* Fixed display of bullet points in the admin panel

= 0.4.6 =

* Improved reference page under *Settings -> Custom Content*, and simplified *readme.txt* to avoid duplicate content
* **[content]** - Added native gallery parameters: *orderby*, *order*, *columns*, *size*, *link*, *include*, *exclude*

= 0.4.5 =

* **[pass]** - Added *varible* parameter - displayed using {VAR} or {VARIABLE}
* **[loop]** - Made *title* parameter case-insensitive

= 0.4.4 =

* **[loop]** - Added *title* parameter; Added conditional statement: *if="all-no-comments"*
* Better code management (on-going)
* Started reference page

= 0.4.2 =

* **[list_shortcodes]** - Display a list of shortcodes defined
* Fixed compatibility issue with a theme


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





