<?php

  function padSeqCheckMultiply ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/multiply/bool.php' ) )
      return padSeqBoolMultiply ( $n, $p );

    if ( file_exists ( 'sequence/types/multiply/generated.php' ) ) 
      return in_array ( $n, PADmultiply );

    if ( file_exists ( 'sequence/types/multiply/fixed.php' ) ) {
      $fixed = include 'sequence/types/multiply/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence multiply='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>