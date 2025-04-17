<?php

  $pqInc = 2;

  $pqStart = $pqFrom * 2;

  if ( $pqTo <> PHP_INT_MAX )
    $pqEnd = $pqTo * 2;

  if ( $pqStart % 2 )
    $pqStart++;

?>