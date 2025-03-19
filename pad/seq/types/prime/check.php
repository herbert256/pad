<?php

  function padSeqCheckPrime ( $f, $n ) {

    if ( file_exists ( 'seq/types/prime/bool.php' ) )
      return padSeqBoolPrime ( $n );

    if ( file_exists ( 'seq/types/prime/generated.php' ) ) 
      return in_array ( $n, PADprime );

    if ( file_exists ( 'seq/types/prime/fixed.php' ) ) {
      $fixed = include 'seq/types/prime/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence prime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>