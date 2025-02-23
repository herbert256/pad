<?php

  function padSeqCheckAdd ( $f, $n ) {

    if ( file_exists ( 'seq/types/add/bool.php' ) )
      return padSeqBoolAdd ( $n );

    if ( file_exists ( 'seq/types/add/generated.php' ) ) 
      return in_array ( $n, PADadd );

    if ( file_exists ( 'seq/types/add/fixed.php' ) ) {
      $fixed = include 'seq/types/add/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq add, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>