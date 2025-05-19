<?php

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'pq') )
      if ( $padK <> 'pqStore' and ! str_starts_with ( $padK, 'pqSet') and $padK <> 'pqEntry' )
        unset ( $GLOBALS [$padK] );

?>