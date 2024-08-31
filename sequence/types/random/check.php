<?php

  function padSeqCheckRandom ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/random/bool.php' ) )
      return padSeqBoolRandom ( $n );

    if ( file_exists ( '/pad/sequence/types/random/generated.php' ) ) 
      return in_array ( $n, PADrandom );

    if ( file_exists ( '/pad/sequence/types/random/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/random/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence random, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>