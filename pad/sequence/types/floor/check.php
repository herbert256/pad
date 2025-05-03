<?php

  function pqCheckFloor ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/floor/bool.php' ) )
      return pqBoolFloor ( $n, $p );

    if ( file_exists ( 'sequence/types/floor/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/floor/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/floor/generated.php' ) ) 
      return in_array ( $n, PADfloor );

    #$text = padCode ( "{sequence floor='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence floor='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>