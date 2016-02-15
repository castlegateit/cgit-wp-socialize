<?php

use Cgit\Socialize;

/**
 * Return Cgit\Socialize object
 */
function cgit_socialize($networks = false) {
    return new Socialize($networks);
}

/**
 * Return an array of sharing URLs
 */
function cgit_socialize_urls($networks = false) {
    $social = new Socialize($networks);
    return $social->getLinks();
}

/**
 * Return an HTML list of sharing links
 */
function cgit_socialize_links($networks = false) {
    $social = new Socialize($networks);
    return $social->render();
}
