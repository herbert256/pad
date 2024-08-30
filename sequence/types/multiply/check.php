<?php

  function padSeqCheckMultiply ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/multiply/bool.php" ) )
      return padSeqBoolMultiply ( $n );

    $text = padCode ( "{sequence multiply, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>