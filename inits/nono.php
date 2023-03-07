<?php

  if ( ! $padNoNo )
    return; 

  $padNoNo = APP . "/pages/$page.php";

  if ( ! padExists ( $padNoNo ) )
    padBootError ("Page does not exists: $padNoNo");

  foreach ($GLOBALS as $key => $value)
    if ( substr($key, 0, 3) == 'pad' and $key <> 'padNoNo' )
      unset ( $GLOBALS[$key] );

  unset ($page);
  unset ($app);
  unset ($key);
  unset ($value);

  unset($PADSESSID);
  unset($PADREFID);
  unset($PADREQID);

  include $padNoNo;

  $padNoBootShutdown = TRUE;

  exit();

?>