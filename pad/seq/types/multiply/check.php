<?php

  function padSeqCheckMultiply ( $f, $n ) {

    if ( file_exists ( 'seq/types/multiply/bool.php' ) )
      return padSeqBoolMultiply ( $n );

    if ( file_exists ( 'seq/types/multiply/generated.php' ) ) 
      return in_array ( $n, PADmultiply );

    if ( file_exists ( 'seq/types/multiply/fixed.php' ) ) {
      $fixed = include 'seq/types/multiply/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq multiply, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>