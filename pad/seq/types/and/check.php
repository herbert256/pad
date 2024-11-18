<?php

  function padSeqCheckAnd ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/and/bool.php' ) )
      return padSeqBoolAnd ( $n );

    if ( file_exists ( PAD . 'seq/types/and/generated.php' ) ) 
      return in_array ( $n, PADand );

    if ( file_exists ( PAD . 'seq/types/and/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/and/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq and, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>