<?php

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'padSeq') )
      if ( $padK <> 'padSeqStore' and $padK <> 'padSeqStartType' )
        unset ( $GLOBALS [$padK] );

?>