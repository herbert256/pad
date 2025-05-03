<?php

  function pqCheckPronic ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/pronic/bool.php' ) )
      return pqBoolPronic ( $n, $p );

    if ( file_exists ( 'sequence/types/pronic/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/pronic/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/pronic/generated.php' ) ) 
      return in_array ( $n, PADpronic );

    #$text = padCode ( "{sequence pronic, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence pronic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>