<?php

  // Resolves typed references and function calls - the TYPE tokens padEvalAfter created,
  // such as field:name, data:items or a pipe function name. Scans $start..$end for the last
  // TYPE token and hands it to eval/type/type.php as $k, with the preceding key as $b;
  // that dispatches on the kind in [2] to either eval/single/ or eval/parms/, writes the
  // value back as a VAL and re-enters padEvalOpr.
  //
  // Rightmost first is deliberate: a call's parameters may themselves contain TYPE tokens,
  // and those have to be values before the outer call is made. Called by padEvalOpr before
  // the precedence walk.

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