<?php

  // dedup (alias unique) - drops repeated values, keeping the first occurrence of each
  // with its key. Flagged in actions/merge/, so a parameter naming stores dedups those
  // together with the current sequence.

  $pqResult = array_unique ( $pqResult );

?>