<?php

  if ( ! $pqParm )
    $pqParm = 1;

  return floor ( $pqLoop / $pqParm ) * $pqParm;

?>