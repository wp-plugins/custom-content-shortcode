
## Pass
---

Use `[pass]` to pass a field value to another shortcode's parameter.

*Display a map based on an address in a field*

~~~
[pass field=address]
  [google_map address='{FIELD}']
[/pass]
~~~

*Pass image IDs in the gallery field*

~~~
[pass field=gallery]
  [isotope_gallery ids='{FIELD}']
[/pass]
~~~


## Pass and if
---

To check the passed value, combine with an `[if]` statement.

~~~
[pass field=some_field]
  [if pass='{FIELD}' value=blue]
    It's blue.
  [/if]
[/pass]
~~~

---

If you're passing a value that could be empty, set *empty=false*.

~~~
[pass field=author_ids]
  [if user_field=id value={FIELD} empty=false]
    The ID field is not empty, and you're the user we're looking for.
  [/if]
[/pass]
~~~

This is necessary because when the *value* parameter is empty, the `[if]` statement by default only checks to see if the field exists.

## Multiple fields
---

Pass values from multiple fields using the *fields* parameter.

~~~
[pass fields=post_type_field,category_field]
  [loop type={POST_TYPE_FIELD} category={CATEGORY_FIELD}]
    [field title]
  [/loop]
[/pass]
~~~

This works in the same way as [field tags](options-general.php?page=ccs_reference&tab=loop#field-tags) for the `[loop]` shortcode.

## Field loop
---

You can loop through a comma-separated list stored in a field, and pass each item.

*Display products from a list of serial numbers*

~~~
[pass field_loop=serial_numbers]
  [loop type=product field=serial_number value={FIELD}]
    Product: [field title]
    Price: [field price]
  [/loop]
[/pass]
~~~

## User field
---

*Pass a single user field*

~~~
[pass user_field=twitter]
  Your twitter address: {USER_FIELD}
[/pass]
~~~

For a list of default user fields, see [the `[user]` shortcode](options-general.php?page=ccs_reference&tab=user).

*Pass several user fields*

~~~
[pass user_fields=name,email]
  Hello {NAME}, you email is {EMAIL}.
[/pass]
~~~

This works in the same way as multiple fields, described above. The replaced tag is an uppercased version of the field name.


## Taxonomy loop
---

You can loop through all terms in a taxonomy, and pass each item.

*Display products from each category*

~~~
[pass taxonomy_loop=category]
  Category: {TERM_NAME}
  [loop type=product category={TERM}]
    Product: [field title]
    Price: [field price]
  [/loop]
[/pass]
~~~

The available tags are: TERM, TERM_NAME and TERM_ID.

---

### Parameters

> **order** - *ASC* or *DESC*

> **orderby** - *id*, *count*, *slug*, *name* (default)

> **current** - Set *true* to get terms assigned to the current post only

## List loop

---

This is a feature to loop through a list of items.

~~~
[pass list=blue,red,green]
  {Item} products
  [loop type=product taxonomy=color term={ITEM}]
    Product: [field title]
  [/loop]
[/pass]
~~~

`{ITEM}` is replaced by the item, for example: *blue*. And `{Item}` capitalizes the first letter, for example: *Blue*.

For more flexibility, you can pass multiple items for each loop.

~~~
[pass list='Beautiful blue:blue,Bright red:red,Lush green:green']
  {ITEM_1} products
  [loop type=product taxonomy=color term={ITEM_2}]
    Product: [field title]
  [/loop]
[/pass]
~~~

## Global variable
---

To pass a global variable, use the *global* parameter.

*Pass a global variable*

~~~
[pass global=some_var]
  Variable value: {FIELD}
[/pass]
~~~

Use the field parameter to pass an element from an array.

*Pass a query variable*

~~~
[pass global=_GET field=q]
  Current URL request: {FIELD}
[/pass]
~~~

*Multiple fields*

~~~
[pass global=_GET fields='type, category, status']
  Queries: {TYPE}, {CATEGORY}, {STATUS}
[/pass]
~~~

*If the global variable is a nested array*

~~~
[pass global=first_array field=second_array sub=field_name]
  Here it is: {FIELD}
[/pass]
~~~

---

### URL Route

There's a special variable called *route*, which can be used to pass the current URL route or its parts.

If the current URL is: `example.com/article/category/special`

~~~
[pass global=route]
  {FIELD} is: article/category/special
  {FIELD_1} is: article
  {FIELD_2} is: category
  {FIELD_3} is: special
[/pass]
~~~
