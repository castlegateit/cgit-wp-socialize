<?php

namespace Cgit\Socialize\Widgets;

use Cgit\Socialize;
use WP_Widget;

class Share extends WP_Widget
{
    /**
     * Register widget
     *
     * @return void
     */
    public function __construct()
    {
        $name = __('Socialize', 'text_domain');
        $options = [
            'description' => 'Social network sharing links.',
        ];

        parent::__construct('cgit_socialize_share', $name, $options);
    }

    /**
     * Display widget content
     *
     * @param array $args Widget parameters
     * @param array $instance Widget instance parameters
     * @return void
     */
    public function widget($args, $instance)
    {
        $instance = wp_parse_args($instance, [
            'icons' => false,
            'embed' => false,
            'captions' => false,
        ]);

        $socialize = new Socialize();
        $network_args = [
            'networks' => [],
            'icons' => $instance['icons'],
            'embed' => $instance['embed'],
            'captions' => $instance['captions'],
        ];

        foreach ($socialize->getAvailableNetworks() as $key => $network) {
            if (isset($instance[$key])) {
                $network_args['networks'][] = $key;
            }
        }

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        echo $socialize->getLinks($network_args);
        echo $args['after_widget'];
    }

    /**
     * Display widget settings
     *
     * If the form has been submitted before, the "saved" field will have a
     * value and the default should be false. If the form has just been created
     * (i.e. this is a new widget), the default is true and so all links will be
     * included in the list.
     *
     * @param array $instance Widget instance parameters
     * @return void
     */
    public function form($instance)
    {
        $defaults = [
            'title' => 'Share',
            'icons' => 0,
            'captions' => 0,
            'embed' => 0,
        ];

        // Get a complete list of all possible networks
        $socialize = new Socialize();
        $networks = $socialize->getAvailableNetworks();
        $active = $socialize->getNetworks();

        // Set the default values for the networks
        foreach (array_keys($networks) as $key) {
            $defaults[$key] = 0;

            // The default active networks in the plugin are the ones that will
            // be active by default in the widget.
            if (!isset($instance['saved']) && array_key_exists($key, $active)) {
                $defaults[$key] = 1;
            }
        }

        // Parse the arguments and update the values accordingly
        $instance = wp_parse_args($instance, $defaults);
        $title = sanitize_text_field($instance['title']);

        ?>

        <input type="hidden" name="<?= $this->get_field_name('saved') ?>" value="1" />

        <p>
            <label for="<?= $this->get_field_id('title') ?>">
                <?= __('Title:') ?>
            </label>
            <input type="text" name="<?= $this->get_field_name('title') ?>" id="<?= $this->get_field_id('title') ?>" value="<?= esc_attr($title) ?>" class="widefat" />
        </p>

        <p>Networks:</p>

        <?php

        foreach ($networks as $key => $network) {
            ?>
            <p>
                <input type="checkbox" name="<?= $this->get_field_name($key) ?>" id="<?= $this->get_field_id($key) ?>" value="1" class="checkbox" <?= checked($instance[$key], 1) ?> />
                <label for="<?= $this->get_field_id($key) ?>">
                    <?= $network['name'] ?>
                </label>
            </p>
            <?php
        }

        ?>

        <p>Icons:</p>

        <p>
            <input type="checkbox" name="<?= $this->get_field_name('icons') ?>" id="<?= $this->get_field_id('icons') ?>" value="1" class="checkbox" <?= checked($instance['icons'], 1) ?> />
            <label for="<?= $this->get_field_id('icons') ?>">
                Show icons?
            </label>
        </p>

        <p>
            <input type="checkbox" name="<?= $this->get_field_name('captions') ?>" id="<?= $this->get_field_id('captions') ?>" value="1" class="checkbox" <?= checked($instance['captions'], 1) ?> />
            <label for="<?= $this->get_field_id('captions') ?>">
                Show icon captions?
            </label>
        </p>

        <p>
            <input type="checkbox" name="<?= $this->get_field_name('embed') ?>" id="<?= $this->get_field_id('embed') ?>" value="1" class="checkbox" <?= checked($instance['embed'], 1) ?> />
            <label for="<?= $this->get_field_id('embed') ?>">
                Embed SVG icons?
            </label>
        </p>

        <?php
    }
}
