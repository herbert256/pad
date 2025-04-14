<?php

  function padSeqCheckHexagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/hexagonal/bool.php' ) )
      return padSeqBoolHexagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/hexagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/hexagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/hexagonal/generated.php' ) ) 
      return in_array ( $n, PADhexagonal );

    $text = padCode ( "{sequence hexagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>