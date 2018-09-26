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
 * Remove dashboard widgets.
 */
add_action('wp_dashboard_setup', function () {
    global $wp_meta_boxes;

    $positions = [
        'dashboard_activity' => 'normal',
        'dashboard_incoming_links' => 'normal',
        'dashboard_plugins' => 'normal',
        'dashboard_recent_comments' => 'normal',
        'dashboard_right_now' => 'normal',
        'dashboard_primary' => 'side',
        'dashboard_quick_press' => 'side',
        'dashboard_recent_drafts' => 'side',
        'dashboard_secondary' => 'side',
    ];

    $boxes = get_theme_support('plate-dashboard');

    foreach (reset($boxes) as $box) {
        $position = $positions[$box] ?: 'normal';

        unset($wp_meta_boxes['dashboard'][$position]['core'][$box]);
    }
});
