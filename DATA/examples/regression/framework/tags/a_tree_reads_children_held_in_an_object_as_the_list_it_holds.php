<?php

  $menu = [
    [ 't' => 'a', 'children' => new ArrayObject ( [ [ 't' => 'a1' ], [ 't' => 'a2' ] ] ) ],
    [ 't' => 'b', 'children' => ( function () { yield [ 't' => 'b1' ]; yield [ 't' => 'b2' ]; } ) () ],
    [ 't' => 'c', 'children' => [ (object) [ 't' => 'c1' ] ] ],
    [ 't' => 'd' ]
  ];

?>
