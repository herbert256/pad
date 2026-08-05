<?php

  // Wipes the subsystem's state so a run starts clean, by dropping every $pq* global.
  //
  // Three are spared: $pqStore, the named sequences that must survive between tags, $pqEntry,
  // which says how this run was started, and the $pqSet* values a direct caller passed in.

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'pq') )
      if ( $padK != 'pqStore' and ! str_starts_with ( $padK, 'pqSet') and $padK != 'pqEntry' )
        unset ( $GLOBALS [$padK] );

?>