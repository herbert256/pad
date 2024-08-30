<?php

  function padSeqCheckPrime ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/prime/bool.php" ) )
      return padSeqBoolPrime ( $n );

    $text = padCode ( "{sequence prime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>