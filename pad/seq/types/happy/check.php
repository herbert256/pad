<?php

  function padSeqCheckHappy ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/happy/bool.php' ) )
      return padSeqBoolHappy ( $n );

    if ( file_exists ( PAD . 'seq/types/happy/generated.php' ) ) 
      return in_array ( $n, PADhappy );

    if ( file_exists ( PAD . 'seq/types/happy/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/happy/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq happy, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>