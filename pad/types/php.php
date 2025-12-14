<?php

  if ( ! strlen ( $padOpt [$pad] [0] ) )
    return call_user_func_array ( $padTag [$pad], [] );

  $padUserFunc = $padOpt [$pad];

  unset ( $padUserFunc [0] );

  return call_user_func_array ( $padTag [$pad], $padUserFunc );

?>