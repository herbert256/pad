<?php

  function padSeqCheckFloor ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/floor/bool.php' ) )
      return padSeqBoolFloor ( $n );

    if ( file_exists ( '/pad/sequence/types/floor/generated.php' ) ) 
      return in_array ( $n, PADfloor );

    if ( file_exists ( '/pad/sequence/types/floor/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/floor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence floor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>