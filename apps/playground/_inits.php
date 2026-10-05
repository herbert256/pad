<?php

  // The playground renders whatever template it is sent: a local request only - the command
  // line, or this machine asking itself with nothing forwarded (padLocal). Anyone else is
  // answered 403 before a page of it runs, the way inits/page.php answers a 404.

  if ( ! padLocal () ) {

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () ) {
      http_response_code ( 403 );
      header ( 'Content-Type: text/plain; charset=UTF-8' );
    }

    echo 'The PAD playground runs the code it is given, so it answers this machine only.';

    $stop = 403;
    include PAD . 'exits/exit.php';

  }

?>
