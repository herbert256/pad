<?php

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'padSeq') )
      if ( $padK <> 'padSeqStore' )
        unset ( $GLOBALS [$padK] );

?>