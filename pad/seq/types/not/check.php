<?php

  function padSeqCheckNot ( $f, $n ) {

    if ( file_exists ( 'seq/types/not/bool.php' ) )
      return padSeqBoolNot ( $n );

    if ( file_exists ( 'seq/types/not/generated.php' ) ) 
      return in_array ( $n, PADnot );

    if ( file_exists ( 'seq/types/not/fixed.php' ) ) {
      $fixed = include 'seq/types/not/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq not, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>