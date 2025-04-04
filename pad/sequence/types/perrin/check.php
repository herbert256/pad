<?php

  function padSeqCheckPerrin ( $f, $n ) {

    if ( file_exists ( 'sequence/types/perrin/bool.php' ) )
      return padSeqBoolPerrin ( $n );

    if ( file_exists ( 'sequence/types/perrin/generated.php' ) ) 
      return in_array ( $n, PADperrin );

    if ( file_exists ( 'sequence/types/perrin/fixed.php' ) ) {
      $fixed = include 'sequence/types/perrin/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence perrin, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>