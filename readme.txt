=== Custom Content Shortcode ===
Contributors: miyarakira
Author: Eliot Akira
Author URI: eliotakira.com
Plugin URI: wordpress.org/plugins/custom-content-shortcode/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T3H8XVEMEA73Y
Tags: loop, query, content, shortcode, post type, field, taxonomy
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 1.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display posts, pages, custom post types, fields, attachments, comments, images, files, menus, sidebars

== Description ==

= Overview =  
<br />
From a single field to entire pages, Custom Content Shortcode is a set of commands to display content where you need.

The **[content]** shortcode displays any of the following: *posts, pages, custom post types, fields, images, menus,* or *widget areas*.

The **[loop]** shortcode performs query loops. It can display, for example, available products in a category, or excerpts from the 5 most recent posts. You can query by parameters such as: *post type, taxonomy, date,* and *field values*.

There is a reference section under Settings -> Custom Content.

= Included =  
<br />
Here are some of the included features:

* **Dynamic templates** with shortcodes
* View your site's **content structure**
* Display **comments** and **attachments**
* **User info** and content based on user status
* **Relative URLs** for links and images
* Simple **gallery field** for any post type
* **Cache** the result of query loops

Support for other plugins:

* [Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/) - Image, relationship, gallery, repeater, flexible content
* [WCK Fields and Post Types](http://wordpress.org/plugins/wck-custom-fields-and-custom-post-types-creator/) - Text, select, checkbox, radio, upload, repeater


== Installation ==

1. Install & activate from *Plugins -> Add New*
1. See: *Settings -> Custom Content*


== Screenshots ==

1. Documentation and examples
2. Content overview page
3. Gallery field

== Frequently Asked Questions ==

**Q:** How do I protect shortcodes from post content formatting?

**A:** Post content is automatically formatted, which can cause unwanted `<p>` and `<br>` tags inside multi-line shortcodes. To prevent this, wrap the section with the [raw] shortcode. You can enable it under Settings -> Custom Content.

**Q:** How do I protect HTML code from the visual editor?

**A:** The visual editor (TinyMCE) sometimes misinterprets HTML tags placed in the text editor. There are several ways to address this issue.

* Disable the visual editor for certain posts or post types, with the [Raw HTML](http://wordpress.org/plugins/raw-html/) plugin. However, if the post needs to be editable by the client, this won't be ideal.

* Put all code in a custom post type, then include it into the post. For example: *[content type="template" name="recent-posts"]*

* Put the code in a custom field, then include it in the post. For example: *[field code_block]*

* Put the code in a file, then include it into the post. For example: *[load dir="views" file="recent-posts.html"]*

* Put your code in a text widget, and use a plugin like [Widgets on Pages](http://wordpress.org/plugins/widgets-on-pages/).


== Upgrade Notice ==



== Changelog ==


= 1.7.3 =

* [for each] - Add *term* parameter; loop through one or more specified terms
* [for-else] - If [for] loop finds no matching term
* [if children] - When used inside [for] loop, check if current term has children

= 1.7.2 =

* [loop] - Support for paginated list

= 1.7.1 =

* [loop] - Correctly filter by taxonomy term when inside [for each], nested or not

= 1.6.9 =

* [for parents="true"] - Loop through parent taxonomies only
* [for each="child"] - Loop through children of current taxonomy term; use inside a parents loop
* [-for] - Support for nested loop
* [-loop] - Support for nested loop
* [taxonomy field] - Add *url* and *link* fields: display term archive URL, or term name linked to the archive

= 1.6.8 =

* [loop] - Correctly build field value query
* [loop acf_date] - Query by ACF date field
* [field acf_date] - Display an ACF date field with selected formatting

= 1.6.7 =

* [comment avatar] - Author avatar image; optional *size* parameter
* [loop]- Improve date field comparison for *future* and *past* when stored as string; by default, assume that time is not included

= 1.6.6 =

* [field link] - Support for ACF page link: post/archive URL
* Set up test server to ensure PHP version compatibility

= 1.6.5 =

* [comments] - Compatibility with PHP <=5.4 - avoid array literal
* [if children] - If the post has child posts

= 1.6.4 =

* [comment] - Display comment content by default (no parameter)
* [comments] - If inside loop, display comments from current post
* [if exists] - Check if a post exists; takes the same parameters as loop
* [content escape] - Escape HTML and shortcodes

= 1.6.3 =

* [loop] - Improve support for paginator add-on

= 1.6.2 =

* [if search] - If current page is search result
* [is author] - If user is author of current post
* [for each] - Enable tags just like [pass taxonomy_loop]
* [pass list] - Pass an arbitrary list of items
* [pass taxonomy_loop] - Default order by taxonomy term name
* [-pass], [--pass] - Enable nested pass: use {-TAG} and {--TAG}
* [repeater] - Do shortcode inside ACF repeater sub field
* [search_form type] - Search specific post type

= 1.5.9 =

* [loop] - If *parent="this"* and no current post, return empty
* [loop] - Exclude child posts by default, unless *include="children"*
* Try the new add-on under development: [Paginator](http://eliotakira.com/plugin/paginator)

= 1.5.8 =

* [comments id] - Return nothing if post/ID is empty
* [content] - Make sure current post exists
* [field thumbnail] - Enable *size* parameter to resize thumbnail; thanks @kurakin_alexander!
* [repeater num] - Display specific repeater field
* [repeater num sub] - Quick way to display a single sub-field
* Starting to add hooks and filters for extensibility
* Improve code organization

= 1.5.6 =

* [loop] - Support up to 3 taxonomy queries
* [array] - Return ACF field array correctly when it's not a sub-field
* [content] - Return translated result if qTranslate Plus is active

= 1.5.4 =

* [array] - Support ACF fields stored as array

= 1.5.3 =

* [array] - Use with [field] to display key-value pairs stored in a field
* [array] - Enable looping through multiple arrays: *each="true"*

= 1.5.1 =

* [attached] - Keep loop contained; don't interfere with parent loop

= 1.5.0 =

* [loop] - Enable multiple categories or tags with *compare="and"*
* [related] - Enable posts related by multiple taxonomies

= 1.4.9 =

* [if taxonomy] - If no term is specified, check if any term exists for the taxonomy
* [each url] - URL of taxonomy term archive
* [each name-link] - Taxonomy term name with link to archive

= 1.4.8 =

* [loop author] - Display posts from current user: *author="this"*

= 1.4.7 =

* [if user_field] - If user field is not empty or has specific value

= 1.4.6 =

* [field meta] - Return author meta field correctly
* [related] - By default, loop posts related by category
* [related] - Add parameter *trim* to remove trailing space or comma

= 1.4.5 =

* [comments] - Show only approved comments if set in Settings -> Discussion
* [each description] - Display taxonomy term description
* [for each] - Enable *current* and *parent* parameters together
* [loop author] - Display posts by author ID or login name
* [loop taxonomy] - Support additional taxonomy query: *taxonomy_2*, *value_2*
* [metabox] - Easier way to display WCK fields; refer to documentation
* [pass] - Pass current post slug with {SLUG}
* [related] - Support post object field (ACF)
* [related taxonomy] - Show posts related to current post by taxonomy
* [repeater] - Support nested repeater fields (ACF)

* I'd like to thank everyone who has donated to this plugin's development, and for all the user feedback. Thanks also to *adwrt* and *kurakin_alexander* for the code contribution.

= 1.4.3 =

* [url logout] - Return to current page by default
* [is] - Enable [else] for user conditions

= 1.4.2 =

* [users] - Users query loop

= 1.4.1 =

* [loop] - Query by beginning of field value: *field="title" start="The"*
* [for each] - If there's a loop inside, filter by each taxonomy term
* [loop trim] - Correctly trim extra space or comma

= 1.3.9 =

* [loop parent] - Get children of current post: *parent="this"*
* [loop parent] - Return empty if no parent found

= 1.3.8 =

* [content] - Format post content by default, as expected

= 1.3.7 =

* [field] - Get sub field by default if inside repeater or flexible content
* [if field] - Check fields inside attachment, repeater or flexible content
* [if field] - Check predefined fields like title
* [repeater], [flex], [acf_gallery] - Add *columns* parameter
* [pass taxonomy_loop] - Pass each taxonomy term in a loop

= 1.3.6 =

* [loop list] - Build a list from loop items
* [if] - Support nested conditions
* [pass] - Pass sub fields if inside repeater or flexible content
* Content Overview - Prevent division by zero

= 1.3.5 =

* [loop parent] - Get parent by slug correctly
* Updated documentation for this and previous version

= 1.3.4 =

* [loop type] - Support multiple post types
* [field post-type] - Display post type of current post in the loop
* [for each] - Display empty terms: *empty="true"*
* [for each exclude] - Exclude taxonomy term by ID or slug
* Mobile Detect - Load correctly when enabled

= 1.3.3 =

* [content exclude="this"] - Exclude current post

= 1.3.2 =

* [field image/image-link] - Default image size: full

= 1.3.1 =

* [taxonomy] - Taxonomy fields: *term="name or ID" field="description"*
* [field excerpt] - Read more: *more="true"*
* [if name/field] - Check only the beginning of name or field value: *start="true"*
* [field] - Image attributes for image field, attachment, and featured image; make sure *image_class* works for all types
* [loop exclude="this"] - Make sure to get the original post ID in which the loop is contained

= 1.3.0 =

* Update all modules for better code organization and performance
* Improve documentation
* [loop cache] - Cache the result for an amount of time
* [cache] - Cache page section or multiple loops
* [timer] - Tool to measure performance
* Gallery field - Improve UI and function
* [attached gallery] - Loop through images in the gallery field
* [if gallery] - If the post has any image in gallery field
* [loop exclude="this"] - Exclude current post from loop
* Mobile detect - now disabled by default
* ..and more

= 1.2.2 =

* [each] - Assume *name* by default
* [loop tag] - Clean up any extra spaces in tag list
* [load view] - Load a template from *views* folder

= 1.2.1 =

* **[pass]** - Add *fields* parameter: pass multiple fields
* **[pass]** - Add *field_loop* parameter: loop through a comma-separated list stored in a field
* **[field image]** - Check with ACF 5
* **Settings** - Enable *shortcode unautop* to remove `<p>` tags around shortcodes
* **Content Overview** - Show available user meta fields
* Improve documentation

= 1.1.9 =

* Fix compatibility for PHP older than 5.3

= 1.1.8 =

* **[loop], [for]** - Add *trim* parameter to remove space or comma at the end
* **[loop fields]** - Expand a list of fields to replace {FIELD} tags
* **[field image]** - Display image field: *image="field_name"*
* **[wck-field]** - Enable shortcodes in field

= 1.1.7 =

* **[wck-field], [wck-repeat]** - Support WCK Fields/Post Types

= 1.1.6 =

* **[field image-link]** - Featured image with link to post
* **[field thumbnail-link]** - Featured image thumbnail with link to post
* **[loop id]** - Preserve post ID order if multiple values given
* **[loop columns]** - Skip empty columns

= 1.1.5 =

* **[related]** - Loop through ACF relationship field
* **[if image]** - If current post has a featured image
* **Settings** - Enable shortcodes inside Text widget
* Test compatibility with WordPress 4.0
* Add donate link on plugin page
* Improve documentation

= 1.1.3 =

* **[attached]** - Make sure to get all attachments
* **[attached]** - Add parameters: *count, offset, orderby, order*

= 1.1.2 =

* **[if parent]** - If parent matches ID or slug
* **[else]** - Display if condition is not met

= 1.1.1 =

* **[attached]** - Display URL to attachment file: [field url]
* **[attached]** - Display link to attachment page: [field page-url]

= 1.1.0 =

* **[is role]** - Display based on user role
* **[is capable]** - Display based on user capability
* **[user role]** - Display user role
* **[field image]** - *image_class* - add class to the `<img>` tag
* **[loop]** - Improve query by date field value; *value="future"* or *"past"*

= 1.0.9 =

* **[attached]** - Loop through attachments in current post or queried posts
* **[if attached]** - If post has attachment
* **[content]** - Display image by default in attachment loop

= 1.0.8 =

* **[loop]** - Parent page by ID: *parent="2"*
* **[if flag]** - Check if featured image exists: *flag="image"*

= 1.0.7 =

* **[comments]** - Fix comment loop with *id* parameter

= 1.0.6 =

* **New plugin settings** - Enable/disable non-essential modules; option to move wp_autop filter to *after* shortcodes, to avoid unwanted formatting
* **[if flag]** - Enable outside loop

= 1.0.4 =

* **[comments]** - Loop through recent comments, and display comment fields
* **[if comment]** - If current post in a query loop has any comments

= 1.0.2 =

* **[loop]** - Query by custom date field, for example: *field="event_date" compare=">" value="now"*
* **[load]**, **[url]** - Make sure to return correctly if wp-content folder is renamed

= 1.0.1 =

* **[field title-link]** - Return the link correctly when limiting by word or length
* **[if not every="X"]** - When the post is *not* every X in the loop
* **[content]** - Make sure to minimize queries when inside loop

= 1.0.0 =

* **[loop]** - Test and improve sort by timestamp
* **[content field="title-link"]** - Post title wrapped in a link to post
* **[field]** - Shorter version of [content field=""] - for example, [field title]
* **[comment template]** - Make sure to look in child theme first, if it exists

= 0.9.9 =

* Mobile detect - Re-include module: back by demand

= 0.9.8 =

* **[if]** - Add parameters: *type, slug, category, taxonomy, term, field, value*
* **[load]** - Improve loading CSS or JS from external site: either specify dir="web" or use `http://` in the file name
* Organize and simplify: remove mobile detect library

= 0.9.6 =

* **[loop]** - Move wpautop filter back to before shortcode; will add an option if this solves formatting issue for some people

= 0.9.5 =

* **[if every]** - For every X number of posts: *every="3"*
* **[if first]** - For the first post
* **[if last]** - For the last post
* **[loop]** - Include sticky posts for parameter *count*
* **[loop]** - Reset query when [loop] is inside another loop
* **[loop]** - Process shortcode *before* wpautop filter to prevent unwanted formatting

= 0.9.4 =

* **[for each]** - For each category/tag/taxonomy of current post: *current="true"*
* Added a note in the documentation about using [loop] to create multiple Bootstrap carousels

= 0.9.3 =

* **[for each]** - For each child category, by parent slug; *each="category" parent="products"*
* **[content field]** - Add *edit-url*; only shows when user is logged in and can edit posts

= 0.9.2 =

* **[pass]** - Correctly pass when field value is an array (for example, post IDs)

= 0.9.1 =

* **[if empty]** - Display something when there is no query result
* **[url login], [url logout]** - Update *go* parameter; by default, return to the same page


= 0.9.0 =

* **[if flag]** - If a field has value, then display something
* **[if no_flag]** - If a field is empty, then display something
* **[for each]** - Now able to use inside loop; display for each category, tag or taxonomy
* Content Overview - Display all taxonomy terms, even unused ones

= 0.8.8 =

* **[content]** - Display multiple vales from checkbox or selector field
* **[repeater]** - ACF: repeater field (correct shortcode name)
* **[loop]** - get a post by name or ID, for repeater field to target

= 0.8.7 =

* Fixed compatibility with a theme

= 0.8.6 =

* Fixed compatibility with older versions of PHP

= 0.8.5 =

* **[loop]** - Improved parameter *clean*
* **[loop]** - Testing parameter *blog* for multisite: *blog="2"*
* **[content]** - Added parameter *meta* for displaying author meta
* **[content]** - Enabled parameter *date_format* for custom field
* **[user]** - Added parameter *field* to display user meta
* **[clean], [format]** - Added format shortcodes

= 0.8.3 =

* **[load]** - Improve performance

= 0.8.2 =

* **[loop]** - Improve formatting parameters: *clean*, *strip_tags*, *allow*

= 0.8.0 =

* **[loop]** - Field and taxonomy queries: make *compare* and *relation* parameters case-insensitive

= 0.7.9 =

* **[loop]** - Taxonomy query - multiple values possible: *value="apple, green"*
* **[loop]** - Taxonomy query - add parameter *compare="AND"*, *compare="NOT"*
* **[loop]** - Correctly display posts with tag(s): *tag="tag1,tag2"*
* **[loop]** - Add parameter *pad* for column padding: *pad="0 10px"*

= 0.7.7 =

* **[for]** - Create loops for each category, tag, or taxonomy; see reference page
* **[loop]** - Improve simple columns

= 0.7.6 =

* **[content]** - Improve *more* tag display: *[content more="true"]*

= 0.7.3 =

* **[loop]** - Add parameter *columns* for simple columns feature: *columns="3"*
* **[content]** - Add parameter *embed* to autoembed URLs: *embed="true"*; it's enabled by default for post content, i.e., [content] inside a loop

= 0.7.2 =

* **[loop]** - Enable multiple values for post ID: *id="1,3,7"*
* **[loop]** - Add parameter *exclude* by post ID: *exclude="7,15,21"*
* **[content]** - Add parameter *more* to display content up to the more tag
* **[content]** - Add field *attach-link* to display image attachment page link
* **[content]** - Process content in correct order: do_shortcode, then wpautop
* **[comment total]** - New parameter to display total comment count of last loop
* **[load]** - Return output instead of echo
* **Gallery Field** - Add all image sizes for parameter *size*

= 0.7.1 =

* **[loop]** - Improved parameter *checkbox* to query by checkbox value(s)
* **[is user]** - Enable multiple values, i.e., *user="1,3,7,guest"*

= 0.7.0 =

* **[content]** - Display correct author name
* **[content]** - Added field *modified* to display date of last post update
* **[loop]** - Added field *parent* (by slug) to display children
* **[loop]** - Improved *orderby="menu_order"*
* **[loop]** - Improved *orderby="modified"*
* Fixed compatibility with a theme

= 0.6.9 =

* **[user]** - User name, id, e-mail, full name, avatar
* **[loop]** - Added parameter *clean="true"* to remove extra *p* and *br* tags
* **[content]** - Added field *title-length*
* **[content]** - Display correct image sizes
* Other minor improvements: performance and content overview page

= 0.6.8 =

* No change in function; improved code so there are no PHP notices when debug is on

= 0.6.5 =

* **[content]** - Add *out=“slug”* to output post taxonomy slug
* **[content]** - Improved check for published status
* **[content]** - Added *post* and *page* parameter, for example: *[content page=“about”]*

= 0.6.4 =

* **[loop]**, **[content]** - Added parameter *status* to filter by post status: *any, publish, pending, draft, future, private*; the default is *publish*

= 0.6.3 =

* Fixed documentation

= 0.6.2 =

* **[loop]** - Added parameter *checkbox* and *checkbox_2*, to query checkbox values
* **[content]** - Added parameter *checkbox* to display checked values

= 0.6.1 =

* **[load]** - Added parameter *dir=“web”*

= 0.6.0 =

* **[content]** - Added *return=“url”* parameter, to return URL of an image; this can be used to set a background image according to a field
* **[content]** - Added *in* parameter, to specify if the image field contains an attachment ID, URL, or object; default is ID
* **[content]** - Added *size* parameter for image size; depending on the theme, you can set *thumbnail*, *medium*, *large*, or custom size

= 0.5.9 =

* Fixed display of shortcode functions in content overview

= 0.5.8 =

* **[content]** - Added *allow* parameter - strips all HTML tags except allowed

= 0.5.7 =

* **[loop]** - Fixed query when field value includes ampersand symbol

= 0.5.6 =

* Content overview: added list of default fields and registered shortcodes
* Reference page: fixed logo when *wp-content* folder has been renamed

= 0.5.5 =

* Content overview: fixed display when there are no fields found

= 0.5.4 =

* **[content]** - post URL field now returns clean permalink structure

= 0.5.3 =

* Improved performance of content overview page

= 0.5.1 =

* Added an overview of site content structure: *Dashboard -> Content*

= 0.5.0 =

* **Mobile Detect** - display content based on device type: *is_phone, isnt_phone, is_tablet, is_mobile, is_computer*
* **[redirect]** - redirect user to another URL: based on login status, device type, etc.
* **[load]** - now able to include files with HTML, PHP script, and shortcodes
* **[content]** - added author ID, URL, avatar

= 0.4.9 =

* Fixed compatibility issue with a theme

= 0.4.8 =

* **[loop]** - Added filter by date: *year*, *month*, *day*

= 0.4.7 =

* Better support for Advanced Custom Fields: gallery, repeater and flexible content - *flex, repeat, layout, sub, sub_image, and acf_gallery*
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

* Fixed one line to be compatible with older versions (less than 5.3) of PHP

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
