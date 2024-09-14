<?php

  foreach ( $padSeqOptions as $padSeqOption ) 
    if ( str_starts_with ( $padSeqOption ['padPrmName'], 'store' ) )
      return;

  if ( $padPair [$pad] )
    return;

  $padSeqStore [$padSeqName] = array_values ( $padSeqResult );

?>