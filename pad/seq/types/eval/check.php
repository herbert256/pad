<?php

  function padSeqCheckEval ( $f, $n ) {

    if ( file_exists ( 'seq/types/eval/bool.php' ) )
      return padSeqBoolEval ( $n );

    if ( file_exists ( 'seq/types/eval/generated.php' ) ) 
      return in_array ( $n, PADeval );

    if ( file_exists ( 'seq/types/eval/fixed.php' ) ) {
      $fixed = include 'seq/types/eval/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq eval, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>