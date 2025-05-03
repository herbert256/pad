<?php

  function pqCheckBell ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/bell/bool.php' ) )
      return pqBoolBell ( $n, $p );

    if ( file_exists ( 'sequence/types/bell/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/bell/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/bell/generated.php' ) ) 
      return in_array ( $n, PADbell );

    #$text = padCode ( "{sequence bell, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence bell, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>