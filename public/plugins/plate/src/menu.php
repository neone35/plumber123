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
 * Remove menu items.
 */
add_action('admin_head', function () {
    $items = get_theme_support('plate-menu');

    $elements = implode(', #menu-', reset($items));

    echo sprintf('<style> #menu-%s { display: none !important; } </style>', $elements);
});
