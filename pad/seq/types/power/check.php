<?php

  function padSeqCheckPower ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/power/bool.php' ) )
      return padSeqBoolPower ( $n );

    if ( file_exists ( PAD . 'seq/types/power/generated.php' ) ) 
      return in_array ( $n, PADpower );

    if ( file_exists ( PAD . 'seq/types/power/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/power/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq power, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>