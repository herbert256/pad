<?php

  // The home page: the examples in their groups for the cards, and what the server says to
  // the island at the top - asked again by its button as JSON, the same page with
  // padFormat=json, which answers only what $padExpose names.

  $groups = reactGroups ();

  $hello = [ 'message'  => 'Hello from PAD',
             'php'      => PHP_VERSION,
             'time'     => date ( 'H:i:s' ),
             'patterns' => count ( reactData ( 'examples' ) ['examples'] ) ];

  $padExpose = [ 'hello' ];

?>
