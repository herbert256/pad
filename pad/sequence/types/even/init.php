<?php

  $pqInc = 2;

  $pqFrom = $pqFrom * 2;

  if ( $pqTo <> PHP_INT_MAX )
    $pqTo = $pqTo * 2;

  if ( $pqFrom % 2 )
    $pqFrom++;

?>
