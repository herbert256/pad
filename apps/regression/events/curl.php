<?php

  // Two remote fetches - a page of this application, and a port nothing listens on - and
  // the curl hook hears both, the failure too.

  padCurl ( $padHost . 'regression/events/?output&padInclude' );
  padCurl ( 'http://127.0.0.1:1/' );

  $heard = implode ( ', ', $GLOBALS ['heardCurl'] ?? [] );

?>
