<?php

  function padSeqCheckEmirp ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/emirp/bool.php" ) )
      return padSeqBoolEmirp ( $n );

    $text = padCode ( "{sequence emirp, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>