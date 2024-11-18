<?php

  function padSeqCheckHeptadecagonal ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/heptadecagonal/bool.php' ) )
      return padSeqBoolHeptadecagonal ( $n );

    if ( file_exists ( PAD . 'seq/types/heptadecagonal/generated.php' ) ) 
      return in_array ( $n, PADheptadecagonal );

    if ( file_exists ( PAD . 'seq/types/heptadecagonal/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/heptadecagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq heptadecagonal, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>