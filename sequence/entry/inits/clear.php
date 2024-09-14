<?php

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'padSeq') and $padK <> 'padSeqStore' )
      unset ( $GLOBALS [$padK] );

?>