<?php

  $padReturn = padAt ( $padTag [$pad], 'tag' );

  if ( $padReturn === INF )
    return FALSE;

  return $padReturn;

?>