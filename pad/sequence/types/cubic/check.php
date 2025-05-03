<?php

  function pqCheckCubic ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/cubic/bool.php' ) )
      return pqBoolCubic ( $n, $p );

    if ( file_exists ( 'sequence/types/cubic/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/cubic/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/cubic/generated.php' ) ) 
      return in_array ( $n, PADcubic );

    #$text = padCode ( "{sequence cubic, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence cubic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>