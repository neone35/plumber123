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
 * Set custom login logo.
 */
add_action('login_head', function () {
    $args = get_theme_support('plate-login');

    if (empty($args[0])) {
        return;
    }

    $styles = [
        sprintf('background-image: url(%s);', $args[0]),
    ];

    if (count($args) >= 2) {
        $styles[] = sprintf('width: %dpx;', $args[1]);
        $styles[] = sprintf('background-size: %dpx auto;', $args[1]);
    }

    echo sprintf('<style> .login h1 a { %s } </style>', implode(' ', $styles));
});

/*
 * Set custom login logo url.
 */
add_filter('login_headerurl', function () {
    return get_bloginfo('url');
});

/*
 * Set custom login error message.
 */
add_filter('login_errors', function () {
    return '<strong>Whoops!</strong> Looks like you missed something there. Have another go.';
});
