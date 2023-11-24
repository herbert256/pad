<?php

  function padSeqBoolMultiple ( $n ) {

    global $padSeqMultiple;

    return ( $n == ceil ( $n / $padSeqMultiple) * $padSeqMultiple );

  }

?>