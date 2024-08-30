<?php

  function padSeqCheckPower ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/power/bool.php" ) )
      return padSeqBoolPower ( $n );

    $text = padCode ( "{sequence power, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>