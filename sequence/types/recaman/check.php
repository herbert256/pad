<?php

  function padSeqCheckRecaman ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/recaman/bool.php" ) )
      return padSeqBoolRecaman ( $n );

    $text = padCode ( "{sequence recaman, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>