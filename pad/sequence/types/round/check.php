<?php

  function padSeqCheckRound ( $f, $n ) {

    if ( file_exists ( 'sequence/types/round/bool.php' ) )
      return padSeqBoolRound ( $n );

    if ( file_exists ( 'sequence/types/round/generated.php' ) ) 
      return in_array ( $n, PADround );

    if ( file_exists ( 'sequence/types/round/fixed.php' ) ) {
      $fixed = include 'sequence/types/round/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence round, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>