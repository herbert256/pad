<?php

  // A signed link writes the page's segments URL-encoded, as padUrl does: a route segment
  // with a space or an accent - signing_route/Café au lait - stood in the link as it is,
  // and a mail client ends a link at its first space. Fetched as the link was made, in the
  // clean form every server runs as index.php/<page>, the signature holds.

  $padCleanUrls = TRUE;
  $routeLink    = padSignedUrl ( 'helpers/signing_route/Café au lait', [ 'id' => 42 ] );
  $padCleanUrls = FALSE;

  $routeLink = str_replace ( "$padApp/helpers/", "$padApp/index.php/helpers/", $routeLink );
  $routeGot  = padCurl ( $routeLink );

  $routeResult = ( preg_match ( '/[\s\x80-\xFF]/', $routeLink ) ? 'raw' : 'encoded' ) . ': '
               . $routeGot ['result'] . ' '
               . trim ( html_entity_decode ( strip_tags ( $routeGot ['data'] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

?>
