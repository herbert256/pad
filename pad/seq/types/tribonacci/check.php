<?php

  function padSeqCheckTribonacci ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/tribonacci/bool.php' ) )
      return padSeqBoolTribonacci ( $n );

    if ( file_exists ( PAD . 'seq/types/tribonacci/generated.php' ) ) 
      return in_array ( $n, PADtribonacci );

    if ( file_exists ( PAD . 'seq/types/tribonacci/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/tribonacci/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq tribonacci, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>