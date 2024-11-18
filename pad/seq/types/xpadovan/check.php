<?php

  function padSeqCheckXpadovan ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/xpadovan/bool.php' ) )
      return padSeqBoolXpadovan ( $n );

    if ( file_exists ( PAD . 'seq/types/xpadovan/generated.php' ) ) 
      return in_array ( $n, PADxpadovan );

    if ( file_exists ( PAD . 'seq/types/xpadovan/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/xpadovan/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq xpadovan, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>