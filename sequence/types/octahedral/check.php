<?php

  function padSeqCheckOctahedral ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/octahedral/bool.php" ) )
      return padSeqBoolOctahedral ( $n );

    $text = padCode ( "{sequence octahedral, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>