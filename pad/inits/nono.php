<?php

  if ( ! $padNoNo )
    return; 

  $padNoNo = padApp . "$padPage.php";

  if ( ! padExists ( $padNoNo ) )
    padBootError ("Page does not exists: $padNoNo");

  foreach ($GLOBALS as $key => $value)
    if ( substr($key, 0, 3) == 'pad' and $key <> 'padNoNo' )
      unset ( $GLOBALS[$key] );

  unset ($padPage);
  unset ($key);
  unset ($value);

  unset($padSesID);
  unset($padRefID);
  unset($padReqID);

  include $padNoNo;

  $padNoBootShutdown = TRUE;

  exit();

?>