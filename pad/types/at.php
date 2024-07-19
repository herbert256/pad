<?php

  $padReturn = padAtValueTag ( $padTag [$pad], -1 );

  if ( $padReturn === INF )
    return FALSE;

  return $padReturn;

?>