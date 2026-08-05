<?php

  // merge='seq' - merges each named store into the sequence in value order, dropping any
  // value that is already present. The work is in combine.php, which keeps duplicates
  // only when $pqAction is 'combine'.

  include PQ . 'actions/types/combine.php';

?>