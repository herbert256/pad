<?php

  function pqCheckEven ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/even/bool.php' ) )
      return pqBoolEven ( $n, $p );

    if ( file_exists ( 'sequence/types/even/fixed.php' ) ) {
      $fixed = include 'sequence/types/even/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/even/generated.php' ) ) 
      return in_array ( $n, PADeven );

    $text = padCode ( "{sequence even, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>