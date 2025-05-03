<?php

  function pqCheckCullen ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/cullen/bool.php' ) )
      return pqBoolCullen ( $n, $p );

    if ( file_exists ( 'sequence/types/cullen/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/cullen/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/cullen/generated.php' ) ) 
      return in_array ( $n, PADcullen );

    #$text = padCode ( "{sequence cullen, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence cullen, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>