<?php

  if ( $name ) {

    if ( ! isset ( $padSeqStore [$name] ) )
      return INF;

    return padAtSearch ( $padSeqStore [$name], $names );

  }

 return padAtSearch ( $padSeqStore, $names );

?>