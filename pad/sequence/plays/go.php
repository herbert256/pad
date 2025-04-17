<?php

  $pqPlayType = 'build';

  foreach ( $pqPlays as $pqPlay ) {
    
    extract ( $pqPlay );

    $pqLoop = $pq;

    include 'sequence/plays/one.php';

    if ( $pq === FALSE )
      return;

  }

?>