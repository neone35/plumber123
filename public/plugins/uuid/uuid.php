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
 * Plugin Name: UUID
 * Description: A UUID plugin for WordPlate.
 * Author: WordPlate
 * Author URI: https://wordplate.github.io
 * Version: 1.0.0
 * Plugin URI: https://github.com/wordplate/uuid
 */

 use Ramsey\Uuid\Uuid;

 /*
  * Add urn to allowed protocols.
  */
 add_filter('kses_allowed_protocols', function ($protocols) {
     $protocols[] = 'urn';

     return $protocols;
 });

 /*
  * Add UUID to new posts.
  */
 add_filter('wp_insert_post_data', function ($data) {
     if (empty($data['guid'])) {
         $uuid = Uuid::uuid4();

         $data['guid'] = wp_slash(sprintf('urn:uuid:%s', $uuid->toString()));
     }

     return $data;
 });

 /*
  * Add UUID to new attachements.
  */
 add_filter('wp_insert_attachment_data', function ($data) {
     $uuid = Uuid::uuid4();

     $data['guid'] = wp_slash(sprintf('urn:uuid:%s', $uuid->toString()));

     return $data;
 });

 /*
  * Use short UUID for file names.
  */
 add_filter('wp_handle_upload_prefilter', function ($file) {
     if (!is_array($file) && !is_string($file)) {
         return $file;
     }

     if (!is_array($file)) {
         $file = ['name' => $file];
     }

     $name = Uuid::uuid4();
     $extension = end(explode('.', $file['name']));

     $file['name'] = sprintf('%s.%s', $name, $extension);

     return $file;
 });
