<?php

  function padSeqCheckOr ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/or/bool.php' ) )
      return padSeqBoolOr ( $n );

    if ( file_exists ( '/pad/sequence/types/or/generated.php' ) ) 
      return in_array ( $n, PADor );

    if ( file_exists ( '/pad/sequence/types/or/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/or/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence or, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>