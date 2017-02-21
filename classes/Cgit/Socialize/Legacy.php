<?php

namespace Cgit\Socialize;

class Legacy
{
    /**
     * Socialize instance
     *
     * @var \Cgit\Socialize
     */
    private $instance;

    /**
     * Active networks
     *
     * @var array
     */
    private $networks = [];

    /**
     * Default networks
     *
     * @var array
     */
    private $defaultNetworks = [
        'digg',
        'facebook',
        'google',
        'linkedin',
        'pinterest',
        'reddit',
        'tumblr',
        'twitter',
    ];

    /**
     * Constructor
     *
     * @param array $keys
     * @return void
     */
    public function __construct($keys = null)
    {
        $this->instance = new \Cgit\Socialize();
        $this->networks = $this->defaultNetworks;
        $this->setNetworks($keys);
    }

    /**
     * Set networks
     *
     * @return void
     */
    public function setNetworks($keys = null)
    {
        if (!is_array($keys)) {
            return;
        }

        $this->networks = array_intersect($keys, $this->defaultNetworks);
    }

    /**
     * Set page title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->instance->setArgs('title', $title);
    }

    /**
     * Set page url
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->instance->setArgs('url', $url);
    }

    /**
     * Set page description
     *
     * @param string $desc
     * @return void
     */
    public function setText($desc)
    {
        $this->instance->setArgs('desc', $desc);
    }

    /**
     * Return an array of URLs
     *
     * @return array
     */
    public function getLinks()
    {
        $networks = $this->instance->getNetworks($this->networks);
        $urls = [];

        foreach ($networks as $key => $network) {
            $urls[$key] = $network['url'];
        }

        return $urls;
    }

    /**
     * Return an HTML list of links
     *
     * @return string
     */
    public function render()
    {
        return $this->instance->getLinks($this->networks);
    }
}
