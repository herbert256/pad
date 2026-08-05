<?php

  // Info block: lists the plays this run ran as "<play>/<sequence>" entries, e.g. keep/prime.
  // Reads $pqPlays (each entry carries pqPlay and pqSeq, put there by plays/add.php) and
  // appends to $pqInfo['plays']; extract() leaves $pqPlay and $pqSeq overwritten afterwards.

  foreach ( $pqPlays as $pqTmp ) {

    extract ( $pqTmp );

    $pqInfo ['plays'] [] = "$pqPlay/$pqSeq";

  }

?>