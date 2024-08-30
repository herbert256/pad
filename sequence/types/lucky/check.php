<?php

  function padSeqCheckLucky ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/lucky/bool.php" ) )
      return padSeqBoolLucky ( $n );

    $text = padCode ( "{sequence lucky, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>