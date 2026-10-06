<?php

  // padTransform calls back for a filled value - 0 is one - and answers the default for a
  // blank one, a Closure default called with the blank value.

  $double = fn ( $v ) => $v * 2;

  $r = json_encode ( [
    padTransform ( 5,    $double ),
    padTransform ( 0,    $double ),
    padTransform ( '0',  $double ),
    padTransform ( '',   $double, 'none' ),
    padTransform ( '  ', $double ),
    padTransform ( NULL, $double, fn ( $v ) => 'made from ' . var_export ( $v, TRUE ) ),
    padTransform ( [],   'count', 'empty' ),
    padTransform ( [ 1, 2, 3 ], 'count', 'empty' ),
    padTransform ( 'ann', 'ucfirst' )
  ] );

?>
