<?php

  // {redirect} to a page with a #fragment goes there - the strict check looked for a page
  // named 'request/redirect_fragment#top' and the request ended in a 500.

  $rpAway   = padCurl ( [ 'url' => $padGoExt . 'request/hoppagefragment&padInclude', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );
  $rpResult = $rpAway ['result'] . ' ' . preg_replace ( '#^.*/regression/pages/#', '', $rpAway ['headers'] ['Location'] ?? '' );

?>
