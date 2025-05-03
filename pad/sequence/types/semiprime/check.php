<?php

  function pqCheckSemiprime ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/semiprime/bool.php' ) )
      return pqBoolSemiprime ( $n, $p );

    if ( file_exists ( 'sequence/types/semiprime/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/semiprime/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/semiprime/generated.php' ) ) 
      return in_array ( $n, PADsemiprime );

    #$text = padCode ( "{sequence semiprime, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence semiprime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>