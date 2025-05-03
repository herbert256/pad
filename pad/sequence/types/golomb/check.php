<?php

  function pqCheckGolomb ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/golomb/bool.php' ) )
      return pqBoolGolomb ( $n, $p );

    if ( file_exists ( 'sequence/types/golomb/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/golomb/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/golomb/generated.php' ) ) 
      return in_array ( $n, PADgolomb );

    #$text = padCode ( "{sequence golomb, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence golomb, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>