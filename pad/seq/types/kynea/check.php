<?php

  function padSeqCheckKynea ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/kynea/bool.php' ) )
      return padSeqBoolKynea ( $n );

    if ( file_exists ( PAD . 'seq/types/kynea/generated.php' ) ) 
      return in_array ( $n, PADkynea );

    if ( file_exists ( PAD . 'seq/types/kynea/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/kynea/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq kynea, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>