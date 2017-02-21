# Castlegate IT WP Socialize

Socialize generates sharing links for common social networks and includes a widget to make it easy to add them to your site. It currently supports the following networks, sites, and protocols:

*   Digg
*   Email
*   Evernote
*   Facebook
*   Google
*   Instapaper
*   LinkedIn
*   Pinterest
*   Pocket
*   Reddit
*   Tumblr
*   Twitter
*   WhatsApp

## Generating URLs and links

The `\Cgit\Socialize` class creates social sharing links. For pages and single posts of any post type, these will include the post URL, title, and (if supported by that network) excerpt. For any other page on the site, the URLs will include the site URL, title, and description.

~~~ php
$socialize = new \Cgit\Socialize();
$foo = $socialize->getNetworks(); // array, including URLs
$bar = $socialize->getLinks(); // HTML list of links
~~~

## Customization

### URL parameters

The `setArgs()` method lets you override the default title, URL, and excerpt to share:

~~~ php
$socialize->setArgs('title', 'Custom title'); // set single parameter

// set multiple parameters
$socialize->setArgs([
    'title' => 'Custom title',
    'url' => 'http://www.example.com/',
    'desc' => 'Custom excerpt',
]);
~~~

### Networks

By default, the class will return links to Facebook, Google, LinkedIn, and Twitter. You can set the default list of networks to return in the constructor:

~~~ php
$socialize = new \Cgit\Socialize(['digg', 'reddit']);
~~~

You can also choose which networks to return on output:

~~~ php
$foo = $socialize->getNetworks(['digg', 'reddit']);
$bar = $socialize->getLinks([
    'networks' => ['digg', 'reddit'],
]);
~~~

Note that you can return a list of all available networks with the `getAvailableNetworks()` method.

### HTML output

The generated HTML links can include just text (default), icons, or text and icons:

~~~ php
$foo = $socialize->getLinks([
    'icons' => true, // use icons
    'embed' => true, // embed SVG icons
    'captions' => true, // include text after each icon
]);
~~~

### Filters

*   `cgit_socialize_default_networks` filters the array of default networks.
*   `cgit_socialize_default_args` filters the array containing the default title, URL, and description.
*   `cgit_socialize_icon_extension` filters the icon file extension, which might be useful for custom icons. Default `.svg`.
*   `cgit_socialize_icon_url` filters the icon directory URL.
*   `cgit_socialize_icon_path` filters the icon directory file system path.
*   `cgit_socialize_class_prefix` filters the HTML class prefix. Default `cgit-socialize-`.

## Widget

The plugin also provides a widget that has the same options for choosing the networks to display and what to include in the output (text, icons, or both). The widget uses the `getLinks()` method to render the HTML links.

## Default CSS

By default, the HTML is output without styles, leaving it up to you to make it fit in with your theme. However, if the constant `CGIT_SOCIALIZE_ENQUEUE_CSS` is defined as `true`, some basic CSS will be added to the page. If you use the embedded SVG icons, this will add colours and padding to them.

## Icons

Icons from [Picons Social](https://picons.me/):

*   Twitter
*   Facebook
*   Google
*   Tumblr
*   Digg
*   Evernote
*   Pinterest
*   Reddit
*   WhatsApp

Icons from [Font Awesome](http://fontawesome.io/):

*   LinkedIn
*   Pocket
*   Email

Icons from [Mui Social](http://linhpham.me/social/):

*   Instapaper

## Changes since version 2.0

Version 2.0 is a complete rewrite of the Socialize plugin and is not backward compatible with previous versions. If you want to upgrade from version 1.x, you have two options:

*   Update your theme or plugin code to use the new class and methods.
*   Switch to the `Cgit\Socialize\Legacy` class, which attempts to be a drop-in replacement for the 1.x class and its methods.
