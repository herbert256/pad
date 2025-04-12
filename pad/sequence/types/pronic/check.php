<?php

  function padSeqCheckPronic ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/pronic/bool.php' ) )
      return padSeqBoolPronic ( $n, $p );

    if ( file_exists ( 'sequence/types/pronic/generated.php' ) ) 
      return in_array ( $n, PADpronic );

    if ( file_exists ( 'sequence/types/pronic/fixed.php' ) ) {
      $fixed = include 'sequence/types/pronic/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence pronic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>