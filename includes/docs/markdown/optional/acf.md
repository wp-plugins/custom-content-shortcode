
# Advanced Custom Fields

---


### Supported field types

- [Checkbox, select, radio](#checkbox-select-radio), [true/false](#true-false), [date](#date-field)
- [Page link](#page-link), [relationship/post object](#relationship), [taxonomy](#related-by-taxonomy-field)
- [Gallery](#gallery), [repeater](#repeater), [flexible content](#flexible-content)
- [Cropped image](#cropped-image)

&nbsp;

## Checkbox/Select/Radio

### Label

To display the selection's label instead of slug, use the following syntax.

~~~
[field select out=label]
~~~

### Multiple selections

Use `[array]` and `[field value]` to loop through multiple selections.

~~~
[array checkboxes]
  [field value]
[array]
~~~

### Choices

Use `[array choices]` to loop through available choices.

~~~
[array choices=checkbox_field]
  Option label: [field label] or {LABEL}
  Option value: [field value] or {VALUE}
[/array]
~~~

If the field is in another post type than the current post, set parameter *type* or *name*.

## True/false

To check the value of a true/false field, use the following syntax.

~~~
[if field=true_false value=1]
  It's true.
[else]
  It's false.
[/if]
~~~

## Date field



Use the *acf_date* parameter to query the date field.


~~~
[loop type=event acf_date=date_field value=future]
~~~


Use the *acf_date* parameter to display the date field with selected formatting.


~~~
[field acf_date=date_field]
~~~



### Date and time

For fields created with ACF Date & Time Picker, it works best if you save as timestamp and use the *field* parameter for loop.

~~~
[loop type=event field=date_and_time_field value=future]
~~~

To display the field formatted, you can still use the *acf_date* parameter.

~~~
[field acf_date=date_and_time_field]
~~~


## Page link


Use the *link* parameter to display a page link field. This will display the URL of a post or archive.

~~~
[field link=page_link]
~~~


## Cropped image

With [ACF Image Crop](https://wordpress.org/plugins/acf-image-crop-add-on/) add-on, use the *cropped* parameter to display a cropped image field.

*Display cropped image*

~~~
[field cropped=field_name]
~~~

*Display cropped image URL*

~~~
[field cropped=field_name return=url]
~~~


## Relationship


Use `[related]` to loop through posts in a relationship field.

~~~
[related field_name]
  [field title]
  [field thumbnail]
[/related]
~~~

You can use the same shortcode for a Post Object field.

## Related by taxonomy field


Use `[related taxonomy_field]` to loop through posts related by a taxonomy field.

~~~
[related taxonomy_field=field_name]
  [field title]
  [field thumbnail]
[/related]
~~~

This excludes the current post by default.

If the taxonomy field contains multiple terms, the loop will include related posts with *any* of the terms. Set *operator=and* to display related posts that have *all* terms.

### Parameters

> **count** - maximum number of results

> **orderby** - order by* id*,* author*,* title*,* name*,* date* (default),* rand* (randomized)

> **order** - ASC (ascending/alphabetical) or DESC (descending/from most recent date)


## Repeater


Use `[repeater]` to loop through each row of a repeater field.


*Display repeater fields*

~~~
[repeater field_name]
  [field title]
  [field image=image]
  [field description format=true]
[/repeater]
~~~

For an image field inside, use the *image* parameter to display the field. You can set the *size* parameter to:* thumbnail*,* medium*,* large* - default is *full*. If the image field returns as URL, set *in=url*.



### Parameters

> **count** - how many rows to loop

> **start** - which row to start; default is 1

> **row** - a specific row from the repeater field: *row=3*

> **row=rand** - a randomly selected row


### If repeater is not empty

~~~
[if field=repeater_field]
  ..Repeater field has value..
[/if]
~~~


### Display a specific row

*Display the third row*

~~~
[repeater field_name row=3]
  [field title]
[/repeater]
~~~

*Display a random row*

~~~
[repeater field_name row=rand]
~~~

*Display specific sub-fields without looping*

~~~
[repeater field_name row=1 sub=title]
[repeater field_name row=2 sub=text]
[repeater field_name row=3 sub_image=image]
~~~

This displays a sub-field from a specific row. It's used by itself without a closing tag.


### Nested repeaters

~~~
[repeater field_name]
  [-repeater inner_field_name]
    ...
  [/-repeater]
[/repeater]
~~~

To display a repeater inside a repeater, use `[-repeater]`.  Please note that the inner repeater field must have a different name than its parent.

## Flexible content


~~~
[flex flexible_content]

  [layout title_text]
    [field title]
    [field text]
  [/layout]

  [layout title_image_text]
    [field title]
    [field image=image size=thumbnail]
    [field text]
  [/layout]

  [layout gallery]
    [acf_gallery field=gallery]
      [acf_image size=thumbnail]
    [/acf_gallery]
  [/layout]

[/flex]
~~~


## Gallery


For gallery fields, use `[acf_gallery]`.

*Display images with title*

~~~
[acf_gallery gallery_field]
  [acf_image]
  [acf_image field=title]
  [acf_image size=thumbnail]
[/acf_gallery]
~~~

`[acf_image]` displays each image in the gallery field. It can also display these fields: *id*,* title*,* caption*,* alt*,* url*, and* description*. You can set the *size* parameter to:* thumbnail*,* medium*,* large*. The default is full-size.



You can pass the image IDs to another shortcode.

~~~
[pass acf_gallery=gallery_field]
  [isotope_gallery ids='{FIELD}']
[/pass]
~~~


## In a loop


Display ACF fields from other posts, using the loop.

~~~
[loop name=hello-world]
  [repeater field_name]
    [field title]
    [field image=image]
    [field description format=true]
  [/repeater]
[/loop]

~~~


## Field stored as array


If the field value is stored as an array - for example, a file field - you can use the [`[array]` shortcode](options-general.php?page=ccs_reference&tab=field#array) to access its contents.


~~~
[array file_field]
  [field title]
  [field description]
  <a href="[field url]" download>Download Link</a>
[/array]

~~~


## Columns


You can use the *columns* parameter for gallery, repeater, or flexible content. For details, please see its description in [`[loop]` under *Parameters: Other*](options-general.php?page=ccs_reference&tab=loop#other).
