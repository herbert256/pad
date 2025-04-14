<?php

  function padSeqCheckOdd ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/odd/bool.php' ) )
      return padSeqBoolOdd ( $n, $p );

    if ( file_exists ( 'sequence/types/odd/fixed.php' ) ) {
      $fixed = include 'sequence/types/odd/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/odd/generated.php' ) ) 
      return in_array ( $n, PADodd );

    $text = padCode ( "{sequence odd, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>