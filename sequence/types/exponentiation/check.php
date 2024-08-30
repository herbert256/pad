<?php

  function padSeqCheckExponentiation ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/exponentiation/bool.php" ) )
      return padSeqBoolExponentiation ( $n );

    $text = padCode ( "{sequence exponentiation, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>