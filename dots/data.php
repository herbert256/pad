<?php

  if ( ! isset ( $padDataStore [$name] ) )
    return INF;

  return padDotSearch ( $padDataStore [$name], $names, $type);

?>