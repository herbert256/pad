<?php

  function pqCheckTetrahedral ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/tetrahedral/bool.php' ) )
      return pqBoolTetrahedral ( $n, $p );

    if ( file_exists ( 'sequence/types/tetrahedral/fixed.php' ) ) {
      $fixed = include 'sequence/types/tetrahedral/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/tetrahedral/generated.php' ) ) 
      return in_array ( $n, PADtetrahedral );

    $text = padCode ( "{sequence tetrahedral, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>