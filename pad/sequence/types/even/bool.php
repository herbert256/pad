<?php

  function padSeqBoolEven( $n, $p=0 ) {

    if ( $n & 1 )
      return FALSE;
    else
      return TRUE;

  }

?>