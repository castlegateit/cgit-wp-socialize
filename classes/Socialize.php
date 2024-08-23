<?php

declare(strict_types=1);

namespace Castlegate\Socialize;

use Exception;
use WP_Post;

class Socialize
{
    /**
     * Post title
     *
     * @var string|null
     */
    public ?string $title = null;

    /**
     * Post URL
     *
     * @var string|null
     */
    public ?string $url = null;

    /**
     * Post excerpt
     *
     * @var string|null
     */
    public ?string $excerpt = null;

    /**
     * Networks to output
     *
     * @var array
     */
    public array $networks = [];

    /**
     * Network names, URL formats, and colors
     *
     * @var array
     */
    public const TEMPLATES = [
        'email' => [
            'name' => 'Email',
            'url' => 'mailto:?subject={title}&body={url}',
        ],
        'bluesky' => [
            'name' => 'Bluesky',
            'url' => 'https://bsky.app/intent/compose?text={url}',
            'color' => '#0085ff',
        ],
        'facebook' => [
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/sharer.php?u={url}',
            'color' => '#1877f2',
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/shareArticle?url={url}&title={title}',
            'color' => '#0a66c2',
        ],
        'pinterest' => [
            'name' => 'Pinterest',
            'url' => 'https://pinterest.com/pin/create/bookmarklet/?url={url}&description={title}',
            'color' => '#bd081c',
        ],
        'pocket' => [
            'name' => 'Pocket',
            'url' => 'https://getpocket.com/save?url={url}',
            'color' => '#ee4056',
        ],
        'reddit' => [
            'name' => 'Reddit',
            'url' => 'https://reddit.com/submit?url={url}&title={title}',
            'color' => '#ff4500',
        ],
        'twitter' => [
            'name' => 'Twitter',
            'url' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
            'color' => '#1da1f2',
        ],
        'whatsapp' => [
            'name' => 'WhatsApp',
            'url' => 'https://api.whatsapp.com/send?text={url}',
            'color' => '#25d366',
        ],
        'x' => [
            'name' => 'X',
            'url' => 'https://x.com/intent/post?url={url}&text={title}',
            'color' => '#000',
        ],
    ];

    /**
     * Network aliases
     *
     * @var array
     */
    public const ALIASES = [
        'mail' => 'email',
    ];

    /**
     * Construct
     *
     * @param int|null $post_id Get content from this post ID
     * @param bool $auto Get content from current post ID
     * @return void
     */
    public function __construct(int $post_id = null, bool $auto = false)
    {
        if (!is_null($post_id)) {
            $this->post($post_id);
        } elseif ($auto) {
            $this->auto();
        }
    }

    /**
     * Set content based on post ID
     *
     * @param int $post_id
     * @return void
     */
    public function post(int $post_id): void
    {
        $this->reset();

        $post = get_post($post_id);

        if (!($post instanceof WP_Post)) {
            throw new Exception('Post does not exist');
        }

        $this->title = get_the_title($post_id);
        $this->url = get_permalink($post_id);
        $this->excerpt = static::getPostExcerpt($post_id);
    }

    /**
     * Set content based on current post ID
     *
     * @return void
     */
    public function auto(): void
    {
        $this->reset();

        $has_post = is_page() || is_single() || in_the_loop();
        $post_id = get_the_ID();

        if (!$has_post || !is_int($post_id)) {
            throw new Exception('Cannot get current post ID');
        }

        $this->post($post_id);
    }

    /**
     * Reset content
     *
     * @return void
     */
    public function reset(): void
    {
        $this->title = null;
        $this->url = null;
        $this->excerpt = null;
    }

    /**
     * Return post excerpt
     *
     * @param int $post_id
     * @return string|null
     */
    private static function getPostExcerpt(int $post_id): ?string
    {
        $excerpt = apply_filters('the_excerpt', get_the_excerpt($post_id));

        if ($excerpt) {
            return strip_tags($excerpt);
        }

        $content = get_the_content(post: $post_id);
        $length = apply_filters('excerpt_length', 55);
        $more = apply_filters('excerpt_more', ' [&hellip;]');

        if (is_string($content)) {
            return wp_trim_words(strip_tags($content), $length, $more);
        }

        return null;
    }

    /**
     * Return sharing links as array
     *
     * @param bool $icons
     * @return array
     */
    public function links(bool $icons = false): array
    {
        if (!$this->url) {
            throw new Exception('Missing URL');
        }

        $links = [];

        foreach ($this->networks as $key) {
            $link = $this->getNetworkLink($key, $icons);

            if ($link) {
                $links[$key] = $link;
            }
        }

        return $links;
    }

    /**
     * Return network link data
     *
     * @param string $key
     * @param bool $icon
     * @return array|null
     */
    private function getNetworkLink(string $key, bool $icon = false): ?array
    {
        $key = static::sanitizeNetworkKey($key);

        if (!$key) {
            return null;
        }

        $template = static::TEMPLATES[$key];

        $link = [
            'name' => static::getNetworkName($key),
            'url' => static::renderNetworkTemplate($template['url'] ?? ''),
            'color' => static::getNetworkColor($key) ?? null,
        ];

        if (!$link['name'] || !$link['url']) {
            return null;
        }

        if ($icon) {
            $link['icon'] = [
                'path' => static::getNetworkIconPath($key),
                'url' => static::getNetworkIconUrl($key),
                'svg' => static::getNetworkIconSvg($key),
            ];
        }

        return $link;
    }

    /**
     * Render template
     *
     * @param string $template
     * @return string
     */
    private function renderNetworkTemplate(string $template): string
    {
        $keys = ['title', 'url', 'excerpt'];
        $subs = [];

        foreach ($keys as $key) {
            $subs['{' . $key . '}'] = rawurlencode((string) $this->$key);
        }

        return str_replace(array_keys($subs), array_values($subs), $template);
    }

    /**
     * Return network name
     *
     * @param string $key
     * @return string|null
     */
    public static function getNetworkName(string $key): ?string
    {
        $key = static::sanitizeNetworkKey($key);

        if ($key) {
            return static::TEMPLATES[$key]['name'] ?? null;
        }

        return null;
    }

    /**
     * Return network color
     *
     * @param string $key
     * @return string|null
     */
    public static function getNetworkColor(string $key): ?string
    {
        $key = static::sanitizeNetworkKey($key);

        if ($key) {
            return static::TEMPLATES[$key]['color'] ?? null;
        }

        return null;
    }

    /**
     * Return network icon path
     *
     * @param string $key
     * @return string|null
     */
    public static function getNetworkIconPath(string $key): ?string
    {
        $relative_path = static::getNetworkIconRelativePath($key);

        if (!$relative_path) {
            return null;
        }

        $path = CGIT_WP_SOCIALIZE_PLUGIN_DIR . '/' . $relative_path;

        if (!is_file($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Return network icon relative path
     *
     * @param string $key
     * @return string|null
     */
    private static function getNetworkIconRelativePath(string $key): ?string
    {
        $key = static::sanitizeNetworkKey($key);

        if (!$key) {
            return null;
        }

        return 'images/' . $key . '.svg';
    }

    /**
     * Return network icon URL
     *
     * @param string $key
     * @return string|null
     */
    public static function getNetworkIconUrl(string $key): ?string
    {
        $relative_path = static::getNetworkIconRelativePath($key);
        $path = static::getNetworkIconPath($key);

        if ($relative_path && $path) {
            return plugin_dir_url(CGIT_WP_SOCIALIZE_PLUGIN_FILE) . $relative_path;
        }

        return null;
    }

    /**
     * Return network icon SVG
     *
     * @param string $key
     * @return string|null
     */
    public static function getNetworkIconSvg(string $key): ?string
    {
        $path = static::getNetworkIconPath($key);

        if ($path) {
            return file_get_contents($path);
        }

        return null;
    }

    /**
     * Sanitize network key
     *
     * @param string $key
     * @return string|null
     */
    private static function sanitizeNetworkKey(string $key): ?string
    {
        $key = strtolower($key);

        if (array_key_exists($key, static::ALIASES)) {
            $key = static::ALIASES[$key];
        }

        if (array_key_exists($key, static::TEMPLATES)) {
            return $key;
        }

        return null;
    }
}
