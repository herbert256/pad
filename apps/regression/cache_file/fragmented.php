<?php

  // The fixture of fragment: a page that answers its fragment alone to an HTMX request - the
  // HX-Request header - and the whole page to everyone else, as lib/respond.php suggests.

  if ( isset ( $_SERVER ['HTTP_HX_REQUEST'] ) )
    $padFragmentOnly = 'part';

?>
