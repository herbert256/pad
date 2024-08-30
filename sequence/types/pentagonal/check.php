<?php

  function padSeqCheckPentagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/pentagonal/bool.php" ) )
      return padSeqBoolPentagonal ( $n );

    $text = padCode ( "{sequence pentagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>