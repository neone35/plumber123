<?php

/*
 * This file is part of WordPlate.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Str;

if (!function_exists('acf_hide_on_screen')) {
    /**
     * Create an ACF hide on screen array.
     *
     * @param string[] $items
     *
     * @deprecated since version 4.0
     *
     * @return array
     */
    function acf_hide_on_screen($items)
    {
        $array = [];

        foreach ($items as $i => $item) {
            $array[$i] = $item;
        }

        return $array;
    }
}

if (!function_exists('acf_location_query')) {
    /**
     * Create an ACF location query array.
     *
     * @param string $param
     * @param string $operator
     * @param string $value
     *
     * @deprecated since version 4.0
     *
     * @return array
     */
    function acf_location_query($param, $operator, $value)
    {
        return compact('param', 'operator', 'value');
    }
}

if (!function_exists('elixir')) {
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param string $file
     * @param string $buildDirectory
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    function elixir($file, $buildDirectory = 'assets/build')
    {
        static $manifest;
        static $manifestPath;

        if (is_null($manifest) || $manifestPath !== $buildDirectory) {
            $manifest = json_decode(file_get_contents(get_template_directory().'/'.$buildDirectory.'/rev-manifest.json'), true);
            $manifestPath = $buildDirectory;
        }

        if (isset($manifest[$file])) {
            return get_template_directory_uri().'/'.$buildDirectory.'/'.$manifest[$file];
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     * Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
