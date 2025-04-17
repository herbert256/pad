<?php

  function pqCheckEval ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/eval/bool.php' ) )
      return pqBoolEval ( $n, $p );

    if ( file_exists ( 'sequence/types/eval/fixed.php' ) ) {
      $fixed = include 'sequence/types/eval/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/eval/generated.php' ) ) 
      return in_array ( $n, PADeval );

    $text = padCode ( "{sequence eval='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>