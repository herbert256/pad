<?php

  function padSeqCheckModulo ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/modulo/bool.php" ) )
      return padSeqBoolModulo ( $n );

    $text = padCode ( "{sequence modulo, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>