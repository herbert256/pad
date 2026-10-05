<?php

  // The page number from the address, kept within the pages
  // there are, and the places of a race.

  $pages = 12;
  $kept  = [];

  foreach ( [ -3, 4, 40 ] as $asked )
    $kept [] = [
      'asked' => $asked,
      'shown' => padNumberClamp ( $asked, 1, $pages )
    ];

  $finished = [ 1, 2, 3, 11, 22, 101 ];
  $ordinals = array_map ( 'padNumberOrdinal', $finished );
  $places   = implode ( ', ', $ordinals );

?>
