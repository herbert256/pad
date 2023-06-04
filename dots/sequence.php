<?php

  if ( ! isset ( $padSeqStore [$name] ) )
    return INF;

  return padDotSearch ( $padSeqStore [$name], $names, $type);

?>