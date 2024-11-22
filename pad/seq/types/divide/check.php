<?php

  function padSeqCheckDivide ( $f, $n ) {

    if ( file_exists ( 'seq/types/divide/bool.php' ) )
      return padSeqBoolDivide ( $n );

    if ( file_exists ( 'seq/types/divide/generated.php' ) ) 
      return in_array ( $n, PADdivide );

    if ( file_exists ( 'seq/types/divide/fixed.php' ) ) {
      $fixed = include 'seq/types/divide/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq divide, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>