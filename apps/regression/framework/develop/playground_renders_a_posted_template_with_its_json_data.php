<?php

  $answer = padCurl ( [
    'url'  => $padHost . 'playground/?render',
    'post' => [ 'source' => '<ul>{items}<li>{$name}</li>{/items}</ul> {$title | upper}',
                'data'   => '{ "title": "fruit", "items": [ { "name": "apple" }, { "name": "pear" } ] }' ]
  ] ) ['data'];

?>
