<?php

  // Loop build for round: rounds each loop value to the nearest multiple of the parameter,
  // which defaults to 1 and then leaves the value alone. So round=10 turns 1, 2, 3, ...
  // into runs of 0, 10, 20, ...; add unique to get the multiples themselves.

  if ( ! $pqParm )
    $pqParm = 1;

  return round ( $pqLoop / $pqParm ) * $pqParm;

?>