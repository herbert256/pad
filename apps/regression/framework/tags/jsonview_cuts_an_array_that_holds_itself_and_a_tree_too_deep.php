<?php

  // An array holding a reference to itself, a list of an object holding itself, and a list
  // nested deeper than json_decode would ever give.

  $self = [ 'x' => 1 ];
  $self ['me'] = &$self;

  $node = new stdClass;
  $node->name = 'root';
  $node->parent = $node;
  $nodes = [ $node ];

  $deep = 1;
  for ( $i = 0; $i < 600; $i++ )
    $deep = [ $deep ];

?>
