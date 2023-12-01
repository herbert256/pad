<?php

  if ( ! $padNoNo )
    return; 

  $padNoNo = padApp . "$padPage.php";

  if ( ! file_exists ( $padNoNo ) )
    padBootError ("Page does not exists: $padNoNo");

  foreach ($GLOBALS as $key => $value)
    if ( substr($key, 0, 3) == 'pad' and $key <> 'padNoNo' )
      unset ( $GLOBALS[$key] );

  unset ($key);
  unset ($value);

  include $padNoNo;

  $padNoBootShutdown = TRUE;

  exit();

?>