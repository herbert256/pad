<?php

  function padSeqCheckOeis ( $f, $n ) {

    if ( file_exists ( 'sequence/types/oeis/bool.php' ) )
      return padSeqBoolOeis ( $n );

    if ( file_exists ( 'sequence/types/oeis/generated.php' ) ) 
      return in_array ( $n, PADoeis );

    if ( file_exists ( 'sequence/types/oeis/fixed.php' ) ) {
      $fixed = include 'sequence/types/oeis/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence oeis, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>