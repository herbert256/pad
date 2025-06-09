<?php

  function pqBoolPower ( $x, $y ) {

    if ( $x == 1 and $y <> 1 )
      return FALSE;

    $a = $x;
    $b = $y;

    while ($x % $y == 0)
        $x = $x / $y;

    if ($x == 1)
      return TRUE;
    else
      return FALSE;

  }

?>