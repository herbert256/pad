<?php

  function padSeqCheckRecaman ( $f, $n ) {

    if ( file_exists ( 'sequence/types/recaman/bool.php' ) )
      return padSeqBoolRecaman ( $n );

    if ( file_exists ( 'sequence/types/recaman/generated.php' ) ) 
      return in_array ( $n, PADrecaman );

    if ( file_exists ( 'sequence/types/recaman/fixed.php' ) ) {
      $fixed = include 'sequence/types/recaman/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence recaman, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>