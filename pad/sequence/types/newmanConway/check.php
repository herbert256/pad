<?php

  function pqCheckNewmanConway ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/newmanConway/bool.php' ) )
      return pqBoolNewmanConway ( $n, $p );

    if ( file_exists ( 'sequence/types/newmanConway/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/newmanConway/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/newmanConway/generated.php' ) ) 
      return in_array ( $n, PADnewmanConway );

    #$text = padCode ( "{sequence newmanConway, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence newmanConway, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>