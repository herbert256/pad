<?php

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'pq') )
      if ( $padK <> 'pqStore' )
        unset ( $GLOBALS [$padK] );

?>