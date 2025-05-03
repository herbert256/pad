<?php

  function pqCheckHappy ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/happy/bool.php' ) )
      return pqBoolHappy ( $n, $p );

    if ( file_exists ( 'sequence/types/happy/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/happy/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/happy/generated.php' ) ) 
      return in_array ( $n, PADhappy );

    #$text = padCode ( "{sequence happy, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence happy, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>