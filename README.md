⚠ This plugin is intended for developers and under active development. Review the code, look for problems, and contribute to making this code better!

# IO Slider
**IO Slider** is a WordPress plugin that creates sliders and carousels for posts, pages, and custom post types using the Swiper library. You can write custom templates for each element with HTML and `{@post_title}` like [tags](#available-tags).

- **Shortcode based**
Write `[io_slider]` everywhere in your content to create a slider. Set the shortcode attributes according to this documentation.
- **Custom templates**
You can provide a custom template for each item rendered in this way: `[io_slider]<h2>{@post_title}</h2>[/io_slider]`.
- **Allow multiple sliders per page**
Add as many sliders as you want on the same page. Each slider won't collide with others, and JS and CSS assets will be loaded only once.
- **Elementor ready**
This plugin adds an **IO Slider** widget to your library with Elementor native options and styling.
- **Compatible with [Pods](https://es.wordpress.org/plugins/pods/) tags**
If Pods plugin is active, you can use field and magic tags with your templates. Make sure to create a *pods*  for your post type.

## Usage
You can create as many sliders as you want. It doesn't matter if they are all in the same URL or show different post types. Just choose one of these options:

- Use the shortcode `[io_slider]` in a page or post content
- If Elementor is enabled, find the IO Slider widget and drag and drop it to your page. You will set options and styling in the Elementor widget panel.
- In your theme or plugin file, call via do_shortcode() function:
    ```php
    do_shortcode("[io_slider]")
    ```

## Settings
Pass each of these settings as a shortcode attribute.

### Query
|Parameter|Default|Description|
|-|-|-|
post_type|`post`|Post type to query: `any` `post` `page` `revision`, or custom post type
posts_per_page|`-1`|Number of items to retrieve. Use -1 to return all post.
orderby|`menu_order`|Items will be ordered by: `none`,`ID`,`author`,`title`,`name`,`type`,`date`,`modified`,`parent`,`rand`,`comment_count`,`relevance`,`menu_order`
order|`ASC`|Ascending or descending order: `ASC` or `DESC`

### Template
Set the content of the shortcode to provide your custom HTML template with `{@field}` tags. Check below the available tags.

If you don't provide one, the default template is used:
```
<div class="io-slide io-slide-{@ID}" data-id="{@ID}">
    {@post_thumbnail}
    <h3 class="title">{@post_title}</h3>
    <div class="content">{@post_content}</div>
</div>
```

### Styling
|Parameter|Values|Description|
|-|-|-|
id|null|(string) If you provide this attribute it will be used in the `id` attribute for the slider container. It's useful for CSS styling.
overflow|`hidden` `visible`|Non-active slides should be visible? Default to `hidden`
navigation|`arrows` `dots` `both`| Choose slider navigation style. Default to `arrows`
arrows_position|`outside` `inside`| Next and previous slide buttons should be positioned inside (over the slides) or outside? Default: `outside`
navigation_previous_icon|`svg`
navigation_next_icon|`svg`
slidesperview|`1`
slidesperview_md|`1`
slidesperview_lg|`2`
slidesperview_xl|`2`
spacebetween|`0`

### Autoplay and loop
|Parameter|Default|Description|
|-|-|-|
loop|`true`
autoplay|`false`
autoplay_speed|`5000`
pause_on_interaction|`false`
pause_on_hover|`false`
autoheight|`false`
slidetoclickedslide|`false`
centeredslides|`false`,

## Available tags
Here is a list of available tags to use in your templates:
|Element|Tag|Equivalent WordPress functions|
|-|-|-|
|ID|`{@ID}` `{@the_ID}` | get_the_ID()
|Title|`{@post_title}` `{@title}` `{@the_title}` | get_the_title()
|Content|`{@post_content}` `{@the_content}` | get_the_content(),
|Permalink|`{@permalink}` | get_the_permalink()
|Thumbnail|`{@post_thumbnail}` | get_the_post_thumbnail()

If you need to display custom fields, taxonomies or images, consider installing Pods plugin. When this plugin is enabled all magic tags and fields will be available for your templates.