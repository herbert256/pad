<?php

  // Info block: lists the plays this run ran as "<play>/<sequence>" entries, e.g. keep/prime.
  // Reads $pqPlays (each entry carries pqPlay and pqSeq, put there by plays/add.php) and
  // appends to $pqInfo['plays'].

  // Read without extract (): it left $pqPlay and $pqSeq those of the last play, and the trace
  // events/sequence.php makes after this filed the run under that play's type.

  foreach ( $pqPlays as $pqTmp )
    $pqInfo ['plays'] [] = $pqTmp ['pqPlay'] . '/' . $pqTmp ['pqSeq'];

?>
