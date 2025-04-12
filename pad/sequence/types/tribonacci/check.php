<?php

  function padSeqCheckTribonacci ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/tribonacci/bool.php' ) )
      return padSeqBoolTribonacci ( $n, $p );

    if ( file_exists ( 'sequence/types/tribonacci/generated.php' ) ) 
      return in_array ( $n, PADtribonacci );

    if ( file_exists ( 'sequence/types/tribonacci/fixed.php' ) ) {
      $fixed = include 'sequence/types/tribonacci/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence tribonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>