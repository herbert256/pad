<?php

  function padSeqCheckMultiply ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/multiply/bool.php' ) )
      return padSeqBoolMultiply ( $n );

    if ( file_exists ( '/pad/sequence/types/multiply/generated.php' ) ) 
      return in_array ( $n, PADmultiply );

    if ( file_exists ( '/pad/sequence/types/multiply/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/multiply/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence multiply, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>