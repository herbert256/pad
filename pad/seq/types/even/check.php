<?php

  function padSeqCheckEven ( $f, $n ) {

    if ( file_exists ( 'seq/types/even/bool.php' ) )
      return padSeqBoolEven ( $n );

    if ( file_exists ( 'seq/types/even/generated.php' ) ) 
      return in_array ( $n, PADeven );

    if ( file_exists ( 'seq/types/even/fixed.php' ) ) {
      $fixed = include 'seq/types/even/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq even, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>