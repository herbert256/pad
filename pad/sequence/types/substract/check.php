<?php

  function padSeqCheckSubstract ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/substract/bool.php' ) )
      return padSeqBoolSubstract ( $n, $p );

    if ( file_exists ( 'sequence/types/substract/fixed.php' ) ) {
      $fixed = include 'sequence/types/substract/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/substract/generated.php' ) ) 
      return in_array ( $n, PADsubstract );

    $text = padCode ( "{sequence substract='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>