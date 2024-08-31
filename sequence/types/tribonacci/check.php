<?php

  function padSeqCheckTribonacci ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/tribonacci/bool.php' ) )
      return padSeqBoolTribonacci ( $n );

    if ( file_exists ( '/pad/sequence/types/tribonacci/generated.php' ) ) 
      return in_array ( $n, PADtribonacci );

    if ( file_exists ( '/pad/sequence/types/tribonacci/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/tribonacci/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence tribonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>