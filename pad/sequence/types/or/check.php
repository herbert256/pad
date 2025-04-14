<?php

  function padSeqCheckOr ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/or/bool.php' ) )
      return padSeqBoolOr ( $n, $p );

    if ( file_exists ( 'sequence/types/or/fixed.php' ) ) {
      $fixed = include 'sequence/types/or/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/or/generated.php' ) ) 
      return in_array ( $n, PADor );

    $text = padCode ( "{sequence or='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>