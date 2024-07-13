<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  if ( file_exists ( padApp  . '_xref' ) ) 
    padRemoveDirectory ( padApp  . '_xref' );

  set_time_limit ( 1000 );

  foreach ( padListFiltered () as $one )
    padCurl ( "$padHost$padScript?" . $one ['item'] . '&padInclude' );

  echo "done";

?>