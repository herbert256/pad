<?php

  function padSeqCheckMoserdebruijn ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/moserdebruijn/bool.php" ) )
      return padSeqBoolMoserdebruijn ( $n );

    $text = padCode ( "{sequence moserdebruijn, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>