<?php

  foreach ( $pqPlays as $pqTmp ) {
        
    extract ( $pqTmp );

    $pqLoop = $pq;

    include 'sequence/plays/one.php';

    if ( $pq === FALSE )
      return;

  }

?>