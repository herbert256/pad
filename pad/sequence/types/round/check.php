<?php

  function pqCheckRound ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/round/bool.php' ) )
      return pqBoolRound ( $n, $p );

    if ( file_exists ( 'sequence/types/round/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/round/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/round/generated.php' ) ) 
      return in_array ( $n, PADround );

    #$text = padCode ( "{sequence round='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence round='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>