<?php

  function padEvalType ( &$result, $myself, $start=0, $end=PHP_INT_MAX  ) {

    $typeK = FALSE;

    $b = -1;

    foreach ( $result as $k => $t ) {

      if ( $k < $start ) continue;
      if ( $k > $end   ) break;

      if ( $result[$k][1] == 'TYPE' ) {
        $typeK = $k;
        $typeB = $b;
      }

      $b = $k;

    }

    if ( $typeK ) {
      $k = $typeK;
      $b = $typeB;
      include PAD . 'eval/type/type.php';
    }

  }

?>
