<?php

  // padRedirect puts the values into the query, in front of a #fragment - padAddGet does,
  // for every address it adds to: they went behind the #top, part of the fragment, which
  // never reaches the server.

  $rhAway = padCurl ( [ 'url' => $padGoExt . 'request/hophash&padInclude', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $rhResult = $rhAway ['result'] . ' ' . substr ( $rhAway ['headers'] ['Location'] ?? '', strlen ( $padHost . $padApp . '/' ) );

?>
