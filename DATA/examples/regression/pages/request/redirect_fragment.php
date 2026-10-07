<?php

  // The values set on {redirect} go into the address's query, before its fragment: they went
  // after the #top, where they are part of the fragment and never reach the server.

  $rfAway = padCurl ( [ 'url' => $padGoExt . 'request/hopfragment&padInclude', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $rfResult = $rfAway ['result'] . ' ' . ( $rfAway ['headers'] ['Location'] ?? '' );

?>
