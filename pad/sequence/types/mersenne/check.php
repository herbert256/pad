<?php

  function pqCheckMersenne ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/mersenne/bool.php' ) )
      return pqBoolMersenne ( $n, $p );

    if ( file_exists ( 'sequence/types/mersenne/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/mersenne/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/mersenne/generated.php' ) ) 
      return in_array ( $n, PADmersenne );

    #$text = padCode ( "{sequence mersenne, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence mersenne, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>