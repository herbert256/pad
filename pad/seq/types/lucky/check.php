<?php

  function padSeqCheckLucky ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/lucky/bool.php' ) )
      return padSeqBoolLucky ( $n );

    if ( file_exists ( PAD . 'seq/types/lucky/generated.php' ) ) 
      return in_array ( $n, PADlucky );

    if ( file_exists ( PAD . 'seq/types/lucky/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/lucky/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq lucky, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>