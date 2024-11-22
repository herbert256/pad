<?php

  function padSeqCheckHeptadecagonal ( $f, $n ) {

    if ( file_exists ( 'seq/types/heptadecagonal/bool.php' ) )
      return padSeqBoolHeptadecagonal ( $n );

    if ( file_exists ( 'seq/types/heptadecagonal/generated.php' ) ) 
      return in_array ( $n, PADheptadecagonal );

    if ( file_exists ( 'seq/types/heptadecagonal/fixed.php' ) ) {
      $fixed = include 'seq/types/heptadecagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq heptadecagonal, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>