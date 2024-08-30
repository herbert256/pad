<?php

  function padSeqCheckRange ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/range/bool.php" ) )
      return padSeqBoolRange ( $n );

    $text = padCode ( "{sequence range, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>