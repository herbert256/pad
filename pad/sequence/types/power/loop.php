<?php

  // Loop build for power, the strategy pqBuild() picks for this type: the parameter raised
  // to the loop value, so power=2 gives 2, 4, 8, 16, 32, ... Membership questions go to
  // bool.php instead.

  return $pqParm ** $pqLoop;

?>