<?php

  function padSeqCheckPell ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/pell/bool.php' ) )
      return padSeqBoolPell ( $n );

    if ( file_exists ( PAD . 'seq/types/pell/generated.php' ) ) 
      return in_array ( $n, PADpell );

    if ( file_exists ( PAD . 'seq/types/pell/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/pell/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq pell, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>