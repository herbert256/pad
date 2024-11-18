<?php

  function padSeqCheckCaterer ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/caterer/bool.php' ) )
      return padSeqBoolCaterer ( $n );

    if ( file_exists ( PAD . 'seq/types/caterer/generated.php' ) ) 
      return in_array ( $n, PADcaterer );

    if ( file_exists ( PAD . 'seq/types/caterer/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/caterer/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq caterer, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>