<?php

  global $pqStore;

  if ( $type )
    if ( isset ( $pqStore [$type] ) )
      return padAtSearch ( $pqStore [$type], $names );
    else
      return INF;

  if ( is_array ( $pqStore ) )
    return padAtSearch ( $pqStore, $names );

  return INF;

?>
