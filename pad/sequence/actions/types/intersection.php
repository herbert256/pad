<?php

  // intersection='seq' - keeps only the values that also occur in every named store, by
  // handing array_intersect to actions/function.php.

  $pqFunction = 'array_intersect';

  $pqResult = include PQ . 'actions/function.php';

?>