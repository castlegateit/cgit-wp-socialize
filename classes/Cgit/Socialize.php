<?php

namespace Cgit;

class Socialize
{
    /**
     * Arguments sent via social network URLs
     *
     * @var array
     */
    private $args = [
        'title' => '',
        'url' => '',
        'desc' => '',
    ];

    /**
     * Default networks
     *
     * @var array
     */
    private $defaultNetworks = [
        'facebook',
        'google',
        'linkedin',
        'twitter',
    ];

    /**
     * Active networks
     *
     * @var array
     */
    private $activeNetworks = [];

    /**
     * Network data
     *
     * The complete list of social networks, with names, URL templates,
     * generated sharing URLs, and icons.
     *
     * @var array
     */
    private $networks = [
        'digg' => [
            'name' => 'Digg',
            'template' => 'http://digg.com/submit?url={url}&title={title}'
        ],
        'email' => [
            'name' => 'Email',
            'template' => 'mailto:?subject={title}&body={url}'
        ],
        'evernote' => [
            'name' => 'Evernote',
            'template' => 'http://www.evernote.com/clip.action?url={url}'
        ],
        'facebook' => [
            'name' => 'Facebook',
            'template' => 'https://www.facebook.com/sharer.php?u={url}'
        ],
        'google' => [
            'name' => 'Google',
            'template' => 'https://plus.google.com/share?url={url}'
        ],
        'instapaper' => [
            'name' => 'Instapaper',
            'template' => 'http://www.instapaper.com/edit?url={url}&title={title}&description={desc}'
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'template' => 'https://www.linkedin.com/shareArticle?url={url}&title={title}'
        ],
        'pinterest' => [
            'name' => 'Pinterest',
            'template' => 'https://pinterest.com/pin/create/bookmarklet/?url={url}&description={title}'
        ],
        'pocket' => [
            'name' => 'Pocket',
            'template' => 'https://getpocket.com/save?url={url}'
        ],
        'reddit' => [
            'name' => 'Reddit',
            'template' => 'https://reddit.com/submit?url={url}&title={title}'
        ],
        'tumblr' => [
            'name' => 'Tumblr',
            'template' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}&caption={desc}'
        ],
        'twitter' => [
            'name' => 'Twitter',
            'template' => 'https://twitter.com/intent/tweet?url={url}&text={title}'
        ],
        'whatsapp' => [
            'name' => 'WhatsApp',
            'template' => 'whatsapp://send?text={url}'
        ],
    ];

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($networks = null)
    {
        $this->activeNetworks = $networks;

        // If no networks are specified in the constructor, use the default
        // network list, which can also be customized via a filter.
        if (is_null($networks)) {
            $this->activeNetworks = apply_filters(
                'cgit_socialize_default_networks',
                $this->defaultNetworks
            );
        }

        // Set default arguments and icon paths
        $this->setDefaultArgs();
        $this->setIconPaths();
    }

    /**
     * Set default arguments
     *
     * Page or post details on a single page or post; site details on any other
     * template. The default arguments can be customized via a filter.
     *
     * @return void
     */
    private function setDefaultArgs()
    {
        $args = [
            'title' => get_bloginfo('name'),
            'url' => get_bloginfo('url'),
            'desc' => get_bloginfo('description'),
        ];

        if (is_page() || is_singular()) {
            $args = [
                'title' => get_the_title(),
                'url' => get_permalink(),
                'desc' => $this->getPostExcerpt(),
            ];
        }

        $this->setArgs(apply_filters('cgit_socialize_default_args', $args));
    }

    /**
     * Set icon paths
     *
     * Sets the URLs and file system paths for the social media icons, which can
     * also be customized via filters.
     *
     * @return void
     */
    private function setIconPaths()
    {
        // Directory URL and path
        $plugin = CGIT_SOCIALIZE_PLUGIN;
        $dir = 'icons';
        $url = plugin_dir_url($plugin) . $dir;
        $path = plugin_dir_path($plugin) . $dir;

        // Apply filters for customization
        $ext = apply_filters('cgit_socialize_icon_extension', '.svg');
        $url = apply_filters('cgit_socialize_icon_url', $url);
        $path = apply_filters('cgit_socialize_icon_path', $path);

        // Make sure directories have trailing slashes
        $url = trailingslashit($url);
        $path = trailingslashit($path);

        // Add URLs and paths to each network
        foreach ($this->networks as $key => $value) {
            $this->networks[$key]['icon'] = $url . $key . $ext;
            $this->networks[$key]['icon_path'] = $path . $key . $ext;
        }
    }

    /**
     * Set one or more arguments
     *
     * @return void
     */
    public function setArgs($key, $value = '')
    {
        // Set an array of arguments
        if (is_array($key)) {
            foreach ($key as $sub_key => $sub_value) {
                $this->setArgs($sub_key, $sub_value);
            }

            return;
        }

        // Check for valid arguments
        if (!array_key_exists($key, $this->args)) {
            return;
        }

        // Set the value and update the network URLs
        $this->args[$key] = urlencode($value);
        $this->updateNetworks();
    }

    /**
     * Update network URLs
     *
     * Regenerates the URLs for each of the social networks using the URL
     * templates and the current values for the URL arguments.
     *
     * @return void
     */
    private function updateNetworks()
    {
        foreach ($this->networks as $net_key => $net_value) {
            $url = $net_value['template'];

            foreach ($this->args as $arg_key => $arg_value) {
                $url = str_replace('{' . $arg_key . '}', $arg_value, $url);
            }

            $this->networks[$net_key]['url'] = $url;
        }
    }

    /**
     * Get post excerpt
     *
     * If the page that is being shared is a page or single post, attempt to get
     * the excerpt from it without any HTML tags.
     *
     * @param int $limit
     * @return string
     */
    private function getPostExcerpt()
    {
        global $post;

        // If this is not a page or a single post, it cannot have an excerpt and
        // so the method should return an empty string.
        if (!is_page() && !is_singular()) {
            return '';
        }

        // Attempt to get a manaul excerpt
        $excerpt = apply_filters('the_excerpt', $post->post_excerpt);

        // If there is no manual excerpt, use the main content instead
        if (!$excerpt) {
            $excerpt = apply_filters('the_content', $post->post_content);
        }

        // Return the excerpt, stripped of any HTML code
        return strip_tags($excerpt);
    }

    /**
     * Return network URLs and other data as an array
     *
     * @param array $keys
     * @param boolean $templates
     * @return array
     */
    public function getNetworks($keys = null, $templates = false)
    {
        // If no keys are specified, use the current active network list
        $keys = is_array($keys) ? $keys : $this->activeNetworks;
        $networks = [];

        // Add each valid network to the output array
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->networks)) {
                $networks[$key] = $this->networks[$key];

                // Remove the URL template
                if (!$templates) {
                    unset($networks[$key]['template']);
                }
            }
        }

        return $networks;
    }

    /**
     * Return HTML network links
     *
     * @param array $args
     * @return string
     */
    public function renderNetworks($args = [])
    {
        // Default method arguments
        $args = wp_parse_args($args, [
            'networks' => array_keys($this->getNetworks()),
            'icons' => false,
            'embed' => false,
            'captions' => false,
        ]);

        // HTML class name prefix
        $prefix = apply_filters(
            'cgit_socialize_class_prefix',
            'cgit-socialize-'
        );

        $networks = $this->getNetworks($args['networks']);
        $items = [];

        // Add a link to each network to the output
        foreach ($networks as $key => $network) {
            $name = $network['name'];
            $content = $name;

            // Use icons instead of text
            if ($args['icons']) {
                $content = '<img src="' . $network['icon'] . '" alt="' . $name
                    . '" class="' . $prefix . 'icon-' . $key . '" />';

                // Embed the icon
                if ($args['embed']) {
                    $content = file_get_contents($network['icon_path']);
                }

                // Include the text after each icon
                if ($args['captions']) {
                    $content .= ' <span class="' . $prefix . 'caption-' . $key
                        . '">' . $name . '</span>';
                }
            }

            // Add each link to the array for output
            $items[] = '<li class="' . $prefix . $key . '">' . '<a href="'
                . htmlspecialchars($network['url']) . '">' . $content
                . '</a></li>';
        }

        return '<ul>' . implode(PHP_EOL, $items) . '</ul>';
    }

    /**
     * Return list of all available networks
     *
     * @return array
     */
    public function getAvailableNetworks()
    {
        return $this->networks;
    }
}
