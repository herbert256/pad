<?php

  function padSeqCheckHexagonal ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/hexagonal/bool.php' ) )
      return padSeqBoolHexagonal ( $n );

    if ( file_exists ( '/pad/sequence/types/hexagonal/generated.php' ) ) 
      return in_array ( $n, PADhexagonal );

    if ( file_exists ( '/pad/sequence/types/hexagonal/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/hexagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence hexagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>