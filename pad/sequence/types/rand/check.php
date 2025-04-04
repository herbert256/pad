<?php

  function padSeqCheckRand ( $f, $n ) {

    if ( file_exists ( 'sequence/types/rand/bool.php' ) )
      return padSeqBoolRand ( $n );

    if ( file_exists ( 'sequence/types/rand/generated.php' ) ) 
      return in_array ( $n, PADrand );

    if ( file_exists ( 'sequence/types/rand/fixed.php' ) ) {
      $fixed = include 'sequence/types/rand/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence rand, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>