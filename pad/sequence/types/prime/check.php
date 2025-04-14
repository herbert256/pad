<?php

  function padSeqCheckPrime ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/prime/bool.php' ) )
      return padSeqBoolPrime ( $n, $p );

    if ( file_exists ( 'sequence/types/prime/fixed.php' ) ) {
      $fixed = include 'sequence/types/prime/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/prime/generated.php' ) ) 
      return in_array ( $n, PADprime );

    $text = padCode ( "{sequence prime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>