<?php

  $data = json_decode ( '{ "name": "Ann", "tags": [ "red", "blue" ] }', TRUE );

  $name = $data ['name'];
  $tags = $data ['tags'];

  $html = padCode ( '<b>{$name}</b>: {array:tags}{$tags}{property:notLast@tags}, {/property:notLast@tags}{/array:tags}' );

?>
