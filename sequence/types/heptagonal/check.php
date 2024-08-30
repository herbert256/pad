<?php

  function padSeqCheckHeptagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/heptagonal/bool.php" ) )
      return padSeqBoolHeptagonal ( $n );

    $text = padCode ( "{sequence heptagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>