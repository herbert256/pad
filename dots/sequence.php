<?php

  if ( $name ) {

    if ( ! isset ( $padSeqStore [$name] ) )
      return INF;

    return padDotSearch ( $padSeqStore [$name], $names, $type );

  }

 return padDotSearch ( $padSeqStore, $names, $type );

?>