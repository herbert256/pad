<?php

  // Marker file, never included: its existence is what makes 'orderly' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // Modifier for the randomize action: the drawn entries keep the order they had in the
  // sequence instead of coming out shuffled. actions/types/randomize.php reads it into
  // $pqRandomOrderly and passes it to pqRandom(). Companions: duplicates, atLeastOnce.

  return TRUE;

?>