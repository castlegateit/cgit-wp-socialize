# Castlegate IT WP Socialize

Socialize generates sharing links for common social networks and provides icons and brand colours. It currently supports the following sites and protocols:

| Network   | Key         |
| --------- | ----------- |
| Email     | `email`     |
| Bluesky   | `bluesky`   |
| Facebook  | `facebook`  |
| LinkedIn  | `linkedin`  |
| Pinterest | `pinterest` |
| Pocket    | `pocket`    |
| Reddit    | `reddit`    |
| Twitter   | `twitter`   |
| WhatsApp  | `whatsapp`  |
| X         | `x`         |

## Generating links

Sharing links require a title and URL. They may also include an excerpt, where supported by the network. There are three ways of specifying the link to be shared using the `Socialize` class:

``` php
use Castlegate\Socialize\Socialize;

// Set the title, URL, and excerpt manually
$socialize = new Socialize();
$socialize->title = 'Example Title';
$socialize->url = 'https://www.example.com/';
$socialize->excerpt = 'Example excerpt ...';

// Set the title, URL, and excerpt based on a post ID
$socialize = new Socialize(post_id: 123);

// Set the title, URL, and excerpt based on the current post ID
$socialize = new Socialize(auto: true);
```

You can then specify the social networks that should appear in the output and generate the links, using the keys in the table above:

``` php
$socialize->networks = [
    'facebook',
    'linkedin',
    'twitter',
];

// Return an array of links without icons
$links = $socialize->links();

// Return an array of links with icons
$links = $socialize->links(icons: true);
```

Each link will be an array, with `name`, `url`, `color`, and (optionally) `icon` keys.

## Utilities

You can use the `Socialize` class's static methods as utilities, e.g. to get the icon of a social network for use elsewhere in a theme.

``` php
$key = 'facebook';

Socialize::getNetworkName($key);     // "Facebook"
Socialize::getNetworkColor($key);    // "#1877f2"
Socialize::getNetworkIconPath($key); // (absolute path to icon file)
Socialize::getNetworkIconUrl($key);  // (icon URL)
Socialize::getNetworkIconSvg($key);  // (icon SVG safe to embed in HTML)
```

## License

Released under the [MIT License](https://opensource.org/licenses/MIT). See [LICENSE](LICENSE) for details.
