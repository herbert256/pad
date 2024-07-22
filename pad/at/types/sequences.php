<?php

  global $padSeqStore;

  if ( $type )
    if ( isset ( $padSeqStore [$type] ) ) 
      return padAtSearch ( $padSeqStore [$type], $names );
    else
      return INF;

  if ( is_array ( $padSeqStore ) )
    return padAtSearch ( $padSeqStore, $names );

  return INF;

?>