<?php

  function padSeqCheckPolite ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/polite/bool.php' ) )
      return padSeqBoolPolite ( $n, $p );

    if ( file_exists ( 'sequence/types/polite/generated.php' ) ) 
      return in_array ( $n, PADpolite );

    if ( file_exists ( 'sequence/types/polite/fixed.php' ) ) {
      $fixed = include 'sequence/types/polite/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence polite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>