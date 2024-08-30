<?php

  function padSeqCheckLucas ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/lucas/bool.php" ) )
      return padSeqBoolLucas ( $n );

    $text = padCode ( "{sequence lucas, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>