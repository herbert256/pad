<?php

  if ( ! $pqParm )
    $pqParm = 1;

  return round ( $pqLoop / $pqParm ) * $pqParm;

?>