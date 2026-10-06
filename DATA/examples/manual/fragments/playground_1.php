<?php

  $data = json_decode ( '{ "name": "Ann", "tags": [ "red", "blue" ] }', TRUE );

  $name = $data ['name'];
  $tags = $data ['tags'];

  $html = padCode ( '<b>{$name}</b>: {tags}{$tags}{notLast@tags}, {/notLast@tags}{/tags}' );

?>
