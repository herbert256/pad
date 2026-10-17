<?php

  // An object a page's PHP leaves - a stdClass record, an ArrayObject, a generator - iterates
  // as the array it holds, as padData reads one: each was "there is no tag named ...".

  $who = (object) [ 'name' => 'Ann', 'city' => 'Paris' ];
  $ao  = new ArrayObject ( [ [ 'a' => 1 ], [ 'a' => 2 ] ] );
  $gen = ( function () { yield [ 'a' => 3 ]; yield [ 'a' => 4 ]; } ) ();

?>
