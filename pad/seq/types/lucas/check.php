<?php

  function padSeqCheckLucas ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/lucas/bool.php' ) )
      return padSeqBoolLucas ( $n );

    if ( file_exists ( PAD . 'seq/types/lucas/generated.php' ) ) 
      return in_array ( $n, PADlucas );

    if ( file_exists ( PAD . 'seq/types/lucas/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/lucas/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq lucas, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>