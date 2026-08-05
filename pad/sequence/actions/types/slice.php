<?php

  // slice='pos|len' - the array_slice of the sequence, offset and optional length taken
  // from the parameter list and handed to actions/function.php. The offset is 0-based and
  // may be negative to count back from the end.

  $pqFunction = 'array_slice';

  $pqResult = include PQ . 'actions/function.php';

?>