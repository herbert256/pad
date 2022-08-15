<?php

  if ( ! $padNoNo )
    return; 

  ob_get_clean();

  $padNoNo = PAD . "$app/pages/$page.php";

  if ( ! file_exists ( $padNoNo ) )
    padBootError ("Page does not exists: $app/$page");

  foreach ($GLOBALS as $key => $value)
    if ( substr($key, 0, 3) == 'pad' and $key <> 'pNo_no')
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