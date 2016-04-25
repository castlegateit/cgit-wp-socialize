# Castlegate IT WP Socialize #

Socialize generates sharing links for common social networks and includes a widget to make it easy to add them to your site. It currently supports the following networks:

*   Digg
*   Facebook
*   Google+
*   LinkedIn
*   Pinterest
*   Reddit
*   Tumblr
*   Twitter

## Creating URLs ##

The `Cgit\Socialize` class creates social sharing links for the current post, page, custom post type entry, or the entire website:

~~~
$social = new Cgit\Socialize();
~~~

You can access these URLs as an associative array:

~~~
$urls = $social->getLinks();
~~~

Alternatively, you can return a ready-made HTML list of links:

~~~
echo $social->render();
~~~

## Customization ##

You can change the selection of links generated by passing an array of social networks to the constructor or using the `setNetworks()` method on an existing object:

~~~
$networks = [
    'digg',
    'facebook',
    'google',
    'linkedin',
    'pinterest',
    'reddit',
    'tumblr',
    'twitter',
];

$social = new Cgit\Socialize($networks);
$social->setNetworks($networks); // alternative method
~~~

You can also edit the URL, title, and text used in the sharing link:

~~~
$social->setUrl('http://www.example.com/');
$social->setTitle('Example title');
$social->setText('Example text');
~~~

## Widget ##

The plugin also provides a widget for displaying a list of links in a dynamic widget area. The list is generated with `$social->render()`.
