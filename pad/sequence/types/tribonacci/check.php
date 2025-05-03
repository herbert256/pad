<?php

  function pqCheckTribonacci ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/tribonacci/bool.php' ) )
      return pqBoolTribonacci ( $n, $p );

    if ( file_exists ( 'sequence/types/tribonacci/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/tribonacci/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/tribonacci/generated.php' ) ) 
      return in_array ( $n, PADtribonacci );

    #$text = padCode ( "{sequence tribonacci, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence tribonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>