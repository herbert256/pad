<?php

  function padSeqCheckTribonacci ( $f, $n ) {

    if ( file_exists ( 'seq/types/tribonacci/bool.php' ) )
      return padSeqBoolTribonacci ( $n );

    if ( file_exists ( 'seq/types/tribonacci/generated.php' ) ) 
      return in_array ( $n, PADtribonacci );

    if ( file_exists ( 'seq/types/tribonacci/fixed.php' ) ) {
      $fixed = include 'seq/types/tribonacci/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence tribonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>