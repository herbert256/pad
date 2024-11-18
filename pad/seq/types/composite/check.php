<?php

  function padSeqCheckComposite ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/composite/bool.php' ) )
      return padSeqBoolComposite ( $n );

    if ( file_exists ( PAD . 'seq/types/composite/generated.php' ) ) 
      return in_array ( $n, PADcomposite );

    if ( file_exists ( PAD . 'seq/types/composite/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/composite/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq composite, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>