<?php

  // A post carries the token of the playground's own form, as the browser sends it: the
  // page is fetched first, and its session cookie and token travel back with the post.

  $form  = padCurl ( $padHost . 'playground/' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';

  $answer = padCurl ( [
    'url'     => $padHost . 'playground/?render',
    'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
    'post'    => [ 'source'       => '<ul>{items}<li>{$name}</li>{/items}</ul> {$title | upper}',
                   'data'         => '{ "title": "fruit", "items": [ { "name": "apple" }, { "name": "pear" } ] }',
                   'padCsrfToken' => $token ]
  ] ) ['data'];

?>
