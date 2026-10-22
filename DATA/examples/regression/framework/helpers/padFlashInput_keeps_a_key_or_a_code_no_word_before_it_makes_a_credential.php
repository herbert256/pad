<?php

  // A song's key, a list's sort key, a meta_key/meta_value pair, a shortcut, a translation
  // editor's key, the codes of an address, a token's name and a passport's number are
  // ordinary fields: they come back after padFlashInput (); padBack ().

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'key' => 'C major', 'sort_key' => 'name', 'meta_key' => 'color', 'group_key' => 'region',
             'shortcutKey' => 'Ctrl+S', 'translation_key' => 'cart.title', 'zip_code' => '2611', 'postal_code' => 'AB1',
             'country_code' => 'NL', 'token_name' => 'CI deploy', 'passport' => 'X123', 'title' => 'x' ];

  padFlashInput ();

  $flashed = [];

  foreach ( array_keys ( $_POST ) as $flashKey )
    $flashed [] = "$flashKey=[" . padOld ( $flashKey ) . ']';

  $flashed = implode ( ' ', $flashed );

?>
