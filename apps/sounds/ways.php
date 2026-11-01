<?php

  $title = 'Ways to play';
  $ways  = [];

  foreach ( soundsWays () as $page => list ( $wayTitle, $wayText ) )
    $ways [] = [ 'page' => "ways/$page", 'wayTitle' => $wayTitle, 'wayText' => $wayText ];

?>
