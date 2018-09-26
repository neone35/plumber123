<?php

/*
 * This file is part of WordPlate.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Set default permalink structure.
 */
add_action('admin_init', function () {
    global $wp_rewrite;

    $pattern = get_theme_support('plate-permalink');

    $wp_rewrite->set_permalink_structure(reset($pattern));
});
