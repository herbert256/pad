<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  set_time_limit ( 300 );

  foreach ( padList ( 0 ) as $one ) {
    $curlPage = "$padHost$padScript?" . $one ['item'];
    padCurl (  $curlPage             );
    padCurl ( "$curlPage&padInclude" );
  }

  echo "done";

?>