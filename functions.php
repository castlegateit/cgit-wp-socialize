<?php

use Cgit\Socialize;

/**
 * Return Cgit\Socialize object
 *
 * @param array|bool $networks
 * @return \Cgit\Socialize
 */
function cgit_socialize($networks = false) {
    return new Socialize($networks);
}

/**
 * Return an array of sharing URLs
 *
 * @param array|bool $networks
 * @return array
 */
function cgit_socialize_urls($networks = false) {
    $social = new Socialize($networks);
    return $social->getLinks();
}

/**
 * Return an HTML list of sharing links
 *
 * @param array|bool $networks
 * @return string
 */
function cgit_socialize_links($networks = false) {
    $social = new Socialize($networks);
    return $social->render();
}
