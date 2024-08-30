<?php

  function padSeqCheckPowerful ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/powerful/bool.php" ) )
      return padSeqBoolPowerful ( $n );

    $text = padCode ( "{sequence powerful, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>