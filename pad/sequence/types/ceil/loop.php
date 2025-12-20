<?php

  if ( ! $pqParm )
    $pqParm = 1;

  return ceil ( $pqLoop / (int) $pqParm ) * $pqParm;

?>
