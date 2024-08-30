<?php

  function padSeqCheckGnomonic ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/gnomonic/bool.php" ) )
      return padSeqBoolGnomonic ( $n );

    $text = padCode ( "{sequence gnomonic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>