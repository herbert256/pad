<?php

  if     ( $padSeqSet        ) $padSeqStore [$padSeqSet]  = array_values ( $padSeqResult );
  elseif ( ! $padPair [$pad] ) $padSeqStore [$padSeqName] = array_values ( $padSeqResult );

  if ( $padType [$pad] == 'one' or $padType [$pad] == 'action' )  
    unset ( $padSeqStore [$padSeqStartStore] );

?>