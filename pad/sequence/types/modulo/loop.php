<?php

  if ( ! $pqParm )
    $pqParm = 1;

  return $pqLoop % (int) $pqParm;

?>