<?php

  function padSeqCheckSquare ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/square/bool.php" ) )
      return padSeqBoolSquare ( $n );

    $text = padCode ( "{sequence square, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>