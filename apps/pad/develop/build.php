<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  if ( file_exists ( '/app/'  . '_xref' ) ) 
    padRemoveDirectory ( '/app/'  . '_xref' );

  set_time_limit ( 1000 );

  foreach ( padListFiltered () as $one )
    padCurl ( "$padHost$padScript?" . $one ['item'] . '&padInclude' );

  echo "done";

?>