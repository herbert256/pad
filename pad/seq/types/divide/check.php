<?php

  function padSeqCheckDivide ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/divide/bool.php' ) )
      return padSeqBoolDivide ( $n );

    if ( file_exists ( PAD . 'seq/types/divide/generated.php' ) ) 
      return in_array ( $n, PADdivide );

    if ( file_exists ( PAD . 'seq/types/divide/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/divide/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq divide, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>