<?php

namespace Cgit;

class Socialize
{
    /**
     * Default supported social networks
     *
     * @var array
     */
    private $defaultNetworks = [
        'digg' => 'Digg',
        'facebook' => 'Facebook',
        'google' => 'Google+',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'reddit' => 'Reddit',
        'tumblr' => 'Tumblr',
        'twitter' => 'Twitter',
    ];

    /**
     * Social networks for this instance
     *
     * @var array
     */
    private $networks;

    /**
     * Social network URLs for this instance
     *
     * @var array
     */
    private $links;

    /**
     * Page details
     *
     * @var string
     */
    private $url;
    private $title;
    private $text;

    /**
     * Constructor
     *
     * Set the list of networks for this instance to the specified list of
     * networks, or the default list if no list is supplied. Set the URL, title,
     * and text based on the page or site information from WordPress.
     *
     * @param array|bool $networks Array of network keys
     * @return void
     */
    public function __construct($networks = false)
    {
        $defaults = $this->defaultNetworks;
        $this->networks = $defaults;

        $this->setDefaults();
        $this->setNetworks($networks);
    }

    /**
     * Get property
     *
     * Provide read-only access to certain private properties.
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'defaultNetworks':
                return $this->defaultNetworks;
        }
    }

    /**
     * Set default URL, title, and text
     *
     * If this is a single post (or custom post type) or a page, use the page
     * URL, title, and excerpt. Otherwise, use the site URL, title, and excerpt.
     *
     * @return void
     */
    private function setDefaults()
    {
        $this->setUrl(get_bloginfo('url'));
        $this->setTitle(get_bloginfo('name'));
        $this->setText(get_bloginfo('description'));

        if (is_page() || is_singular()) {
            $this->setUrl(get_permalink());
            $this->setTitle(get_the_title());
            $this->setText(get_the_excerpt());
        }
    }

    /**
     * Set URL
     *
     * @param string $url URL to share
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = urlencode($url);
    }

    /**
     * Set title
     *
     * @param string $title Page title to share
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = urlencode($title);
    }

    /**
     * Set text
     *
     * @param string $text Page excerpt to share (if possible)
     * @return void
     */
    public function setText($text)
    {
        $this->text = urlencode($text);
    }

    /**
     * Update links
     *
     * Update the array of links based on the current state of the array of
     * social networks.
     *
     * @return void
     */
    private function updateLinks()
    {
        $this->links = [];

        foreach (array_keys($this->networks) as $network) {
            $method = 'get' . ucfirst(strtolower($network));

            if (method_exists($this, $method)) {
                $this->links[$network] = $this->$method();
            }
        }
    }

    /**
     * Get links
     *
     * Return an associative array of social network URLs.
     *
     * @return array
     */
    public function getLinks()
    {
        $this->updateLinks();

        return $this->links;
    }

    /**
     * Set networks
     *
     * Update the list of social networks for this instance. If a network is not
     * present in the array of default networks, it will be ignored.
     *
     * @param array $networks
     * @return void
     */
    public function setNetworks($networks)
    {
        if (!is_array($networks)) {
            return false;
        }

        $defaults = $this->defaultNetworks;
        $this->networks = [];

        foreach ($networks as $network) {
            if (array_key_exists($network, $defaults)) {
                $this->networks[$network] = $defaults[$network];
            }
        }

        $this->updateLinks();
    }

    /**
     * Render list of HTML links
     *
     * @return string
     */
    public function render()
    {
        $links = $this->getLinks();
        $items = [];

        foreach ($links as $key => $url) {
            $url = htmlspecialchars($url);
            $name = $this->networks[$key];
            $items[] = '<li class="' . $key . '"><a href="' . $url . '">'
                . $name . '</a></li>';
        }

        return '<ul>' . implode(PHP_EOL, $items) . '</ul>';
    }

    /**
     * Return Digg URL
     *
     * @return string
     */
    private function getDigg()
    {
        return 'http://digg.com/submit?url=' . $this->url . '&title='
            . $this->title;
    }

    /**
     * Return Facebook URL
     *
     * @return string
     */
    private function getFacebook()
    {
        return 'http://www.facebook.com/sharer.php?u=' . $this->url;
    }

    /**
     * Return Google+ URL
     *
     * @return string
     */
    private function getGoogle()
    {
        return 'http://plus.google.com/share?url=' . $this->url;
    }

    /**
     * Return LinkedIn URL
     *
     * @return string
     */
    private function getLinkedin()
    {
        return 'http://www.linkedin.com/shareArticle?url=' . $this->url
            . '&title=' . $this->title;
    }

    /**
     * Return Pinterest URL
     *
     * @return string
     */
    private function getPinterest()
    {
        return 'http://pinterest.com/pin/create/bookmarklet/?url='
            . $this->url . '&description=' . $this->title;
    }

    /**
     * Return Reddit URL
     *
     * @return string
     */
    private function getReddit()
    {
        return 'http://reddit.com/submit?url=' . $this->url . '&title='
            . $this->title;
    }

    /**
     * Return Tumblr URL
     *
     * @return string
     */
    private function getTumblr()
    {
        return 'http://www.tumblr.com/widgets/share/tool?canonicalUrl='
            . $this->url . '&title=' . $this->title . '&caption=' . $this->text;
    }

    /**
     * Return Twitter URL
     *
     * @return string
     */
    private function getTwitter()
    {
        return 'http://twitter.com/share?url=' . $this->url . '&text='
            . $this->title;
    }
}
