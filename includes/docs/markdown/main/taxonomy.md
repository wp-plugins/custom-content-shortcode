
## Taxonomy
---

Use `[taxonomy]` to display taxonomy terms from the current post.

*Display categories*

~~~
[taxonomy category]
~~~

Optionally, you can specify the *field* parameter.

~~~
[taxonomy category field=id]
~~~

Available fields are: *name* (default), *id*, *slug*, *description*, *url*, *link*, or custom taxonomy field.

---

If there is more than one term, they will be displayed as a comma-separated list.

You can also use [`[for/each]`](#for--each) to loop through a list of terms.

---

## Related posts
---

Use `[related]` to loop through posts related by taxonomy.

*Display posts in the same category as current post*

~~~
[related taxonomy=category count=3]
  [field title-link]
[/related]
~~~

The current post is not included in the result.

---

### Parameters

> **taxonomy** - *category*, *tag*, or custom taxonomy; multiple values possible

> **relation** - for multiple taxonomies, set *relation=and* to get posts that share all taxonomies

> **count** - maximum number of results

> **orderby** - order by* id*,* author*,* title*,* name*,* date* (default),* rand* (randomized)

> **order** - ASC (ascending/alphabetical) or DESC (descending/from most recent date)

> **children** - include children for hierarchical taxonomies - *true* (default) or *false*


## For / each
---

This is a feature to create a loop for each category, tag, or taxonomy term.

*Display 3 most recent posts for each category*

~~~
[for each=category]
  Category: [each name]
  [loop type=post count=3]
    [field title-link]
  [/loop]
[/for]
~~~

The `[for]` shortcode loops through *all existing terms* of a given taxonomy. To limit by terms associated with the current post, set *current=true*.

The `[each]`shortcode displays the term name.

Each term is also automatically passed to the `[loop]` inside.



&nbsp;

### For

Available parameters for the **[for]** shortcode are:


> **each** - *category*, *tag*, or custom taxonomy

> **current** - set *current=true* to limit by terms associated with the current post

> **count** - limit number of terms: *count=3*

> **parent** - get child categories by parent slug

> **parents** - set *true* to get only parent terms; see section below on child terms

> **exclude** - exclude taxonomy term by ID or slug; *exlude=uncategorized*

> **empty** - set *true* to include terms that have no associated posts; use outside the loop

> **term/terms** - specify which term(s) to get, by ID or slug; can be a comma-separated list

> **orderby** - order by *name* (default), *count* (post count), *id*, or *slug*

> **order** - *ASC* (ascending - default), or *DESC* (descending)

> **trim** - set *true* to trim space or comma at the end



&nbsp;

### Each

Available parameters for the **[each]** shortcode are:

> **name** - name of category, tag, or taxonomy term

> **url** - URL of the taxonomy term archive

> **link** - name of taxonomy term with link to the archive

> **slug** - term slug

> **id** - term ID

If no parameter is specified, the term name is displayed.


&nbsp;

### Inside loop

Inside a loop, the **[for]** shortcode gets taxonomy terms from the current post in the loop.

*Display a link for each category assigned to a post*

~~~
[loop type=post]
  [field title]
  [for each=category]
    [each link]
  [/for]
[/loop]
~~~



&nbsp;

### Current post

Outside the loop, the **[for]** shortcode gets all terms that have any posts associated. Use the *current* parameter to display terms of the current post only.

*Display a link for each category assigned to the current post*

~~~
[for each=category current=true]
  [each link]
[/for]

~~~


&nbsp;

### No term

To display something if there's no term found, use **[for-else]**.


~~~
[for each=category current=true]
  [each link]
[for-else]
  No category found
[/for]

~~~


&nbsp;

### Child terms

To display child terms separately from parents, use a nested **[for]** loop.

*List parent categories, with children*

~~~
[for each=category parents=true]
  Parent: [each link]
  [-for each=child]
    Child: [-each link]
  [/-for]
[/for]

~~~

You can use **[if children]** to display something only if the current term has child terms.

~~~
[for each=category]
  [if children]
    Parent: [each link]
    [-for each=child]
      Child: [-each link]
    [/-for]
  [else]
    [each link]
  [/if]
[/for]
~~~




&nbsp;

### List

Use the *trim* parameter to create a list of terms. It removes extra space or comma at the end.

*Display a list of categories*

~~~
[for each=category trim=true]
  [each],
[/for]
~~~

*Trim other characters*

~~~
[for each=category trim='/']
  [each] /
[/for]
~~~
