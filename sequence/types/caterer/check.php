<?php

  function padSeqCheckCaterer ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/caterer/bool.php" ) )
      return padSeqBoolCaterer ( $n );

    $text = padCode ( "{sequence caterer, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>