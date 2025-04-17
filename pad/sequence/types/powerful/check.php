<?php

  function pqCheckPowerful ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/powerful/bool.php' ) )
      return pqBoolPowerful ( $n, $p );

    if ( file_exists ( 'sequence/types/powerful/fixed.php' ) ) {
      $fixed = include 'sequence/types/powerful/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/powerful/generated.php' ) ) 
      return in_array ( $n, PADpowerful );

    $text = padCode ( "{sequence powerful, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>