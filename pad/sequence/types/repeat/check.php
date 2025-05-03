<?php

  function pqCheckRepeat ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/repeat/bool.php' ) )
      return pqBoolRepeat ( $n, $p );

    if ( file_exists ( 'sequence/types/repeat/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/repeat/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/repeat/generated.php' ) ) 
      return in_array ( $n, PADrepeat );

    #$text = padCode ( "{sequence repeat='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence repeat='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>