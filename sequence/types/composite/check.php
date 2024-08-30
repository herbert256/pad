<?php

  function padSeqCheckComposite ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/composite/bool.php" ) )
      return padSeqBoolComposite ( $n );

    $text = padCode ( "{sequence composite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>