<?php

  function pqCheckPolite ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/polite/bool.php' ) )
      return pqBoolPolite ( $n, $p );

    if ( file_exists ( 'sequence/types/polite/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/polite/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/polite/generated.php' ) ) 
      return in_array ( $n, PADpolite );

    #$text = padCode ( "{sequence polite, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence polite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>