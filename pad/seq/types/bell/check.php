<?php

  function padSeqCheckBell ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/bell/bool.php' ) )
      return padSeqBoolBell ( $n );

    if ( file_exists ( PAD . 'seq/types/bell/generated.php' ) ) 
      return in_array ( $n, PADbell );

    if ( file_exists ( PAD . 'seq/types/bell/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/bell/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq bell, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>