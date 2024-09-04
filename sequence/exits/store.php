<?php

  foreach ( $padOptionsMulti as $padStartOption )
    if ( str_starts_with ( $padStartOption ['padPrmName'], 'store' ) )
      return;

  if ( $padPair [$pad] )
    return;

  $padSeqStore [$padSeqName] = array_values ( $padSeqResult );

?>