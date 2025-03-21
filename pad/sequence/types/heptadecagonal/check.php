<?php

  function padSeqCheckHeptadecagonal ( $f, $n ) {

    if ( file_exists ( 'sequence/types/heptadecagonal/bool.php' ) )
      return padSeqBoolHeptadecagonal ( $n );

    if ( file_exists ( 'sequence/types/heptadecagonal/generated.php' ) ) 
      return in_array ( $n, PADheptadecagonal );

    if ( file_exists ( 'sequence/types/heptadecagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/heptadecagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence heptadecagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>