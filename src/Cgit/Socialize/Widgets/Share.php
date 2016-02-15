<?php

namespace Cgit\Socialize\Widgets;

use Cgit\Socialize;
use WP_Widget;

class Share extends WP_Widget
{
    /**
     * Register widget
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
     */
    public function widget($args, $instance)
    {
        $social = new Socialize();
        $networks = [];

        // If the network name is saved in the widget instance, add it to the
        // array of networks passed to the Socialize instance.
        foreach (array_keys($social->defaultNetworks) as $key) {
            if (isset($instance[$key]) && $instance[$key]) {
                $networks[] = $key;
            }
        }

        // Set networks
        $social->setNetworks($networks);

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        // Display list of social network links
        echo $social->render();
        echo $args['after_widget'];
    }

    /**
     * Display widget settings
     *
     * If the form has been submitted before, the "saved" field will have a
     * value and the default should be false. If the form has just been created
     * (i.e. this is a new widget), the default is true and so all links will be
     * included in the list.
     */
    public function form($instance)
    {
        $defaults = [
            'title' => 'Share this page',
        ];

        // Set the default values, depending on whether the hidden input has
        // been submitted (i.e. whether the widget has been saved before).
        foreach (array_keys($this->defaultNetworks) as $key) {
            $defaults[$key] = isset($instance['saved']) ? 0 : 1;
        }

        $instance = wp_parse_args($instance, $defaults);
        $title = sanitize_text_field($instance['title']);

        ?>
        <input
            type="hidden"
            name="<?= $this->get_field_name('saved') ?>"
            value="1" />
        <p>
            <label for="<?= $this->get_field_id('title') ?>">
                <?= __('Title:') ?>
            </label>
            <input
                type="text"
                name="<?= $this->get_field_name('title') ?>"
                id="<?= $this->get_field_id('title') ?>"
                value="<?= esc_attr($title) ?>"
                class="widefat" />
        </p>
        <?php

        foreach ($this->defaultNetworks as $key => $value) {

            ?>
            <p>
                <input
                    type="checkbox"
                    name="<?= $this->get_field_name($key) ?>"
                    id="<?= $this->get_field_id($key) ?>"
                    value="1"
                    class="checkbox"
                    <?= checked($instance[$key], 1) ?> />
                <label for="<?= $this->get_field_id($key) ?>">
                    <?= $value ?>
                </label>
            </p>
            <?php

        }
    }
}
