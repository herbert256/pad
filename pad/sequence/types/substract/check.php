<?php

  function padSeqCheckSubstract ( $f, $n ) {

    if ( file_exists ( 'sequence/types/substract/bool.php' ) )
      return padSeqBoolSubstract ( $n );

    if ( file_exists ( 'sequence/types/substract/generated.php' ) ) 
      return in_array ( $n, PADsubstract );

    if ( file_exists ( 'sequence/types/substract/fixed.php' ) ) {
      $fixed = include 'sequence/types/substract/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence substract, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>