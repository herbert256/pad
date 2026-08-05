<?php

  // The sequences type: search the sequence subsystem's store. With a $second part the
  // reference names one sequence and only that one is searched; without it the whole
  // store is. INF when the named sequence does not exist or nothing matched.

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