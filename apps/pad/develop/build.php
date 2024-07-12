<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  if ( file_exists ( padApp  . '_xref' ) ) padRemoveDirectory ( padApp  . '_xref' );
  if ( file_exists ( padData . 'xref'  ) ) padRemoveDirectory ( padData . 'xref'  );

  set_time_limit ( 1000 );

  foreach ( padList ( 0 ) as $one ) {
    $curlPage = "$padHost$padScript?" . $one ['item'];
    padCurl (  $curlPage             );
    padCurl ( "$curlPage&padInclude" );
  }

  echo "done";

?>