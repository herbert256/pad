<?php

  // First wind-down stage: lets the sequence type clean up after itself by including
  // types/<$pqSeq>/exit.php when that type provides one. Runs before any action is applied,
  // so the type still sees $pqResult exactly as it built it.

  if ( file_exists ( PT . "$pqSeq/exit.php" ) )
    include PT . "$pqSeq/exit.php";

?>