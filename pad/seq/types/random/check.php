<?php

  function padSeqCheckRandom ( $f, $n ) {

    if ( file_exists ( 'seq/types/random/bool.php' ) )
      return padSeqBoolRandom ( $n );

    if ( file_exists ( 'seq/types/random/generated.php' ) ) 
      return in_array ( $n, PADrandom );

    if ( file_exists ( 'seq/types/random/fixed.php' ) ) {
      $fixed = include 'seq/types/random/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq random, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>