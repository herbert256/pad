<?php

  // $padErrorAction 'exit': drop the request the moment anything goes wrong. padErrorGo runs
  // exits/exit.php directly - no message, no log, no dump, and none of padExit's normal
  // header and output work.

  include PAD . "error/error.php";

  // The request ends where it stood, shipping nothing - as a failure: it ended on PHP's
  // default 200, an empty page that said all was well.

  function padErrorGo ( $error, $file, $line ) {

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () )
      http_response_code ( 500 );

    include PAD . 'exits/exit.php';

  }

?>