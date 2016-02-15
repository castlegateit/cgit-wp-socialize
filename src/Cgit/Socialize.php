<?php

namespace Cgit;

class Socialize
{
    /**
     * Default supported social networks
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
     */
    private $networks;

    /**
     * Social network URLs for this instance
     */
    private $links;

    /**
     * Page details
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
     */
    public function setUrl($url)
    {
        $this->url = urlencode($url);
    }

    /**
     * Set title
     */
    public function setTitle($title)
    {
        $this->title = urlencode($title);
    }

    /**
     * Set text
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
     */
    private function getDigg()
    {
        return 'http://digg.com/submit?url=' . $this->url . '&title='
            . $this->title;
    }

    /**
     * Return Facebook URL
     */
    private function getFacebook()
    {
        return 'http://www.facebook.com/sharer.php?u=' . $this->url;
    }

    /**
     * Return Google+ URL
     */
    private function getGoogle()
    {
        return 'http://plus.google.com/share?url=' . $this->url;
    }

    /**
     * Return LinkedIn URL
     */
    private function getLinkedin()
    {
        return 'http://www.linkedin.com/shareArticle?url=' . $this->url
            . '&title=' . $this->title;
    }

    /**
     * Return Pinterest URL
     */
    private function getPinterest()
    {
        return 'http://pinterest.com/pin/create/bookmarklet/?url='
            . $this->url . '&description=' . $this->title;
    }

    /**
     * Return Reddit URL
     */
    private function getReddit()
    {
        return 'http://reddit.com/submit?url=' . $this->url . '&title='
            . $this->title;
    }

    /**
     * Return Tumblr URL
     */
    private function getTumblr()
    {
        return 'http://www.tumblr.com/widgets/share/tool?canonicalUrl='
            . $this->url . '&title=' . $this->title . '&caption=' . $this->text;
    }

    /**
     * Return Twitter URL
     */
    private function getTwitter()
    {
        return 'http://twitter.com/share?url=' . $this->url . '&text='
            . $this->title;
    }
}
