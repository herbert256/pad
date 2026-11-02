<?php

  // A post carries the token of Live preview's own form, as the browser sends it: the
  // page is fetched first, and its session cookie and token travel back with the post.

  $form  = padCurl ( $padHost . 'live/' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';

  $answer = padCurl ( [
    'url'     => $padHost . 'live/?render',
    'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
    'post'    => [ 'source'       => '<ul>{array:items}<li>{$name}</li>{/array:items}</ul> {$title | upper}',
                   'data'         => '{ "title": "fruit", "items": [ { "name": "apple" }, { "name": "pear" } ] }',
                   'padCsrfToken' => $token ]
  ] ) ['data'];

?>
