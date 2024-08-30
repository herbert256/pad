<?php

  function padSeqCheckPronic ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/pronic/bool.php" ) )
      return padSeqBoolPronic ( $n );

    $text = padCode ( "{sequence pronic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>